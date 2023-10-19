<?php

namespace App\Models;


class Mbtianswer extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'answers',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getUserOrderInfo($output)
    {
        $ordooOrder = $this->user->orders()
            ->whereHas('orderproducts', function ($q) {
                $q->whereIn('product_id', Product::whereHas('parents', function ($q) {
                    $q->whereIn('parent_id', [
                        1,
                        13,
                    ]);
                })
                    ->pluck('id'));
            })
            ->whereIn('orderstatus_id', [config('constants.ORDER_STATUS_CLOSED')])
            ->get();

        switch ($output) {
            case 'productName':
                if ($ordooOrder->isEmpty()) {
                    return '';
                }
                return $ordooOrder->first()->orderproducts->first()->product->name;
            case 'orderStatus':
                if ($ordooOrder->isEmpty()) {
                    return '';
                }
                return $ordooOrder->first()->orderstatus->displayName;
            default:
                break;
        }
    }
}
