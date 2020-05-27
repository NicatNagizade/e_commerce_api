<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Auth')->prefix('auth')->group(function(){
    Route::post('login', 'ApiController@login');
    Route::post('register', 'ApiController@register');
    Route::post('reset', 'ApiController@sendPasswordReset');
    Route::post('confirm', 'ApiController@confirmPassword');
    Route::middleware('auth:api')->group(function(){
        Route::get('me', 'ApiController@me');
        Route::post('logout', 'ApiController@logout');
    });
});
Route::namespace('Auth')->prefix('admin')->group(function(){
    Route::post('login', 'AdminController@login');
    Route::middleware('auth:api')->group(function(){
        Route::post('register', 'AdminController@register');
        Route::post('role', 'AdminController@updateUserRoles');
    });
});

Route::middleware('auth:api')->group(function(){
    Route::prefix('dev')->namespace('Dev')->group(function(){
        Route::get('operation_log','OperationLogController@allLogsAndFilter');
        Route::get('translation/{table}','TranslationController@getAllTranslations');
        Route::put('translation/{table}/{id}','TranslationController@updateTranslation');
    });

    Route::prefix('product')->namespace('Product')->group(function(){
        Route::get('','ProductController@getProducts');
        Route::post('','ProductController@createProduct');
        Route::put('{id}','ProductController@updateProduct');
        Route::delete('{id}','ProductController@deleteProduct');
    });

    Route::prefix('msk')->namespace('Msk')->group(function(){
        Route::prefix('country')->group(function(){
            Route::get('', 'CountryController@index');
            Route::post('', 'CountryController@store');
            Route::put('{id}', 'CountryController@update');
            Route::delete('{id}', 'CountryController@destroy');
        });
        Route::prefix('color')->group(function(){
            Route::get('', 'ColorController@index');
            Route::post('', 'ColorController@store');
            Route::put('{id}', 'ColorController@update');
            Route::delete('{id}', 'ColorController@destroy');
        });
        Route::prefix('region')->group(function(){
            Route::get('', 'RegionController@index');
            Route::post('', 'RegionController@store');
            Route::put('{id}', 'RegionController@update');
            Route::delete('{id}', 'RegionController@destroy');
        });
        Route::prefix('metro')->group(function(){
            Route::get('', 'MetroController@index');
            Route::post('', 'MetroController@store');
            Route::put('{id}', 'MetroController@update');
            Route::delete('{id}', 'MetroController@destroy');
        });
        Route::prefix('category')->group(function(){
            Route::get('', 'CategoryController@index');
            Route::post('', 'CategoryController@store');
            Route::put('{id}', 'CategoryController@update');
            Route::delete('{id}', 'CategoryController@destroy');
        });
        Route::prefix('sub_category')->group(function(){
            Route::get('', 'SubCategoryController@index');
            Route::post('', 'SubCategoryController@store');
            Route::put('{id}', 'SubCategoryController@update');
            Route::delete('{id}', 'SubCategoryController@destroy');
        });
        Route::prefix('product_type')->group(function(){
            Route::get('', 'ProductTypeController@index');
            Route::post('', 'ProductTypeController@store');
            Route::put('{id}', 'ProductTypeController@update');
            Route::delete('{id}', 'ProductTypeController@destroy');
        });
        Route::prefix('sub_product_type')->group(function(){
            Route::get('', 'SubProductTypeController@index');
            Route::post('', 'SubProductTypeController@store');
            Route::put('{id}', 'SubProductTypeController@update');
            Route::delete('{id}', 'SubProductTypeController@destroy');
        });
        Route::prefix('size')->group(function(){
            Route::get('', 'SizeController@index');
            Route::post('', 'SizeController@store');
            Route::put('{id}', 'SizeController@update');
            Route::delete('{id}', 'SizeController@destroy');
        });
        Route::prefix('manufacturer')->group(function(){
            Route::get('', 'ManufacturerController@index');
            Route::post('', 'ManufacturerController@store');
            Route::put('{id}', 'ManufacturerController@update');
            Route::delete('{id}', 'ManufacturerController@destroy');
        });
        Route::prefix('currency')->group(function(){
            Route::get('', 'CurrencyController@index');
            Route::post('', 'CurrencyController@store');
            Route::put('{id}', 'CurrencyController@update');
            Route::delete('{id}', 'CurrencyController@destroy');
        });
        Route::prefix('tag')->group(function(){
            Route::get('', 'TagController@index');
            Route::post('', 'TagController@store');
            Route::put('{id}', 'TagController@update');
            Route::delete('{id}', 'TagController@destroy');
        });
        Route::prefix('type_of_delivery')->group(function(){
            Route::get('', 'TypeOfDeliveryController@index');
            Route::post('', 'TypeOfDeliveryController@store');
            Route::put('{id}', 'TypeOfDeliveryController@update');
            Route::delete('{id}', 'TypeOfDeliveryController@destroy');
        });
        Route::prefix('availability_of_product')->group(function(){
            Route::get('', 'AvailabilityOfProductController@index');
            Route::post('', 'AvailabilityOfProductController@store');
            Route::put('{id}', 'AvailabilityOfProductController@update');
            Route::delete('{id}', 'AvailabilityOfProductController@destroy');
        });
        Route::prefix('user_notification')->group(function(){
            Route::get('', 'UserNotificationController@index');
            Route::post('', 'UserNotificationController@store');
            Route::put('{id}', 'UserNotificationController@update');
            Route::delete('{id}', 'UserNotificationController@destroy');
        });
        Route::prefix('coupon')->group(function(){
            Route::get('', 'CouponController@index');
            Route::post('', 'CouponController@store');
            Route::put('{id}', 'CouponController@update');
            Route::delete('{id}', 'CouponController@destroy');
        });
        Route::prefix('design')->group(function(){
            Route::get('', 'DesignController@index');
            Route::post('', 'DesignController@store');
            Route::put('{id}', 'DesignController@update');
            Route::delete('{id}', 'DesignController@destroy');
        });
    });
});