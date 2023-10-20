<?php


namespace App\Models;


use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CouponDetail
{

    public $couponName;
    public $couponCode;
    public $totalDiscount;
    public $numberOfProducts;
//    protected $couponType;
    public $detail;


    /**
     * Creates models from the raw results (it does not check the fillable attributes and so on)
     * @param  array  $rawResult
     * @return Collection
     */
    public static function hydrate($rawResult = [])
    {
        $objects = [];
        foreach ($rawResult as $result) {
            $object = new static();

            $object->setRawAttributes((array) $result);

            $objects[] = $object;
        }

        return new Collection($objects);
    }

    private function setRawAttributes(array $param)
    {
        foreach ($param as $key => $value) {
            $k = Str::camel($key);
            if ($k == 'detail') {
                $value = Str::replaceFirst("\"", '', $value);
                $value = Str::replaceLast("\"", '', $value);
                $this->$k = self::camelCase(json_decode(stripslashes($value)));

            } else {
                $this->$k = $value;
            }
        }
    }

    private static function camelCase($inputs)
    {
        $result = [];
        foreach ($inputs as $kk => $input) {
            foreach ($input as $key => $value) {
                $k = Str::camel($key);
                $result[$kk][$k] = $value;
            }
        }
        return $result;
    }
}
