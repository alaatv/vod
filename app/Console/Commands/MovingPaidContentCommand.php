<?php

namespace App\Console\Commands;

use App\Models\Content;
use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Traits\Content\ContentControllerResponseTrait;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class MovingPaidContentCommand extends Command
{

    use ContentControllerResponseTrait;

    public const NEZAM_GHADIM_CONTENTS_TO_ANALYSE = [
        13053,
        13057,
        13093,
        13097,
        13101,
        13105,
        13109,
        13113,
        13117,
        13121,
        13125,
        13129,
        13133,
        13137,
        13141,
        13145,
        13149,
        13153,
        13157,
        13161,
        13165,
        13169,
        13173,
        13177,
        13181,
        13185,
        13189,
        13193,
        13197,
        13201,
        13205,
        13209,
        13213,
        13217,
        13221,
        13225,
        13229,
        13233,
        10793,
        10805,
        10813,
        10825,
        10829,
        10833,
        10837,
        10841,
        10845,
        10849,
        10853,
        10857,
        10861,
        10865,
        10869,
        10873,
        10877,
        10881,
        10885,
        10889,
        10893,
        10897,
        10901,
        10905,
        10909,
        10913,
        10917,
        10921,
        10925,
        10929,
        10933,
        10937,
        10941,
        10945,
        10949,
        10953,
        10957,
        10961,
        10965,
        10969,
        10973,
        10977,
        10981,
        13401,
        13405,
        13409,
        13413,
        13417,
        13421,
        13425,
        13429,
        13433,
        13437,
        13441,
        13445,
        13449,
        13453,
        13457,
        13461,
        13465,
        13469,
        13473,
        13477,
        13481,
        13485,
        13489,
        13493,
        13497,
        13501,
        13505,
        13509,
        13513,
        13517,
        13521,
        13525,
        13529,
        13533,
        13537,
        13541,
        13545,
        12705,
        12709,
        12713,
        12721,
        12729,
        12745,
        12749,
        12753,
        12757,
        12761,
        12765,
        12769,
        12773,
        12777,
        12781,
        12785,
        12789,
        12793,
        12797,
        12801,
        12805,
        12809,
        12813,
        12817,
        12821,
        12825,
        12829,
        12833,
        12837,
        12841,
        12845,
        12849,
        12853,
        12861,
        12869,
        12873,
        12877,
        12881,
        12885,
        12889,
        12893,
        12897,
        13761,
        13765,
        13769,
        13781,
        13789,
        13797,
        13801,
        13805,
        13809,
        13813,
        13817,
        13821,
        13825,
        13829,
        13837,
        13849,
        13853,
        13857,
        13861,
        13865,
        13869,
        13873,
        13877,
        13881,
        13885,
        13889,
        13893,
        13897,
        13901,
        13905,
        13613,
        13617,
        13621,
        13625,
        13629,
        13633,
        13637,
        13641,
        13645,
        13649,
        13653,
        13657,
        13661,
        13665,
        13669,
        13673,
        13677,
        13681,
        12153,
        12157,
        12161,
        12165,
        12173,
        12181,
        12213,
        12221,
        12229,
        12237,
        12245,
        12253,
        12265,
        12273,
        12277,
        12281,
        12285,
        12289,
        12293,
        12297,
        12301,
        12305,
        12309,
        12313,
        12317,
        12321,
        12325,
        12329,
        12333,
        12337,
        12341,
        12345,
        12349,
        12353,
        12357,
        12361,
        12365,
        12369,
        12373,
        12377,
        12381,
        12385,
        12389,
        12393,
        12397,
        12401,
        11837,
        11841,
        11845,
        11853,
        11861,
        11865,
        11869,
        11873,
        11877,
        11881,
        11885,
        11889,
        11893,
        11897,
        11901,
        11905,
        11909,
        11913,
        11917,
        11921,
        11925,
        11929,
        11933,
        11937,
        11941,
        11945,
        12901,
        12905,
        12909,
        12921,
        12925,
        12929,
        12933,
        12937,
        12941,
        12945,
        12949,
        12953,
        12957,
        12961,
        12965,
        12969,
        12973,
        12977,
        12981,
        12985,
        12989,
        12993,
        12997,
        13001,
        13005,
        13009,
        13013,
        13017,
        13021,
        13025,
        13029,
        13033,
        13037,
        13041,
        13045,
        13049,
        13053,
        13057,
        10793,
        10805,
        10813,
        10825,
        10829,
        10833,
        10837,
        10841,
        10845,
        10849,
        10853,
        10857,
        10861,
        10865,
        10869,
        10873,
        10877,
        10881,
        10885,
        10889,
        10893,
        10897,
        10901,
        10905,
        10909,
        10913,
        10917,
        10921,
        10925,
        10929,
        10933,
        10937,
        10941,
        10945,
        10949,
        10953,
        10957,
        10961,
        10965,
        10969,
        10973,
        10977,
        10981,
        12001,
        12005,
        12009,
        12017,
        12021,
        12025,
        12029,
        12033,
        12037,
        12041,
        12045,
        12049,
        12053,
        12057,
        12061,
        12065,
        12405,
        12409,
        12413,
        12421,
        12425,
        12429,
        12433,
        12437,
        12441,
        12445,
        12449,
        12453,
        12457,
        12461,
        12465,
        12469,
        12473,
        12477,
        12481,
        12485,
        12489,
        12493,
        12497,
        12705,
        12709,
        12713,
        12721,
        12729,
        12745,
        12749,
        12753,
        12757,
        12761,
        12765,
        12769,
        12773,
        12777,
        12781,
        12785,
        12789,
        12793,
        12797,
        12801,
        12805,
        12809,
        12813,
        12817,
        12821,
        12825,
        12829,
        12833,
        12837,
        12841,
        12845,
        12849,
        12853,
        12861,
        12869,
        12873,
        12877,
        12881,
        12885,
        12889,
        12893,
        12897,
        11001,
        11013,
        11017,
        11021,
        11025,
        11029,
        11033,
        11037,
        11041,
        11045,
        11049,
        11053,
        11057,
        11061,
        11065,
        11069,
        11073,
        11609,
        11613,
        11617,
        11625,
        11633,
        11637,
        11641,
        11645,
        11649,
        11653,
        11657,
        11661,
        11665,
        11669,
        11673,
        11677,
        11681,
        11685,
        11689,
        11693,
        13577,
        13581,
        13589,
        13593,
        13597,
        13601,
        13605,
        13609,
        13613,
        13617,
        13621,
        13625,
        13629,
        13633,
        13637,
        13641,
        13645,
        13649,
        13653,
        13657,
        13661,
        13665,
        13669,
        13673,
        13677,
        13681,
        11697,
        11701,
        11705,
        11713,
        11717,
        11721,
        11725,
        11729,
        11733,
        11737,
        11741,
        11745,
        11749,
        11753,
        11757,
        11761,
        11769,
        11773,
        11777,
        11781,
        11785,
        11789,
        11793,
        11797,
        11801,
        11805,
        11809,
        11813,
        11817,
        11821,
        11825,
        11829,
        11833,
        9569,
        9573,
        9577,
        9585,
        9593,
        9601,
        9609,
        9617,
        9625,
        9629,
        9633,
        9637,
        9641,
        9645,
        9649,
        9653,
        9657,
        9661,
        11077,
        11081,
        11085,
        11093,
        11097,
        11101,
        11105,
        11109,
        11113,
        11117,
        11121,
        11837,
        11841,
        11845,
        11853,
        11861,
        11865,
        11869,
        11873,
        11877,
        11881,
        11885,
        11889,
        11893,
        11897,
        11901,
        11905,
        11909,
        11913,
        11917,
        11921,
        11925,
        11929,
        11933,
        11937,
        11941,
        11945,
        12957,
        12961,
        12965,
        12969,
        12973,
        12977,
        12981,
        12985,
        12989,
        12993,
        12997,
        13001,
        13005,
        13009,
        13013,
        13017,
        13021,
        13025,
        13029,
        13033,
        13037,
        13041,
        13045,
        13049,
        13053,
        13057,
        11949,
        11953,
        11957,
        11965,
        11969,
        11973,
        11977,
        11981,
        11985,
        11989,
        11993,
        11997,
        12957,
        12961,
        12965,
        12969,
        12973,
        12977,
        12981,
        12985,
        12989,
        12993,
        12997,
        13001,
        13005,
        13009,
        13013,
        13017,
        13021,
        13025,
        13029,
        13033,
        13037,
        13041,
        13045,
        13049,
        13053,
        13057,
        12001,
        12005,
        12009,
        12017,
        12021,
        12025,
        12029,
        12033,
        12037,
        12041,
        12045,
        12049,
        12053,
        12057,
        12061,
        12065,
        12069,
        12073,
        12077,
        12085,
        12093,
        12097,
        12101,
        12105,
        12109,
        12113,
        12117,
        12121,
        12125,
        12129,
        12133,
        12137,
        12141,
        12145,
        12149,
        12405,
        12409,
        12413,
        12421,
        12425,
        12429,
        12433,
        12437,
        12441,
        12445,
        12449,
        12453,
        12457,
        12461,
        12465,
        12469,
        12473,
        12477,
        12481,
        12485,
        12489,
        12493,
        12497,
        12501,
        12505,
        12509,
        12517,
        12525,
        12533,
        12537,
        12541,
        12545,
        12549,
        12553,
        12557,
        12561,
        12565,
        12569,
        12573,
        12577,
        12581,
        12585,
        12649,
        12653,
        12657,
        12669,
        12673,
        12677,
        12681,
        12685,
        12689,
        12693,
        12697,
        12701,
        11225,
        11229,
        11233,
        11237,
        11245,
        11249,
        11253,
        11257,
        11261,
        11265,
        11269,
        11273,
        11277,
        11281,
        11285,
        11289,
        11293,
        11297,
        11301,
        11305,
        11309,
        11313,
        11317,
        11321,
        11325,
        11329,
        11333,
        11337,
        11341,
        11345,
        11349,
        11353,
        11357,
        11361,
        11365,
        11369,
        11373,
        11377,
        11381,
        11385,
        11389,
        11393,
        11397,
        11401,
        11609,
        11613,
        11617,
        11625,
        11633,
        11637,
        11641,
        11645,
        11649,
        11653,
        11657,
        11661,
        11665,
        11669,
        11673,
        11677,
        11681,
        11685,
        11689,
        11693,
        11697,
        11701,
        11705,
        11713,
        11717,
        11721,
        11725,
        11729,
        11733,
        11737,
        11741,
        11745,
        11749,
        11753,
        11757,
        11761,
        11769,
        11773,
        11777,
        11781,
        11785,
        11789,
        11793,
        11797,
        11801,
        11805,
        11809,
        11813,
        11817,
        11821,
        11825,
        11829,
        11833,
        11077,
        11081,
        11085,
        11093,
        11097,
        11101,
        11105,
        11109,
        11113,
        11117,
        11121,
        9569,
        9573,
        9577,
        9585,
        9593,
        9601,
        9609,
        9617,
        9625,
        9629,
        9633,
        9637,
        9641,
        9645,
        9649,
        9653,
        9657,
        9661,
        11125,
        11129,
        11133,
        11141,
        11145,
        11153,
        11157,
        11161,
        11165,
        11169,
        11173,
        11177,
        11181,
        11189,
        11197,
        11209,
        11213,
        11217,
        11221,
        11225,
        11229,
        11233,
        11237,
        11245,
        11249,
        11253,
        11257,
        11261,
        11265,
        11269,
        11273,
        11277,
        11281,
        11285,
        11289,
        11293,
        11297,
        11301,
        11305,
        11309,
        11313,
        11317,
        11321,
        11325,
        11329,
        11333,
        11337,
        11341,
        11345,
        11349,
        11353,
        11357,
        11361,
        11365,
        11369,
        11373,
        11377,
        11381,
        11385,
        11389,
        11393,
        11397,
        11401,
        13825,
        13829,
        13837,
        13849,
        13853,
        13857,
        13861,
        13865,
        13869,
        13873,
        13877,
        13881,
        13885,
        13889,
        13893,
        13897,
        13901,
        13905,
        13909,
        13913,
        13917,
        13921,
        13925,
        13929,
        13933,
        13937,
        13941,
        13945,
        13949,
        13953,
        13957,
        13961,
        13965,
        13969,
        13973,
        13977,
        13981,
        13985,
        13989,
        13993,
        13997,
        14001,
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:movingPaidContent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Moving paid contents to media folder';

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
     * Transferring paid contents to media folder
     *
     * @return mixed
     */
    public function handle()
    {
        $basePath = config('constants.DOWNLOAD_SERVER_ROOT');
        $products = ProductRepository::getProductsById(Product::ALL_ARASH_TITAN)->get();
        $productsCount = $products->count();

        if (!$this->confirm("$productsCount products found. Do you wish to continue?", true)) {
            $this->info('Process interrupted');
            return 0;
        }
        $productsBar = $this->output->createProgressBar($productsCount);
        /** @var Product $product */
        foreach ($products as $product) {
            $tags = $this->refineProductTags(optional($product->tags)->tags);

            $this->info('Processing product '.$product->id);
            Log::channel('movePaidFilesProcessLog')->info('Processing product '.$product->id);
            $sets = $product->sets;
            $setsCount = $sets->count();

            $this->info($setsCount.' set found for product '.$product->id);
            Log::channel('movePaidFilesProcessLog')->info($setsCount.' set found for product '.$product->id);

            foreach ($sets as $set) {
                Log::channel('movePaidFilesProcessLog')->info('Processing set '.$set->id);

                $setFolder = 'media/'.$set->id;
                $makeSubFolder = $this->makeSetFolder($basePath, $setFolder);
                if (!$makeSubFolder) {
                    continue;
                }

                $contents = $set->contents;
                foreach ($contents as $content) {
                    /** @var Content $content */
                    $files = $content->getRawOriginal('file');
                    $files = json_decode($files);

                    if (!is_array($files)) {
                        Log::channel('movePaidFilesProcessLog')->info('Json_decode of file column is not an array, content:'.$content->id);
                        continue;
                    }

                    $contentType = $content->contenttype;
                    $contenttypeName = optional($contentType)->name;
                    $contentTypeDisplayName = optional($contentType)->displayName;
                    Log::channel('movePaidFilesProcessLog')->info('Processing '.(isset($contenttypeName) ? $contenttypeName : 'content').' '.$content->id.' with '.count($files).' files');

                    $hasContentFilesChanges = false;
                    foreach ($files as $key => $file) {
                        $fileNumber = $key + 1;
                        Log::channel('movePaidFilesProcessLog')->info('Processing file '.$fileNumber.' : '.$file->res);

                        $fileName = optional($file)->fileName;
                        if (is_null($fileName)) {
                            Log::channel('movePaidFilesProcessLog')->info('File name is null, content:'.$content->id);
                            continue;
                        }

                        if ($content->isVideo()) {
                            $qualityFolderName = $this->determineQulaitySubFolder($file);
                            if (!isset($qualityFolderName)) {
                                Log::channel('movePaidFilesProcessLog')->info('No quality folder found, content:'.$content->id);
                                continue;
                            }

                            $newPartialFileFolder = $setFolder."/$qualityFolderName";
                            $newFullPath = $basePath.$newPartialFileFolder;

                            $fullPath = $this->makePaidFileFullPath($fileName, $content, $basePath);
                            if (is_null($fullPath)) {
                                continue;
                            }

                            $makeSubFolder = $this->makeQualitySubFolder($newFullPath);
                            if (!$makeSubFolder) {
                                continue;
                            }

                            $videoMove = $this->moveVideo($fullPath, $newFullPath, $fileName, $content, $files, $key);

                            if (!$videoMove) {
                                continue;
                            }

                            $hasContentFilesChanges = true;
                            $file->fileName = $this->makeFreeVideoFileName($newPartialFileFolder, $fileName);
                            $file->url = config('constants.DOWNLOAD_SERVER_PROTOCOL').config('constants.CDN_SERVER_NAME').'/'.$newPartialFileFolder.'/'.basename($fileName);
                            $file->disk = config('disks.ALAA_CDN_SFTP');
                        } elseif ($content->isPamphlet()) {
                            $fullPath = $this->makePaidFileFullPath($fileName, $content, $basePath);
                            if (is_null($fullPath)) {
                                continue;
                            }

                            $movePamphlet = $this->movePamphlet($fullPath, $basePath, $fileName, $content, $files,
                                $key);
                            if (!$movePamphlet) {
                                continue;
                            }

                            $hasContentFilesChanges = true;
                            $file->fileName = basename($fileName);
                            $file->url = null;
                            $file->disk = config('disks.PAMPHLET_SFTP');
                        }
                    }

                    if (!$hasContentFilesChanges) {
                        continue;
                    }

                    $this->updateContent($files, $content, $tags, $contentTypeDisplayName, $contenttypeName);
                    if (is_null($content->author_id)) {
                        Log::channel('movePaidFilesProcessLog')->info('Content has no author: '.$content->id);
                    }

                }

                $set->tags = Arr::prepend($tags, 'دسته_محتوا');
                $set->display = 1;
                $set->updateWithoutTimestamp();
            }

            $product->enable = 0;
            $product->display = 0;
            $product->updateWithoutTimestamp();

            $productsBar->advance();
            $this->info("\n");
        }

        $productsBar->finish();
        $this->info('Done!');
        Artisan::call('cache:clear');
        return 0;
    }

    /**
     * Removing product tag
     * @param $tags
     * @return array
     */
    private function refineProductTags(?array $tags): array
    {
        if (!isset($tags)) {

            return [];
        }

        if (($key = array_search('محصول', $tags)) !== false) {
            unset($tags[$key]);
        }

        return $tags;
    }

    /**
     * @param  string  $basePath
     * @param  string  $setFolder
     *
     * @return bool
     */
    private function makeSetFolder(string $basePath, string $setFolder): bool
    {
        if (file_exists($basePath.$setFolder)) {

            return true;
        }

        try {
            mkdir($basePath.$setFolder, 0776, true);
            chmod($basePath.$setFolder, 0776);
        } catch (Exception $e) {
            Log::channel('movePaidFilesProcessLog')->info('Could not make directory: '.$basePath.$setFolder);
            return false;
        }


        return true;
    }

    /**
     * @param $file
     *
     * @return string
     */
    private function determineQulaitySubFolder($file): ?string
    {
        if ($file->res == '240p') {
            return '240p';
        } else {
            if ($file->res == '480p') {
                return 'hq';
            } else {
                if ($file->res == '720p') {
                    return 'HD_720p';
                }
            }
        }

        return null;
    }

    /**
     * @param                               $fileName
     * @param  Content  $content
     * @param  string  $basePath
     *
     * @return string
     */
    private function makePaidFileFullPath($fileName, Content $content, string $basePath): ?string
    {
        $explode = explode('/paid/', $fileName);
        if (!isset($explode[1])) {
            Log::channel('movePaidFilesProcessLog')->info('File '.$fileName.' does not match paid pattern, content: '.$content->id);
            return null;
        }

        return $basePath.'paid/private/'.$explode[1];
    }

    /**
     * @param  string  $newFullPath
     *
     * @return bool
     */
    private function makeQualitySubFolder(string $newFullPath): bool
    {
        if (file_exists($newFullPath)) {

            return true;
        }

        try {
            mkdir($newFullPath);
            chmod($newFullPath, 0776);
        } catch (Exception $e) {
            Log::channel('movePaidFilesProcessLog')->info('Could not make directory: '.$newFullPath);
            return false;
        }


        return true;
    }

    /**
     * @param  string  $fullPath
     * @param  string  $newFullPath
     * @param         $fileName
     * @param  Content  $content
     * @param  array  $files
     * @param  int  $key
     *
     * @return bool
     */
    private function moveVideo(
        string $fullPath,
        string $newFullPath,
        $fileName,
        Content $content,
        array &$files,
        int $key
    ): bool {
        try {
            if (file_exists($fullPath)) {
                if (file_exists($newFullPath.'/'.basename($fileName))) {
                    Log::channel('movePaidFilesProcessLog')->info('Video '.$fullPath.' was existed in destination '.$newFullPath.'/'.basename($fileName).', content: '.$content->id);
                    return true;
                }

                rename($fullPath, $newFullPath.'/'.basename($fileName));
                Log::channel('movePaidFilesProcessLog')->info('Video '.$fullPath.' was moved to '.$newFullPath.'/'.basename($fileName).', content: '.$content->id);
                return true;
            }
            Arr::pull($files, $key);
            Log::channel('movePaidFilesProcessLog')->info('Video '.$fullPath.' was not exist in source, content: '.$content->id);
            Log::channel('movePaidFilesProcessLog')->info(json_encode($files));

        } catch (Exception $e) {
            Log::channel('movePaidFilesProcessLog')->info('Could not move video of content: '.$content->id.' , '.$fullPath);
        }

        return false;
    }

    /**
     * @param  string  $newPartialFileFolder
     * @param  string  $fileName
     *
     * @return string
     */
    private function makeFreeVideoFileName(string $newPartialFileFolder, string $fileName): string
    {
        return '/'.$newPartialFileFolder.'/'.basename($fileName);
    }

    /**
     * @param  string|null  $fullPath
     * @param  string  $basePath
     * @param                               $fileName
     * @param  Content  $content
     * @param  array  $files
     * @param  int  $key
     *
     * @return bool
     */
    private function movePamphlet(
        ?string $fullPath,
        string $basePath,
        $fileName,
        Content $content,
        array &$files,
        int $key
    ): bool {
        try {
            if (file_exists($fullPath)) {
                if (file_exists($basePath.'paid/public/c/pamphlet/'.basename($fileName))) {
                    Log::channel('movePaidFilesProcessLog')->info('Pamphlet '.$fullPath.' was existed in destination '.$basePath.'paid/public/c/pamphlet/'.basename($fileName).', content: '.$content->id);
                    return true;
                }

                rename($fullPath, $basePath.'paid/public/c/pamphlet/'.basename($fileName));
                Log::channel('movePaidFilesProcessLog')->info('Pamphlet '.$fullPath.' was moved to '.$basePath.'paid/public/c/pamphlet/'.basename($fileName).', content: '.$content->id);
                return true;

            }
            Arr::pull($files, $key);
            Log::channel('movePaidFilesProcessLog')->info('Pamphlet '.$fullPath.' was not exist in source, content: '.$content->id);
            Log::channel('movePaidFilesProcessLog')->info(json_encode($files));

        } catch (Exception $e) {
            Log::channel('movePaidFilesProcessLog')->info('Could not move pamphlet of content: '.$content->id.' , '.$fullPath);
        }


        return false;
    }

    /**
     * @param  array  $files
     * @param  Content  $content
     * @param         $tags
     * @param         $contentTypeDisplayName
     * @param         $contenttypeName
     *
     * @return bool
     */
    private function updateContent(
        array $files,
        Content $content,
        $tags,
        $contentTypeDisplayName,
        $contenttypeName
    ): bool {
        try {
            if (empty($files)) {
                Log::channel('contentsWithNoFile')->info($content->id);
            }
            $content->file = empty($files) ? null : collect($files);
            $content->enable = empty($files) ? 0 : 1;
            $content->display = empty($files) ? 0 : 1;
            $content->isFree = 1;
            $content->tags = Arr::prepend($tags, $contentTypeDisplayName);
            $content->updateWithoutTimestamp();
            Log::channel('movePaidFilesProcessLog')->info('Updating '.(isset($contenttypeName) ? $contenttypeName : 'content').' '.$content->id);
            Log::channel('movePaidFilesProcessLog')->info(json_encode($files));
            return true;
        } catch (QueryException $e) {
            Log::channel('movePaidFilesProcessLog')->info('Could not update content: '.$content->id);
        }

        return false;
    }


    /**
     * Transferring Nezam Ghadim contents to media folder
     * Some contents where listed on error log , I want to analyse these contents to see whether they
     * have been transferred successfully or not
     *
     *
     * Note: after running this command I found out there was no problem
     *
     * @return mixed
     */
//    public function handle()
//    {
//        $basePath      = config('constants.DOWNLOAD_SERVER_ROOT');
//        $contentIds = self::NEZAM_GHADIM_CONTENTS_TO_ANALYSE;
//
//        $contents = Content::whereIn('id' , $contentIds)->get();
//
//        foreach ($contents as $content)
//        {
//            /** @var Content $content */
//            $files = $content->getRawOriginal('file');
//            $files = json_decode($files);
//
//            if (!is_array($files))
//            {
//                Log::channel('moveNezamGhadimFilesErrorLog')->info('Json_decode of file column is not an array, content:' . $content->id);
//                continue;
//            }
//
//            foreach ($files as $key => $file)
//            {
//                $fileName = optional($file)->fileName;
//                if (is_null($fileName))
//                {
//                    Log::channel('moveNezamGhadimAnalyse')->info('File name is null, content:' . $content->id);
//                    continue;
//                }
//
//                $explode  = explode('media/', $fileName);
//                if(!isset($explode[1]))
//                {
//                    Log::channel('moveNezamGhadimAnalyse')->info('File '.$fileName.' does not match paid pattern, content: '.$content->id);
//                    continue ;
//                }
//
//                if(!file_exists($basePath.$fileName))
//                {
//                    Log::channel('moveNezamGhadimAnalyse')->info('File '.$fileName.' does not exist, content: '.$content->id);
//                    continue ;
//                }
//            }
//        }
//
//        $this->info('Done');
//        return 0;
//    }

}
