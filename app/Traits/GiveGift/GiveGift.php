<?php


namespace App\Traits\GiveGift;


use App\Models\Major;
use App\Models\Product;

interface GiveGift
{
    public const REYAZI = Major::RIYAZI;
    public const TAJROBI = Major::TAJROBI;
    public const ENSANI = Major::ENSANI;
    public const PRODUCTS = 'products';
    public const GIFTS = 'gifts';
    public const ARASH = 'arash';
    public const AZMOON = 'azmoon';
    public const TITAN_ADABIYAT = 'titan_adabiyat';

    public const PLANS = [
        self::ARASH => [
            self::PRODUCTS => [Product::ARASH_PACK_RIYAZI, Product::ARASH_PACK_TAJROBI],
            self::GIFTS => [
                Product::ARASH_PACK_RIYAZI => [
                    Product::ARASH_SHIMI_1400,
                    Product::ARASH_RIYAZIYAT_RIYAZI_1400,
                    Product::ARASH_FIZIK_1400,
                    Product::ARASH_ZABAN,
                    Product::ARASH_DINI_1400,
                    Product::ARASH_ARABI
                ],
                Product::ARASH_PACK_TAJROBI => [
                    Product::ARASH_SHIMI_1400,
                    Product::ARASH_RIYAZI_TAJROBI_SABETI,
                    Product::ARASH_FIZIK_1400,
                    Product::ARASH_ZABAN,
                    Product::ARASH_DINI_1400,
                    Product::ARASH_ARABI
                ]
            ]
        ],

        self::AZMOON => [
            self::PRODUCTS => Product::ARASH_PRODUCTS_ARRAY,
            self::GIFTS => [
                Major::RIYAZI => [
                    Product::RIAZI_4K
                ],
                Major::TAJROBI => [
                    Product::TAJROBI_4K
                ],
                Major::ENSANI => [
                    Product::ENSANI_4K
                ]
            ]
        ],
    ];


}
