<?php

namespace App\Console\Commands;

//use App\Classes\Search\Tag\ProductTagManagerViaApi;
use App\Classes\Search\Tag\ProductTagManagerViaApi;
use App\Http\Controllers\Web\ProductController;
use App\Models\Orderproduct;
use App\Models\Product;
use App\Traits\APIRequestCommon;
use App\Traits\ProductCommon;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;

//use App\Traits\TaggableTrait;

class MergeProductCommand extends Command
{
    use ProductCommon;
    use APIRequestCommon;

//    use TaggableTrait;  //Has not been merged in to project yet

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:merge:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'merges old products';

    private $productArray;

    private $tagging;


    public function __construct()
    {
        parent::__construct();
        $this->productArray = $this->initializing();
//        $this->tagging = $tagging;
    }

    /**
     * Create a new command instance.
     *
     * @param  ProductTagManagerViaApi  $tagging
     */
//    public function __construct(ProductTagManagerViaApi $tagging)
    /**
     * @return array
     */
    private function initializing()
    {
//        $initialArray = [];
//        if(Schema::hasTable("products"))
//        {
//            $allOfShimiProductId = 231;
//            $allOfShimiProduct = Product::FindOrFail($allOfShimiProductId); //All Shimi product
//            $shimiSubCategory1Id = 244;
//            $shimiSubCategory1 = Product::FindOrFail($shimiSubCategory1Id); //All Difransiel product
//            $shimiChildren =
//                [
//                    [
//                        "title" => "جمع بندی شیمی ۲ و۳ آقای صنیعی",
//                        "id" => 91,
//                        "description" => "شامل 16 ساعت و 17 دقیقه فیلم با حجم 1.7 گیگ",
//                        "type" =>1,
//                    ],
//                    [
//                        "title" => "همایش های طلایی",
//                        "description" => "شامل 16 ساعت و 35 دقیقه فیلم با حجم 2 گیگ",
//                        "parent" => $shimiSubCategory1,
//                        "children"=>[
//                            [
//                                "title" => "همایش حل مسائل شیمی کنکور آقای صنیعی",
//                                "id" => 100,
//                                "description" => "شامل 10 ساعت و 40 دقیقه فیلم با حجم 1.2 گیگ",
//                                "type" =>2,
//                            ],
//                            [
//                                "title" => "همایش حل مسائل ترکیبی شیمی کنکور آقای صنیعی",
//                                "id" => 217,
//                                "description" => "شامل 5 ساعت 55 دقیقه فیلم با حجم 828 مگابایت",
//                                "type" =>1,
//                            ],
//                        ],
//                    ],
//                    [
//                        "title" => "همایش 5+1 شیمی (پیش 1) آقای صنیعی",
//                        "id" => 145,
//                        "description" => "شامل 8 ساعت و 58 دقیقه فیلم با حجم 956 مگ",
//                        "type" =>2,
//                    ]
//                    ,
//                ];
//
//            $allOfPhysicProductId = 233;
//            $allOfPhysicProduct = Product::FindOrFail($allOfPhysicProductId); //All Physic product
//            $physicSubCategory1Id = 245;
//            $physicSubCategory1 = Product::FindOrFail($physicSubCategory1Id); //All Difransiel product
//            $physicChildren = [
//                [
//                    "title" => "جمع بندی",
//                    "id" => 92,
//                    "description" => "شامل 19 ساعت و 38 دقیقه فیلم با حجم 2.3 گیگ",
//                    "type" =>1,
//                ],
//                [
//                    "title" => "همایش های طلایی",
//                    "description" => "شامل 22 ساعت و 58 دقیقه فیلم با حجم 3.9 گیگ",
//                    "parent" => $physicSubCategory1,
//                    "children"=>[
//                        [
//                            "title" => "همایش طلایی  200 تست فیزیک کنکور آقای طلوعی",
//                            "id" => 216,
//                            "description" => "شامل 21 ساعت و 8 دقیقه فیلم با حجم 2.7 گیگ",
//                            "type" =>1,
//                        ],
//                        [
//                            "title" => "همایش طلایی فیزیک کنکور آقای طلوعی",
//                            "id" => 88,
//                            "description" => "شامل 21 ساعت و 50 دقیقه فیلم با حجم 1.2 گیگ",
//                            "type" =>2,
//                        ],
//                    ],
//                ],
//                [
//                    "title" => "همایش ۱+۵ فیزیک (پیش 1) آقای طلوعی",
//                    "id" => 157,
//                    "description" => "شامل 10 ساعت و 5 دقیقه فیلم با حجم 986 مگابایت",
//                    "type" =>2,
//                ],
//            ];
//
//            $allOfZistProductId = 235;
//            $allOfZistProduct = Product::FindOrFail($allOfZistProductId); //All Zist product
//            $zistSubCategory1Id = 246;
//            $zistSubCategory1 = Product::FindOrFail($zistSubCategory1Id); //All Difransiel product
//            $zistChildren = [
//                [
//                    "title" => "همایش های طلایی",
//                    "description" => "شامل 37 ساعت و 7 دقیقه فیلم با حجم 5.1 گیگ",
//                    "parent" => $zistSubCategory1,
//                    "children"=>[
//                        [
//                            "title" => "همایش طلایی زیست کنکور آقای چلاجور",
//                            "id" => 212,
//                            "description" => "شامل 18 ساعت و 20 دقیقه فیلم با حجم 2.4 گیگ",
//                            "type" =>1,
//                        ],
//                        [
//                            "title" => "همایش طلایی ژنتیک کنکور آقای آل علی",
//                            "id" => 221,
//                            "description" => "شامل 6 ساعت و 8 دقیقه فیلم با حجم 940 مگابایت",
//                            "type" =>1,
//                        ],
//                        [
//                            "title" => "همایش طلایی زیست آقای پازوکی",
//                            "id" => 109,
//                            "description" => "شامل 12 ساعت و 39 دقیقه فیلم با حجم 1.8 گیگ",
//                            "type" =>2,
//                        ],
//                    ],
//                ],
//                [
//                    "title" => "همایش ۱+۵ زیست (پیش 1) آقای جعفری",
//                    "id" => 141,
//                    "description" => "شامل 8 ساعت و 42 دقیقه فیلم با حجم 919 گیگ",
//                    "type" =>2,
//                ],
//            ];
//
//            $allOfArabiProductId = 237;
//            $allOfArabiProduct = Product::FindOrFail($allOfArabiProductId); //All Arabi product
//            $ArabiChildren = [
//                [
//                    "title" => "همایش 200 تست طلایی کنکور عربی آقای ناصح زاده",
//                    "id" => 214,
//                    "description" => "شامل 7 ساعت و 1 دقیقه فیلم با حجم 1.2 گیگ",
//                    "type" =>1,
//                ],
//                [
//                    "title" => "همایش 5+1 عربی (پیش 1) آقای آهویی",
//                    "id" => 149,
//                    "description" => "شامل 5 ساعت و 36 دقیقه فیلم با حجم 677 مگابایت",
//                    "type" =>2,
//                ],
//            ];
//
//            $allOfDiniProductId = 239;
//            $allOfDiniProduct = Product::FindOrFail($allOfDiniProductId); //All Dini product
//            $DiniChildren = [
//                [
//                    "title" => "همایش طلایی دین و زندگی خانم کاغذی",
//                    "id" => 211,
//                    "description" => "شامل 18 ساعت و 9 دقیقه فیلم با حجم 2.1 گیگ",
//                    "type" =>1,
//                ],
//                [
//                    "title" => "همایش طلایی دین و زندگی آقای رنجبرزاده",
//                    "id" => 105,
//                    "description" => "شامل 7 ساعت و 20 دقیقه فیلم با حجم 1 گیگ",
//                    "type" =>2,
//                ],
//            ];
//
//            $allOfRiyaziTajrobiProductId = 241;
//            $allOfRiyaziTajrobiProduct = Product::FindOrFail($allOfRiyaziTajrobiProductId); //All RiyaziTajrobi product
//            $riyaziTajrobiCategory1Id = 247;
//            $riyaziTajrobiCategory1 = Product::FindOrFail($riyaziTajrobiCategory1Id); //All Difransiel product
//            $riyaziTajrobiCategory2Id = 248;
//            $riyaziTajrobiCategory2 = Product::FindOrFail($riyaziTajrobiCategory2Id); //All Difransiel product
//            $RiyaziTajrobiChildren = [
//                [
//                    "title" => "همایش های طلایی",
//                    "description" => "شامل 31 ساعت و 9 دقیقه فیلم با حجم 10.5 گیگ",
//                    "parent" => $riyaziTajrobiCategory1,
//                    "children"=>[
//                        [
//                            "title" => "همایش طلایی ریاضی تجربی کنکور آقای نباخته",
//                            "id" => 220,
//                            "description" => "شامل 8 ساعت و 2 دقیقه فیلم با حجم 953 مگابایت",
//                            "type" =>1,
//                        ],
//                        [
//                            "title" => "همایش طلایی ریاضی تجربی کنکور آقای امینی راد",
//                            "id" => 219,
//                            "description" => "شامل 12 ساعت و 17 دقیقه فیلم با حجم 1.6 گیگ",
//                            "type" =>1,
//                        ],
//                        [
//                            "title" => "همایش ریاضی تجربی آقای شامی زاده",
//                            "id" => 90,
//                            "description" => "شامل 10 ساعت و 50 دقیقه فیلم با حجم 8 گیگ",
//                            "type" =>2,
//                        ],
//                    ],
//                ],
//                [
//                    "title" => "همایش های 1+5",
//                    "description" => "شامل 15 ساعت و 19 دقیقه فیلم با حجم 1.3 گیگ",
//                    "parent" => $riyaziTajrobiCategory2,
//                    "children"=>[
//                        [
//                            "title" => "همایش 1+5 ریاضی تجربی آقای نباخته",
//                            "id" => 137,
//                            "description" => "شامل 7 ساعت و 7 دقیقه فیلم با حجم 442 مگابایت",
//                            "type" =>2,
//                        ],
//                        [
//                            "title" => "همایش 1+5 ریاضی تجربی آقای امینی راد",
//                            "id" => 133,
//                            "description" => "شامل 8 ساعت و 12 دقیقه فیلم با حجم 934 مگابایت",
//                            "type" =>2,
//                        ],
//                    ],
//                ],
//            ];
//
//            $allOfDifransielProductId = 243;
//            $allOfDifransielProduct = Product::FindOrFail($allOfDifransielProductId); //All Difransiel product
//            $difransielSubCategory1Id = 249;
//            $difransielSubCategory1 = Product::FindOrFail($difransielSubCategory1Id); //All Difransiel product
//            $DifransielChildren = [
//                [
//                    "title" => "همایش طلایی 48 تست کنکور ریاضی آقای ثابتی",
//                    "id" => 218,
//                    "description" => "شامل 21 ساعت و 54 دقیقه فیلم با حجم 3 گیگ",
//                    "type" =>1,
//                ],
//                [
//                    "title" => "جمع بندی دیفرانسیل و ریاضی پایه آقای ثابتی",
//                    "parent" => $difransielSubCategory1,
//                    "description" => "شامل 9 ساعت و 36 دقیقه فیلم با حجم 840 مگابایت",
//                    "children"=>[
//                        [
//                            "title" => "همایش 1+5 دیفرانسیل (پیش 1) آقای ثابتی",
//                            "id" => 125,
//                            "description" => "شامل 5 ساعت و 12 دقیقه فیلم با حجم 630 مگابایت",
//                            "type" =>2,
//                        ],
//                        [
//                            "title" => "همایش دیفرانسیل و ریاضی پایه کنکور آقای ثابتی",
//                            "id" => 96,
//                            "description" => "شامل 4 ساعت و 24 دقیقه فیلم با حجم 210 مگابایت",
//                            "type" =>2,
//                        ],
//                    ],
//                    "type" =>1,
//                ],
//                [
//                    "title" => "همایش 1+5 تحلیلی (پیش 1) آقای ثابتی",
//                    "id" => 121,
//                    "description" => "شامل 5 ساعت و 31 دقیقه فیلم با حجم 648 گیگ",
//                    "type" =>2,
//                ],[
//                    "title" => "همایش 1+5 گسسته (پیش 1) آقای مؤذنی پور",
//                    "id" => 165,
//                    "description" => "شامل 5 ساعت و 10 دقیقه فیلم با حجم 601 گیگ",
//                    "type" =>2,
//                ]
//            ];
//
//
//            $initialArray =  [
//                [
//                    "title" => "Shimi",
//                    "parent" => $allOfShimiProduct ,
//                    "children" => $shimiChildren
//                ],
//                [
//                    "title" => "Physics",
//                    "parent" => $allOfPhysicProduct ,
//                    "children" => $physicChildren
//                ],
//                [
//                    "title" => "Zist",
//                    "parent" => $allOfZistProduct ,
//                    "children" => $zistChildren
//                ],
//                [
//                    "title" => "Arabi",
//                    "parent" => $allOfArabiProduct ,
//                    "children" => $ArabiChildren
//                ],
//                [
//                    "title" => "Dini",
//                    "parent" => $allOfDiniProduct ,
//                    "children" => $DiniChildren
//                ],
//                [
//                    "title" => "RiyaziTajrobi",
//                    "parent" => $allOfRiyaziTajrobiProduct ,
//                    "children" => $RiyaziTajrobiChildren
//                ],
//                [
//                    "title" => "Difransiel",
//                    "parent" => $allOfDifransielProduct ,
//                    "children" => $DifransielChildren
//                ],
//            ];
//        }
//
//        return $initialArray;

    }

