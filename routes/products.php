<?php

use App\Http\Controllers\Api\AbrishamDashboardPageController;
use App\Http\Controllers\Api\AbrishamDashboardPageV2Controller;
use App\Http\Controllers\Api\Admin\BlockProductsController;
use App\Http\Controllers\Api\Admin\BlockRelationsController;
use App\Http\Controllers\Api\Admin\BlockSetsController;
use App\Http\Controllers\Api\Admin\BlockSlideshowController;
use App\Http\Controllers\Api\Admin\BlockTypesController;
use App\Http\Controllers\Api\Admin\ContentInComeController;
use App\Http\Controllers\Api\AttributevalueController;
use App\Http\Controllers\Api\BlockController;
use App\Http\Controllers\Api\BookmarkPageV2Controller;
use App\Http\Controllers\Api\ChannelController as ApiChannelController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\ContentStatusController;
use App\Http\Controllers\Api\DashboardPageV2Controller;
use App\Http\Controllers\Api\FavorableController;
use App\Http\Controllers\Api\LandingPageController;
use App\Http\Controllers\Api\Product3aExamController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductLandingController;
use App\Http\Controllers\Api\ProductphotoController;
use App\Http\Controllers\Api\RahAbrishamController;
use App\Http\Controllers\Api\SetController;
use App\Http\Controllers\Api\StudyEventController;
use App\Http\Controllers\Api\TaftanDashboardPageController;
use App\Http\Controllers\Api\TimepointController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VastContentController;
use App\Http\Controllers\Api\VastController;
use App\Http\Controllers\Api\VastSetController;
use Illuminate\Support\Facades\Route;

