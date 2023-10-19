<?php

namespace App\Console\Commands;

use App\Models\Content;
use App\Models\Contentset;
use App\Models\Product;
use App\Models\Productfile;
use App\Repositories\ProductRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class ConvertProductFileToContentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:convert:productFilesToContent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Converts product files to content';

    protected $output;

    protected $productAuthorLookupTable;

    /**
     * ConvertProductFileToContentCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->productAuthorLookupTable = [
            '1' => null,
            '2' => null,
            '3' => null,
            '4' => null,
            '5' => null,
            '6' => null,
            '7' => null,
            '8' => null,
            '9' => null,
            '10' => 37203,
            '11' => 37220,
            '12' => 37220,
            '13' => null,
            '14' => null,
            '15' => null,
            '16' => null,
            '17' => null,
            '18' => 37203,
            '19' => 37203,
            '20' => 37224,
            '21' => 37224,
            '22' => 37224,
            '23' => 37206,
            '24' => 37206,
            '25' => 37206,
            '26' => 37221,
            '27' => 37221,
            '28' => 37221,
            '29' => 37222,
            '30' => 37222,
            '31' => 37222,
            '32' => 37222,
            '33' => 37222,
            '34' => 37222,
            '35' => null,
            '36' => 37226,
            '37' => 37226,
            '38' => 37214,
            '39' => 37214,
            '40' => 37242,
            '41' => 37242,
            '42' => 37242,
            '43' => 37223,
            '44' => 37223,
            '45' => 37219,
            '46' => 37219,
            '47' => 37219,
            '48' => 37195,
            '49' => 37195,
            '50' => 37195,
            '51' => null,
            '52' => null,
            '53' => null,
            '54' => null,
            '55' => null,
            '56' => null,
            '57' => null,
            '58' => null,
            '59' => null,
            '60' => null,
            '61' => 37222,
            '62' => 37222,
            '63' => 37222,
            '64' => 37222,
            '65' => 37196,
            '66' => 37196,
            '67' => 37196,
            '69' => 37204,
            '70' => 37204,
            '71' => 37204,
            '72' => 37204,
            '73' => 37223,
            '74' => 37196,
            '75' => 37222,
            '76' => 37222,
            '77' => 37222,
            '78' => 37223,
            '79' => 37223,
            '80' => 37196,
            '81' => 37196,
            '82' => 37204,
            '83' => 37204,
            '84' => 37204,
            '85' => 37224,
            '86' => 37224,
            '87' => 37224,
            '88' => 37224,
            '89' => 37222,
            '90' => 37196,
            '91' => 37229,
            '92' => 37224,
            '93' => 37201,
            '94' => 37222,
            '95' => 37222,
            '96' => 37222,
            '97' => 37220,
            '98' => 37204,
            '99' => 37229,
            '100' => 37229,
            '101' => 37229,
            '102' => 37229,
            '103' => 37229,
            '104' => 37241,
            '105' => 37241,
            '106' => 37241,
            '107' => 37229,
            '108' => 37229,
            '109' => 37204,
            '110' => 37229,
            '111' => 37222,
            '112' => 37222,
            '113' => 37222,
            '114' => 37222,
            '115' => 37222,
            '116' => 37222,
            '117' => 37222,
            '118' => 37222,
            '119' => 37222,
            '120' => 37222,
            '121' => 37222,
            '122' => null,
            '123' => 37222,
            '124' => 37222,
            '125' => 37222,
            '126' => null,
            '127' => 37226,
            '128' => 37226,
            '129' => 37226,
            '130' => 37226,
            '131' => 37226,
            '132' => 37226,
            '133' => 37226,
            '134' => 37226,
            '135' => 37265,
            '136' => 37265,
            '137' => 37265,
            '138' => 37265,
            '139' => 37232,
            '140' => 37232,
            '141' => 37232,
            '142' => 37232,
            '143' => 37229,
            '144' => 37229,
            '145' => 37229,
            '146' => 37229,
            '147' => 37268,
            '148' => 37268,
            '149' => 37268,
            '150' => 37268,
            '151' => null,
            '152' => null,
            '153' => null,
            '154' => null,
            '155' => 37224,
            '156' => 37224,
            '157' => 37224,
            '158' => 37224,
            '159' => null,
            '160' => null,
            '161' => null,
            '162' => null,
            '163' => 37223,
            '164' => 37223,
            '165' => 37223,
            '166' => 37223,
            '167' => null,
            '168' => null,
            '169' => null,
            '170' => null,
            '171' => null,
            '172' => null,
            '173' => null,
            '174' => null,
            '175' => null,
            '176' => null,
            '177' => null,
            '178' => null,
            '179' => null,
            '180' => null,
            '181' => 37263,
            '182' => null,
            '183' => 37203,
            '184' => null,
            '185' => null,
            '186' => null,
            '193' => null,
            '194' => null,
            '195' => null,
            '196' => null,
            '199' => null,
            '200' => null,
            '201' => null,
            '202' => null,
            '203' => null,
            '204' => null,
            '205' => null,
            '206' => null,
            '207' => 37229,
            '208' => 37229,
            '209' => 37224,
            '210' => 37228,
            '211' => 80737,
            '212' => 88130,
            '213' => 88130,
            '214' => 37203,
            '215' => null,
            '216' => 37224,
            '217' => 37229,
            '218' => 37222,
            '219' => 37226,
            '220' => 37265,
            '221' => null,
            '222' => 37226,
            '223' => null,
            '224' => null,
            '225' => 37263,
            '226' => 37216,
            '227' => 37216,
            '228' => 37216,
            '229' => 37216,
            '230' => 37229,
            '231' => 37229,
            '232' => 37224,
            '233' => 37224,
            '234' => null,
            '235' => null,
            '236' => null,
            '237' => null,
            '238' => null,
            '239' => null,
            '240' => null,
            '241' => null,
            '242' => null,
            '243' => null,
            '244' => null,
            '245' => null,
            '246' => null,
            '247' => null,
            '248' => null,
            '249' => 37222,
            '250' => 37229,
            '251' => 37229,
            '252' => 37224,
            '253' => 37224,
            '254' => 37204,
            '255' => 37232,
            '256' => 37268,
            '257' => 37241,
            '258' => 37196,
            '259' => 37265,
            '260' => 37226,
            '261' => 37222,
            '262' => 37222,
            '263' => 37222,
            '264' => 37223,
            '265' => 37195,
            '266' => 37216,
            '267' => 37229,
            '268' => 37222,
            '269' => 37226,
            '270' => 37222,
            '271' => null,
            '272' => null,
            '273' => 37190,
            '274' => 37194,
            '275' => 37263,
            '276' => null,
            '277' => null,
            '278' => null,
            '279' => null,
            '280' => null,
            '281' => null,
            '282' => null,
            '283' => null,
            '284' => null,
            '285' => 37196,
            '286' => 37196,
            '287' => 37214,
            '288' => null,
            '289' => 37190,
            '290' => 37194,
            '291' => 37202,
            '292' => null,
            '293' => 37196,

        ];
        $this->output = new ConsoleOutput();
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::table('contentset_product')
            ->truncate();
        $this->migrateData();
    }

    private function migrateData(): void
    {

        $productFiles = Productfile::orderBy('order')
            ->get();
        $productFiles->load('product');
        $productFiles->load('productfiletype');
        $productFilesGroupedByProductId = $productFiles->groupBy('product_id');

        $this->output->writeln('update product files....');
        $progress = new ProgressBar($this->output, $productFilesGroupedByProductId->count());


        foreach ($productFilesGroupedByProductId as $productId => $files) {

            //get Product
            $product = Product::find($productId);

            /** @var Contentset $set */
            $set = tap($this->makeSetForProductFiles($files, $product), function (Contentset $set) use ($files) {

                Productfile::whereIn('id', $files->modelKeys())
                    ->update(['contentset_id' => $set->id]);


                $this->attachSetToProducts($set, $files->first()->file);
            });

            $videoOrder = 1;
            $pamphletOrder = 1;
            /** @var Productfile $productFile */
            foreach ($files as $productFile) {
                $productFile = $productFile->fresh();
                if ($productFile->productfiletype_id == 1) {
                    $order = $pamphletOrder++;
                } else {
                    $order = $videoOrder++;
                }
                /** @var Content $content */
                $content = tap($this->getAssosiatedProductFileContent($productFile, $product, $order, $set),
                    function (Content $content) use ($productFile) {
                        $productTypeContentTypeLookupTable = [
                            '1' => 'pamphlet',
                            '2' => 'video',
                        ];

                        $files = collect();
                        $url = $productFile->cloudFile ?? $productFile->file;
                        $files->push([
                            'uuid' => null,
                            'disk' => config('disks.PRODUCT_FILE_SFTP'),
                            'url' => null,
                            'fileName' => parse_url($url)['path'],
                            'size' => null,
                            'caption' => $productFile->productfiletype_id == 2 ? 'کیفیت بالا' : 'جزوه',
                            'res' => $productFile->productfiletype_id == 2 ? '480p' : null,
                            'type' => $productTypeContentTypeLookupTable[$productFile->productfiletype_id],
                            'ext' => pathinfo(parse_url($url)['path'], PATHINFO_EXTENSION),
                        ]);

                        $content->file = $files;
                        $content->updateWithoutTimestamp();
                    });
            }
            $progress->advance();
        }
        $progress->finish();
        $this->output->writeln('Done!');
    }

    /**
     * @param            $files
     * @param  Product  $product
     *
     *
     * @return Contentset
     */
    protected function makeSetForProductFiles($files, Product $product): Contentset
    {
        /** @var Productfile $pFile */
        $pFile = $files->first();
        $set = $pFile->set;

        if ($set !== null) {
            return $set;
        }
        $setImage = isset($product->image[0]) ? route('image', [
            'category' => '4',
            'w' => '460',
            'h' => '259',
            'filename' => $product->image,
        ]) : '/acm/image/460x259.png';
        $set = Contentset::create([
            'name' => $product->name,
            'photo' => $setImage,
            'tags' => (array) optional(optional($product->grandParent)->tags)->tags,
            'enable' => 1,
            'display' => 0,
        ]);

        return $set;
    }

    /**
     * @param  Contentset  $set
     * @param  string  $fileName
     *
     */
    private function attachSetToProducts(Contentset $set, string $fileName)
    {
        $products = ProductRepository::getProductsThatHaveValidProductFileByFileNameRecursively($fileName);
        $this->output->writeln('  -Count(products):'.$products->count());
        foreach ($products as $product) {

            $product->sets()
                ->syncWithoutDetaching($set, [
                    'order' => $set->id,
                ]);

        }
        $this->output->writeln('');
    }

    private function getAssosiatedProductFileContent(
        Productfile $productFile,
        Product $product,
        $order,
        Contentset $set
    ):
    Content {
        if ($productFile->content_id != null) {
            return $this->assosiateSetToContent($productFile->content, $set);
        }
        //make content for each productFiles
        $productTypeContentTypeLookupTable = [
            '1' => Content::CONTENT_TYPE_PAMPHLET,
            '2' => Content::CONTENT_TYPE_VIDEO,
        ];
        $productTypeContentTemplateLookupTable = [
            '1' => Content::CONTENT_TEMPLATE_PAMPHLET,
            '2' => Content::CONTENT_TEMPLATE_VIDEO,
        ];
        $content = Content::create([
            'name' => $productFile->name,
            'description' => isset($productFile) && strlen($productFile->description) > 1 ? $productFile->description : null,
            'context' => null,
            'file' => null,
            'order' => $order,
            'validSince' => $productFile->validSince,
            'metaTitle' => null,
            'metaDescription' => null,
            'metaKeywords' => null,
            'tags' => (array) optional(optional($product->grandParent)->tags)->tags,
            'author_id' => $this->productAuthorLookupTable[$product->id] ?: null,
            'template_id' => $productTypeContentTemplateLookupTable[$productFile->productfiletype_id],
            'contenttype_id' => $productTypeContentTypeLookupTable[$productFile->productfiletype_id],
            'isFree' => false,
            'enable' => $productFile->enable,
        ]);
        $content->forceFill([
            'created_at' => $productFile->created_at,
            'updated_at' => $productFile->updated_at,
        ])
            ->save();
        $content = $this->assosiateSetToContent($content, $set);

        $productFile->timestamps = false;
        $productFile->content_id = $content->id;
        $productFile->update();
        $productFile->timestamps = true;

        return $content;

    }

    /**
     * @param  Content|null  $content
     * @param  Contentset|null  $set
     */
    private function assosiateSetToContent(Content $content, Contentset $set): Content
    {
        $content->contentset_id = $set->id;
        $content->updateWithoutTimestamp();
        $content->fresh();
        return $content;
    }
}
