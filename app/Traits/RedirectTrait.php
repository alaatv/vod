<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2019-02-08
 * Time: 18:41
 */

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

trait RedirectTrait
{
    protected function redirectTo(Request $request = null)
    {
        $redirectTo = url('/');
        $targetUrl = redirect()
            ->intended()
            ->getTargetUrl();
        if (strcmp($targetUrl, $redirectTo) == 0) {
            // Indicates a strange situation when target url is the home page despite
            // the fact that there is a probability that user must be redirected to another page except home page

            if (strcmp(URL::previous(), route('login')) != 0) // User first had opened a page and then went to login
            {
                $redirectTo = URL::previous();
            }
        } else {
            $redirectTo = $targetUrl;
        }

        return $redirectTo;
    }
}