Route::prefix('v2')->group(function () {

    // Block routes
    Route::prefix('block')->name('api.')->group(function () {
        Route::resource('/', BlockController::class)->only(['show', 'index']);
        Route::get('{block}/products', [BlockRelationsController::class, 'products'])->name('block.products');
        Route::post('{block}/products/attach',
            [BlockRelationsController::class, 'attachProducts'])->name('block.attachProducts');
        Route::post('{block}/products/detach',
            [BlockRelationsController::class, 'detachProducts'])->name('block.detachProducts');
        Route::get('{block}/sets', [BlockRelationsController::class, 'sets'])->name('block.sets');
        Route::post('{block}/sets/attach', [BlockRelationsController::class, 'attachSets'])->name('block.attachSets');
        Route::post('{block}/sets/detach', [BlockRelationsController::class, 'detachSets'])->name('block.detachSets');
        Route::get('{block}/contents', [BlockRelationsController::class, 'contents'])->name('block.contents');
        Route::post('{block}/contents/attach',
            [BlockRelationsController::class, 'attachContents'])->name('block.attachContents');
        Route::post('{block}/contents/detach',
            [BlockRelationsController::class, 'detachContents'])->name('block.detachContents');
        Route::get('{block}/banners', [BlockRelationsController::class, 'banners'])->name('block.banners');
        Route::post('{block}/banners/attach',
            [BlockRelationsController::class, 'attachBanners'])->name('block.attachBanners');
        Route::post('{block}/banners/detach',
            [BlockRelationsController::class, 'detachBanners'])->name('block.detachBanners');
    });
    Route::get('get-blocks', [BlockController::class, 'block'])
        ->name('blocks.get');
    Route::get('blockSlideShows', [BlockSlideshowController::class, 'index'])->name('blockSlideShow.index');
    Route::get('blockTypes', [BlockTypesController::class, 'index'])->name('blockTypes.index');
    Route::get('blockSets', [BlockSetsController::class, 'index'])->name('blockSets.index');
    Route::get('blockProducts', [BlockProductsController::class, 'index'])->name('blockProducts.index');

    // Landing routes
    Route::prefix('landing')->group(function () {
        for ($i = 1; $i <= 10; $i++) {
            Route::get($i, [ProductLandingController::class, 'landing'.$i])->name('api.v2.landing.'.$i);
        }
        Route::get('17', [ProductLandingController::class, 'landing17'])->name('api.v2.landing.17');
        Route::get('13Aban', [LandingPageController::class, 'roozeDaneshAmooz'])->name('api.v2.landing.13Aban');
        Route::get('13aban', [LandingPageController::class, 'roozeDaneshAmooz2'])->name('api.v2.landing.13aban');
    });
    Route::get('anarestan',
        [ProductLandingController::class, 'anareshtan'])->name('api.landing.anarestan');

    // Routes related to 'abrisham'
    Route::group(['prefix' => 'abrisham', 'as' => 'abrisham.'], function () {
        Route::get('lessons', [ProductController::class, 'abrishamLessons'])->name('lessons');
        Route::get('flatLessons', [ProductController::class, 'flatLessons'])->name('flatLessons');
        Route::get('whereIsKarvan', [StudyEventController::class, 'whereIsKarvan'])->name('whereIsKarvan');
        Route::get('majors', [ProductController::class, 'abrishamMajors'])->name('majors');
        Route::get('/selectPlan/create',
            [RahAbrishamController::class, 'selectPlanCreate'])->name('selectPlan.create');
        Route::get('/myStudyPlan', [StudyEventController::class, 'showMyStudyEvent'])->name('myStudyPlan.get');
        Route::post('/myStudyPlan', [StudyEventController::class, 'storeMyStudyEvent'])->name('myStudyPlan.store');
        Route::get('/findStudyPlan', [StudyEventController::class, 'findStudyPlan'])->name('findStudyPlan');
        Route::get('/systemReport', [RahAbrishamController::class, 'indexSystemReport'])->name('systemReport.get');
        Route::get('lessons', [ProductController::class, 'abrishamLessons'])->name('bonyadLessons');
    });

    // Routes related to 'taftan'
    Route::group(['prefix' => 'taftan', 'as' => 'taftan.'], function () {
        Route::get('lessons', [ProductController::class, 'taftanLessons'])->name('lessons');
        Route::get('majors', [ProductController::class, 'taftanMajors'])->name('majors');
    });

    // Routes related to 'chatre Nejat'
    Route::group(['prefix' => 'chatreNejat', 'as' => 'chatreNejat.'], function () {
        Route::get('lessons', [ProductController::class, 'chatreNejatLessons'])->name('lessons');
        Route::get('majors', [ProductController::class, 'chatrNejatMajors'])->name('majors');
    });

    //Chatre nejat Dashboard
    Route::group(['prefix' => 'panel', 'as' => 'api.user.panel'], function () {
        Route::get('chatre-nejat', [
            AbrishamDashboardPageController::class, 'chatreNejatDashboard',
        ])->name('.chatreNejatDashboard');
    });

    // Dashboard routes
    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('/', '\\'.DashboardPageV2Controller::class)->name('api.v2.asset');
        Route::get('/abrisham', '\\'.AbrishamDashboardPageV2Controller::class)->name('api.v2.asset.abrisham');
    });

    // Bookmark route
    Route::get('bookmark', '\\'.BookmarkPageV2Controller::class)->name('api.v2.bookmark');

    // Time point routes
    Route::resource('timepoint', '\\'.TimepointController::class)->except(['create', 'edit']);
    Route::group(['prefix' => 'timepoint'], function () {
        Route::get('{timepoint}/favored', [FavorableController::class, 'getUsersThatFavoredThisFavorable'])
            ->name('api.v2.get.user.favorite.content.timepoint');
        Route::post('{timepoint}/favored', [FavorableController::class, 'markFavorableFavorite'])
            ->name('api.v2.mark.favorite.content.timepoint');
        Route::post('{timepoint}/unfavored', [FavorableController::class, 'markUnFavorableFavorite'])
            ->name('api.v2.mark.unfavorite.content.timepoint');
    });

    //Product

    Route::group(['prefix' => 'product'], function () {
        Route::get('{product}', [ProductController::class, 'showV2'])->name('api.v2.product.show');
        Route::put('{product}', [ProductController::class, 'updateV2'])->name('api.v2.product.update');
        Route::get('', [ProductController::class, 'index'])->name('api.v2.product.index');

        Route::get('{product}/transferToDana',
            [ProductController::class, 'transferToDana'])->name('api.product.transferToDana');
        Route::get('{product}/createConfiguration', [ProductController::class, 'createConfiguration']);
        Route::patch('updateProductsConfig',
            [ProductController::class, 'updateProductsConfig'])->name('updateProductsConfig');
        Route::post('{product}/makeConfiguration', [ProductController::class, 'makeConfiguration']);
        Route::get('{product}/editAttributevalues',
            [ProductController::class, 'editAttributevalues'])->name('api.product.attributevalue.edit');
        Route::post('{product}/updateAttributevalues',
            [ProductController::class, 'updateAttributevalues'])->name('api.product.attributevalue.update');
        Route::post('{product}/attribute-value/attach',
            [ProductController::class, 'attachAttributeValue'])->name('api.product.attributevalue.attach');
        Route::delete('{product}/attribute-value/{attribute_value}/detach',
            [ProductController::class, 'detachAttributeValue'])->name('api.product.attributevalue.detach');
        Route::put('{product}/addGift', [ProductController::class, 'addGift']);
        Route::delete('{product}/removeGift', [ProductController::class, 'removeGift']);
        Route::post('{product}/copy', [ProductController::class, 'copy']);
        Route::post('{product}/attachBlock',
            [ProductController::class, 'attachBlock'])->name('api.product.attach.block');
        Route::delete('{product}/detachBlock',
            [ProductController::class, 'detachBlock'])->name('api.product.detach.block');
        Route::put('child/{product}', [ProductController::class, 'childProductEnable']);
        Route::put('addComplimentary/{product}', [ProductController::class, 'addComplimentary']);
        Route::put('removeComplimentary/{product}', [ProductController::class, 'removeComplimentary']);
        Route::post('{product}/photo/update-order',
            [ProductphotoController::class, 'updateOrder'])->name('api.product.update.order');
        Route::get('{product}/attribute-value',
            [AttributevalueController::class, 'productAttributeValueIndex'])->name('api.product.attributevalue.index');
        Route::get('lives', [ProductController::class, 'lives'])->name('lives');

        Route::get('{product}/sample', [ProductController::class, 'sampleVideo'])->name('sample');
        Route::get('{product}/faq', [ProductController::class, 'faq'])->name('faq');
        Route::get('{product}/complimentary', [ProductController::class, 'complimentary'])->name('complimentary');
        Route::get('{product}/exams', [ProductController::class, 'exams'])->name('exams');
        Route::get('gift-products/{product}',
            [ProductController::class, 'giftProducts'])->name('api.v2.product.gift-products');
        Route::get('{product}/sets', [ProductController::class, 'sets'])->name('sets');
        Route::get('{product}/contents', [ProductController::class, 'contents'])->name('api.v2.product.contents');
        Route::get('{product}/content-comments',
            [ProductController::class, 'contentComments'])->name('api.v2.product.content.comments');
        Route::get('{product}/favored', [FavorableController::class, 'getUsersThatFavoredThisFavorable'])
            ->name('api.v2.get.user.favorite.product');
        Route::post('{product}/favored', [FavorableController::class, 'markFavorableFavorite'])
            ->name('api.v2.mark.favorite.product');
        Route::post('{product}/unfavored',
            [FavorableController::class, 'markUnFavorableFavorite'])->name('api.v2.mark.unfavorite.product');
        Route::post('create', [ProductController::class, 'storeV2'])->name('api.v2.product.store');

        Route::get('{product}/toWatch', [ProductController::class, 'nextWatchContent'])
            ->name('api.v2.product.nextWatchContent');
        Route::get('{product}/liveInfo', [ProductController::class, 'liveInfo'])
            ->name('api.v2.product.liveInfo');
        Route::post('{product}/updateSetOrder', [ProductController::class, 'updateSetOrder'])
            ->name('api.v2.product.updateSetOrder');
        Route::get('soalaa/all', [ProductController::class, 'soalaaProducts'])->name('api.v2.product.soalaaProducts');
    });
    Route::get('product-categories', [ProductController::class, 'productCategory'])->name('api.v2.product.category');
    Route::post('getPricgroupIndexe/{product}',
        [ProductController::class, 'refreshPriceV2'])->name('api.v2.refreshPrice');

    //Set
    Route::group(['prefix' => 'set'], function () {
        Route::get('{set}/list/links', [SetController::class, 'indexContentLinks'])->name('api.set.list.links');
        Route::get('{set}/list', [SetController::class, 'indexContent'])->name('api.set.list.contents');
        Route::get('{set}/transferToDana', [SetController::class, 'transferToDana'])->name('api.set.transferToDana');
        Route::post('{set}/products',
            [SetController::class, 'toggleProductForSet'])->name('api.set.toggleProductForSet');
        Route::get('{setId}/transfer-to-dana-info',
            [SetController::class, 'transferToDanaInfo'])->name('api.set.transferToDanaInfo');
        Route::get('', [SetController::class, 'index'])->name('set.index');
        Route::get('{set}', [SetController::class, 'showV2'])->name('set.show');
        Route::get('{set}/contents', [SetController::class, 'contents'])->name('contents');
        Route::get('{set}/favored', [FavorableController::class, 'getUsersThatFavoredThisFavorable'])
            ->name('api.v2.get.user.favorite.set');
        Route::post('{set}/favored', [FavorableController::class, 'markFavorableFavorite'])
            ->name('api.v2.mark.favorite.set');
        Route::post('{set}/unfavored', [FavorableController::class, 'markUnFavorableFavorite'])
            ->name('api.v2.mark.unfavorite.set');
        Route::post('bulk-activate', [SetController::class, 'bulkActivate'])->name('set.bulk-activate');
    });
    Route::get('content-set/{set}', [SetController::class, 'showWithContents']);

    //Content
    Route::prefix('c')->group(function () {
        Route::name('c.')->group(function () {
            Route::get('uploadContent', [ContentController::class, 'uploadContent'])->name('upload.content');
            Route::get('createArticle', [ContentController::class, 'createArticle'])->name('create.article');
            Route::post('updateTmpDescription',
                [ContentController::class, 'createArticle'])->name('update.pending.description');
            Route::post('{c}/updateSet', [ContentController::class, 'updateSet'])->name('updateSet');
            Route::post('{c}/copyTmp', [ContentController::class, 'copyTimepoints'])->name('copyTmp');
            Route::get('{c}/transferToDana', [ContentController::class, 'transferToDana'])->name('transferToDana');
            Route::get('{contentId}/transfer-to-dana-info',
                [ContentController::class, 'transferToDanaInfo'])->name('transferToDanaInfo');
            Route::get('{c}', [ContentController::class, 'showV2'])->name('api.v2.content.show');
            Route::get('{c}/products', [ContentController::class, 'products'])->name('api.v2.content.products');
            Route::put('updateDuration',
                [ContentController::class, 'updateDuration'])->name('api.v2.content.updateDuration');
            Route::get('{c}/favored', [FavorableController::class, 'getUsersThatFavoredThisFavorable'])->name('api.v2.get.user.favorite.content');
            Route::post('{c}/favored', [FavorableController::class, 'markFavorableFavorite'])->name('api.v2.mark.favorite.content');
            Route::post('{c}/unfavored', [FavorableController::class, 'markUnFavorableFavorite'])->name('api.v2.mark.unfavorite.content');
        });
    });
    Route::prefix('contents')->group(function () {
        Route::put('bulk-update', [ContentController::class, 'bulkUpdate'])->name('content.bulk-update');
        Route::put('bulk-edit-text', [ContentController::class, 'bulkEditText'])->name('content.bulk-edit-text');
        Route::put('bulk-edit-tags', [ContentController::class, 'bulkEditTags'])->name('content.bulk-edit-tags');
    });
    Route::get('content-statuses', [ContentStatusController::class, 'index']);

    // Content Income routes
    Route::get('contentIncomeGroupIndex', [ContentInComeController::class, 'groupIndex']);
    Route::resource('contentIncome', '\\'.ContentInComeController::class)->only(['index', 'show']);

    //Product 3A Exam
    Route::post('product/{product}/detachExam/{exam}',
        [Product3aExamController::class, 'detachExam'])->name('api.product.detachExam');
    Route::post('product/{product}/attachExam',
        [Product3aExamController::class, 'attachExam'])->name('api.product.attachExam');

    //asset
    Route::group(['prefix' => 'asset', 'as' => 'api.user.asset'], function () {
        Route::get('/', [UserController::class, 'userProductFiles'])->name('');
        Route::get('abrisham',
            [AbrishamDashboardPageController::class, 'oldDashboard'])->name('.abrisham'); //TODO:Need to change
        Route::get('abrishamPro',
            [AbrishamDashboardPageController::class, 'proDashboard'])->name('.abrisham.pro'); //TODO:Need to change
        Route::get('taftan', '\\'.TaftanDashboardPageController::class)->name('.taftan'); //TODO:Need to change
    });

    //attributes
    Route::group(['prefix' => 'attribute'], function () {
        Route::get('{attribute}/attribute-value', [
            AttributevalueController::class, 'attributeAttributeValueIndex',
        ])->name('api.attribute.attributevalue.index');
    });

    //Vast
    Route::resource('vast', '\\'.VastController::class)->except('index');
    Route::resource('vasts/{vast}/contents', '\\'.VastContentController::class, ['as' => 'api.vasts'])->only([
        'index', 'destroy', 'store',
    ]);
    Route::resource('vasts/{vast}/sets', '\\'.VastSetController::class, ['as' => 'api.vasts'])->only([
        'index', 'store', 'destroy',
    ]);

    // attribute value
    Route::resource('attributevalue', AttributevalueController::class)->except(['create', 'show', 'index']);

    // Api Channel Controller Route
    Route::resource('ch', '\\'.ApiChannelController::class, ['as' => 'api'])->only(['show']);

});
