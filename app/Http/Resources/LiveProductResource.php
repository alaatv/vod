<?php

namespace App\Http\Resources;

use App\Traits\Product\Resource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * Class ProductIndex
 *
 * @mixin \App\Product
 * */
class LiveProductResource extends AlaaJsonResource
{
    use Resource;

    public function __construct(\App\Product $model)
    {
        parent::__construct($model);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     *
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof \App\Product)) {
            return [];
        }

        $redirectUrl = $this->redirect_url;

        return [
            'id' => $this->id,
            'redirect_url' => $this->when(isset($redirectUrl), Arr::get($redirectUrl, 'url')),
            'title' => $this->when(isset($this->name), $this->name),
            'price' => $this->getPrice(),
            'url' => $this->getUrl(),
            'photo' => $this->when(isset($this->photo), $this->photo),
            'redirect_code' => $this->when(isset($redirectUrl), Arr::get($redirectUrl, 'code')),
            'attributes' => new Attribute($this),
            'category' => $this->category,
            'variant' => '-',
//            'is_purchased'  => $this->is_ordered,
            'is_purchased' => 1,
            'is_live' => $this->isLive(),
        ];
    }

    private function isLive()
    {
        if ($this->liveConductors->isNotEmpty()) {
            foreach ($this->liveConductors as $liveConductor) {
                if (
                    $liveConductor->date == now()->toDateString() &&
                    $liveConductor->start_time <= Carbon::parse(now())->setTimezone('Asia/Tehran')->toTimeString() &&
                    $liveConductor->finish_time > Carbon::parse(now())->setTimezone('Asia/Tehran')->toTimeString()
                ) {
                    return 1;
                }
            }
        }
        return 0;
    }
}
