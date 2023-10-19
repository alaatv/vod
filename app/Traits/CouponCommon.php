<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

trait CouponCommon
{
    private function setValidSince(FormRequest $request)
    {
        if (
            !$request->has('validSinceEnable') ||
            !$request->has('validSince') ||
            strlen($request->get('validSince')) <= 0
        ) {
            return null;
        }

        $validSince = Carbon::parse($request->get('validSince'))->format('Y-m-d');
        $sinceTime = $request->get('sinceTime');
        $sinceTime = strlen($sinceTime) > 0
            ? Carbon::parse($sinceTime)->format('H:i:s')
            : '00:00:00';

        return $validSince.' '.$sinceTime;
    }

    /**
     * @param  FormRequest  $request
     * @return string|null
     */
    private function setValidUntil(FormRequest $request)
    {
        if (
            !$request->has('validUntilEnable') ||
            !$request->has('validUntil') ||
            strlen($request->get('validUntil')) <= 0
        ) {
            return null;
        }

        $validUntil = Carbon::parse($request->get('validUntil'))->format('Y-m-d');
        $untilTime = $request->get('untilTime');
        $untilTime = strlen($untilTime) > 0
            ? Carbon::parse($untilTime)->format('H:i:s')
            : '00:00:00';

        return $validUntil.' '.$untilTime;
    }
}
