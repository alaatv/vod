<?php

namespace App\Providers;

use App\Models\{Afterloginformcontrol,
    Article,
    Articlecategory,
    Assignment,
    Attribute,
    Attributegroup,
    Attributeset,
    Attributevalue,
    Category,
    City,
    Consultation,
    Contact,
    Content,
    Contentset,
    Coupon,
    Descriptionwithperiod,
    Employeetimesheet,
    Eventresult,
    Faq,
    LiveDescription,
    Map,
    MapDetail,
    MapDetailType,
    Mbtianswer,
    Order,
    Orderproduct,
    Phone,
    Product,
    Productfile,
    Productphoto,
    Productvoucher,
    Section,
    Slideshow,
    Source,
    Studyevent,
    Ticket,
    TicketMessage,
    Timepoint,
    User,
    Userbon,
    Userupload,
    Wallet,
    Websitesetting};
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

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
        });
    }

    /**
     *
     */
    private function modelBinding()
    {
        Route::bind('user', function ($value) {
            return User::find($value) ?? abort(Response::HTTP_NOT_FOUND);
        });
        Route::bind('assignment', function ($value) {
            return Assignment::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });
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
                ?? abort(Response::HTTP_NOT_FOUND);
        });
        Route::bind('orderproduct', function ($value) {
            return Orderproduct::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });
        Route::bind('attributevalue', function ($value) {
            return Attributevalue::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });
        Route::bind('permission', function ($value) {
            return Permission::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });
        Route::bind('role', function ($value) {
            return Role::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });
        Route::bind('coupon', function ($value) {
            return Coupon::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });
        Route::bind('userupload', function ($value) {
            return Userupload::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });
        Route::bind('attribute', function ($value) {
            return Attribute::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });
        Route::bind('attributeset', function ($value) {
            return Attributeset::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });
        Route::bind('attributegroup', function ($value) {
            return Attributegroup::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });
        Route::bind('userbon', function ($value) {
            return Userbon::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });
        Route::bind('mbtianswer', function ($value) {
            return Mbtianswer::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });
        Route::bind('contact', function ($value) {
            return Contact::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });
        Route::bind('phone', function ($value) {
            return Phone::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });
        Route::bind('afterloginformcontrol', function ($value) {
            return Afterloginformcontrol::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });
        Route::bind('article', function ($value) {
            return Article::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });
        Route::bind('articlecategory', function ($value) {
            return Articlecategory::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });
        Route::bind('slideshow', function ($value) {
            return Slideshow::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });
        Route::bind('websiteSetting', function ($value) {
            return Websitesetting::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });
        Route::bind('productfile', function ($value) {
            return Productfile::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });
        Route::bind('city', function ($value) {
            return City::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });
        Route::bind('c', function ($value) {
            return Content::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });

        Route::bind('set', function ($value) {
            return Contentset::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });

        Route::bind('employeetimesheet', function ($value) {
            return Employeetimesheet::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });
        Route::bind('productphoto', function ($value) {
            return Productphoto::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });
        Route::bind('wallet', function ($value) {
            return Wallet::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });
        Route::bind('eventresult', function ($value) {
            return Eventresult::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });

        Route::bind('livedescription', function ($value) {
            return LiveDescription::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });

        Route::bind('section', function ($value) {
            return Section::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });

        Route::bind('cat', function ($value) {
            return Category::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });

        Route::bind('periodDescription', function ($value) {
            return Descriptionwithperiod::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });

        Route::bind('faq', function ($value) {
            return Faq::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });

        Route::bind('source', function ($value) {
            return Source::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });

        Route::bind('voucher', function ($value) {
            return Productvoucher::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });

        Route::bind('timepoint', function ($value) {
            return Timepoint::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });

        Route::bind('ticket', function ($value) {
            return Ticket::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });

        Route::bind('ticketMessage', function ($value) {
            return TicketMessage::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });

        Route::bind('map', function ($value) {
            return Map::query()->where('id', $value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });

        Route::bind('mapDetail', function ($value) {
            return MapDetail::query()->where('id', $value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });

        Route::bind('mapDetailType', function ($value) {
            return MapDetailType::query()->where('id', $value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });

        Route::bind('studyevent', function ($value) {
            return Studyevent::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });

        Route::bind('faq', function ($value) {
            return Faq::find($value)
                ?? abort(Response::HTTP_NOT_FOUND);
        });
    }
}
