<?php

namespace App\Models;

use App\Classes\Advertisable;
use App\Classes\CacheFlush;
use App\Classes\FavorableInterface;
use App\Classes\Pricing\Alaa\AlaaProductPriceCalculator;
use App\Classes\SEO\SeoInterface;
use App\Classes\SEO\SeoMetaTagsGenerator;
use App\Classes\Taggable;
use App\Classes\Uploader\Uploader;
use App\Collection\ProductCollection;
use App\Collection\SetCollection;
use App\Services\TransactionsSerivce;
use App\Traits\APIRequestCommon;
use App\Traits\CommentTrait;
use App\Traits\DateTrait;
use App\Traits\favorableTraits;
use App\Traits\Helper;
use App\Traits\ModelTrackerTrait;
use App\Traits\Product\ProductAttributeTrait;
use App\Traits\Product\ProductBonTrait;
use App\Traits\Product\ProductPhotoTrait;
use App\Traits\Product\Resource;
use App\Traits\Product\TaggableProductTrait;
use App\Traits\ProductCommon;
use App\Traits\User\AssetTrait;
use App\Traits\WatchHistoryTrait;
use Carbon\Carbon;
use Config;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Purify;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends BaseModel implements Advertisable, FavorableInterface, SeoInterface, Taggable
{
    use APIRequestCommon;
    use AssetTrait;
    use CommentTrait;
    use DateTrait;
    use favorableTraits;
    use HasFactory;
    use Helper;
    use LogsActivity;
    use ModelTrackerTrait;
    use ProductAttributeTrait;
    use ProductBonTrait;

    /*
    |--------------------------------------------------------------------------
    | Traits
    |--------------------------------------------------------------------------
    */
    //    use Searchable;
    use ProductCommon;
    use ProductPhotoTrait;
    use Resource;
    use TaggableProductTrait;
    use WatchHistoryTrait;

    public const PHOTO_FIELD = 'image';

    public const TRANSMISSION_PRODUCTS = [];

    public const YALDA_SUBSCRIPTION = 629;

    public const GAMEBEGAME_HENDESEH = 563;

    public const GAMEBEGAM_GOSSASTE = 564;

    public const GAMBEGAM_HESABAN = 565;

    /*
    |--------------------------------------------------------------------------
    | Properties
    |--------------------------------------------------------------------------
    */
    public const ALL_PACK = [
        self::TAFTAN1400_RIYAZI_PACKAGE,        //527
        self::TAFTAN1400_TAJROBI_PACKAGE,       //528
        self::TAFTAN1401_OMOOMI_PACKAGE,        //532
        self::TAFTAN1401_RIYAZI_PACKAGE,        //663
        self::TAFTAN1401_TAJROBI_PACKAGE,       //662
        self::ARASH_PACK_RITAZI_1400,           //555
        self::ARASH_PACK_TAJROBI_1400,          //556
        self::ARASH_PACK_OMOOMI_1400,           //557
        self::ARASH_PACK_ENSANI,                //436
        self::ARASH_PACK_RIYAZI,                //437
        self::ARASH_PACK_TAJROBI,               //438
        self::RAHE_ABRISHAM99_PACK_TAJROBI,     //445
        self::RAHE_ABRISHAM99_PACK_RIYAZI,      //446
        self::RAHE_ABRISHAM1401_PACK_OMOOMI,    //573
        self::ZARBIN_ABRISHAM_REYAZI_PACK,      //585
        self::ZARBIN_ABRISHAM_TAJROBI_PACK,     //586
        self::ZARBIN_ABRISHAM_OMOOMI_PACK,      //584
    ];

    public const TAFTAN1400_RIYAZI_PACKAGE = 527;

    // todo: set this const ids plz
    public const TAFTAN1400_TAJROBI_PACKAGE = 528;

    public const TAFTAN1401_OMOOMI_PACKAGE = 532;

    // todo: check these ids with you database
    public const TAFTAN1401_RIYAZI_PRODUCTS = [
        self::TAFTAN1401_SHIMI,
        self::TAFTAN1401_FIZIK_RIYAZI,
        self::TAFTAN1401_AMAROEHTEMAL,
        self::TAFTAN1401_HENDESE,
        self::TAFTAN1401_HESABAN,
        self::TAFTAN1401_ZABAN,
        self::TAFTAN1401_DINI,
        self::TAFTAN1401_ADABIYAT,
        self::TAFTAN1401_ARABI,
    ];

    public const TAFTAN1401_TAJROBI_PRODUCTS = [
        self::TAFTAN1401_FIZIK_TAJROBI,
        self::TAFTAN1401_RIYAZI_TAJROBI,
        self::TAFTAN1401_ZIST,
        self::TAFTAN1401_SHIMI,
        self::TAFTAN1401_ZABAN,
        self::TAFTAN1401_DINI,
        self::TAFTAN1401_ADABIYAT,
        self::TAFTAN1401_ARABI,
    ];

    public const TAFTAN1401_OMOOMI_PRODUCTS = [530, 529, 526, 525, 524, 523, 522, 402, 401, 397, 396, 392];

    //All Packs
    public const ALL_TAFTAN_PRODUCTS = [
        self::TAFTAN1401_ARABI_ENSANI => ['color' => '#5F432D', 'lesson_name' => 'عربی انسانی'],
        self::TAFTAN1401_FALSAFE => ['color' => '#FF776D', 'lesson_name' => 'فلسفه و منطق'],
        self::TAFTAN1401_FIZIK_TAJROBI => ['color' => '#0288D1', 'lesson_name' => 'فیزیک تجربی'],
        self::TAFTAN1401_RIYAZI_TAJROBI => ['color' => '#F44336', 'lesson_name' => 'ریاضیات تجربی'],
        self::TAFTAN1401_ZIST => ['color' => '#4CAF50', 'lesson_name' => 'زیست شناسی'],
        self::TAFTAN1401_SHIMI => ['color' => '#7E57C2', 'lesson_name' => 'شیمی'],
        self::TAFTAN1401_FIZIK_RIYAZI => ['color' => '#0288D1', 'lesson_name' => 'فیزیک ریاضی'],
        self::TAFTAN1401_AMAROEHTEMAL => ['color' => '#FB8C00', 'lesson_name' => 'آمار و احتمال'],
        self::TAFTAN1401_HENDESE => ['color' => '#FB8C00', 'lesson_name' => 'هندسه'],
        self::TAFTAN1401_HESABAN => ['color' => '#FB8C00', 'lesson_name' => 'حسابان'],
        self::TAFTAN1401_ZABAN => ['color' => '#009688', 'lesson_name' => 'زبان انگلیسی'],
        self::TAFTAN1401_DINI => ['color' => '#FFCEAB', 'lesson_name' => 'دین و زندگی'],
        self::TAFTAN1401_ARABI => ['color' => '#5F432D', 'lesson_name' => 'عربی'],
        self::TAFTAN1401_ADABIYAT => ['color' => '#FF776D', 'lesson_name' => 'ادبیات'],
    ];

    //Taftan 1400
    public const TAFTAN_PRODUCTS_CATEGORY = [
        'ekhtesasi_riyazi' => [
            'user_major_category' => Major::RIYAZI, 'products' => self::TAFTAN1401_RIYAZI_PRODUCTS,
            'title' => 'اختصاصی ریاضی',
        ],
        'ekhtesasi_tajrobi' => [
            'user_major_category' => Major::TAJROBI, 'products' => self::TAFTAN1401_TAJROBI_PRODUCTS,
            'title' => 'اختصاصی تجربی',
        ],
    ];

    public const TAFTAN1401_RIYAZI_PACKAGE = 663;

    public const TAFTAN1401_TAJROBI_PACKAGE = 662;

    public const TAFTAN1401_ADABIYAT = 648;

    public const TAFTAN1401_ARABI = 649;

    public const TAFTAN1401_DINI = 650;

    public const TAFTAN1401_ZABAN = 651;

    public const TAFTAN1401_HESABAN = 652;

    public const TAFTAN1401_HENDESE = 653;

    public const TAFTAN1401_AMAROEHTEMAL = 654;

    public const TAFTAN1401_FIZIK_RIYAZI = 655;

    public const TAFTAN1401_SHIMI = 656;

    public const TAFTAN1401_ZIST = 657;

    public const TAFTAN1401_RIYAZI_TAJROBI = 658;

    public const TAFTAN1401_FIZIK_TAJROBI = 659;

    public const TAFTAN1401_ARABI_ENSANI = 661;

    public const TAFTAN1401_FALSAFE = 660;

    public const RIAZI_4K = 561;

    public const ENSANI_4K = 560;

    public const TAJROBI_4K = 559;

    public const _3A_JAMBANDI_YAZDAHOM_TAJROBI_MIAN_TERM1_1401 = 602;

    public const _3A_JAMBANDI_YAZDAHOM_RIYAZI_MIAN_TERM1_1401 = 603;

    public const _3A_JAMBANDI_YAZDAHOM_ENSANI_MIAN_TERM1_1401 = 601;

    public const _3A_JAMBANDI_DAVAZDAHOM_TAJROBI_MIAN_TERM1_1401 = 605;

    //3A products
    public const _3A_JAMBANDI_DAVAZDAHOM_RIYAZI_MIAN_TERM1_1401 = 606;

    public const _3A_JAMBANDI_DAVAZDAHOM_ENSANI_MIAN_TERM1_1401 = 604;

    public const _3A_JAMBANDI_DAVAZDAHOM_ENSANI_TERM1_1401 = 632;

    public const _3A_JAMBANDI_DAVAZDAHOM_RIYAZI_TERM1_1401 = 630;

    public const _3A_JAMBANDI_DAVAZDAHOM_TAJROBI_TERM1_1401 = 631;

    public const _3A_JAMBANDI_YAZDAHOM_TAJROBI_MIAN_TERM2_1401 = 666;

    public const _3A_JAMBANDI_YAZDAHOM_RIYAZI_MIAN_TERM2_1401 = 664;

    public const _3A_JAMBANDI_YAZDAHOM_ENSANI_MIAN_TERM2_1401 = 665;

    public const _3A_DAVAZDAHOM_ENSANI_JAMBADNI_PAYE_BA_JOGHRAFI_DAHOM = 633;

    public const _3A_DAVAZDAHOM_ENSANI_JAMBADNI_PAYE_BA_JOGHRAFI_YAZDAHOM = 644;

    public const _3A_DAVAZDAHOM_RIYAZI_JAMBANDI_PAYE_BA_FIZIK_DAHOM = 635;

    public const _3A_DAVAZDAHOM_RIYAZI_JAMBANDI_PAYE_BA_FIZIK_YAZDAHOM = 645;

    public const _3A_DAVAZDAHOM_TAJROBI_JAMBANDI_PAYE_BA_FIZIK_DAHOM = 638;

    public const _3A_DAVAZDAHOM_TAJROBI_JAMBANDI_PAYE_BA_FIZIK_YAZDAHOM = 646;

    public const _3A_DAVAZDAHOM_RIYAZI_JAMBANDI_DAHOM_VA_YAZDAHOM = 668;

    // these are use as free items in Api/OrderController@hasFreeStatus
    public const _3A_DAVAZDAHOM_TAJROBI_JAMBANDI_DAHOM_VA_YAZDAHOM = 667;

    public const _3A_DAVAZDAHOM_ENSANI_JAMBANDI_DAHOM_VA_YAZDAHOM = 669;

    public const DONATE_PRODUCT_5_HEZAR = 180;

    public const CUSTOM_DONATE_PRODUCT = 182;

    public const ASIATECH_PRODUCT = 224;

    public const COUPON_PRODUCT = 434;

    public const DONATE_PRODUCT_ARRAY = [
        self::CUSTOM_DONATE_PRODUCT,
        self::DONATE_PRODUCT_5_HEZAR,
    ];

    public const SUBSCRIPTION_1_MONTH = 444;

    public const SUBSCRIPTION_3_MONTH = 447;

    //Donate
    public const SUBSCRIPTION_12_MONTH = 448;

    public const SUBSCRIPTION_1_MONTH_TIMEPOINT_ONLY = 472;

    public const ONLY_PURCHASE_ONE = [
        self::SUBSCRIPTION_1_MONTH => [self::SUBSCRIPTION_3_MONTH, self::SUBSCRIPTION_12_MONTH],
        self::SUBSCRIPTION_3_MONTH => [self::SUBSCRIPTION_1_MONTH, self::SUBSCRIPTION_12_MONTH],
        self::SUBSCRIPTION_12_MONTH => [self::SUBSCRIPTION_1_MONTH, self::SUBSCRIPTION_3_MONTH],
    ];

    public const TIMEPOINT_SUBSCRIPTON_PRODUCTS = [
        Product::SUBSCRIPTION_12_MONTH,
        Product::SUBSCRIPTION_3_MONTH,
        Product::SUBSCRIPTION_1_MONTH,
        Product::SUBSCRIPTION_1_MONTH_TIMEPOINT_ONLY,
    ];

    public const ARASH_FIZIK_1400 = 410;

    //Subscriptions
    public const ARASH_FIZIK_1400_TOLOUYI = 549;

    public const ARASH_FIZIK_TETA_1400 = 546;

    public const ARASH_RIYAZI_TAJROBI_AMINI = 411;

    public const ARASH_ADABIYAT = 412;

    public const ARASH_ARABI = 413;

    public const ARASH_ZABAN = 414;

    //Arash 99
    public const ARASH_RIYAZIYAT_RIYAZI_1400 = 415;

    public const ARASH_SHIMI_1400 = 416;

    public const ARASH_DINI_1400 = 418;

    public const ARASH_RIYAZI_TAJROBI_SABETI = 433;

    public const ARASH_OLOOM_FONOON_1400 = 435;

    public const ARASH_ADABIYAT_1400 = 547;

    public const ARASH_ZIST_1400 = 548;

    public const ARASH_ZIST_TETA_1400 = 421;

    public const ARASH_PACK_RITAZI_1400 = 555;

    public const ARASH_PACK_TAJROBI_1400 = 556;

    public const ARASH_PACK_OMOOMI_1400 = 557;

    public const ARASH_PACK_ENSANI = 436;

    public const ARASH_PACK_RIYAZI = 437;

    public const ARASH_PACK_TAJROBI = 438;

    public const ARASH_RIYAZI_TAJROBI_1401 = 433;

    public const ARASH_RIYAZI_RIYAZI_1401 = 415;

    public const ARASH_ZIST_1401 = 548;

    public const ARASH_SHIMI_1401 = 416;

    public const ARASH_FIZIK_1401_YARI = 693;

    public const ARASH_ZABAN_1401 = 696;

    public const ARASH_ADABIYAT_1401 = 547;

    public const ARASH_ARABI_1401 = 694;

    public const ARASH_DINI_1401 = 695;

    public const ARASH_PACK_TAJROBI_1401 = 556;

    public const ARASH_PACK_RIYAZI_1401 = 555;

    public const ARASH_PACK_OMOOMI_1401 = 557;

    public const ALL_ARASH_SINGLE_1401 = [
        self::ARASH_RIYAZI_TAJROBI_1401,
        self::ARASH_RIYAZI_RIYAZI_1401,
        self::ARASH_ZIST_1401,
        self::ARASH_SHIMI_1401,
        self::ARASH_ZABAN_1401,
        self::ARASH_ADABIYAT_1401,
        self::ARASH_ARABI_1401,
        self::ARASH_DINI_1401,
        self::ARASH_FIZIK_1401_YARI,
    ];

    public const ARASH_TETA_SHIMI = 731;

    public const TETA_ZIST = 421;

    public const TETA_FIZIK_1400 = 546;

    public const TETA_ADABIAT = 628;

    public const ARASH_PRODUCTS_ARRAY = [
        self::ARASH_FIZIK_1400,
        self::ARASH_FIZIK_1400_TOLOUYI,
        self::ARASH_FIZIK_TETA_1400,
        self::ARASH_RIYAZI_TAJROBI_AMINI,
        self::ARASH_ADABIYAT,
        self::ARASH_ARABI,
        self::ARASH_ZABAN,
        self::ARASH_RIYAZIYAT_RIYAZI_1400,
        self::ARASH_SHIMI_1400,
        self::ARASH_DINI_1400,
        self::ARASH_RIYAZI_TAJROBI_SABETI,
        self::ARASH_OLOOM_FONOON_1400,
        self::ARASH_ADABIYAT_1400,
        self::ARASH_ZIST_1400,
        self::ARASH_ZIST_TETA_1400,
        self::ARASH_PACK_RITAZI_1400,
        self::ARASH_PACK_TAJROBI_1400,
        self::ARASH_PACK_OMOOMI_1400,
        self::ARASH_PACK_ENSANI,
        self::ARASH_PACK_RIYAZI,
        self::ARASH_PACK_TAJROBI,
    ];

    public const ARASH_PACK_PRODUCTS_ARRAY = [
        self::ARASH_PACK_RITAZI_1400,
        self::ARASH_PACK_TAJROBI_1400,
        self::ARASH_PACK_OMOOMI_1400,
    ];

    public const ARASH_PACK_OMOOMI_1400_SUBSET = [
        Product::ARASH_ZABAN, Product::ARASH_ADABIYAT_1400, Product::ARASH_DINI_1400, Product::ARASH_ARABI,
    ];

    public const ARASH_PACK_RIYAZI_1400_SUBSET = [Product::ARASH_SHIMI_1400, Product::ARASH_RIYAZIYAT_RIYAZI_1400];

    public const ARASH_PACK_RIYAZI_99_SUBSET = [
        Product::ARASH_SHIMI_1400, Product::ARASH_FIZIK_1400, Product::ARASH_RIYAZIYAT_RIYAZI_1400,
        Product::ARASH_ZABAN, Product::ARASH_DINI_1400, Product::ARASH_ARABI,
    ];

    public const ARASH_PACK_TAJROBI_1400_SUBSET = [
        Product::ARASH_ZIST_1400, Product::ARASH_RIYAZI_TAJROBI_SABETI, Product::ARASH_SHIMI_1400,
    ];

    public const ARASH_PACK_TAJROBI_99_SUBSET = [
        Product::ARASH_SHIMI_1400, Product::ARASH_FIZIK_1400, Product::ARASH_RIYAZI_TAJROBI_SABETI,
        Product::ARASH_ZABAN, Product::ARASH_DINI_1400, Product::ARASH_ARABI,
    ];

    public const ARASH_PRODUCTS_LESSON_NAME = [
        self::ARASH_RIYAZI_TAJROBI_AMINI => 'ریاضی',
        self::ARASH_ADABIYAT_1400 => 'ادبیات',
        self::ARASH_OLOOM_FONOON_1400 => 'علوم و فنون ادبی',
        self::ARASH_RIYAZI_TAJROBI_SABETI => 'ریاضی',
        self::ARASH_DINI_1400 => 'دین و زندگی',
        self::ARASH_SHIMI_1400 => 'شیمی',
        self::ARASH_ZABAN => 'زبان انگلیسی',
        self::ARASH_ARABI => 'عربی',
        self::ARASH_FIZIK_1400 => 'فیزیک',
        self::ARASH_ZIST_1400 => 'زیست شناسی',
        self::ARASH_RIYAZIYAT_RIYAZI_1400 => 'ریاضی',
        self::TETA_ZIST => 'زیست',
        self::TETA_FIZIK_1400 => 'فیزیک',
    ];

    public const ARASH_PRODUCTS_TEACHER_NAME = [
        self::ARASH_ADABIYAT_1400 => 'آقای حسین خانی',
        self::ARASH_OLOOM_FONOON_1400 => 'آقای صادقی',
        self::ARASH_RIYAZI_TAJROBI_SABETI => 'آقای ثابتی',
        self::ARASH_DINI_1400 => 'آقای ناصری',
        self::ARASH_SHIMI_1400 => 'آقای پویان نظر',
        self::ARASH_ZABAN => 'آقای عزتی',
        self::ARASH_ARABI => 'آقای علیمرادی',
        self::ARASH_FIZIK_1400 => 'آقای کازرانیان',
        self::ARASH_ZIST_1400 => 'آقای موقاری',
        self::ARASH_RIYAZIYAT_RIYAZI_1400 => 'آقای ثابتی',
        self::ARASH_RIYAZI_TAJROBI_AMINI => 'آقای امینی',
        self::TETA_ZIST => 'آقای موقاری',
        self::TETA_FIZIK_1400 => 'آقای یاری',
    ];

    public const GODAR_FIZIK_99 = 373;

    public const GODAR_FIZIK_1400 = 497;

    public const GODAR_RIYAZI_TAJROBI_AMINI = 377;

    public const GODAR_GOSASTE = 379;

    public const GODAR_HESABAN = 383;

    public const GODAR_RIYAZI_TAJROBI_SABETI = 385;

    public const GODAR_SHIMI = 387;

    // Godar98
    public const GODAR_HENDESE = 381;

    public const GODAR_ZIST_99 = 389;

    public const GODAR_ZIST_1400 = 496;

    public const GODAR_ALL = [
        Product::GODAR_FIZIK_99,
        Product::GODAR_FIZIK_1400,
        Product::GODAR_GOSASTE,
        Product::GODAR_HESABAN,
        Product::GODAR_RIYAZI_TAJROBI_SABETI,
        Product::GODAR_SHIMI,
        Product::GODAR_HENDESE,
        Product::GODAR_ZIST_1400,
    ];

    public const GODAR_PRODUCT_NAMES = [
        Product::GODAR_FIZIK_99 => 'محصول همایش گدار فیزیک',
        Product::GODAR_GOSASTE => 'محصول همایش گدار گسسته',
        Product::GODAR_HENDESE => 'محصول همایش گدار هندسه',
        Product::GODAR_HESABAN => 'محصول همایش گدار حسابان',
        Product::GODAR_RIYAZI_TAJROBI_SABETI => 'محصول همایش گدار ریاضی تجربی',
        Product::GODAR_SHIMI => 'محصول همایش گدار شیمی',
        Product::GODAR_FIZIK_1400 => 'محصول همایش گدار فیزیک 1400',
        Product::GODAR_ZIST_1400 => 'محصول همایش گدار زیست 1400',
    ];

    public const RAHE_ABRISHAM99_RIYAZIAT_TAJROBI = 347;

    public const RAHE_ABRISHAM99_RIYAZIAT_RIYAZI = 439;

    public const RAHE_ABRISHAM99_FIZIK_RIYAZI = 440;

    public const RAHE_ABRISHAM99_FIZIK_TAJROBI = 441;

    public const RAHE_ABRISHAM99_ZIST = 442;

    public const RAHE_ABRISHAM99_SHIMI = 443;

    public const RAHE_ABRISHAM99_PACK_TAJROBI = 445;

    //Abrisham 1400
    public const RAHE_ABRISHAM99_PACK_RIYAZI = 446;

    public const RAHE_ABRISHAM1401_PACK_OMOOMI = 573;

    public const RAHE_ABRISHAM1401_ZABAN = 569;

    public const RAHE_ABRISHAM1401_DINI = 570;

    public const RAHE_ABRISHAM1401_ARABI = 571;

    public const RAHE_ABRISHAM1401_ADABIYAT = 572;

    public const RAHE_ABRISHAM1401_PRO_SHIMI = 751;

    public const RAHE_ABRISHAM1401_PRO_ZIST = 750;

    public const RAHE_ABRISHAM1401_PRO_FIZIK_KAZERANIAN = 749;

    public const RAHE_ABRISHAM1401_PRO_FIZIK_TOLOUYI = 748;

    public const RAHE_ABRISHAM1401_PRO_RIYAZIYAT_RIYAZI = 747;

    public const RAHE_ABRISHAM1401_PRO_RIYAZI_TAJROBI = 746;

    public const RAHE_ABRISHAM1401_PRO_ADABIYAT = 755;

    public const RAHE_ABRISHAM1401_PRO_ARABI = 754;

    public const RAHE_ABRISHAM1401_PRO_DINI = 753;

    public const RAHE_ABRISHAM1401_PRO_ZABAN = 752;

    public const RAHE_ABRISHAM1401_PRO_PACK_OMOOMI = 758;

    public const RAHE_ABRISHAM1401_PRO_PACK_RIYAZI = 757;

    public const RAHE_ABRISHAM1401_PRO_PACK_TAJROBI = 756;

    public const RAHE_ABRISHAM1402_RIYAZIAT_TAJROBI_SABETI = 1092;

    public const RAHE_ABRISHAM1402_RIYAZIAT_TAJROBI_NABAKHTE = 1100;

    public const RAHE_ABRISHAM1402_HESABAN_NABAKHTE = 1090;

    public const RAHE_ABRISHAM1402_HESABAN_SABETI = 1101;

    public const RAHE_ABRISHAM1402_PACK_TAJROBI = 1096;

    public const RAHE_ABRISHAM1402_PACK_RIYAZI = 1097;

    public const RAHE_ABRISHAM1402_GOSASTE_AMAR_EHTEMAL = 1099;

    public const RAHE_ABRISHAM1402_RIYAZI_ENSANI = 1098;

    public const RAHE_ABRISHAM1402_SHIMI = 1095;

    public const RAHE_ABRISHAM1402_FIZIK = 1094;

    public const RAHE_ABRISHAM1402_ZIST = 1093;

    public const RAHE_ABRISHAM1402_HENDESE = 1091;

    public const RAHE_ABRISHAM1402_HESABAN = 1090;

    public const HAMAYESH_RIAZI_TAK_PACK_HESABAN = 781;

    public const HAMAYESH_RIAZI_TAK_PACK_HENDESE = 782;

    public const HAMAYESH_RIAZI_TAK_PACK_GOSASTE = 783;

    public const HAMAYESH_RIAZI_TAK_PACK_FIZIK = 784;

    public const HAMAYESH_RIAZI_TAK_PACK_SHIMI = 786;

    public const HAMAYESH_TAJROBI_TAK_PACK_FIZIK = 785;

    public const HAMAYESH_TAJROBI_TAK_PACK_SHIMI = 786;

    public const HAMAYESH_TAJROBI_TAK_PACK_RIAZI = 787;

    public const HAMAYESH_TAJROBI_TAK_PACK_ZIST = 788;

    public const HAMAYESH_TAJROBI_TAK_PACK_ZAMIN = 789;

    public const HAMAYESH_ENSANI_TAK_PACK_RIAZI = 790;

    public const HAMAYESH_ENSANI_TAK_PACK_FALSAFE = 791;

    public const HAMAYESH_ENSANI_TAK_PACK_ADABIAT = 792;

    public const HAMAYESH_ENSANI_TAK_PACK_RAVANSHENASI = 796;

    public const HAMAYESH_ENSANI_TAK_PACK_EGHTESAD = 797;

    public const HAMAYESH_ENSANI_TAK_PACK_JAMESHENASI = 798;

    public const HAMAYESH_ENSANI_TAK_PACK_JAMESHENASI110 = 799;

    public const HAMAYESH_ENSANI_TAK_PACK_ARABI = 800;

    public const HAMAYESH_ENSANI_TAK_PACK_TARIKH_JOGHRAFI = 951;

    public const HAMAYESH_BUNDLES_ENSANI = 793;

    public const HAMAYESH_BUNDLES_TAJROBI = 794;

    public const HAMAYESH_BUNDLES_RIAZI = 795;

    public const ALL_FORIAT_ENSANI_PRODUCTS = [
        self::HAMAYESH_ENSANI_TAK_PACK_RIAZI => ['color' => '#FB8C00', 'lesson_name' => 'ریاضی'],
        self::HAMAYESH_ENSANI_TAK_PACK_FALSAFE => ['color' => '#FB8C00', 'lesson_name' => 'فلسفه و منطق'],
        self::HAMAYESH_ENSANI_TAK_PACK_ADABIAT => ['color' => '#FB8C00', 'lesson_name' => 'ادبیات تخصصی'],
        self::HAMAYESH_ENSANI_TAK_PACK_RAVANSHENASI => ['color' => '#FB8C00', 'lesson_name' => 'روانشناسی'],
        self::HAMAYESH_ENSANI_TAK_PACK_EGHTESAD => ['color' => '#FB8C00', 'lesson_name' => 'اقتصاد'],
        self::HAMAYESH_ENSANI_TAK_PACK_JAMESHENASI => ['color' => '#FB8C00', 'lesson_name' => 'جامعه شناسی'],
        self::HAMAYESH_ENSANI_TAK_PACK_ARABI => ['color' => '#FB8C00', 'lesson_name' => 'عربی'],
        self::HAMAYESH_ENSANI_TAK_PACK_TARIKH_JOGHRAFI => ['color' => '#FB8C00', 'lesson_name' => 'تاریخ و جغرافیا'],
    ];

    public const ALL_ABRISHAM_PRODUCTS = [
        self::RAHE_ABRISHAM99_RIYAZIAT_RIYAZI => ['color' => '#FB8C00', 'lesson_name' => 'ریاضیات ریاضی'],
        self::RAHE_ABRISHAM99_FIZIK_RIYAZI => ['color' => '#81D4FA', 'lesson_name' => 'فیزیک'],
        self::RAHE_ABRISHAM99_FIZIK_TAJROBI => ['color' => '#0288D1', 'lesson_name' => 'فیزیک'],
        self::RAHE_ABRISHAM99_ZIST => ['color' => '#4CAF50', 'lesson_name' => 'زیست شناسی'],
        self::RAHE_ABRISHAM99_SHIMI => ['color' => '#7E57C2', 'lesson_name' => 'شیمی'],
        self::RAHE_ABRISHAM99_PACK_TAJROBI => ['color' => 'red', 'lesson_name' => 'پک اختصاصی تجربی'],
        self::RAHE_ABRISHAM99_PACK_RIYAZI => ['color' => 'red', 'lesson_name' => 'پک اختصاصی ریاضی'],
        self::RAHE_ABRISHAM99_RIYAZIAT_TAJROBI => ['color' => '#F44336', 'lesson_name' => 'ریاضیات تجربی'],
        self::RAHE_ABRISHAM1401_PACK_OMOOMI => ['color' => 'red', 'lesson_name' => 'پک عمومی'],
        self::RAHE_ABRISHAM1401_ZABAN => ['color' => '#009688', 'lesson_name' => 'زبان انگلیسی'],
        self::RAHE_ABRISHAM1401_DINI => ['color' => '#FFCEAB', 'lesson_name' => 'دین و زندگی'],
        self::RAHE_ABRISHAM1401_ARABI => ['color' => '#5F432D', 'lesson_name' => 'عربی'],
        self::RAHE_ABRISHAM1401_ADABIYAT => ['color' => '#FF776D', 'lesson_name' => 'ادبیات'],
    ];

    public const ALL_CHATR_NEJAT2_PRODUCTS = [
        self::CHATR_NEJAT2_GOSASTE => ['color' => '#FB8C00', 'lesson_name' => 'گسسته'],
        self::CHATR_NEJAT2_HENDESE => ['color' => '#FB8C00', 'lesson_name' => 'هندسه'],
        self::CHATR_NEJAT2_HESABAN => ['color' => '#FB8C00', 'lesson_name' => 'حسابان'],
        self::CHATR_NEJAT2_EGHTESAD => ['color' => '#FB8C00', 'lesson_name' => 'اقتصاد'],
        self::CHATR_NEJAT2_RIYAZI_ENSANI => ['color' => '#FB8C00', 'lesson_name' => 'ریاضی انسانی'],
        self::CHATR_NEJAT2_FIZIK => ['color' => '#81D4FA', 'lesson_name' => 'فیزیک'],
        self::CHATR_NEJAT2_ZIST => ['color' => '#4CAF50', 'lesson_name' => 'زیست شناسی'],
        self::CHATR_NEJAT2_ZAMIN_SHENASI => ['color' => '#4CAF50', 'lesson_name' => 'زمین شناسی'],
        self::CHATR_NEJAT2_FALSAFE_MANTEGH => ['color' => '#4CAF50', 'lesson_name' => 'فلسفه و منطق'],
        self::CHATR_NEJAT2_RAVANSHENASI => ['color' => '#FFCEAB', 'lesson_name' => 'روانشناسی'],
        self::CHATR_NEJAT2_JAMEE_SHENASI => ['color' => '#FFCEAB', 'lesson_name' => 'جامعه شناسی'],
        self::CHATR_NEJAT2_SHIMI => ['color' => '#7E57C2', 'lesson_name' => 'شیمی'],
        self::CHATR_NEJAT2_PACK_TAJROBI => ['color' => 'red', 'lesson_name' => 'پک اختصاصی تجربی'],
        self::CHATR_NEJAT2_PACK_RIYAZI => ['color' => 'red', 'lesson_name' => 'پک اختصاصی ریاضی'],
        self::CHATR_NEJAT2_RIYAZI_TAJROBI => ['color' => '#F44336', 'lesson_name' => 'ریاضیات تجربی'],
        self::CHATR_NEJAT2_PACK_ENSANI => ['color' => 'red', 'lesson_name' => 'پک انسانی'],
        self::CHATR_NEJAT2_ARABI_ENSANI => ['color' => '#5F432D', 'lesson_name' => 'عربی انسانی'],
        self::CHATR_NEJAT2_ADABIYAT => ['color' => '#FF776D', 'lesson_name' => 'ادبیات'],
        self::CHATR_NEJAT2_TARIKH_JOGHRAFI => ['color' => '#009688', 'lesson_name' => 'جغرافی'],
    ];

    public const ALL_ABRISHAM_PRODUCTS_OMOOMI = [
        self::RAHE_ABRISHAM1401_ZABAN,      // 569
        self::RAHE_ABRISHAM1401_DINI,       // 570
        self::RAHE_ABRISHAM1401_ARABI,      // 571
        self::RAHE_ABRISHAM1401_ADABIYAT,   // 572
    ];

    public const ALL_ABRISHAM_PRODUCTS_EKHTESASI_RIYAZI = [
        self::RAHE_ABRISHAM99_SHIMI,            // 443
        self::RAHE_ABRISHAM99_RIYAZIAT_RIYAZI,  // 439
        self::RAHE_ABRISHAM99_FIZIK_RIYAZI,     // 440
    ];

    public const ALL_ABRISHAM_PRODUCTS_EKHTESASI_TAJROBI = [
        self::RAHE_ABRISHAM99_SHIMI,            // 443
        self::RAHE_ABRISHAM99_RIYAZIAT_TAJROBI, // 347
        self::RAHE_ABRISHAM99_FIZIK_TAJROBI,    // 441
        self::RAHE_ABRISHAM99_ZIST,             // 442
    ];

    public const ALL_ABRISHAM_PRO_PRODUCTS = [
        self::RAHE_ABRISHAM1401_PRO_SHIMI => ['color' => '#FB8C00', 'lesson_name' => 'شیمی'],
        self::RAHE_ABRISHAM1401_PRO_ZIST => ['color' => '#81D4FA', 'lesson_name' => 'زیست شناسی'],
        self::RAHE_ABRISHAM1401_PRO_FIZIK_KAZERANIAN => ['color' => '#0288D1', 'lesson_name' => 'فیزیک'],
        self::RAHE_ABRISHAM1401_PRO_FIZIK_TOLOUYI => ['color' => '#4CAF50', 'lesson_name' => 'فیزیک'],
        self::RAHE_ABRISHAM1401_PRO_RIYAZIYAT_RIYAZI => ['color' => '#7E57C2', 'lesson_name' => 'ریاضیات ریاضی'],
        self::RAHE_ABRISHAM1401_PRO_RIYAZI_TAJROBI => ['color' => 'red', 'lesson_name' => 'ریاضیات تجربی'],
        self::RAHE_ABRISHAM1401_PRO_ADABIYAT => ['color' => 'red', 'lesson_name' => 'ادبیات'],
        self::RAHE_ABRISHAM1401_PRO_ARABI => ['color' => '#F44336', 'lesson_name' => 'عربی'],
        self::RAHE_ABRISHAM1401_PRO_DINI => ['color' => 'red', 'lesson_name' => 'دین و زندگی'],
        self::RAHE_ABRISHAM1401_PRO_ZABAN => ['color' => '#009688', 'lesson_name' => 'زبان انگلیسی'],
        self::RAHE_ABRISHAM1401_PRO_PACK_OMOOMI => ['color' => '#FFCEAB', 'lesson_name' => 'پک عمومی'],
        self::RAHE_ABRISHAM1401_PRO_PACK_RIYAZI => ['color' => '#5F432D', 'lesson_name' => 'پک اختصاصی ریاضی'],
        self::RAHE_ABRISHAM1401_PRO_PACK_TAJROBI => ['color' => '#FF776D', 'lesson_name' => 'پک اختصاصی تجربی'],
    ];

    public const  ALL_ABRISHAM_PRO_TABDIL = [
        771,
        770,
        769,
        768,
        767,
        766,
        765,
        764,
        763,
        762,
        761,
        760,
        759,
    ];

    public const ALL_ABRISHAM_PRO_PRODUCTS_OMOOMI = [
        self::RAHE_ABRISHAM1401_PRO_ADABIYAT,
        self::RAHE_ABRISHAM1401_PRO_ARABI,
        self::RAHE_ABRISHAM1401_PRO_DINI,
        self::RAHE_ABRISHAM1401_PRO_ZABAN,
    ];

    public const ALL_ABRISHAM_PRO_PRODUCTS_EKHTESASI_RIYAZI = [
        self::RAHE_ABRISHAM1401_PRO_SHIMI,
        self::RAHE_ABRISHAM1401_PRO_FIZIK_TOLOUYI,
        self::RAHE_ABRISHAM1401_PRO_RIYAZIYAT_RIYAZI,
    ];

    public const ALL_ABRISHAM_PRO_PRODUCTS_EKHTESASI_TAJROBI = [
        self::RAHE_ABRISHAM1401_PRO_SHIMI,
        self::RAHE_ABRISHAM1401_PRO_ZIST,
        self::RAHE_ABRISHAM1401_PRO_FIZIK_KAZERANIAN,
        self::RAHE_ABRISHAM1401_PRO_RIYAZI_TAJROBI,
    ];

    public const ABRISHAM_PRODUCTS_CATEGORY = [
        'omoomi' => ['user_major_category' => -1, 'products' => self::ALL_ABRISHAM_PRODUCTS_OMOOMI, 'title' => 'عمومی'],
        'ekhtesasi_riyazi' => [
            'user_major_category' => Major::RIYAZI, 'products' => self::ALL_ABRISHAM_PRODUCTS_EKHTESASI_RIYAZI,
            'title' => 'اختصاصی ریاضی',
        ],
        'ekhtesasi_tajrobi' => [
            'user_major_category' => Major::TAJROBI, 'products' => self::ALL_ABRISHAM_PRODUCTS_EKHTESASI_TAJROBI,
            'title' => 'اختصاصی تجربی',
        ],
    ];

    public const ABRISHAM_PRO_PRODUCTS_CATEGORY = [
        'omoomi' => [
            'user_major_category' => -1, 'products' => self::ALL_ABRISHAM_PRO_PRODUCTS_OMOOMI, 'title' => 'عمومی',
        ],
        'ekhtesasi_riyazi' => [
            'user_major_category' => Major::RIYAZI, 'products' => self::ALL_ABRISHAM_PRO_PRODUCTS_EKHTESASI_RIYAZI,
            'title' => 'اختصاصی ریاضی',
        ],
        'ekhtesasi_tajrobi' => [
            'user_major_category' => Major::TAJROBI, 'products' => self::ALL_ABRISHAM_PRO_PRODUCTS_EKHTESASI_TAJROBI,
            'title' => 'اختصاصی تجربی',
        ],
    ];

    public const ALL_CHATR_NEJAT_PRODUCTS_ENSANI = [
        self::CHATR_NEJAT2_RIYAZI_ENSANI,
        self::CHATR_NEJAT2_FALSAFE_MANTEGH,
        self::CHATR_NEJAT2_RAVANSHENASI,
        self::CHATR_NEJAT2_EGHTESAD,
        self::CHATR_NEJAT2_JAMEE_SHENASI,
        self::CHATR_NEJAT2_ARABI_ENSANI,
        self::CHATR_NEJAT2_TARIKH_JOGHRAFI,
    ];

    public const ALL_CHATR_NEJAT2_PRODUCTS_EKHTESASI_RIYAZI = [
        self::CHATR_NEJAT2_HENDESE,
        self::CHATR_NEJAT2_HESABAN,
        self::CHATR_NEJAT2_GOSASTE,
        self::CHATR_NEJAT2_FIZIK,
        self::CHATR_NEJAT2_SHIMI,
        self::CHATR_NEJAT2_ADABIYAT,
    ];

    public const ALL_CHATR_NEJAT2_PRODUCTS_EKHTESASI_TAJROBI = [
        self::CHATR_NEJAT2_SHIMI,
        self::CHATR_NEJAT2_RIYAZI_TAJROBI,
        self::CHATR_NEJAT2_ZIST,
        self::CHATR_NEJAT2_ZAMIN_SHENASI,
        self::CHATR_NEJAT2_ADABIYAT,
        self::CHATR_NEJAT2_FIZIK,
    ];

    public const CHATR_NEJAT2_PRODUCTS_CATEGORY = [
        //        'omoomi'            => ['user_major_category' => -1, 'products' => self::ALL_CHATR_NEJAT_PRODUCTS_ENSANI, 'title' => 'انسانی'],
        'ekhtesasi_riyazi' => [
            'user_major_category' => Major::RIYAZI, 'products' => self::ALL_CHATR_NEJAT2_PRODUCTS_EKHTESASI_RIYAZI,
            'title' => 'اختصاصی ریاضی',
        ],
        'ekhtesasi_tajrobi' => [
            'user_major_category' => Major::TAJROBI, 'products' => self::ALL_CHATR_NEJAT2_PRODUCTS_EKHTESASI_TAJROBI,
            'title' => 'اختصاصی تجربی',
        ],
    ];

    public const ARASH_1400_PACKS = [
        self::ARASH_PACK_RITAZI_1400,
        self::ARASH_PACK_TAJROBI_1400,
        self::ARASH_TITAN_PACK_TAJROBI,
        self::ARASH_TITAN_FIZIK,
        self::ARASH_TITAN_PACK_RIYAZI,
        self::ARASH_PACK_RIYAZI_1401,
        self::ARASH_PACK_TAJROBI_1401,
    ];

    public const CHATR_NEJAT2_HENDESE = 981;

    public const CHATR_NEJAT2_HESABAN = 980;

    public const CHATR_NEJAT2_GOSASTE = 979;

    public const CHATR_NEJAT2_FIZIK = 978;

    public const CHATR_NEJAT2_SHIMI = 976;

    public const CHATR_NEJAT2_RIYAZI_TAJROBI = 975;

    // Note: پک های آرش 1400 (تجربی، ریاضی)
    public const CHATR_NEJAT2_ZIST = 974;

    public const CHATR_NEJAT2_ZAMIN_SHENASI = 973; //Riyazi

    public const CHATR_NEJAT2_RIYAZI_ENSANI = 972; //Riyazi

    public const CHATR_NEJAT2_FALSAFE_MANTEGH = 971; //Riyazi

    public const CHATR_NEJAT2_ADABIYAT = 970; //Riyazi

    public const CHATR_NEJAT2_RAVANSHENASI = 966; //Riyazi , Tajrobi

    public const CHATR_NEJAT2_EGHTESAD = 965; //Tajrobi

    public const CHATR_NEJAT2_JAMEE_SHENASI = 964; //Tajrobi

    public const CHATR_NEJAT2_ARABI_ENSANI = 963; //Tajrobi

    public const CHATR_NEJAT2_TARIKH_JOGHRAFI = 962; //Ensani

    public const CHATR_NEJAT2_PACK_ENSANI = 969; //Ensani

    public const CHATR_NEJAT2_PACK_TAJROBI = 968; // Riyazi , Tajrobi

    public const CHATR_NEJAT2_PACK_RIYAZI = 967; //Ensani

    public const ALL_CHATR_NEJAT2_ENSANI_PRODUCTS =
        [
            self::CHATR_NEJAT2_RIYAZI_ENSANI,
            self::CHATR_NEJAT2_FALSAFE_MANTEGH,
            self::CHATR_NEJAT2_RAVANSHENASI,
            self::CHATR_NEJAT2_EGHTESAD,
            self::CHATR_NEJAT2_JAMEE_SHENASI,
            self::CHATR_NEJAT2_ARABI_ENSANI,
            self::CHATR_NEJAT2_TARIKH_JOGHRAFI,
        ];

    //Ensani
    public const USER_RECEIVABLE_PRODUCTS_RAHE_ABRISHAM_EKHTESASI = [
        self::RAHE_ABRISHAM99_FIZIK_RIYAZI,
        self::RAHE_ABRISHAM99_FIZIK_TAJROBI,
    ];

    //Ensani
    public const USER_RECEIVABLE_PRODUCTS_RAHE_ABRISHAM_PRO_EKHTESASI = [
        self::RAHE_ABRISHAM1401_PRO_FIZIK_KAZERANIAN,
        self::RAHE_ABRISHAM1401_PRO_FIZIK_TOLOUYI,
    ];

    //Ensani
    public const USER_RECEIVABLE_PRODUCTS_RAHE_ABRISHAM2_TAJROBI = [
        self::RAHE_ABRISHAM1402_RIYAZIAT_TAJROBI_SABETI,
        self::RAHE_ABRISHAM1402_RIYAZIAT_TAJROBI_NABAKHTE,
    ];

    //Ensani
    public const USER_RECEIVABLE_PRODUCTS_RAHE_ABRISHAM2_RIYAZI = [
        self::RAHE_ABRISHAM1402_HESABAN_SABETI,
        self::RAHE_ABRISHAM1402_HESABAN_NABAKHTE,
    ];

    //Pack
    public const USER_RECEIVABLE_PRODUCTS_ARASH_EKHTESASI = [
        self::ARASH_FIZIK_1400,
        self::ARASH_FIZIK_1400_TOLOUYI,
        self::ARASH_FIZIK_1401_YARI,
    ];

    //Pack
    public const USER_RECEIVABLE_PRODUCTS_RAHE_ABRISHAM_EKHTESASI_AND_ARASH_1400_SINBLES = [
        self::RAHE_ABRISHAM99_FIZIK_RIYAZI => self::USER_RECEIVABLE_PRODUCTS_RAHE_ABRISHAM_EKHTESASI,
        self::RAHE_ABRISHAM99_FIZIK_TAJROBI => self::USER_RECEIVABLE_PRODUCTS_RAHE_ABRISHAM_EKHTESASI,
        self::ARASH_FIZIK_1400 => self::USER_RECEIVABLE_PRODUCTS_ARASH_EKHTESASI,
        self::ARASH_FIZIK_1400_TOLOUYI => self::USER_RECEIVABLE_PRODUCTS_ARASH_EKHTESASI,
        self::ARASH_FIZIK_1401_YARI => self::USER_RECEIVABLE_PRODUCTS_ARASH_EKHTESASI,
        self::RAHE_ABRISHAM1401_PRO_FIZIK_KAZERANIAN => self::USER_RECEIVABLE_PRODUCTS_RAHE_ABRISHAM_PRO_EKHTESASI,
        self::RAHE_ABRISHAM1401_PRO_FIZIK_TOLOUYI => self::USER_RECEIVABLE_PRODUCTS_RAHE_ABRISHAM_PRO_EKHTESASI,
        self::RAHE_ABRISHAM1402_RIYAZIAT_TAJROBI_SABETI => self::USER_RECEIVABLE_PRODUCTS_RAHE_ABRISHAM2_TAJROBI,
        self::RAHE_ABRISHAM1402_RIYAZIAT_TAJROBI_NABAKHTE => self::USER_RECEIVABLE_PRODUCTS_RAHE_ABRISHAM2_TAJROBI,
        self::RAHE_ABRISHAM1402_HESABAN_SABETI => self::USER_RECEIVABLE_PRODUCTS_RAHE_ABRISHAM2_RIYAZI,
        self::RAHE_ABRISHAM1402_HESABAN_NABAKHTE => self::USER_RECEIVABLE_PRODUCTS_RAHE_ABRISHAM2_RIYAZI,
    ];

    //Pack
    public const USER_RECEIVABLE_PRODUCTS_RAHE_ABRISHAM_EKHTESASI_AND_ARASH_1400_PACKS = [
        self::RAHE_ABRISHAM99_FIZIK_RIYAZI => self::RAHE_ABRISHAM_EKHTESASI_PACKS,
        self::RAHE_ABRISHAM99_FIZIK_TAJROBI => self::RAHE_ABRISHAM_EKHTESASI_PACKS,
        self::ARASH_FIZIK_1400 => self::ARASH_1400_PACKS,
        self::ARASH_FIZIK_1400_TOLOUYI => self::ARASH_1400_PACKS,
        self::ARASH_FIZIK_1401_YARI => [
            self::ARASH_TITAN_PACK_TAJROBI, self::ARASH_TITAN_FIZIK, self::ARASH_TITAN_PACK_RIYAZI,
            self::ARASH_PACK_RIYAZI_1401, self::ARASH_PACK_TAJROBI_1401,
        ],
        self::RAHE_ABRISHAM1401_PRO_FIZIK_KAZERANIAN => self::RAHE_ABRISHAM1401_PRO_EKHTESASI_PACKS,
        self::RAHE_ABRISHAM1401_PRO_FIZIK_TOLOUYI => self::RAHE_ABRISHAM1401_PRO_EKHTESASI_PACKS,
        self::RAHE_ABRISHAM1402_RIYAZIAT_TAJROBI_SABETI => [self::RAHE_ABRISHAM1402_PACK_TAJROBI],
        self::RAHE_ABRISHAM1402_RIYAZIAT_TAJROBI_NABAKHTE => [self::RAHE_ABRISHAM1402_PACK_TAJROBI],
        self::RAHE_ABRISHAM1402_HESABAN_SABETI => [self::RAHE_ABRISHAM1402_PACK_RIYAZI],
        self::RAHE_ABRISHAM1402_HESABAN_NABAKHTE => [self::RAHE_ABRISHAM1402_PACK_RIYAZI],
    ];

    // Note: محصولاتی که کاربر باید از قبل خریده باشد تا بتواند محصول مورد نظر را به سفارشش اضافه کنیم
    public const ALL_SINGLE_ABRISHAM_EKHTESASI_PRODUCTS = [
        self::RAHE_ABRISHAM99_RIYAZIAT_RIYAZI,
        self::RAHE_ABRISHAM99_FIZIK_RIYAZI,
        self::RAHE_ABRISHAM99_FIZIK_TAJROBI,
        self::RAHE_ABRISHAM99_ZIST,
        self::RAHE_ABRISHAM99_SHIMI,
        self::RAHE_ABRISHAM99_RIYAZIAT_TAJROBI,
    ];

    public const ALL_SINGLE_ABRISHAM_PRODUCTS = [
        self::RAHE_ABRISHAM99_RIYAZIAT_RIYAZI,
        self::RAHE_ABRISHAM99_FIZIK_RIYAZI,
        self::RAHE_ABRISHAM99_FIZIK_TAJROBI,
        self::RAHE_ABRISHAM99_ZIST,
        self::RAHE_ABRISHAM99_SHIMI,
        self::RAHE_ABRISHAM99_RIYAZIAT_TAJROBI,
        self::RAHE_ABRISHAM1401_ZABAN,
        self::RAHE_ABRISHAM1401_DINI,
        self::RAHE_ABRISHAM1401_ARABI,
        self::RAHE_ABRISHAM1401_ADABIYAT,
    ];

    public const RAHE_ABRISHAM_EKHTESASI_PACKS = [
        self::RAHE_ABRISHAM99_PACK_TAJROBI,
        self::RAHE_ABRISHAM99_PACK_RIYAZI,
        self::ZARBIN_ABRISHAM_REYAZI_PACK,
        self::ZARBIN_ABRISHAM_TAJROBI_PACK,
    ];

    public const RAHE_ABRISHAM1401_PRO_EKHTESASI_PACKS = [
        self::RAHE_ABRISHAM1401_PRO_PACK_RIYAZI,
        self::RAHE_ABRISHAM1401_PRO_PACK_TAJROBI,
    ];

    public const ALL_PACK_ABRISHAM_PRODUCTS = [
        self::RAHE_ABRISHAM99_PACK_TAJROBI,
        self::RAHE_ABRISHAM99_PACK_RIYAZI,
        self::RAHE_ABRISHAM1401_PACK_OMOOMI,
    ];

    // Note: محصولاتی که کاربر باید از قبل خریده باشد تا بتواند محصول مورد نظر را به سفارشش اضافه کنیم
    public const TOOR_ABSRIHAM_1400_RIYAZI = 607;

    // Note: محصولاتی که کاربر باید از قبل خریده باشد تا بتواند محصول مورد نظر را به سفارشش اضافه کنیم
    public const TOOR_ABRISHAM_1400_TAJROBRI = 608;

    public const RAHE_ABRISHAM_RIYAZI_PASS_AZMOON = 611;

    public const RAHE_ABRISHAM_TAJROBI_PASS_AZMOON = 612;

    // Note: پک های راه ابریشم 1399 (تجربی، ریاضی)
    public const TITAN_AMAR_1400 = 545;

    public const TITAN_HENDESE_1400 = 544;

    public const TITAN_HESABAN_1400 = 543;

    public const TITAN_ZIST_1400 = 542;

    public const TITAN_RIYAZI_TAJROBI_1400 = 541;

    public const TITAN_ZABAN_1400 = 539;

    public const TITAN_ARABI_1400 = 537;

    public const TITAN_DINI_1400 = 538;

    public const TITAN_ADABIYAT_1400 = 536;

    public const TITAN_FIZIK_1400 = 534;

    public const TITAN_SHIMI_1400 = 535;

    public const TITAN_RIYAZI_TAJROBI_1401 = 713;

    public const TITAN_ZIST_1401 = 712;

    public const TITAN_ZABAN_1401 = 539;

    public const TITAN_ADABIYAT_1401 = 536;

    public const TITAN_ARABI_1401 = 537;

    public const TITAN_DINI_1401 = 715;

    public const TITAN_HESABAN_1401 = 714;

    public const TITAN_PACK_RIYAZI_1401 = 709;

    public const TITAN_PACK_TAJROBI_1401 = 708;

    public const TITAN_PACK_OMOOMI_1401 = 707;

    public const ALL_TITAN_SINGLE_1401 = [
        self::TITAN_AMAR_1400,
        self::TITAN_HENDESE_1400,
        self::TITAN_HESABAN_1401,
        self::TITAN_RIYAZI_TAJROBI_1401,
        self::TITAN_ZIST_1401,
        self::TITAN_ZABAN_1401,
        self::TITAN_ADABIYAT_1401,
        self::TITAN_ARABI_1401,
        self::TITAN_DINI_1401,
        self::TITAN_FIZIK_1400,
        self::TITAN_SHIMI_1400,
    ];

    public const TITAN_OMOOMI_PRODUCTS = [
        self::TITAN_ADABIYAT_1400,
        self::TITAN_ARABI_1400,
        self::TITAN_DINI_1400,
        self::TITAN_ZABAN_1400,
    ];

    public const TITAN_EKHTESASI_PRODUCTS = [
        self::TITAN_FIZIK_1400,
        self::TITAN_RIYAZI_TAJROBI_1400,
        self::TITAN_ZIST_1400,
        self::TITAN_HESABAN_1400,
        self::TITAN_HENDESE_1400,
        self::TITAN_AMAR_1400,
    ];

    public const SHOROO_AZ_NO = 684;

    public const AMOUNT_LIMIT = [
        'نامحدود',
        'محدود',
    ];

    public const ENABLE_STATUS = [
        'غیرفعال',
        'فعال',
    ];

    public const DISPLAY_STATUS = [
        'عدم نمایش',
        'نمایش',
    ];

    public const RECOMMENDER_CONTENTS_BUCKET = 'rp';

    public const SAMPLE_CONTENTS_BUCKET = 'relatedproduct';

    public const ARASH_RIYAZI_SPECIFIC = [
        Product::ARASH_PACK_RIYAZI, Product::ARASH_PACK_RITAZI_1400, Product::ARASH_RIYAZIYAT_RIYAZI_1400,
    ];

    public const ARASH_TAJROBI_SPECIFIC = [
        Product::ARASH_PACK_TAJROBI, Product::ARASH_PACK_TAJROBI_1400, Product::ARASH_ZIST_1400,
        Product::ARASH_RIYAZI_TAJROBI_AMINI, Product::ARASH_RIYAZI_TAJROBI_SABETI, Product::ARASH_ZIST_TETA_1400,
    ];

    public const USER_PRODUCTS_PANEL_EXCLUDE_PRODUCTS = [
        self::ARASH_PACK_RITAZI_1400,
        self::ARASH_PACK_TAJROBI_1400,
        self::ARASH_PACK_OMOOMI_1400,
        self::TAFTAN1400_RIYAZI_PACKAGE,
        self::TAFTAN1400_TAJROBI_PACKAGE,
        self::TAFTAN1401_OMOOMI_PACKAGE,
        self::COUPON_PRODUCT,
        self::SUBSCRIPTION_1_MONTH,
        self::SUBSCRIPTION_3_MONTH,
        self::SUBSCRIPTION_12_MONTH,
        self::SUBSCRIPTION_1_MONTH_TIMEPOINT_ONLY,
        self::RAHE_ABRISHAM99_PACK_RIYAZI,
        self::RAHE_ABRISHAM99_PACK_TAJROBI,
        self::RAHE_ABRISHAM1401_PACK_OMOOMI,
        574,
        575,
        576,
        577,
        578,
        579,
        580,
        581,
        582,
        583,
        584,
        585,
        586,
        587,
        588,
        589,
        self::RAHE_ABRISHAM1401_PRO_PACK_OMOOMI,
        self::RAHE_ABRISHAM1401_PRO_PACK_RIYAZI,
        self::RAHE_ABRISHAM1401_PRO_PACK_TAJROBI,
        self::SUBSCRIPTION_1_MONTH,
        self::SUBSCRIPTION_3_MONTH,
        self::SUBSCRIPTION_12_MONTH,
        self::RAHE_ABRISHAM1402_PACK_TAJROBI,
        self::RAHE_ABRISHAM1402_PACK_RIYAZI,
    ];

    public const ZARBIN_ABRISHAM_REYAZI_PACK = 585;

    public const ZARBIN_ABRISHAM_TAJROBI_PACK = 586;

    public const ZARBIN_ABRISHAM_OMOOMI_PACK = 584;

    public const ARASH_TITAN_RIYAZI_TAJORBI = 697;

    public const ARASH_TITAN_ZIST = 698;

    public const ARASH_TITAN_RIYAZI_RIYAZI = 699;

    public const ARASH_TITAN_SHIMI = 700;

    public const ARASH_TITAN_FIZIK = 701;

    public const ARASH_TITAN_ARABI = 702;

    public const ARASH_TITAN_ZABAN = 703;

    public const ARASH_TITAN_ADABIYAT = 704;

    public const ARASH_TITAN_PACK_TAJROBI = 705;

    public const ARASH_TITAN_PACK_RIYAZI = 706;

    public const ARASH_TITAN_PACK_OMOOMI = 710;

    public const ARASH_TITAN_DINI = 711;

    public const ALL_ARASH_TITAN = [
        self::ARASH_TITAN_RIYAZI_TAJORBI,
        self::ARASH_TITAN_ZIST,
        self::ARASH_TITAN_RIYAZI_RIYAZI,
        self::ARASH_TITAN_SHIMI,
        self::ARASH_TITAN_FIZIK,
        self::ARASH_TITAN_ARABI,
        self::ARASH_TITAN_ZABAN,
        self::ARASH_TITAN_ADABIYAT,
        self::ARASH_TITAN_DINI,
    ];

    public const ALL_NAHAYI_1402_PRODUCTS = [
        Product::EMTEHAN_NAHAYI_1402_SHIMI => ['color' => '#FB8C00', 'lesson_name' => ''],
        Product::EMTEHAN_NAHAYI_1402_FIZIK => ['color' => '#FB8C00', 'lesson_name' => ''],
        Product::EMTEHAN_NAHAYI_1402_ZIST => ['color' => '#FB8C00', 'lesson_name' => ''],
        Product::EMTEHAN_NAHAYI_1402_RIYAZIYAT_TAJROBI => ['color' => '#FB8C00', 'lesson_name' => ''],
        Product::EMTEHAN_NAHAYI_1402_HENDESE => ['color' => '#FB8C00', 'lesson_name' => ''],
        Product::EMTEHAN_NAHAYI_1402_GOSASTE => ['color' => '#FB8C00', 'lesson_name' => ''],
        Product::EMTEHAN_NAHAYI_1402_HESABAN => ['color' => '#FB8C00', 'lesson_name' => ''],
        Product::EMTEHAN_NAHAYI_1402_ZABAN => ['color' => '#FB8C00', 'lesson_name' => ''],
        Product::EMTEHAN_NAHAYI_1402_DINI => ['color' => '#FB8C00', 'lesson_name' => ''],
        Product::EMTEHAN_NAHAYI_1402_ARABI => ['color' => '#FB8C00', 'lesson_name' => ''],
        Product::EMTEHAN_NAHAYI_1402_ADABIYAT => ['color' => '#FB8C00', 'lesson_name' => ''],

    ];

    public const ALL_NAHAYI_1402_PRODUCTS_EKHTESASI_TAJROBI = [
        Product::EMTEHAN_NAHAYI_1402_SHIMI,
        Product::EMTEHAN_NAHAYI_1402_FIZIK,
        Product::EMTEHAN_NAHAYI_1402_ZIST,
        Product::EMTEHAN_NAHAYI_1402_RIYAZIYAT_TAJROBI,
        Product::EMTEHAN_NAHAYI_1402_ZABAN,
        Product::EMTEHAN_NAHAYI_1402_DINI,
        Product::EMTEHAN_NAHAYI_1402_ARABI,
        Product::EMTEHAN_NAHAYI_1402_ADABIYAT,
    ];

    public const ALL_NAHAYI_1402_PRODUCTS_EKHTESASI_RIYAZI = [
        Product::EMTEHAN_NAHAYI_1402_SHIMI,
        Product::EMTEHAN_NAHAYI_1402_FIZIK,
        Product::EMTEHAN_NAHAYI_1402_HENDESE,
        Product::EMTEHAN_NAHAYI_1402_GOSASTE,
        Product::EMTEHAN_NAHAYI_1402_HESABAN,
        Product::EMTEHAN_NAHAYI_1402_ZABAN,
        Product::EMTEHAN_NAHAYI_1402_DINI,
        Product::EMTEHAN_NAHAYI_1402_ARABI,
        Product::EMTEHAN_NAHAYI_1402_ADABIYAT,
    ];

    public const ALL_FORIYAT_110_PRODUCTS = [
        983, 951, 800, 798, 797, 796, 795, 794, 793, 792, 791, 790, 789, 788, 787, 786, 785, 784, 782, 781, 783,
    ];

    public const EMTEHAN_NAHAYI_1401 = [
        0 => 717,
        10 => 718,
        30 => 719,
        50 => 723,
        100 => 720,
        150 => 721,
        200 => 722,
    ];

    public const TAHLIL_KONKUT_1401 = [
        0 => 739,
        10 => 740,
        30 => 741,
        50 => 742,
        100 => 743,
        150 => 744,
        200 => 745,
    ];

    public const EMTEHAN_NAHAYI_1402_SHIMI = 995;

    public const EMTEHAN_NAHAYI_1402_FIZIK = 996;

    public const EMTEHAN_NAHAYI_1402_ZIST = 997;

    public const EMTEHAN_NAHAYI_1402_ADABIYAT = 998;

    public const EMTEHAN_NAHAYI_1402_ZABAN = 999;

    //keus are cost of products
    public const EMTEHAN_NAHAYI_1402_HENDESE = 1000;

    public const EMTEHAN_NAHAYI_1402_ARABI = 1001;

    public const EMTEHAN_NAHAYI_1402_GOSASTE = 1002;

    public const EMTEHAN_NAHAYI_1402_DINI = 1003;

    public const EMTEHAN_NAHAYI_1402_RIYAZIYAT_TAJROBI = 1004;

    public const EMTEHAN_NAHAYI_1402_HESABAN = 1005;

    public const EMTEHAN_NAHAYI_1402_RIYAZI_PACK = 1009;

    public const EMTEHAN_NAHAYI_1402_TAJROBI_PACK = 1008;

    public const EMTEHAN_NAHAYI_1402_FULL_PACK = 1007;

    public const ALL_EMTEHAN_NAHAYI_NOHOM_1402 = [
        1058,
        1059,
        1060,
        1061,
        1062,
        1063,
    ];

    public const YEKROOZE_FIZIK_1401 = 736;

    public const YEKROOZE_RIYAZI_1401 = 737;

    public const TAFTAN_PRODUCT_CATEGORY = 'همایش/تفتان';

    public const GODAR_PRODUCT_CATEGORY = 'همایش/گدار';

    public const ARASH_PRODUCT_CATEGORY = 'همایش/آرش';

    public const TETA_PRODUCT_CATEGORY = 'همایش/تتا'; // 2 RESHTE

    public const TITAN_PRODUCT_CATEGORY = 'همایش/تایتان';

    public const ABRISHAM_PRODUCT_CATEGORY = 'VIP';

    protected const PURIFY_NULL_CONFIG = ['HTML.Allowed' => ''];

    public const   ABRISHAM_2_DATA = [
        1094 => [
            'color' => '#76BDFF',
            'lesson_name' => 'فیزیک',
            'majorIds' => [1, 2],
        ],
        1095 => [
            'color' => '#9A77FF',
            'lesson_name' => 'شیمی',
            'majorIds' => [1, 2],
        ],
        1100 => [
            'color' => '#E25D5F',
            'lesson_name' => 'ریاضی تجربی',
            'majorIds' => [2],
        ],
        1092 => [
            'color' => '#E25D5F',
            'lesson_name' => 'ریاضی تجربی',
            'majorIds' => [2],
        ],
        1091 => [
            'color' => '#FD9D44',
            'lesson_name' => 'هندسه',
            'majorIds' => [1],
        ],
        1090 => [
            'color' => '#FD9D44',
            'lesson_name' => 'حسابان',
            'majorIds' => [1],
        ],
        1099 => [
            'color' => '#FD9D44',
            'lesson_name' => 'گسسته و آمار و احتمال',
            'majorIds' => [1],
        ],
        1098 => [
            'color' => '#FDD33F',
            'lesson_name' => 'ریاضی و آمار انسانی',
            'majorIds' => [3],
        ],
        1093 => [
            'color' => '#7AD05B',
            'lesson_name' => 'زیست',
            'majorIds' => [2],
        ],
    ];

    public const ENTEKHAB_RESHTE_IDS = [
        1239, 1240, 1241, 1242, 1243,
    ];

    public const MOSHAVERE_ENTEKHAB_RESHTE = 1238;

    public const INDEX_PAGE_NAME = 'productPage';

    protected static $recordEvents = ['updated', 'created', 'replicating', 'deleted'];

    protected static $console_description = ' from console';

    public bool $logFillable = true;

    public string $disk;

    public $draftAbles = [
        'shortDescription',
        'longDescription',
        'specialDescription',
    ];

    /**
     * @var array|mixed
     */
    protected $discount_ammount_cache;

    protected $price_cache;

    protected $fillable = [
        'name',
        'shortName',
        'basePrice',
        'shortDescription',
        'longDescription',
        'slogan',
        'image',
        'file',
        'validSince',
        'validUntil',
        'enable',
        'display',
        'order',
        'producttype_id',
        'discount',
        'amount',
        'attributeset_id',
        'isFree',
        'specialDescription',
        'redirectUrl',
        'category',
        'recommender_contents',
        'financial_category_id',
        'has_instalment_option',
        'instalments',
        'audience_description',
        'prerequisite_description',
        'usage_description',
        'objective_description',
        'wide_image',
    ];

    protected $cachedMethods = [
        'getPriceAttribute',
        'getMajorsAttribute',
        'getHoursAttribute',
    ];

    protected $appends = [
        'price',
        'instalmentsPrice',
    ];

    protected $hidden = [
        'gifts',
        'basePrice',
        'discount',
        'grand_id',
        'producttype_id',
        'attributeset_id',
        'file',
        'producttype',
        'validSince',
        'deleted_at',
        'validUntil',
        'image',
        'pivot',
        'created_at',
        'attributevalues',
        'grand',
        'productSet',
        'attributeset',
        'intro_videos',
        'block_id',
        'metaTitle',
        'metaDescription',
    ];

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = [
        'attributeset',
        //      'validProductfiles',
        'bons',
        'attributevalues',
        'gifts',
    ];

    protected $casts = [
        'instalments' => 'array',
    ];

    protected $with = [
        'producttype',
        'attributeset',
        'children',
        'bons',
    ];

    protected $major_cache;

    protected $hours_cache;

    /*
    |--------------------------------------------------------------------------
    | Private Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Gets specific number of products
     *
     *
     * @return $this
     */
    public static function recentProducts($number)
    {
        return self::getProducts(0, 1)
            ->take($number)
            ->orderBy('created_at', 'Desc');
    }

    /**
     * Gets desirable products
     *
     * @param  int  $onlyGrand
     * @param  int  $onlyEnable
     * @param  array  $excluded
     * @param  string  $orderBy
     * @param  string  $orderMethod
     * @param  array  $included
     * @return $this|Product|Builder
     */
    public static function getProducts(
        $onlyGrand = 0,
        $onlyEnable = 0,
        $excluded = [],
        $orderBy = 'created_at',
        $orderMethod = 'asc',
        $included = []
    ) {
        /** @var Product $products */
        if ($onlyGrand == 1) {
            $products = Product::isGrand();
        } else {
            if ($onlyGrand == 0) {
                $products = Product::query();
            }
        }

        if ($onlyEnable == 1) {
            $products = $products->enable();
        }

        if (! empty($excluded)) {
            $products->whereNotIn('id', $excluded);
        }

        if (! empty($included)) {
            $products->whereIn('id', $included);
        }

        switch ($orderMethod) {
            case 'asc' :
                $products->orderBy($orderBy);
                break;
            case 'desc' :
                $products->orderBy($orderBy, 'desc');
                break;
            default:
                break;
        }

        return $products;
    }

    public static function getProductsHaveBestOffer(): ProductCollection
    {
        return new ProductCollection();

        return Product::find([
            294,
            330,
            295,
            329,
            181,
            225,
            275,
            226,

        ]);
    }

    public static function getPackageProducts()
    {
        return Cache::tags(['package_products'])
            ->remember('product:packages', config('constants.CACHE_600'), function () {
                return Product::whereHas('packageProducts')->get();
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function getActivitylogOptions(): LogOptions
    {
        $model = explode('\\', self::class)[1];

        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $eventName
            ) => (auth()->check()) ? $eventName : $eventName.self::$console_description)
            ->useLogName("{$model}");
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        return new ProductCollection($models);
    }

    /**
     * Converts content's validUntil to Jalali
     */
    public function validUntil_Jalali($withTime = true): string
    {
        /*$explodedDateTime = explode(" ", $this->validUntil);*/
        //        $explodedTime = $explodedDateTime[1] ;
        return $this->convertDate($this->validUntil, 'toJalali');
    }

    /**
     * Scope a query to only include active Products.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeActive($query)
    {
        /** @var Product $query */
        return $query->enable()
            ->valid();
    }

    /**
     * Scope a query to only include enable(or disable) Products.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeEnable($query)
    {
        return $query->where('enable', 1);
    }

    /**
     * Scope a query to only include configurable Products.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeConfigurable($query)
    {
        return $query->where('producttype_id', '=', 2);
    }

    /**
     * Scope a query to only include simple Products.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeSimple($query)
    {
        return $query->where('producttype_id', '=', 1);
    }

    public function scopeIsGrand($query)
    {
        return $query->whereNull('grand_id');
    }

    public function scopeIsChild($query)
    {
        return $query->whereNotNull('grand_id');
    }

    public function scopeIdFrom($query, int $from)
    {
        return $query->where('id', '>=', $from);
    }

    public function scopeIdTo($query, int $to)
    {
        return $query->where('id', '<=', $to);
    }

    public function scopeId($query, int $id)
    {
        return $query->where('id', $id);
    }

    /**
     * Scope a query to only include valid Products.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeValid($query)
    {
        $now = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now('Asia/Tehran'));

        return $query->where(function ($q) use ($now) {
            $q->where('validSince', '<', $now)
                ->orWhereNull('validSince');
        })
            ->where(function ($q) use ($now) {
                $q->where('validUntil', '>', $now)
                    ->orWhereNull('validUntil');
            });
    }

    /**
     * Scope a query to only include product without redirect url.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeMain($query)
    {
        return $query->whereNull('redirectUrl');
    }
    /*
    |--------------------------------------------------------------------------
    | Accessor
    |--------------------------------------------------------------------------
    */

    /**
     * Scope a query to only include displayable products.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeDisplay($query)
    {
        return $query->where('display', 1);
    }
    /*
    |--------------------------------------------------------------------------
    | Accessor
    |--------------------------------------------------------------------------
    */

    /**
     * Scope a query to only include displayable products.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeBelongsToAbrishamProducts($query)
    {
        return $query->whereIn('id', array_keys(self::ALL_ABRISHAM_PRODUCTS));
    }

    /**
     * Scope a query to only include soalaa products.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeSoalaaProducts($query, bool $onlyGrand = false)
    {
        if ($onlyGrand) {
            $query->whereNull('grand_id');
        }

        return $query
            ->where('seller', config('constants.SOALAA_SELLER'))
            ->whereIn('attributeset_id',
                [config('constants.AZMOUN_ATTRIBUTE_SET'), config('constants.JOZVE_ATTRIBUTE_SET')]);
    }

    /**
     * Makes product's title
     */
    public function title(): string
    {
        if (! (isset($this->slogan) && strlen($this->slogan) > 0)) {
            return $this->name;
        }

        return $this->name.':'.$this->slogan;
    }

    /**
     * Gets product's tags
     *
     * @param $value
     * @return mixed
     */
    public function getInstalmentsDetailAttribute()
    {
        if (! $this->has_instalment_option) {
            return null;
        }
        $instalments = [];
        for ($i = 0; $i < count($this->instalments); $i++) {
            $data = [];
            $data['value'] = ($this->getPrice()['final_instalmentally'] * $this->instalments[$i]) / 100;
            $data['date'] = now()->addMonths($i);
            $instalments[] = $data;
        }

        return $instalments;
    }

    public function getTagsAttribute($value)
    {
        return json_decode($value);
    }

    /**
     * Gets product's tags
     *
     *
     * @return mixed
     */
    public function getSampleContentsAttribute($value)
    {
        return json_decode($value);
    }

    /**
     * Gets product's tags
     *
     *
     * @return mixed
     */
    public function getRecommenderContentsAttribute($value)
    {
        return json_decode($value);
    }

    public function getRedirectUrlAttribute($value)
    {
        if (! isset($value)) {
            return null;
        }

        $value = json_decode($value);
        $url = parse_url($value->url);

        return [
            'url' => url($url['path']),
            'code' => $value->code,
        ];
    }

    public function setRedirectUrlAttribute($value)
    {
        $this->attributes['redirectUrl'] = ! isset($value) ? null : json_encode($value);
    }

    /**
     * @param  User|null  $user
     */
    public function getPriceTextAttribute(): array
    {
        if ($this->isFree) {
            return 'رایگان';
        }

        $priceInfo = $this->price;

        if (is_null($priceInfo['base'])) {
            $basePriceText = 'پس از انتخاب محصول';
        } else {
            $basePriceText = number_format($priceInfo['base']).' تومان';
        }

        $finalPriceText = number_format($priceInfo['final']).' تومان';
        $customerDiscount = $priceInfo['discount'];

        return [
            'basePriceText' => $basePriceText,
            'finalPriceText' => $finalPriceText,
            'discount' => $customerDiscount,
        ];
    }

    /**
     * @param  User|null  $user
     */
    public function getPriceTextForInstalmentAttribute(): array
    {
        if ($this->isFree) {
            return 'رایگان';
        }

        $priceInfo = $this->price_for_instalment;

        if (is_null($priceInfo['base'])) {
            $basePriceText = 'پس از انتخاب محصول';
        } else {
            $basePriceText = number_format($priceInfo['base']).' تومان';
        }

        $finalPriceText = number_format($priceInfo['final']).' تومان';
        $customerDiscount = $priceInfo['discount'];

        return [
            'basePriceText' => $basePriceText,
            'finalPriceText' => $finalPriceText,
            'discount' => $customerDiscount,
        ];
    }

    /**
     * @return array|string
     */
    public function getPriceAttribute()
    {
        if (! is_null($this->price_cache)) {
            return $this->price_cache;
        }
        $costArray = $this->calculatePayablePrice();

        $cost = $costArray['cost'];
        $customerPrice = $costArray['customerPrice'];
        $customerInstalmentallyPrice = $costArray['customerPriceInstalmentally'];
        if (! isset($cost)) {
            return [
                'base' => null,
                'discount' => null,
                'discount_instalmentally' => null,
                'final' => null,
                'final_instalmentally' => null,
            ];
        }
        $this->price_cache = [
            'base' => $cost,
            'discount' => $cost - $customerPrice,
            'discount_instalmentally' => $cost - $customerInstalmentallyPrice,
            'final' => $customerPrice,
            'final_instalmentally' => $customerInstalmentallyPrice,
        ];

        return $this->price_cache;
    }

    /*
    |--------------------------------------------------------------------------
    | Mutator
    |--------------------------------------------------------------------------
    */

    public function calculatePayablePrice()
    {
        $costArray = [];
        $costInfo = $this->obtainCostInfo();
        $costArray['cost'] = $costInfo->info->productCost;
        $costArray['customerPrice'] = $costInfo->price;
        $costArray['customerPriceInstalmentally'] = $costInfo->price_instalmentally;
        $costArray['productDiscount'] =
            $costInfo->info->discount->info->product->info->percentageBase->percentage;
        $costArray['productDiscountValue'] =
            $costInfo->info->discount->info->product->info->percentageBase->decimalValue;
        $costArray['productInstalmentallyDiscount'] =
            $costInfo->info->discount->info->product->info->percentageBase->instalmentally_percentage;
        $costArray['productInstalmentallyDiscountValue'] =
            $costInfo->info->discount->info->product->info->percentageBase->instalmentally_decimalValue;
        $costArray['productDiscountAmount'] = $costInfo->info->discount->info->product->info->amount;
        $costArray['bonDiscount'] = 0;
        $costArray['customerDiscount'] = $costInfo->info->discount->totalAmount;

        return $costArray;
    }

    /**
     * Obtains product's cost
     *
     * @param  User|null  $user
     * @return mixed
     */
    private function obtainCostInfo()
    {
        $key =
            'product:obtainCostInfo:'.$this->cacheKey();
        $cacheTags = ['product', 'product_'.$this->id, 'productCost', 'cost'];

        return Cache::tags($cacheTags)
            ->remember($key, config('constants.CACHE_60'), function () {
                $cost = new AlaaProductPriceCalculator($this);

                return json_decode($cost->getPrice());
            });
    }

    public function getPriceForInstalmentAttribute()
    {
        //ToDo : Doesn't work for app
        $user = null;
        if (Auth::check()) {
            $user = Auth::user();
        }

        $costArray = $this->calculatePayablePriceForInstalmentPurchase($user);
        $cost = $costArray['cost'];
        $customerPrice = $costArray['customerPrice'];
        if (! isset($cost)) {
            return [
                'base' => null,
                'discount' => null,
                'final' => null,
            ];
        }

        return [
            'base' => $cost,
            'discount' => $cost - $customerPrice,
            'final' => $customerPrice,
        ];
    }

    /**
     * Get the content's meta title .
     *
     * @param $value
     */
    public function getMetaTitleAttribute(): string
    {
        $text = $this->getCleanTextForMetaTags($this->name);

        return mb_substr($text, 0, config('constants.META_TITLE_LIMIT'), 'utf-8');
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    /**
     * @return mixed
     */
    private function getCleanTextForMetaTags(string $text)
    {
        return Purify::clean($text, self::PURIFY_NULL_CONFIG);
    }

    public function getShowDiscountAttribute(): float
    {
        return 0;
        /** @var User $user */
        $user = auth()->user();
        if (isset($user)) {

            $productId = $this->id;
            $tempKey = 'getShowDiscountAttributeP'.$productId.'U'.$user->id;
            $costInfo = Config::get($tempKey);
            if (isset($costInfo)) {
                return $costInfo;
            }

            $key = 'getShowDiscountAttribute:'.$user->id.'-product:'.$this->id;
            $tags = ['getShowDiscountAttribute:'.$user->id, CacheFlush::YALDA_1400_TAG.$user->id];
            $result = Cache::tags($tags)->remember($key, config('constants.CACHE_5'), function () use ($user) {
                /** @var User $user */
                if (isset($user) && in_array($this->id, array_keys(self::ALL_ABRISHAM_PRODUCTS))) {
                    $openOrder = $user->getOpenOrderOrCreate();

                    return $openOrder->couponDiscount / 100;
                }

                return 0;
            });
            $costInfo = $result;
            Config::set($tempKey, $costInfo);

            return $result;
        }

        return 0;

    }

    /**
     * Get the content's meta description .
     *
     * @param $value
     */
    public function getMetaDescriptionAttribute(): string
    {
        $text = $this->getCleanTextForMetaTags($this->shortDescription.' '.$this->longDescription);

        return mb_substr($text, 0, config('constants.META_DESCRIPTION_LIMIT'), 'utf-8');
    }

    /**
     * Gets product's meta tags array
     */
    public function getMetaTags(): array
    {
        $metaTitle = $this->metaTitle;
        if (in_array($this->id, self::ARASH_PRODUCTS_ARRAY)) {
            $metaTitle = $this->getMeteTitleOfArashProducts();
        }

        $metaDescription = $this->metaDescription;
        if (in_array($this->id, self::ARASH_PRODUCTS_ARRAY)) {
            $metaDescription = $this->getMetaDescriptionOfArashProducts();
        }

        return [
            'title' => $metaTitle,
            'description' => $metaDescription,
            'url' => action('Web\ProductController@show', $this),
            'canonical' => action('Web\ProductController@show', $this),
            'site' => 'آلاء',
            'imageUrl' => $this->image,
            'imageWidth' => '338',
            'imageHeight' => '338',
            'tags' => $this->tags,
            'seoMod' => SeoMetaTagsGenerator::SEO_MOD_PRODUCT_TAGS,
        ];
    }

    public function getMeteTitleOfArashProducts()
    {
        switch ($this->id) {
            case Product::ARASH_PACK_RITAZI_1400 :
                return 'همایش جمع بندی دروس تخصصی کنکور ریاضی آرش آلا';
                break;
            case Product::ARASH_PACK_TAJROBI_1400 :
                return 'همایش جمع بندی دروس تخصصی کنکور تجربی آرش آلا';
                break;
            case Product::ARASH_PACK_OMOOMI_1400  :
                return 'همایش جمع بندی دروس عمومی کنکور  آرش آلا';
                break;
            default:
                $lessonName = Arr::get(self::ARASH_PRODUCTS_LESSON_NAME, $this->id);
                $metaTitle = 'همایش جمع‌بندی '.$lessonName.' کنکور آرش آلاء';
                if (in_array($this->id, [
                    self::TETA_FIZIK_1400, self::ARASH_FIZIK_1400, self::ARASH_RIYAZI_TAJROBI_SABETI,
                    self::ARASH_RIYAZI_TAJROBI_AMINI,
                ])) {
                    $metaTitle .= ' - '.Arr::get(self::ARASH_PRODUCTS_TEACHER_NAME, $this->id);
                }

                return $metaTitle;
        }
    }

    public function getMetaDescriptionOfArashProducts()
    {
        switch ($this->id) {
            case Product::ARASH_PACK_RITAZI_1400 :
                return 'همایش جمع بندی صفر تا صد دروس تخصصی کنکور ریاضی آرش آلاء، شامل جمع بندی کامل دروس به صورت نکته و تست همراه با درسنامه و تحلیل کامل سوالات کنکور 98و99';
                break;
            case Product::ARASH_PACK_TAJROBI_1400 :
                return 'همایش جمع بندی صفر تا صد دروس تخصصی کنکور تجربی آرش آلاء، شامل جمع بندی کامل دروس به صورت نکته و تست همراه با درسنامه و تحلیل کامل سوالات کنکور 98و99';
                break;
            case Product::ARASH_PACK_OMOOMI_1400  :
                return 'همایش جمع بندی صفر تا صد دروس عمومی کنکور  آرش آلاء، شامل جمع بندی کامل دروس به صورت نکته و تست همراه با درسنامه و تحلیل کامل سوالات کنکور 98و99';
                break;
            default:
                $lessonName = Arr::get(self::ARASH_PRODUCTS_LESSON_NAME, $this->id);
                $teacherName = Arr::get(self::ARASH_PRODUCTS_TEACHER_NAME, $this->id);

                return 'همایش جمع بندی صفر تا صد '.$lessonName.' '.$teacherName.' کنکور آرش آلاء، شامل جمع بندی کامل '.$lessonName.' به صورت نکته و تست همراه با درسنامه و تحلیل کامل سوالات کنکور 98و99';
                break;
        }
    }

    /** Setter mutator for limit
     *
     */
    public function setAmountAttribute($value): void
    {
        if ($value == 0) {
            $this->attributes['amount'] = null;
        } else {
            $this->attributes['amount'] = $value;
        }
    }

    /** Setter mutator for discount
     *
     */
    public function setDiscountAttribute($value): void
    {
        if ($this->strIsEmpty($value)) {
            $this->attributes['discount'] = null;
        } else {
            $this->attributes['discount'] = $value;
        }
    }

    /** Setter mutator for discount
     *
     */
    public function setShortDescriptionAttribute($value): void
    {
        if ($this->strIsEmpty($value)) {
            $this->attributes['shortDescription'] = null;
        } else {
            $this->attributes['shortDescription'] = $value;
        }
    }

    /** Setter mutator for discount
     *
     */
    public function setLongDescriptionAttribute($value): void
    {
        if ($this->strIsEmpty($value)) {
            $this->attributes['longDescription'] = null;
        } else {
            $this->attributes['longDescription'] = $value;
        }
    }

    /** Setter mutator for discount
     *
     */
    public function setSpecialDescriptionAttribute($value): void
    {
        if ($this->strIsEmpty($value)) {
            $this->attributes['specialDescription'] = null;
        } else {
            $this->attributes['specialDescription'] = $value;
        }
    }

    /** Setter mutator for order
     *
     */
    public function setOrderAttribute($value = null): void
    {
        if ($this->strIsEmpty($value)) {
            $value = 0;
        }

        $this->attributes['order'] = $value;
    }

    /**
     * Set the content's tag.
     *
     *
     * @return void
     */
    public function setTagsAttribute(array $value = null)
    {
        $tags = null;
        if (! empty($value)) {
            $tags = json_encode([
                'bucket' => 'content',
                'tags' => $value,
            ], JSON_UNESCAPED_UNICODE);
        }

        $this->attributes['tags'] = $tags;
    }

    /**
     * Set the content's tag.
     *
     *
     * @return void
     */
    public function setSampleContentsAttribute(array $value = null)
    {
        $tags = null;
        if (! empty($value)) {
            $tags = json_encode([
                'bucket' => 'relatedproduct',
                'tags' => $value,
            ], JSON_UNESCAPED_UNICODE);
        }

        $this->attributes['sample_contents'] = $tags;
    }

    /**
     * @return void
     */
    public function setRecommenderContentsAttribute(array $value = null)
    {
        $tags = null;
        if (! empty($value)) {
            $tags = json_encode([
                'bucket' => self::RECOMMENDER_CONTENTS_BUCKET,
                'recommenders' => $value,
            ], JSON_UNESCAPED_UNICODE);
        }

        $this->attributes['recommender_contents'] = $tags;
    }

    /**
     * Set the product's thumbnail.
     *
     *
     * @return void
     */
    public function setIntroVideosAttribute(Collection $input = null)
    {
        $this->attributes['intro_videos'] = optional($input)->toJson(JSON_UNESCAPED_UNICODE);
    }

    public function exams()
    {
        return $this->hasMany(_3aExam::class);
    }

    public function productExams()
    {
        return $this->hasMany(Exam::class);
    }

    public function producttype()
    {
        return $this->belongsTo(Producttype::class)
            ->withDefault();
    }

    public function orderproducts()
    {
        return $this->hasMany(Orderproduct::class);
    }

    public function gifts()
    {
        return $this->belongsToMany(Product::class, 'product_product', 'p1_id', 'p2_id')
            ->withPivot('relationtype_id')
            ->join('productinterrelations',
                'relationtype_id', 'productinterrelations.id')
            ->where('relationtype_id', config('constants.PRODUCT_INTERRELATION_GIFT'));
    }

    public function upgrade()
    {
        return $this->belongsToMany(Product::class, 'product_product', 'p1_id', 'p2_id')
            ->where('relationtype_id', config('constants.PRODUCT_INTERRELATION_UPGRADE'))
            ->withPivot(['relationtype_id', 'choiceable', 'required_when']);
    }

    public function transformer()
    {
        return $this->belongsToMany(Product::class, 'product_product', 'p2_id', 'p1_id')
            ->where('relationtype_id', config('constants.PRODUCT_INTERRELATION_UPGRADE'));
    }

    public function packageProducts()
    {
        //ToDo : There is not PRODUCT_INTERRELATION_PACKAGE and sould be replaced with PRODUCT_INTERRELATION_ITEM
        //ToDo : But with doing that also the belongToMany shloud be refactored
        return $this->belongsToMany(Product::class, 'product_product', 'p1_id', 'p2_id')
            ->where('relationtype_id', config('constants.PRODUCT_INTERRELATION_PACKAGE'));
    }

    public function productProduct()
    {
        return $this->belongsToMany(Product::class, 'product_product', 'p1_id', 'p2_id')
            ->withPivot(['relationtype_id', 'choiceable', 'required_when']);
    }

    public function itemAndGift()
    {
        return $this->belongsToMany(Product::class, 'product_product', 'p1_id', 'p2_id')
            ->whereIn('relationtype_id', [
                config('constants.PRODUCT_INTERRELATION_GIFT'),
                config('constants.PRODUCT_INTERRELATION_ITEM'),
            ])
            ->withPivot(['relationtype_id', 'choiceable', 'required_when']);
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class);
    }

    public function blocks()
    {
        return $this->belongsToMany(Block::class)
            ->withPivot('order', 'enable')
            ->withTimestamps()
            ->orderBy('order');
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function livedescriptions()
    {
        return $this->morphMany(LiveDescription::class, 'entity');
    }

    public function referralCodeCommission()
    {
        return $this->morphMany(ReferralCodeCommission::class, 'entity');
    }

    public function descriptionWithPeriod()
    {
        return $this->hasMany(Descriptionwithperiod::class, 'product_id', 'id');
    }

    public function productfiles()
    {
        return $this->hasMany(Productfile::class);
    }

    public function faqs()
    {
        return $this->hasMany(Faq::class);
    }

    public function grandsChildren()
    {
        return $this->hasMany(Product::class, 'grand_id');
    }

    public function isChildOfGrandChild()
    {
        if (! empty($this->grand)) {
            $product = $this->grand;
            if (! empty($product->grand)) {
                return true;
            }
        }

        return false;
    }

    public function mapDetails()
    {
        return $this->morphMany(MapDetail::class, 'entity');
    }

    //TODO: issue #97

    public function hardship()
    {
        return $this->belongsTo(Hardship::class);
    }

    /**Determines whether this product has any gifts or not
     *
     * @return bool
     */
    public function hasGifts(): bool
    {
        $key = 'product:hasGifts:'.$this->cacheKey();

        return Cache::tags(['product', 'product_'.$this->id, 'productGift', 'gift'])
            ->remember($key, config('constants.CACHE_60'), function () {
                return $this->gifts->isEmpty() ? false : true;
            });
    }

    /**Determines whether this product has valid files or not
     *
     * @param $fileType
     *
     * @return bool
     */
    public function hasValidFiles($fileType): bool
    {
        $key = 'product:hasValidFiles:'.$fileType.$this->cacheKey();

        return Cache::tags(['product', 'product_'.$this->id, 'validFiles', 'file'])
            ->remember($key, config('constants.CACHE_60'), function () use ($fileType) {
                return ! $this->validProductfiles($fileType)
                    ->get()
                    ->isEmpty();
            });
    }

    /**
     * @param  string  $fileType
     * @param  int  $getValid
     */
    public function validProductfiles($fileType = '', $getValid = 1)
    {
        $product = $this;

        $files = $product->hasMany(Productfile::class)
            ->enable();
        if ($getValid) {
            $files->valid();
        }
        $fileTypeId = [
            'video' => config('constants.PRODUCT_FILE_TYPE_VIDEO'),
            'pamphlet' => config('constants.PRODUCT_FILE_TYPE_PAMPHLET'),
            '' => null,
        ][$fileType];

        if (isset($fileTypeId)) {
            $files->where('productfiletype_id', $fileTypeId);
        }

        $files->orderBy('order');

        return $files;
    }

    public function isAbrisham()
    {
        return in_array($this->id, array_keys(self::ALL_ABRISHAM_PRODUCTS));
    }

    public function isAvailableForAds()
    {
        return $this->isActive() && is_null($this->redirectUrl) && $this->isRoot();
    }

    /**
     * Checks whether the product is active or not .
     */
    public function isActive(): bool
    {
        return $this->isEnable() && $this->isValid();
    }

    /**
     * Checks whether the product is enable or not .
     */
    public function isEnable(): bool
    {
        return $this->enable ?? false;
    }

    /**
     * Checks whether the product is valid or not .
     */
    public function isValid(): bool
    {
        //        if (($this->validSince < Carbon::createFromFormat('Y-m-d H:i:s',
        //                    Carbon::now())
        //                    ->timezone('Asia/Tehran') || $this->validSince === null) && ($this->validUntil > Carbon::createFromFormat('Y-m-d H:i:s',
        //                    Carbon::now())
        //                    ->timezone('Asia/Tehran') || $this->validUntil === null)) {
        //            return true;
        //        }
        //
        //        return false;

        // TODO: I think the following code is better than the above code.
        return ($this->validSince < Carbon::now('Asia/Tehran') || is_null($this->validSince)) &&
            ($this->validUntil > Carbon::now('Asia/Tehran') || is_null($this->validUntil));

        // TODO: I think the following code is better than two above codes for api.
        //        return ($this->validSince < Carbon::now('Asia/Tehran')->format('Y-m-d H:i:s') || is_null($this->validSince)) &&
        //            ($this->validUntil > Carbon::now('Asia/Tehran')->format('Y-m-d H:i:s') || is_null($this->validUntil));
    }

    public function isRoot(): bool
    {
        return is_null($this->grand_id);
    }

    public function migrationGrand()
    {
        $key = 'product:GrandParent:'.$this->cacheKey();

        return Cache::tags([
            'product', 'parentProduct', 'productGrandParent', 'product_'.$this->id, 'product_'.$this->id.'_parents',
            'product_'.$this->id.'_grandParents',
        ])
            ->remember($key, config('constants.CACHE_60'), function () {
                $parentsArray = $this->getAllParents();
                if ($parentsArray->isEmpty()) {
                    return false;
                }

                return $parentsArray->last();

            });
    }

    public function getAllParents(): Collection
    {
        $myProduct = $this;
        $key = 'product:getAllParents:'.$myProduct->cacheKey();

        return Cache::tags(['product', 'parentProduct', 'product_'.$this->id, 'product_'.$this->id.'_parents'])
            ->remember($key, config('constants.CACHE_600'), function () use ($myProduct) {
                $parents = collect();
                while ($myProduct->hasParents()) {
                    $myparent = $myProduct->parents->first();
                    $parents->push($myparent);
                    $myProduct = $myparent;
                }

                return $parents;
            });
    }

    /** Determines whether this product has parent or not
     *
     * @param  int  $depth
     */
    public function hasParents($depth = 1): bool
    {
        $key = 'product:hasParents:'.$depth.'-'.$this->cacheKey();

        return Cache::tags(['product', 'parentProduct', 'product_'.$this->id, 'product_'.$this->id.'_parents'])
            ->remember($key, config('constants.CACHE_60'), function () use ($depth) {
                $counter = 1;
                $myParent = $this->parents->first();
                while (isset($myParent)) {
                    if ($counter >= $depth) {
                        break;
                    }
                    $myParent = $myParent->parents->first();
                    $counter++;
                }
                if (! isset($myParent) || $counter != $depth) {
                    return false;
                }

                return true;
            });
    }

    public function getGrandParentAttribute()
    {
        $key = 'product:GrandParent:'.$this->cacheKey();

        return Cache::tags([
            'product', 'parentProduct', 'productGrandParent', 'product_'.$this->id, 'product_'.$this->id.'_parents',
            'product_'.$this->id.'_grandParents',
        ])
            ->remember($key, config('constants.CACHE_60'), function () {
                return $this->grand;
            });
    }

    /**
     * Get the Grand parent record associated with the product.
     */
    public function grand()
    {
        return $this->hasOne(Product::class, 'id', 'grand_id');
    }

    public function complimentaryproducts()
    {
        return $this->belongsToMany(Product::class, 'complimentaryproduct_product', 'product_id',
            'complimentary_id')->withPivot('is_dependent')->withTimestamps();
    }

    public function complimentedproducts()
    {
        return $this->belongsToMany(Product::class, 'complimentaryproduct_product', 'complimentary_id',
            'product_id')->withPivot('is_dependent')->withTimestamps();
    }

    public function liveConductors()
    {
        return $this->hasMany(Conductor::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Other
    |--------------------------------------------------------------------------
    */

    /**
     * Checks whether the product is in stock or not .
     */
    public function isInStock(): bool
    {
        $isInStock = false;
        if ($this->isLimited()) {
            if ($this->amount > 0) {
                $isInStock = true;
            }
        } else {
            $isInStock = true;
        }

        return $isInStock;
    }

    /**Determines whether ths product's amount is limited or not
     *
     * @return bool
     */
    public function isLimited(): bool
    {
        return isset($this->amount);
    }

    public function getTypeAttribute()
    {
        return [
            'id' => $this->producttype->id,
            'type' => $this->producttype->name,
            'hint' => $this->producttype->displayName,
        ];
    }

    public function getUrlAttribute($value): string
    {
        return appUrlRoute('product.show', $this->id);
    }

    public function getApiUrlAttribute($value): array
    {
        return [
            'v2' => route('api.v2.product.show', $this),
        ];
    }

    public function getApiUrlV1Attribute()
    {
        return route('api.v2.product.show', $this);
    }

    public function getApiUrlV2Attribute($value)
    {
        return appUrlRoute('api.v2.product.show', $this->id);
    }

    public function getGiftAttribute(): ProductCollection
    {
        return $this->getGifts();
    }

    public function getGifts(): ProductCollection
    {
        $key = 'product:gifts:'.$this->cacheKey();

        return Cache::tags(['product', 'gift', 'product_'.$this->id, 'product_'.$this->id.'_gifts'])
            ->remember($key, config('constants.CACHE_60'), function () {
                return $this->gifts->merge(optional($this->grandParent)->gift ?? collect());
            });
    }

    public function validateProduct()
    {
        if (! $this->enable) {
            return 'محصول مورد نظر غیر فعال است';
        }

        if (isset($this->amount) && $this->amount >= 0) {
            return 'محصول مورد نظر تمام شده است';
        }

        if (isset($this->validSince) && Carbon::now() < $this->validSince) {
            return 'تاریخ شروع سفارش محصول مورد نظر آغاز نشده است';
        }

        if (isset($this->validUntil) && Carbon::now() > $this->validUntil) {
            return 'تاریخ سفارش محصول مورد نظر  به پایان رسیده است';
        }

        return '';
    }

    /**
     * Get the index name for the model.
     *
     * @return string
     */
    public function searchableAs()
    {
        return 'products_index';
    }

    public function shouldBeSearchable()
    {
        return $this->isPublished();
    }

    private function isPublished()
    {
        return $this->isActive();
    }

    /**
     * Checks whether the product is 3a exam or not .
     */
    public function is3aExam(): bool
    {
        return
            $this->seller == config('constants.SOALAA_SELLER') &&
            $this->financial_category_id == FinancialCategory::_3A &&
            (
                $this->attributeset_id == config('constants.AZMOUN_ATTRIBUTE_SET') ||
                $this->attributeset_id == config('constants.JOZVE_ATTRIBUTE_SET') ||
                $this->attributeset_id == config('constants.HAMAYESH_ATTRIBUTE_SET')
            );
    }

    /**Determines whether this product is available for purchase or not
     *
     * @return bool
     */
    public function isEnableToPurchase(): bool
    {
        $key = 'product:isEnableToPurchase:'.$this->cacheKey();

        return Cache::tags(['product', 'product_'.$this->id])
            ->remember($key, config('constants.CACHE_600'), function () {

                //ToDo : should be removed in future
                if (in_array($this->id, [
                    self::CUSTOM_DONATE_PRODUCT,
                    self::DONATE_PRODUCT_5_HEZAR,
                ])) {
                    return true;
                }
                $grandParent = $this->grandParent;
                if (isset($grandParent) && ! $grandParent->enable) {
                    return false;
                }

                if ($this->hasParents() && ! $this->parents()->first()->enable) {
                    return false;
                }

                if (! $this->enable) {
                    return false;
                }

                return true;
            });
    }

    public function parents()
    {
        return $this->belongsToMany(Product::class, 'childproduct_parentproduct', 'child_id', 'parent_id')
            ->withPivot('isDefault', 'control_id',
                'description'); //                    ->with('parents')
    }

    public function isSimple(): bool
    {
        return $this->producttype_id == config('constants.PRODUCT_TYPE_SIMPLE');
    }

    public function isConfigurable(): bool
    {
        return $this->producttype_id == config('constants.PRODUCT_TYPE_CONFIGURABLE');
    }

    public function isSelectable(): bool
    {
        return $this->producttype_id == config('constants.PRODUCT_TYPE_SELECTABLE');
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();
        $keys = [
            'basePrice',
            'discount',
            'discount',
            'isFree',
            'amount',
            'image',
            'file',
            'introVideo',
            'enable',
            'order',
            'producttype_id',
            'attributeset_id',
            'redirectUrl',
            'tags',
            'page_view',
            'created_at',
            'updated_at',
            'validSince',
            'validUntil',
            'slogan',
            'recommender_contents',
            'sample_contents',
        ];
        foreach ($keys as $key) {
            unset($array[$key]);
        }
        if (! $this->isActive() || isset($this->redirectUrl)) {
            foreach ($array as $key => $value) {

                $array[$key] = null;
            }
        }

        return $array;
    }

    /**
     * Get the value used to index the model.
     *
     * @return mixed
     */
    public function getScoutKey()
    {
        return $this->id;
    }

    /**
     * @throws Exception
     */
    public function getAddItems(): Collection
    {
        // TODO: Implement getAddItems() method.
        throw new Exception('product Advertisable should be impediment');
    }

    public function productFileTypesOrder(): collection
    {
        $defaultProductFileOrders = collect();
        $productFileTypes = Productfiletype::pluck('name', 'id')
            ->toArray();

        foreach ($productFileTypes as $key => $productFileType) {
            $lastProductFile = $this->validProductfiles($productFileType, 0)
                ->get()
                ->first();
            if (isset($lastProductFile)) {
                $lastOrderNumber = $lastProductFile->order + 1;
                $defaultProductFileOrders->push([
                    'fileTypeId' => $key,
                    'lastOrder' => $lastOrderNumber,
                ]);
            } else {
                $defaultProductFileOrders->push([
                    'fileTypeId' => $key,
                    'lastOrder' => 1,
                ]);
            }
        }

        return $defaultProductFileOrders;
    }

    /** Equalizing this product's children to him
     */
    public function equalizingChildrenPrice(): void
    {
        if (! $this->hasChildren()) {
            return;
        }
        foreach ($this->children as $child) {
            $child->basePrice = $this->basePrice;
            $child->update();
        }
    }

    /**Determines whether this product has any children or not
     *
     * @param  int  $depth
     *
     * @return bool
     */
    public function hasChildren($depth = 1): bool
    {
        $key = 'product:hasChildren:'.$depth.$this->cacheKey();

        return Cache::tags(['product', 'childProduct', 'product_'.$this->id, 'product_'.$this->id.'_children'])
            ->remember($key, config('constants.CACHE_600'), function () use ($depth) {
                $counter = 1;
                $myChildren = $this->children->first();
                while (isset($myChildren)) {
                    if ($counter >= $depth) {
                        break;
                    }
                    $myChildren = $myChildren->children->first();
                    $counter++;
                }
                if (! isset($myChildren) || $counter != $depth) {
                    return false;
                }

                return true;
            });
    }

    public function makeProductLink(): string
    {
        $key = 'product:makeProductLink:'.$this->cacheKey();

        return Cache::tags(['product', 'productLnk', 'product_'.$this->id, 'product_'.$this->id.'_link'])
            ->remember($key, config('constants.CACHE_60'), function () {
                $link = '';
                $grandParent = $this->grandParent;
                if (isset($grandParent)) {
                    if ($grandParent->enable) {
                        $link = action('Web\ProductController@show', $this->grandParent);
                    }
                } else {
                    if ($this->enable) {
                        $link = action('Web\ProductController@show', $this);
                    }
                }

                return $link;
            });
    }

    /** Makes an array of files with specific type
     *
     * @param  string  $type
     */
    public function makeFileArray($type): array
    {
        $filesArray = [];
        $productsFiles = $this->validProductfiles($type)
            ->get();
        foreach ($productsFiles as $productfile) {
            $filesArray[] = [
                'file' => $productfile->file,
                'name' => $productfile->name,
                'product_id' => $productfile->product_id,
            ];
        }

        return $filesArray;
    }

    /**
     * Checks whether this product is free or not
     */
    public function isFree(): bool
    {
        return ($this->isFree) ? true : false;
    }

    /**
     * @return Collection
     */
    public function getProductChain()
    {
        $productChain = collect();
        $parents = $this->getAllParents();
        $productChain = $productChain->merge($parents);

        $children = $this->getAllChildren();
        $productChain = $productChain->merge($children);

        return $productChain;
    }

    /**
     * Gets a collection containing all of product children
     */
    public function getAllChildren(bool $enableChildren = false, bool $loadSets = false): Collection
    {
        $onlyEnable = $enableChildren ? '1' : '0';
        $key = 'product:makeChildrenArray:onlyEnable:'.$onlyEnable.':'.$this->cacheKey();

        return Cache::tags(['product', 'childProduct', 'product_'.$this->id, 'product_'.$this->id.'_children'])
            ->remember($key, config('constants.CACHE_600'), function () use ($enableChildren, $loadSets) {
                $children = collect();
                if (! $this->hasChildren()) {

                    return $children;
                }
                $thisChildren = $this->children;
                if ($loadSets) {
                    $thisChildren->load('sets');
                }
                $thisChildren = $enableChildren ? $thisChildren->where('enable', 1) : $thisChildren;
                $children = $children->merge($thisChildren);
                foreach ($thisChildren as $child) {
                    $children = $children->merge($child->getAllChildren());
                }

                return $children;
            });
    }

    /**
     * Obtains product's discount percentage
     *
     * @return float|int
     */
    public function obtainDiscount()
    {
        $discount = $this->getFinalDiscountValue();

        return $discount / 100;
    }

    /**
     * Obtains discount value base on product parents
     *
     * @return float
     */
    public function getFinalDiscountValue()
    {
        $key = 'product:getFinalDiscountValue:'.$this->cacheKey();
        $tags = ['product', 'discount', 'product_'.$this->id, 'product_'.$this->id.'_discount'];

        return Cache::tags($tags)
            ->remember($key, config('constants.CACHE_10'), function () {
                return $this->determineDiscount('discount');
            });
    }

    public function determineDiscount(string $discountFieldName)
    {
        $discount = 0;
        if (! $this->isRoot()) {
            $grandParent = $this->grandParent;
            $grandParentProductType = $grandParent->producttype_id;
            if ($grandParentProductType == config('constants.PRODUCT_TYPE_CONFIGURABLE')) {
                if ($this->$discountFieldName > 0) {
                    $discount += $this->$discountFieldName;
                } else {
                    if ($grandParent->$discountFieldName > 0) {
                        $discount += $this->parents->first()->discount;
                    }
                }
            } else {
                if ($grandParentProductType == config('constants.PRODUCT_TYPE_SELECTABLE')) {
                    $discount = $this->$discountFieldName;
                }
            }
        } else {
            $discount = $this->$discountFieldName;
        }

        return $discount;
    }

    /**
     * Obtains product's discount percentage
     *
     * @return float|int
     */
    public function obtainInstalmentallyDiscount()
    {
        $discount = $this->getFinalInstalmentallyDiscountValue();

        return $discount / 100;
    }

    /**
     * Obtains discount value base on product parents
     *
     * @return float
     */
    public function getFinalInstalmentallyDiscountValue()
    {
        $key = 'product:getFinalInstalmentallyDiscountValue:'.$this->cacheKey();
        $tags = ['product', 'discount', 'product_'.$this->id, 'product_'.$this->id.'_discount'];

        return Cache::tags($tags)
            ->remember($key, config('constants.CACHE_10'), function () {
                return $this->determineDiscount('discount_in_instalment_purchase');
            });
    }

    /**
     * Obtains product's discount percentage
     *
     * @return float|int
     */
    public function obtainDiscountForInstalmentPurchase()
    {
        $discount = $this->getFinalDiscountValueForInstalmentPurchase();

        return $discount / 100;
    }

    /**
     * Obtains discount value base on product parents
     *
     * @return float
     */
    public function getFinalDiscountValueForInstalmentPurchase()
    {
        $key = 'product:getFinalDiscountValueForInstalmentPurchase:'.$this->cacheKey();

        return Cache::tags(['product', 'discount', 'product_'.$this->id, 'product_'.$this->id.'_discount'])
            ->remember($key, 0, function () {
                return $this->determineDiscount('discount_in_instalment_purchase');
            });
    }

    /**
     * Obtains product's discount amount in cash
     */
    public function obtainDiscountAmount(): int
    {
        $key = 'product:obtainDiscountAmount:'.$this->cacheKey();

        if (is_null($this->discount_ammount_cache)) {
            $this->discount_ammount_cache = Cache::tags([
                'product', 'discount', 'product_'.$this->id, 'product_'.$this->id.'_discount',
            ])
                ->remember($key, config('constants.CACHE_10'), function () {
                    $discountAmount = 0;
                    if ($this->isRoot()) {
                        return $discountAmount;
                    }
                    $grandParent = $this->grandParent;
                    $grandParentProductType = $grandParent->producttype_id;
                    if (! ($grandParentProductType == config('constants.PRODUCT_TYPE_SELECTABLE') && $this->basePrice == 0)) {
                        return $discountAmount;
                    }
                    $children = $this->children;
                    foreach ($children as $child) {
                        $discountAmount += ($child->discount / 100) * $child->basePrice;
                    }

                    return $discountAmount;
                });
        }

        return $this->discount_ammount_cache;
    }

    /**
     * Disables the product
     */
    public function setDisable(): void
    {
        $this->enable = 0;
    }

    /**
     * Enables the product
     */
    public function setEnable(): void
    {
        $this->enable = 1;
    }

    /** edit amount of product
     *
     */
    public function decreaseProductAmountWithValue(int $value): void
    {
        if (! (isset($this->amount) && $this->amount > 0)) {
            return;
        }
        if ($this->amount < $value) {
            $this->amount = 0;
        } else {
            $this->amount -= $value;
        }
        $this->update();
    }

    public function getActiveAttribute()
    {
        if ($this->validSince == null) {
            return true;
        }
        $now = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now())->timezone('Asia/Tehran');
        if (Carbon::parse($this->validSince)->lte($now) && Carbon::parse($this->validUntil)->gte($now)) {
            return true;
        }

        return false;
    }

    public function getSetsAttribute()
    {
        $key = 'product:sets:'.$this->cacheKey();

        return Cache::tags(['product', 'set', 'product_'.$this->id, 'product_'.$this->id.'_sets'])
            ->remember($key, config('constants.CACHE_600'), function () {
                /** @var SetCollection $sets */
                $sets = $this->sets()->active()
                    ->get();

                return $sets;
            });
    }

    /**
     * The products that belong to the set.
     */
    public function sets()
    {
        return $this->belongsToMany(Contentset::class)
            ->withPivot([
                'order',
                'isInstallmentally',
            ])
            ->withTimestamps()
            ->orderBy('order');
    }

    public function getActiveChildrenAttribute()
    {
        return $this->children->filterEnable();
    }

    public function getEnableAttribute($value)
    {
        //ToDo
        //        if (hasAuthenticatedUserPermission(config('constants.SHOW_PRODUCT_ACCESS')))
        return $value;
    }

    public function getAttributeSetAttribute()
    {
        $product = $this;
        $key = 'product:attributeset:'.$product->cacheKey();

        return Cache::tags(['product', 'attributeset', 'product_'.$this->id, 'product_'.$this->id.'_attributesets'])
            ->remember($key, config('constants.CACHE_600'), function () use ($product) {
                //ToDo
                //                if (hasAuthenticatedUserPermission(config('constants.SHOW_PRODUCT_ACCESS')))
                return optional($product->attributeset()
                    ->first())->setVisible([
                        'name',
                        'description',
                        'order',
                    ]);

            });
    }

    public function attributeset()
    {
        return $this->belongsTo(Attributeset::class);
    }

    public function getJalaliValidSinceAttribute()
    {
        $product = $this;
        $key = 'product:jalaliValidSince:'.$product->cacheKey();

        return Cache::tags([
            'product', 'jalaliValidSince', 'product_'.$this->id, 'product_'.$this->id.'_jalaliValidSince',
        ])
            ->remember($key, config('constants.CACHE_600'), function () use ($product) {
                if (hasAuthenticatedUserPermission(config('constants.SHOW_PRODUCT_ACCESS'))) {
                    return $this->convertDate($product->validSince, 'toJalali');
                }

                return null;
            });
    }

    public function getJalaliValidUntilAttribute()
    {
        $product = $this;
        $key = 'product:jalaliValidUntil:'.$product->cacheKey();

        return Cache::tags([
            'product', 'jalaliValidUntil', 'product_'.$this->id, 'product_'.$this->id.'_jalaliValidUntil',
        ])
            ->remember($key, config('constants.CACHE_600'), function () use ($product) {
                if (hasAuthenticatedUserPermission(config('constants.SHOW_PRODUCT_ACCESS'))) {
                    return $this->convertDate($product->validUntil, 'toJalali');
                }

                return null;
            });
    }

    public function getJalaliCreatedAtAttribute()
    {
        $product = $this;
        $key = 'product:jalaliCreatedAt:'.$product->cacheKey();

        return Cache::tags([
            'product', 'jalaliCreatedAt', 'product_'.$this->id, 'product_'.$this->id.'_jalaliCreatedAt',
        ])
            ->remember($key, config('constants.CACHE_600'), function () use ($product) {
                if (hasAuthenticatedUserPermission(config('constants.SHOW_PRODUCT_ACCESS'))) {
                    return $this->convertDate($product->created_at, 'toJalali');
                }

                return null;
            });
    }

    public function getJalaliUpdatedAtAttribute()
    {
        $product = $this;
        $key = 'product:jalaliUpdatedAt:'.$product->cacheKey();

        return Cache::tags([
            'product', 'jalaliUpdatedAt', 'product_'.$this->id, 'product_'.$this->id.'_jalaliUpdatedAt',
        ])
            ->remember($key, config('constants.CACHE_600'), function () use ($product) {
                return $this->convertDate($product->updated_at, 'toJalali');
            });
    }

    public function getBonPlusAttribute()
    {
        if (hasAuthenticatedUserPermission(config('constants.SHOW_PRODUCT_ACCESS'))) {
            return $this->calculateBonPlus(Bon::ALAA_BON);
        }

        return null;
    }

    public function getBonDiscountAttribute()
    {
        if (hasAuthenticatedUserPermission(config('constants.SHOW_PRODUCT_ACCESS'))) {
            return $this->obtainBonDiscount(config('constants.BON1'));
        }

        return null;
    }

    public function getChildrenAttribute()
    {
        $product = $this;
        $key = 'product:children:'.$product->cacheKey();

        return Cache::tags(['product', 'childProduct', 'product_'.$this->id, 'product_'.$this->id.'_children'])
            ->remember($key, config('constants.CACHE_600'), function () {
                return $this->children()
                    ->get();
            });

    }

    public function children()
    {
        return $this->belongsToMany(Product::class, 'childproduct_parentproduct', 'parent_id', 'child_id')
            ->withPivot('isDefault', 'control_id', 'description', 'parent_id')
            ->with('children');
    }

    public function getInstalmentsPriceAttribute()
    {
        if ($this->has_instalment_option && isset($this->instalments)) {
            return TransactionsSerivce::calculateInstalments([
                ['cost' => $this->basePrice, 'instalmentQty' => $this->instalments],
            ])->pluck('cost');
        }

        return null;
    }

    public function getIntroVideoAttribute()
    {
        $intro = $this->intro_videos;
        if (is_null($intro)) {
            return null;
        }

        $intro = json_decode($intro);

        $intro_videos = Arr::get($intro, '0');
        if (! isset($intro_videos) || ! isset($intro_videos->video)) {
            return null;
        }

        $videos = $intro_videos->video;

        $firstQuality = Arr::get($videos, 0);
        if (! isset($firstQuality) || ! isset($firstQuality->fileName)) {
            return null;
        }

        return Uploader::url($firstQuality->disk, $firstQuality->fileName, false);
    }

    public function getInputVideoShow()
    {

        return json_decode($this->intro_videos)[0]->video[0]->fileName ?? null;
    }

    public function getIntroVideoThumbnailAttribute()
    {
        $intro = $this->intro_videos;
        if (is_null($intro)) {
            return null;
        }

        $intro = json_decode($intro);

        $intro_videos = Arr::get($intro, '0');
        if (! isset($intro_videos) || ! isset($intro_videos->thumbnail)) {
            return null;
        }

        $thumbnail = $intro_videos->thumbnail;

        if (! isset($thumbnail) || ! isset($thumbnail->fileName)) {
            return null;
        }

        return Uploader::url($thumbnail->disk, $thumbnail->fileName, false);
    }

    public function getCategoryNameAttribute()
    {
        if (in_array($this->category, ['همایش/آرش', 'همایش/تفتان', 'همایش/گدار', 'قدیم'])) {
            return 'همایش';
        }

        return $this->category;
    }

    public function getIsFavoredAttribute()
    {
        $authUser = auth()->user();
        if (! isset($authUser)) {
            return false;
        }

        return Cache::tags([
            'favorite', 'user', 'user_'.$authUser->id, 'user_'.$authUser->id.'_favorites',
            'user_'.$authUser->id.'_favoriteProducts',
        ])
            ->remember('user:'.$authUser->id.':hasFavored:product:'.$this->cacheKey(), config('constants.CACHE_10'),
                function () use ($authUser) {
                    return $authUser->hasFavoredProduct($this);
                });
    }

    // return author of product

    public function drafts()
    {
        return $this->morphMany(Draft::class, 'draftable');
    }

    public function getIsPurchaseBtnShowableAttribute()
    {
        return ! in_array($this->id, [self::RIAZI_4K, self::TAJROBI_4K, self::ENSANI_4K]);
    }

    // return major of product

    public function getAuthorAttribute(): array
    {
        $attribute = Cache::tags(['attribute', 'attribute_author'])->remember('attribute:author',
            config('constants.CACHE_600'), function () {
                return Attribute::where('name', 'teacher')->first();
            });
        if (isset($attribute)) {
            return Cache::tags(['product', 'product_'.$this->id])->remember('product:getAuthorAttribute:'.$this->id,
                config('constants.CACHE_600'), function () use ($attribute) {
                    return $this->attributevalues->where('attribute_id', $attribute->id)->pluck('name')->toArray();
                });
        }

        return [];
    }

    public function getMajorsAttribute(): array
    {
        if (! is_null($this->major_cache)) {
            return $this->major_cache;
        }
        $attribute = Cache::tags(['attribute', 'attribute_major'])->remember('attribute:major',
            config('constants.CACHE_600'), function () {
                return Attribute::where('name', 'major')->first();
            });

        if (! isset($attribute)) {
            return [];
        }

        $this->major_cache = Cache::tags([
            'product', 'product_'.$this->id,
        ])->remember('product:getMajorsAttribute:'.$this->id, config('constants.CACHE_600'),
            function () use ($attribute) {
                return $this->attributevalues->where('attribute_id', $attribute->id)->pluck('name')->toArray();
            });

        return $this->major_cache;
    }

    // return hours of product

    public function getHoursAttribute(): string
    {
        if (! is_null($this->hours_cache)) {
            return $this->hours_cache;
        }
        $attribute = Cache::tags(['attribute', 'attribute_hours'])->remember('attribute:hours',
            config('constants.CACHE_600'), function () {
                return Attribute::where('name', 'duration')->first();
            });
        $this->hours_cache = $this->attributevalues->where('attribute_id',
            $attribute->id)->pluck('name')->toArray()[0] ?? '';

        return $this->hours_cache;
    }

    public function saveWithoutEvents(array $options = [])
    {
        return static::withoutEvents(function () use ($options) {
            return $this->save($options);
        });
    }

    public function getImageAttribute()
    {
        return $this->getRawOriginal('image');
    }

    public function setDisk()
    {
        return $this->disk = config('disks.PRODUCT_IMAGE_MINIO');
    }

    public function getIsPurchasedAttribute(): bool
    {
        $authUser = auth()->user();
        if (! isset($authUser)) {
            return false;
        }
        $orderProduct = $authUser->getPurchasedOrderproduct($this->id);
        if (! is_null($orderProduct)) {
            return true;
        } else {
            return false;
        }
    }

    public function getIsOrderedAttribute(): bool
    {
        $authUser = auth()->user();
        if (! isset($authUser)) {
            return false;
        }

        $productIds = [$this->id];
        $orderProduct = $authUser->getOrderedOrderproduct($productIds);
        if (! is_null($orderProduct)) {
            return true;
        } else {
            return false;
        }
    }
}