    /**
     * Execute the console command.
     *
     * @param  ProductController  $productController
     *
     * @return mixed
     * @throws Exception
     */
    public function handle(ProductController $productController)
    {
        //TODO: pull from master
        //dd("TODO");
        $productCount = count($this->productArray);
        if ($this->confirm('Products will be merged into '.$productCount.'. Do you wish to continue?', true)) {
            $this->performMergingForAllProducts($productController, $productCount);

            if ($this->confirm('Do you want to clear cache ', true)) {
                Artisan::call('cache:clear');
            }

            $this->info('Merging Successfully Done!');
        } else {
            $this->info('Action Aborted');
        }
    }

    /**
     * @param  ProductController  $productController
     * @param                     $productCount
     *
     * @return void
     */
    private function performMergingForAllProducts(ProductController $productController, $productCount): void
    {
        $bar = $this->output->createProgressBar($productCount);
        foreach ($this->productArray as $productElement) {

            $allCategoryProduct = $this->getParent($productElement);
            $totalGrandChildrenCost = 0;
            $totalGrandChildrenDiscount = 25;

            if ($allCategoryProduct->hasParents()) {
                $grandParent = $allCategoryProduct->grandParent;
            } else {
                //This should not happen
                $this->info('Warning! Could not find grandparent for #'.$allCategoryProduct->id);
                continue;
            }

            $children = $productElement['children'];
            foreach ($children as $child) {
                $grandChildren = $this->extractChildren($child, $allCategoryProduct);

                $newParent = $this->getParent($child);
                $parent = $allCategoryProduct;
                if (isset($newParent)) {
                    $parent = $newParent;
                    $allCategoryProduct->children()
                        ->updateExistingPivot($parent->id, ['description' => $child['description']]);
                }

                $grandChildrenCount = count($grandChildren);
                $this->info("\n Found ".$grandChildrenCount.' items for '.$productElement['title']);
                $subBar = $this->output->createProgressBar($grandChildrenCount);
                foreach ($grandChildren as $grandChild) {
                    $newProductId = $grandChild['id'];

                    $hasConfigurableParent = false;
                    if ($grandChild['type'] == config('constants.PRODUCT_TYPE_CONFIGURABLE')) {
                        $hasConfigurableParent = true;
                    }

                    if ($hasConfigurableParent) {
                        $originalProduct = Product::Find($grandChild['id']);
                        if (!isset($originalProduct)) {
                            $this->info("\n Could not find original product #:".$grandChild['id']);
                            continue;
                        }

                        if (isset($originalProduct)) {
                            $newProductId = $this->copyOriginalProduct($productController, $originalProduct);
                        }
                    }

                    $newProduct = Product::Find($newProductId);
                    if (!isset($newProduct)) {
                        $this->info("\n Could not find new product of original product #:".$grandChild['id']);
                        continue;
                    }
                    $totalGrandChildrenCost += $newProduct->basePrice;

                    $newCost = $newProduct->basePrice;
                    $newDiscount = $newProduct->discount;
                    if ($hasConfigurableParent) {
                        $newCost = $originalProduct->basePrice;
                        $newDiscount = $originalProduct->discount;

                        $this->copyProductBelongings($originalProduct, $newProduct, $grandChild['title']);

                        $originalProduct->setDisable();
                        $originalProduct->update();

                        if ($originalProduct->hasParents()) {
                            $originalProductParent = $originalProduct->parents->first();
                            $this->copyProductBelongings($originalProductParent, $newProduct, $grandChild['title']);

                            $originalProductParent->setDisable();
                            $originalProductParent->update();
                        }

                        $this->info('Deleting orderproducts');
                        Orderproduct::deleteOpenedTransactions([$originalProduct->id],
                            [config('constants.ORDER_STATUS_OPEN')]);
                    }

                    ///////////////////////////
                    //Update new product //////
                    ///////////////////////////
                    $this->setNewProductAttributes($newProduct, $grandChild['title'], $newCost, $newDiscount,
                        $grandParent);
                    $newProduct->update();
                    ////////////////////////////
                    /////////////End ///////////
                    ////////////////////////////

                    ///////////////////////////
                    //Attaching children //////
                    ///////////////////////////
                    $parent->children()
                        ->attach($newProductId,
                            [
                                'control_id' => 2, 'description' => $grandChild['description'],
                                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
                            ]);
                    ///////////////////////////
                    ////////////End////////////
                    ///////////////////////////

                    $subBar->advance();
                }
                $subBar->finish();
            }

            ///////////////////////////////////////
            //Update grandparent and setting tags///
            ////////////////////////////////////////
            $grandParent->setEnable();
            $grandParent->update();
            $this->setTags($grandParent);
            ///////////////////////////
            //////////End//////////////
            ///////////////////////////

            $allCategoryProduct->basePrice = $totalGrandChildrenCost;
            $allCategoryProduct->discount = $totalGrandChildrenDiscount;
            $allCategoryProduct->setEnable();
            $allCategoryProduct->update();

            $this->info("\n\n");
            $this->info('Total Progress:');
            $bar->advance();
        }

        $bar->finish();
        $this->info("\n");
    }

