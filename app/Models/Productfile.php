<?php

namespace App\Models;

use App\Traits\DateTrait;
use App\Traits\Helper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class Productfile extends BaseModel
{
    use DateTrait;
    use Helper;

    /**
     * @var array
     */
    protected $fillable = [
        'product_id',
        'productfiletype_id',
        'file',
        'name',
        'description',
        'order',
        'enable',
        'validSince',
        'cloudFile',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function set()
    {
        return $this->belongsTo(Contentset::class, 'contentset_id');
    }

    public function productfiletype()
    {
        return $this->belongsTo(Productfiletype::class);
    }


    /**
     * Scope a query to only include enable(or disable) Products.
     *
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeEnable($query)
    {
        return $query->where('enable', '=', 1);
    }

    public function scopeValid($query)
    {
        return $query->where(function ($q) {
            $q->where('validSince', '<', Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now('Asia/Tehran')))
                ->orwhereNull('validSince');
        });
    }

    /**
     * @param $fileName
     * @param $productId
     *
     * @return string
     */
    public function getExternalLinkForProductFileByFileName($fileName, $productId): string
    {
        $cloudFile = Productfile::where('file', $fileName)
            ->whereIn('product_id', $productId)
            ->get()
            ->first()->cloudFile;
        //TODO: verify "$productFileLink = "http://".env("SFTP_HOST" , "").":8090/". $cloudFile;"
        $productFileLink = Productfile.phpconfig('constants.DOWNLOAD_SERVER_PROTOCOL',
                'https://').config('constants.PAID_SERVER_NAME').$cloudFile;
        $unixTime = Carbon::today()
            ->addDays(2)->timestamp;
        $userIP = request()->ip();
        //TODO: fix diffrent Ip
        $ipArray = explode('.', $userIP);
        $ipArray[3] = 0;
        $userIP = implode('.', $ipArray);

        $linkHash = generateSecurePathHash($unixTime, $userIP, 'TakhteKhak', $cloudFile);
        $externalLink = $productFileLink.'?md5='.$linkHash.'&expires='.$unixTime;
        return $externalLink;
    }
}
