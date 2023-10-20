<?php

namespace App\Console\Commands;

use App\Events\ChangeDanaStatus;
use App\Jobs\DanaEditCourseJob;
use App\Jobs\DeleteOrTransferContentToDanaProductJob;
use App\Models\Content;
use App\Models\Contentset;
use App\Models\DanaAuthor;
use App\Models\DanaContentTransfer;
use App\Models\DanaProductContentTransfer;
use App\Models\DanaProductSetTransfer;
use App\Models\DanaProductTransfer;
use App\Models\DanaSetTransfer;
use App\Models\Product;
use App\Models\User;
use App\Services\DanaProductService;
use App\Services\DanaService;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Helper\ProgressBar;

class TransferToDanaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaatv:transfer:dana';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'transfer file to dana';

    private $product;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    //createTeachers
    public function handle1()
    {
        $this->products =
            Product::query()->WhereIn('id', array_keys(Product::ABRISHAM_2_DATA))->get();
        $this->info('Transferring new teachers to Dana');
        $products = $this->products;
        $addedTeacherIds = [];

        $productCount = $products->count();
        if (!$this->confirm("{$productCount} ,continue?")) {
            return false;
        }
        $this->info('Progress bar indicates products which are being transferred');
        $progressBar = new ProgressBar($this->output, $productCount);


        foreach ($products as $product) {
            $sets = $product->sets()->get();
            $countOfSets = $sets->count();
            $this->info("{$countOfSets} sets were found for product {$product->id}");
            foreach ($sets as $set) {


                $teacher = $set?->user;
                $allContents = $set->contents->whereNotNull('author_id');
                if (!isset($teacher)) {
                    $firstContent =
                        $allContents->where('contenttype_id',
                            config('constants.CONTENT_TYPE_VIDEO'))->sortBy('order')->first();
                    $teacher = $firstContent?->user;
                }
                if (!isset($teacher)) {
                    $firstContent =
                        $allContents->where('contenttype_id',
                            config('constants.CONTENT_TYPE_PAMPHLET'))->sortBy('order')->first();
                    $teacher = $firstContent?->user;
                }
                if (!isset($teacher)) {
                    $this->info("No teachers found for set {$set->id}");
                    $progressBar->advance();

                    continue;
                }

                if (in_array($teacher->id, $addedTeacherIds)) {
                    $this->info("Teacher {$teacher->id} was already added");
                    $progressBar->advance();

                    continue;
                }

                $danaAuthor = DanaAuthor::where('author_id', $teacher->id)->first();
                if (isset($danaAuthor)) {
                    $this->info("Teacher {$teacher->id} has been transferred before");
                    $progressBar->advance();
                    continue;
                }

                $this->info("Adding user {$teacher->id}");
                $result = DanaService::createTeacher($teacher);
                if (!$result) {
                    $this->error('Teacher was not created');
                    $progressBar->advance();
                    continue;
                }

                $addedTeacherIds[] = $teacher->id;
            }

            $progressBar->advance();
        }


        $progressBar->finish();
        $this->info('Done');
    }

    //GetTeachers
    public function handle2()
    {
        $this->info('Storing new teachers to alaa database');
        $result = DanaService::getTeachers();
        $data = $result['result']['data'];

        $count = count($data);
        if (!$this->confirm("{$count} ,continue?")) {
            return false;
        }
        $this->info('Progress bar indicates teachers which are being stored');
        $progressBar = new ProgressBar($this->output, $count);

        foreach ($data as $datum) {
            $teacher = User::query()->where('mobile', $datum['cellPhone'])->whereHas('roles', function ($q) {
                $q->where('id', 10);
            })->first();

            if (!isset($teacher)) {
                $this->info("No Alaa account found for Dana teacher {$datum['cellPhone']}");
                $progressBar->advance();
                continue;
            }

            $danaAuthor = DanaAuthor::where('author_id', $teacher->id)->first();
            if (isset($danaAuthor)) {
                $this->info("Alaa teacher {$teacher->id} had been transferred before");
                $progressBar->advance();
                continue;
            }

            $danaTeacher = new DanaAuthor();
            $danaTeacher->dana_author_id = $datum['teacherId'];
            $danaTeacher->author_id = $teacher->id;
            $danaTeacher->save();

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->info('Done!');
    }

    //creating courses
    public function handle3()
    {
        $this->info('Transferring contents to Dana');
        $products = $this->products;
        $productCount = $products->count();
        if (!$this->confirm("{$productCount} ,continue?")) {
            return false;
        }
        $this->info('Progress bar indicates products which are being transferred');
        $progressBar = new ProgressBar($this->output, $productCount);
        foreach ($products as $product) {
            $sets = $product->sets()->active()->whereNull('redirectUrl')->get();
            foreach ($sets as $set) {
                $freeContentsCount =
                    $set->activeContents()->where('contenttype_id',
                        config('constants.CONTENT_TYPE_VIDEO'))->where('isFree', 1)->count();
                if ($freeContentsCount) {
                    $this->warn("{$product->id} has free contents in set {$set->id}");
                    $progressBar->advance();
                    continue;
                }

                if (str_contains($set->name, 'تکلیف') || str_contains($set->name, 'تکالیف') || str_contains($set->name,
                        'تمرین') || str_contains($set->name, 'مشاوره')) {
                    $progressBar->advance();
                    continue;
                }

                $courseKey = DanaService::createCourse($set, $product);
                if ($courseKey == false) {
                    Log::channel('debug')->debug("In TransferToDanaCommand : content set {$set->id} was not transferred");
                    $this->error("Content set {$set->id} was not transferred");
                    $progressBar->advance();
                    continue;
                }

                $danaTransfer = DanaSetTransfer::where('contentset_id', $set->id)->first();
                $danaTransfer->update(['status' => DanaSetTransfer::TRANSFERRING]);
                $contents = $set->activeContents()->whereNull('redirectUrl')->get();
                if ($contents->isEmpty()) {
                    Log::channel('debug')->debug("In TransferToDanaCommand : content set {$set->id} has no contents");
                    $this->warn("In TransferToDanaCommand : content set {$set->id} has no contents");
                    $progressBar->advance();
                    continue;
                }
                $this->info('transferring conents of set '.$set->id);
                foreach ($contents as $content) {
                    $danaSession = DanaContentTransfer::query()->where('educationalcontent_id', $content->id)->first();
                    if (isset($danaSession) && in_array($danaSession->status,
                            [DanaContentTransfer::SUCCESSFULLY_TRANSFERRED, DanaContentTransfer::TRANSFERRING])) {
                        continue;
                    }

                    self::transferContentToDana($courseKey, $content);
                }
                $danaTransfer->update(['status' => DanaSetTransfer::SUCCESSFULLY_TRANSFERRED]);
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->info('Done');
        return 0;
    }

    //Fixing sessions of DanaContentTransfer table

    public static function transferContentToDana($courseKey, $content)
    {
        $order = $content->order;

        if ($content->contenttype_id == config('constants.CONTENT_TYPE_VIDEO')) {
            $introContent = Content::query()->where('contentset_id', $content->contentset_id)
                ->where('contenttype_id', $content->contenttype_id)
                ->where('order', 0)
                ->first();
            if (isset($introContent)) {
                $order = $content->order + 1;
            }
            DanaService::createVideoSession($courseKey, $content, $order);
        } else {
            if ($content->contenttype_id == config('constants.CONTENT_TYPE_PAMPHLET')
                && Content::active()->where('contentset_id', $content->contentset_id)
                    ->whereNull('redirectUrl')
                    ->where('contenttype_id', config('constants.CONTENT_TYPE_VIDEO'))
                    ->where('order', $content->order)
                    ->doesntExist()) {
                DanaService::createPamphletSession($courseKey, $content, $order);
            }
        }
    }

    //Tranffering Chatre Nejat products to Dana

    public function handle4()
    {
        $this->info('Updating sessions');

        $danaSessions = DanaContentTransfer::all()->chunk(4);
        $count = $danaSessions->count();
        if (!$this->confirm("{$count} chunks,continue?")) {
            return false;
        }
        $this->info('Progress bar indicates session chunks which are being updated');
        $progressBar = new ProgressBar($this->output, $count);
        foreach ($danaSessions as $danaSessionChunk) {
            $updateData = $this->preparingContentTransferDtoForUpdatingSessions($danaSessionChunk);
            DanaService::updateSessionPriority($updateData);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->info('Done');
        return 0;
    }

    //add moshavere set to chatr nejat products

    private function preparingContentTransferDtoForUpdatingSessions(Collection $danaSessions)
    {
        $dto = [];
        $addedSessions = [];
        $contents = Content::whereIn('id', $danaSessions->pluck('educationalcontent_id'))->get();
        $arrayKey = 0;
        foreach ($danaSessions as $key => $danaSession) {
            if (in_array($danaSession->dana_session_id, $addedSessions)) {
                continue;
            }

            $content = $contents->where('id', $danaSession->educationalcontent_id)->first();
            if (!isset($content)) {
                Log::channel('debug')->debug('In TransferToDanaCommand preparingDtoForUpdatingSessions : content '.$danaSession->educationalcontent_id.' not found');
                continue;
            }

            $order = $content->order;
            if (!isset($order)) {
                Log::channel('debug')->debug('In TransferToDanaCommand preparingDtoForUpdatingSessions : '.$danaSession->educationalcontent_id.' has no order');
                continue;
            }
            $dto[$arrayKey]['Id'] = $danaSession->dana_session_id;
            $dto[$arrayKey]['Priority'] = $order;
            $addedSessions[] = $danaSession->dana_session_id;
            $arrayKey++;
        }

        return $dto;
    }

    //updating contents

    public function handle5()
    {
        $products = $this->products;

        $productCount = $products->count();
        if (!$this->confirm("{$productCount} ,continue?")) {
            return false;
        }
        $this->info('Progress bar indicates products which are being transferred');
        $progressBar = new ProgressBar($this->output, $productCount);


        foreach ($products as $product) {
            $sets = $product->sets;
            foreach ($sets as $set) {
                foreach ($set->activeContents as $content) {
                    DeleteOrTransferContentToDanaProductJob::dispatch($content);
                }
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->info('Done!');
        return false;
    }

    //insert data from dana_set_transfer to dana_product_transfer

    public function handle7()
    {
        $moshavereSet = Contentset::find(2294);
        $moshavereSet->small_name = 'مشاوره';
        $danaProducts = DanaProductTransfer::all();
        $productCount = $danaProducts->count();
        $progressBar = new ProgressBar($this->output, $productCount);
        foreach ($danaProducts as $danaProduct) {
            DanaProductService::createSession($danaProduct->dana_course_id, $moshavereSet, 1, $danaProduct->product_id);
            $progressBar->advance();
        }
        $progressBar->finish();
    }

    //update hardship of dana course

    public function handle8()
    {
        $this->info('Updating products');

        $ids =
            DanaProductContentTransfer::where('status', 4)->where('created_at', '>=',
                '2023-05-06 00:00:00')->pluck('educationalcontent_id')->toArray();
        $contents = Content::whereIn('id', $ids)->get();

        $count = $contents->count();
        if (!$this->confirm("{$count} ,continue?")) {
            return false;
        }
        $this->info('Progress bar indicates products which are being transferred');
        $progressBar = new ProgressBar($this->output, $count);

        foreach ($contents as $content) {
            DeleteOrTransferContentToDanaProductJob::dispatch($content);
            $progressBar->advance();
        }

        $this->info('Done!');
        $this->info("\n");
        $progressBar->finish();
    }

    public function handle9()
    {
        $this->info('insert data from dana_set_transfer to dana_product_transfer');
        $danaSets = DanaSetTransfer::all();
        $count = $danaSets->count();
        if (!$this->confirm("$count found , continue?")) {
            return false;
        }
        $data = [];
        foreach ($danaSets as $danaSet) {
            $data[] = [
                'dana_course_id' => $danaSet->dana_course_id,
                'product_id' => Contentset::find($danaSet->contentset_id)->products->first()->id,
                'status' => $danaSet->status,
                'created_at' => $danaSet->created_at,
                'updated_at' => $danaSet->updated_at,
                'insert_type' => 1,
            ];
        }
        DanaProductTransfer::insert($data);
        $this->info('done');
    }

    // delete adviser set from dana courses and re-upload it

    public function handle10()
    {
        $this->info('update hardship of dana course');
        $productsInDana =
            DanaProductTransfer::where('insert_type', 2)->where('status',
                DanaProductTransfer::SUCCESSFULLY_TRANSFERRED)->get();
        $count = $productsInDana->count();
        if (!$this->confirm("$count found , continue?")) {
            return false;
        }
        foreach ($productsInDana as $productInDana) {
            $chatrIds = array_keys(Product::ALL_CHATR_NEJAT2_PRODUCTS);
            $tetaIds = [628, 683, 600];
            if (in_array($productInDana->product_id, $chatrIds)) {
                $extraDto['hardship'] = 3;
            } else {
                if (in_array($productInDana->product_id, $tetaIds)) {
                    $extraDto['hardship'] = 4;
                } else {
                    $extraDto['hardship'] = 5;
                }
            }

            DanaEditCourseJob::dispatch(Product::find($productInDana->product_id));
        }

        if (!$this->confirm("$count found , continue to change statuses?")) {
            return false;
        }
        foreach ($productsInDana as $productInDana) {
            ChangeDanaStatus::dispatch($productInDana->dana_course_id);
        }

        $this->info('done');
    }

    //delete duplicate file in sessions

    public function handle11()
    {
        $danaProducts = DanaProductTransfer::where('status', 3)->get();
        $count = $danaProducts->count();
        if (!$this->confirm("$count found , continue?")) {
            return false;
        }
        $bar = $this->output->createProgressBar($count);
        foreach ($danaProducts as $danaProduct) {
            try {
                DanaProductService::editShareOfCp($danaProduct->dana_course_id);
                ChangeDanaStatus::dispatch($danaProduct->dana_course_id);
            } catch (Exception $exception) {
                Log::channel('danaTransfer')->debug($exception->getMessage());
            }
            $bar->advance();
        }
        $bar->finish();
        $this->info('done');
    }

    public function handle12()
    {
        $this->info('delete adviser set from dana courses and re-upload it');
        $danaAdviserSessions = DanaProductSetTransfer::where('contentset_id', 2294)->get();
        $count = $danaAdviserSessions->count();
        if (!$this->confirm("$count found , continue?")) {
            return false;
        }
        $progressBar = new ProgressBar($this->output, $count);
        $progressBar->start();
        foreach ($danaAdviserSessions as $danaAdviserSession) {
            $productId = $danaAdviserSession->product_id;
            $danaCourse = DanaProductTransfer::where('product_id', $productId)->pluck('dana_course_id');
            if (is_null($danaCourse)) {
                $this->info("dana course for product #{$productId} was not found");
                $progressBar->advance();
                continue;
            }

            $courseId = $danaCourse->first();
            $sessionId = $danaAdviserSession->dana_session_id;
            DanaProductService::deleteSession(['courseId' => $courseId, 'sessionId' => $sessionId]);
            DanaProductContentTransfer::where('dana_session_id', $sessionId)->delete();
            $danaAdviserSession->delete();
            $danaSessionId = DanaProductService::createSession($courseId, Contentset::find(2294), 1, $productId);

            DanaProductService::updateSessionPriority([
                'Id' => $danaSessionId,
                'Priority' => 1,
            ]);
            $progressBar->advance();
        }
        $progressBar->finish();
        $this->info('Done!');
    }

    public function handle13()
    {
        $this->info('delete duplicate file in sessions');
        $danaSets =
            DanaProductSetTransfer::where('status', DanaProductSetTransfer::SUCCESSFULLY_TRANSFERRED)->get();
        $count = $danaSets->count();
        if (!$this->confirm("$count found , continue?")) {
            return false;
        }
        $progressBar = new ProgressBar($this->output, $count);
        $progressBar->start();
        foreach ($danaSets as $danaSet) {
            $danaCourseId = DanaProductTransfer::where('product_id', $danaSet->product_id)->first()->dana_course_id;
            $danaSessionContentInfo = DanaProductService::getSessionContent($danaCourseId, $danaSet->dana_session_id);
            unset($danaSessionContentInfo['status_code']);
            $danaSessionContentIdInfo = array_column($danaSessionContentInfo, 'contentId');
            $differenceContents = array_unique(array_diff_assoc($danaSessionContentIdInfo,
                array_unique($danaSessionContentIdInfo)));

            foreach ($differenceContents as $differenceContent) {
                $danaInfoArrayKey = array_search($differenceContent,
                    array_column($danaSessionContentInfo, 'contentId'));
                $danaInfo = $danaSessionContentInfo[$danaInfoArrayKey];

                $danaProductContent = DanaProductContentTransfer::where('dana_session_id', $danaInfo['session'])
                    ->where('dana_course_id', $danaInfo['courseId'])
                    ->where('dana_filemanager_content_id', $danaInfo['contentId'])
                    ->where('status', DanaProductContentTransfer::SUCCESSFULLY_TRANSFERRED)->first();
                if (is_null($danaProductContent)) {
                    Log::channel('danaTransfer')->debug("In handle13 : content with filemanagerId={$danaInfo['contentId']} and courseId={$danaInfo['courseId']} and sessionId={$danaInfo['session']} not found so was delete duplicated file and was not upload again");
                    continue;
                }

                DanaProductService::deleteContent($danaInfo['courseId'], $danaInfo['session'], $danaInfo['contentId'],
                    $danaInfo['type']);


                $educationalContentId = $danaProductContent->educationalcontent_id;
                $danaProductContent->delete();

                DanaProductService::createContentWithoutUpload($danaInfo, $educationalContentId);
                ChangeDanaStatus::dispatch($danaInfo['courseId']);
            }
            $progressBar->advance();

        }
        $progressBar->finish();
        $this->info('done');
    }

    public function login()
    {
        return Cache::remember('dana-auth-token', 'https://ugcbe.danaapp.ir/api', function () {
            try {
                $client = new Client();
                $response = $client->request(
                    'POST',
                    '/api/v1/general/auth/login',
                    [
                        'headers' => [
                            'Accept' => 'application/json'
                        ],
                        'form_params' => [
                            'name' => config('services.bonyad.name'),
                            'password' => config('services.bonyad.password')
                        ]
                    ]
                );
            } catch (Exception $exception) {
                Log::error($exception->getCode().' - '.$exception->getMessage());
                $errors = json_decode($exception->getResponse()->getBody()->getContents());
                return ['errors' => $errors->erros, 'status_code' => $exception->getCode()];
            }
            $data = json_decode($response->getBody()->getContents(), true);
            $data['status_code'] = $response->getStatusCode();
            return $data['data']['authorisation']['token'];
        });
    }
}