    /**
     * @param $productElement
     *
     * @return mixed
     */
    private function getParent($productElement)
    {
        $parent = null;
        if (isset($productElement['parent'])) {
            $parent = $productElement['parent'];
        }

        return $parent;
    }

    /**
     * Extracts children
     *
     * @param           $child
     * @param  Product  $currentParent
     *
     * @return array
     */
    private function extractChildren($child, Product $currentParent): array
    {
        if (isset($child['children'])) {
            $grandChildren = $child['children'];
        } else {
            $grandChildren = [$child];
        }

        return $grandChildren;
    }

    /**
     * @param  ProductController  $productController
     * @param                     $originalProduct
     *
     * @return mixed
     */
    private function copyOriginalProduct(ProductController $productController, $originalProduct)
    {
        $response = $productController->copy($originalProduct);
        $responseContent = json_decode($response->getContent());
        if ($response->getStatusCode() == Response::HTTP_OK) {
            $newProductId = $responseContent->newProductId;
        } else {
            $newProductId = 0;
        }

        return $newProductId;
    }

    /**
     * @param $originalProduct
     * @param $newProduct
     * @param $title
     *
     * @return void
     */
    private function copyProductBelongings($originalProduct, $newProduct, $title): void
    {

        $this->copyProductFiles($originalProduct, $newProduct);

        $newProductPhotoInfo = ['title' => 'نمونه جزوه '.$title, 'description' => ''];
        $this->copyProductPhotos($originalProduct, $newProduct, $newProductPhotoInfo);
    }

