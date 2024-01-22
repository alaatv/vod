<?php

namespace App\Providers;

use App\Models\Afterloginformcontrol;
use App\Models\Attribute;
use App\Models\Attributegroup;
use App\Models\Attributeset;
use App\Models\Attributevalue;
use App\Models\Category;
use App\Models\City;
use App\Models\Consultation;
use App\Models\Contact;
use App\Models\Content;
use App\Models\Contentset;
use App\Models\Coupon;
use App\Models\Descriptionwithperiod;
use App\Models\Employeetimesheet;
use App\Models\Eventresult;
use App\Models\Faq;
use App\Models\LiveDescription;
use App\Models\Map;
use App\Models\MapDetail;
use App\Models\MapDetailType;
use App\Models\Mbtianswer;
use App\Models\Order;
use App\Models\Orderproduct;
use App\Models\Phone;
use App\Models\Product;
use App\Models\Productfile;
use App\Models\Productphoto;
use App\Models\Productvoucher;
use App\Models\Section;
use App\Models\Slideshow;
use App\Models\Source;
use App\Models\Studyevent;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\Timepoint;
use App\Models\User;
use App\Models\Userbon;
use App\Models\Userupload;
use App\Models\Wallet;
use App\Models\Websitesetting;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        //
        parent::boot();
        $this->modelBinding();
        RateLimiter::for('api', function (Request $request) {
            //TODO:// ratelimiter base user and route Documentation (https://laravel.com/docs/10.x/routing#rate-limiting)
            return Limit::perMinute(1000)
                ->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('api')
                ->prefix('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/crm.php'));

            Route::middleware('api')
                ->prefix('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/products.php'));

            Route::middleware('api')
                ->prefix('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/orders.php'));

            Route::middleware('api')
                ->prefix('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/admin.php'));
        });
    }

    private function modelBinding()
    {
        Route::bind('user', function ($value) {
            return User::find($value) ?? abort(Response::HTTP_NOT_FOUND);
        });
//        Route::bind('assignment', function ($value) {
//            return Assignment::find($value)
//                ?? abort(Response::HTTP_NOT_FOUND);
//        });
        Route::bind('consultation', function ($value) {
            return Consultation::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });
        Route::bind('order', function ($value) {
            return Order::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });
        Route::bind('product', function ($value) {
            return Product::find($value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });
        Route::bind('orderproduct', function ($value) {
            return Orderproduct::find($value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });
        Route::bind('attributevalue', function ($value) {
            return Attributevalue::find($value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });
//        Route::bind('permission', function ($value) {
//            return Permission::find($value)
//                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
//        });
//        Route::bind('role', function ($value) {
//            return Role::find($value)
//                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
//        });
        Route::bind('coupon', function ($value) {
            return Coupon::find($value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });
        Route::bind('userupload', function ($value) {
            return Userupload::find($value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });
        Route::bind('attribute', function ($value) {
            return Attribute::find($value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });
        Route::bind('attributeset', function ($value) {
            return Attributeset::find($value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });
        Route::bind('attributegroup', function ($value) {
            return Attributegroup::find($value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });
        Route::bind('userbon', function ($value) {
            return Userbon::find($value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });
        Route::bind('mbtianswer', function ($value) {
            return Mbtianswer::find($value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });
        Route::bind('contact', function ($value) {
            return Contact::find($value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });
        Route::bind('phone', function ($value) {
            return Phone::find($value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });
        Route::bind('afterloginformcontrol', function ($value) {
            return Afterloginformcontrol::find($value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });

        Route::bind('slideshow', function ($value) {
            return Slideshow::find($value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });
        Route::bind('websiteSetting', function ($value) {
            return Websitesetting::find($value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });
        Route::bind('productfile', function ($value) {
            return Productfile::find($value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });
        Route::bind('city', function ($value) {
            return City::find($value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });
        Route::bind('c', function ($value) {
            return Content::find($value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });

        Route::bind('set', function ($value) {
            return Contentset::find($value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });

        Route::bind('employeetimesheet', function ($value) {
            return Employeetimesheet::find($value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });
        Route::bind('productphoto', function ($value) {
            return Productphoto::find($value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });
        Route::bind('wallet', function ($value) {
            return Wallet::find($value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });
        Route::bind('eventresult', function ($value) {
            return Eventresult::find($value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });

        Route::bind('livedescription', function ($value) {
            return LiveDescription::find($value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });

        Route::bind('section', function ($value) {
            return Section::find($value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });

        Route::bind('cat', function ($value) {
            return Category::find($value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });

        Route::bind('periodDescription', function ($value) {
            return Descriptionwithperiod::find($value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });

        Route::bind('faq', function ($value) {
            return Faq::find($value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });

        Route::bind('source', function ($value) {
            return Source::find($value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });

        Route::bind('voucher', function ($value) {
            return Productvoucher::find($value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });

        Route::bind('timepoint', function ($value) {
            return Timepoint::find($value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });

        Route::bind('ticket', function ($value) {
            return Ticket::find($value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });

        Route::bind('ticketMessage', function ($value) {
            return TicketMessage::find($value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });

        Route::bind('map', function ($value) {
            return Map::query()->where('id', $value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });

        Route::bind('mapDetail', function ($value) {
            return MapDetail::query()->where('id', $value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });

        Route::bind('mapDetailType', function ($value) {
            return MapDetailType::query()->where('id', $value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });

        Route::bind('studyevent', function ($value) {
            return Studyevent::find($value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });

        Route::bind('faq', function ($value) {
            return Faq::find($value)
                ?? abort(ResponseAlias::HTTP_NOT_FOUND);
        });
    }
}
