<?php

namespace App\Console\Commands;

use App\Models\Content;
use App\Models\Contentset;
use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ParachuteDataForStageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaatv:parachute:data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parachute data for stage';

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
    public function handle()
    {
        Product::whereIn('id', Product::ALL_CHATR_NEJAT2_PRODUCTS)->update(['enable' => 1, 'display' => 1]);
        $products = Product::whereIn('id', Product::ALL_CHATR_NEJAT2_PRODUCTS)->get();
        $setIds = [];
        foreach ($products as $product) {
            foreach ($product->sets()->get() as $set) {
                $setIds[] = $set->id;
            }
        }
        Contentset::whereIn('id', $setIds)->update(['enable' => 1, 'display' => 1, 'author_id' => 4]);

        $setIdsForVideos = array_chunk($setIds, ceil(count($setIds) / 2));
        $setIdsForVideos[0] = array_merge($setIdsForVideos[0], $setIdsForVideos[0], $setIdsForVideos[0]);
        $setIdsForVideos[1] = array_merge($setIdsForVideos[1], $setIdsForVideos[1]);
        $setIdsForVideos = array_merge($setIdsForVideos[0], $setIdsForVideos[1]);
        asort($setIdsForVideos);
        $setIdsForVideos = array_values($setIdsForVideos);

        $videoContents =
            Content::where('contenttype_id', 8)
                ->where('enable', true)
                ->where('display', true)
                ->where('file', '<>', null)
                ->orderBy('created_at', 'desc')
                ->limit(count($setIdsForVideos))
                ->get();
        $pdfContents =
            Content::where('contenttype_id', 1)
                ->where('enable', true)
                ->where('display', true)
                ->where('file', '<>', null)
                ->orderBy('created_at', 'desc')
                ->limit(count($setIds))
                ->get();
        $videoCounter = 0;
        $user = User::find(1);
        auth()->login($user);
        function addUserFavored($videoContent, $user)
        {
            $videoContent->favoring($user);
            $data = [
                [
                    'insertor_id' => 1, 'content_id' => $videoContent->id, 'title' => 'parachute-test',
                    'time' => randomNumber(4)
                ],
                [
                    'insertor_id' => 1, 'content_id' => $videoContent->id, 'title' => 'parachute-test',
                    'time' => randomNumber(4)
                ],
            ];
            DB::table('timepoints')->insert($data);
            foreach ($videoContent->times->where('content_id', $videoContent->id)->where('insertor_id',
                1)->where('title', 'parachute-test') as $time) {
                $time->favoring($user);
            }
        }

        foreach ($videoContents as $videoContent) {
            try {
                match ($setIdsForVideos[$videoCounter]) {
                    1850, 1925, 2066, 2223, => addUserFavored($videoContent, $user),
                    default => '',
                };
                $videoContent->update([
                    'contentset_id' => $setIdsForVideos[$videoCounter], 'validSince' => '2023-01-01 10:19:04'
                ]);
            } catch (Exception $exception) {

            }
            $comments = [];
            for ($i = 1; $i <= 30; $i++) {
                $comments[] = [
                    'comment' => 'کامنت تست '.$videoContent->name.$i
                ];
            }
            $videoContent->comments()->createMany($comments);
            $videoCounter++;
        }
        $pdfCounter = 0;
        foreach ($pdfContents as $pdfContent) {
            try {
                $pdfContent->update(['contentset_id' => $setIds[$pdfCounter], 'validSince' => '2023-01-01 10:19:04']);
            } catch (Exception $exception) {

            }
            $comments = [];
            for ($i = 1; $i <= 30; $i++) {
                $comments[] = [
                    'comment' => 'کامنت تست '.$pdfContent->name.$i
                ];
            }
            $videoContent->comments()->createMany($comments);
            $pdfCounter++;
        }
        auth()->logout();


        //moshavere set
        try {
            Contentset::where('id', 2224)->update(['enable' => 1, 'display' => 1, 'author_id' => 4]);
            Content::where('contentset_id', 1698)->update(['contentset_id' => 2224]);
        } catch (Exception $exception) {

        }
        $this->info('done');
        return 0;
    }
}
