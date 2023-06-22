<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\User\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'App\Http\Controllers\Site\HomeController@index')->name('home');
Route::get('/verify/{token}', [RegisterController::class, 'verify'])->name('register.verify');
Route::post('/verify-phone', [RegisterController::class, 'verifyPhone'])->name('register.verify-phone');
Route::post('/verify-exists-phone', [ProfileController::class, 'verifyPhone'])->name('profile.verify-phone');
Auth::routes();

Route::post('/subscribe-news', [ProfileController::class, 'subscribe'])->name('subscribe');
Route::post('/unsubscribe-news', [ProfileController::class, 'unSubscribe'])->name('un-subscribe')->middleware(['auth', 'can:verify-user']);

Route::group([
    'prefix' => 'shop',
    'as' => 'shop.',
    'namespace' => 'App\Http\Controllers\Catalog'
], function () {
    Route::get('/tag/{tag}', 'CatalogController@tag')->name('tag');
    Route::get('/brand/{brand}', 'CatalogController@brands')->name('brand');
    Route::get('/filter', 'CatalogController@filter')->name('filter');
    Route::get('/search', 'CatalogController@search')->name('search');
    Route::post('/ajax-search', 'CatalogController@ajaxSearch')->name('ajax-search');
    Route::post('/add-favorite/{product}', 'CatalogController@addFavorite')->name('add-favorite');
    Route::post('/remove-favorite/{product}', 'CatalogController@removeFavorite')->name('remove-favorite');
});

Route::group([
    'prefix'    => 'cart',
    'as'        => 'cart.',
    'namespace' => 'App\Http\Controllers\Catalog'
],function () {
    Route::get('/', 'CartController@index')->name('index');
    Route::post('/add/{product}', 'CartController@add')->name('add');
    Route::post('/change-quantity', 'CartController@changeQuantity')->name('change-quantity');
    Route::post('/delete-item', 'CartController@deleteItem')->name('delete-item');
    Route::get('/checkout', 'CheckoutController@index')->name('checkout');
    Route::post('/create-order', 'CheckoutController@store')->name('create-order');
});

Route::group([
    'prefix'    => 'catalog',
    'as'        => 'catalog.',
    'namespace' => 'App\Http\Controllers\Catalog'
], function () {
    Route::get('/{product_path?}', 'CatalogController@index')->name('index')->where('product_path', '.+');
});

Route::group([
    'prefix'    => 'blog',
    'as'        => 'blog.',
    'namespace' => 'App\Http\Controllers\Blog'
], function () {
    Route::get('/{post_path?}', 'BlogController@index')->name('index')->where('post_path', '.+');
});

Route::group(
    [
        'prefix' => 'cabinet',
        'as' => 'cabinet.',
        'namespace' => 'App\Http\Controllers\User',
        'middleware' => ['auth', 'can:verify-user'],
    ],
    function () {
        Route::group(['prefix' => 'profile', 'as' => 'profile.'], function () {
            Route::get('/', 'ProfileController@showProfile')->name('index');
            Route::get('/edit', 'ProfileController@edit')->name('edit');
            Route::post('/update', 'ProfileController@update')->name('update');
            Route::get('/update', 'ProfileController@update')->name('update');
            Route::post('/confirm-phone', 'ProfileController@confirmPhone')->name('confirm-phone');
            Route::get('/add-delivery-address', 'ProfileController@getAddressForm')->name('add-delivery-address');
            Route::post('/store-delivery-address', 'ProfileController@storeDeliveryAddress')->name('store-delivery-address');
            Route::post('/remove-delivery-address/{address}', 'ProfileController@removeAddress')->name('remove-delivery-address');
//            Route::post('/phone', 'PhoneController@request');
//            Route::get('/phone', 'PhoneController@form')->name('phone');
//            Route::put('/phone', 'PhoneController@verify')->name('phone.verify');
//            Route::post('/phone/auth', 'PhoneController@auth')->name('phone.auth');
        });
    }
);