    /**
     * @param $title
     * @param $newProduct
     * @param $newCost
     * @param $newDiscount
     * @param $grandParent
     */
    private function setNewProductAttributes($newProduct, $title, $newCost, $newDiscount, $grandParent): void
    {
        $newProduct->name = $title;
        $newProduct->basePrice = $newCost;
        $newProduct->discount = $newDiscount;
        $newProduct->redirectUrl = action('Web\ProductController@show', $grandParent);
    }

    /**
     * @param  Product  $product
     *
     * @return void
     */
    private function setTags(Product $product): void
    {
//       $this->sendTagsOfTaggableToApi($product ,$this->tagging );
        if (!(isset($product->tags) && isset($product->tags->tags))) {
            return;
        }
        $itemTagsArray = $product->tags->tags;
        $params = [
            'tags' => json_encode($itemTagsArray),
        ];

        if (isset($product->created_at) && strlen($product->created_at) > 0) {
            $params['score'] = Carbon::createFromFormat('Y-m-d H:i:s', $product->created_at)->timestamp;
        }

        $response =
            $this->sendRequest(config('constants.TAG_API_URL').'id/product/'.$product->id, 'PUT', $params);

//        if ($response["statusCode"] == Response::HTTP_OK) {
//            //
//        } else {
//        }
    }
}
