<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ContentIncome extends Model
{
    protected $table = 'content_income';

    protected $fillable = [
        'content_id',
        'transaction_id',
        'orderproduct_id',
        'tmp_gateway',
        'transaction_completed_at',
        'share_cost_gw',
        'share_cost'
    ];

    public static function unZeroCost(): Builder
    {
        return self::query()->where(ContentIncome::getAuthorizedShareCostIndex(), '<>', 0);
    }

    public static function getAuthorizedShareCostIndex(): string
    {
        $shareCostIndex = 'share_cost';
        if (optional(auth()->user())->hasRole(config('constants.ROLE_AUDITOR'))) {
            $shareCostIndex = 'share_cost_gw';
        }

        return $shareCostIndex;
    }

    public function orderproduct()
    {
        return $this->belongsTo(Orderproduct::class);
    }

    public function scopeAccountant($query)
    {
        return $query->where('tmp_gateway', 'درگاه بانک ملت');
    }
}