Route::group(
    [
        'prefix' => 'admin',
        'as' => 'admin.',
        'namespace' => 'App\Http\Controllers\Admin',
        'middleware' => ['auth', 'can:admin-panel'],
    ],
    function () {
        Route::get('/', 'HomeController@index')->name('home');

        Route::get('/file-manager', 'FileManagerController@index')->name('file-manager');

        Route::resource('/navigations', 'NavigationController');
        Route::post('/navigations/find', 'NavigationController@find');
        Route::delete('/navigations/menu-item-delete/{navItem}', 'NavigationController@destroyItem');
        Route::delete('/navigations/menu-item-delete-image/{navItem}', 'NavigationController@deleteImage');
        Route::post('ajax/get-form-menu', 'NavigationController@getFormMenu')->name('ajax.form-menu');
        Route::post('ajax/get-form-menu-items', 'NavigationController@getFormMenuItems')->name('ajax.form-menu-items');

        Route::post('photos/get-photos', 'PhotoController@getPhotos')->name('photos.get-photos');
        Route::post('photos/update-photo', 'PhotoController@updatePhoto')->name('photos.update-photo');
        Route::post('photos/get-variant-photos', 'PhotoController@getVariantPhotos')->name('photos.get-variant-photos');
        Route::post('photos/update-variant-photo', 'PhotoController@updateVariantPhoto')->name('photos.update-variant-photo');


        Route::group(['prefix' => 'shop', 'as' => 'shop.', 'namespace' => 'Shop'], function () {
            Route::resource('categories', 'CategoryController');
            Route::post('categories/{category}/first', 'CategoryController@first')->name('categories.first');
            Route::post('categories/{category}/up', 'CategoryController@up')->name('categories.up');
            Route::post('categories/{category}/down', 'CategoryController@down')->name('categories.down');
            Route::post('categories/{category}/last', 'CategoryController@last')->name('categories.last');
            Route::post('categories/{category}/toggle-publish', 'CategoryController@togglePublished')->name('category.toggle.published');

            Route::post('categories/photo/{category}/{photo}/up', 'CategoryController@photoUp')->name('categories.photo.up');
            Route::post('categories/photo/{category}/{photo}/down', 'CategoryController@photoDown')->name('categories.photo.down');
            Route::post('categories/photo/{category}/{photo}/remove', 'CategoryController@photoRemove')->name('categories.photo.remove');

            Route::resource('attributes', 'AttributeController');
            Route::post('attributes/assign-categories/{attribute}', 'AttributeController@assignCategories')->name('attributes.assign-categories');
            Route::post('attributes/un-assign-categories/{attribute}', 'AttributeController@unAssignCategory')->name('attributes.un-assign-category');

            Route::resource('brands', 'BrandController');
            Route::post('brands/{brand}/logo/remove', 'BrandController@photoRemove')->name('brands.logo.remove');

            Route::resource('tags', 'TagController');
            Route::post('tags/create-ajax', 'TagController@ajaxCreate')->name('tags.ajax-create');

            Route::resource('products', 'ProductController');

            Route::post('products/get-attributes-form', 'ProductController@getAttributesForm')->name('products.get-attributes-form');
            Route::post('products/get-variants-form', 'ProductController@getVariantsForm')->name('products.get-variants-form');
            Route::post('products/set-active', 'ProductController@setActive')->name('product.set-active');

            Route::post('products/set-status', "ProductController@setStatus")->name('products.set-status');

            Route::post('products/photo/{product}/{photo}/up', 'ProductController@photoUp')->name('products.photo.up');
            Route::post('products/photo/{product}/{photo}/down', 'ProductController@photoDown')->name('products.photo.down');
            Route::post('products/photo/{product}/{photo}/remove', 'ProductController@photoRemove')->name('products.photo.remove');

            Route::resource('/delivery-methods', 'DeliveryMethodsController');
            Route::post('/delivery-methods/remove', 'DeliveryMethodsController@remove')->name('delivery-methods.remove');

            Route::resource('filters', 'FilterController');
            Route::post('filters/remove-batch', 'FilterController@removeBatch')->name('filters.remove-batch');
            Route::post('filters/add-group', 'FilterController@addGroup')->name('filters.add-group');
        });

        Route::group(['prefix' => 'blog', 'as' => 'blog.', 'namespace' => 'Blog'], function () {
            Route::resource('categories', 'CategoryController');
            Route::post('categories/{category}/first', 'CategoryController@first')->name('categories.first');
            Route::post('categories/{category}/up', 'CategoryController@up')->name('categories.up');
            Route::post('categories/{category}/down', 'CategoryController@down')->name('categories.down');
            Route::post('categories/{category}/last', 'CategoryController@last')->name('categories.last');
            Route::post('categories/{category}/toggle-status', 'CategoryController@toggleStatus')->name('category.toggle.status');

            Route::post('categories/photo/{category}/{photo}/up', 'CategoryController@photoUp')->name('categories.photo.up');
            Route::post('categories/photo/{category}/{photo}/down', 'CategoryController@photoDown')->name('categories.photo.down');
            Route::post('categories/photo/{category}/{photo}/remove', 'CategoryController@photoRemove')->name('categories.photo.remove');

            Route::resource('posts', 'PostController');
            Route::post('posts/{post}/set-status', 'PostController@setStatus')->name('posts.set-status');
        });
    }
);
