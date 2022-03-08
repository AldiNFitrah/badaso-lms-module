<?php

use Illuminate\Support\Facades\Route;
use Uasoft\Badaso\Middleware\ApiRequest;
use Uasoft\Badaso\Middleware\BadasoAuthenticate;
use Uasoft\Badaso\Module\LMSModule\Helpers\Route as HelpersRoute;

$api_route_prefix = config('Badaso.api_route_prefix', 'badaso-api');

Route::group(['prefix' => $api_route_prefix, 'as' => 'badaso.', 'middleware' => [ApiRequest::class]], function() {
    Route::group(['prefix' => 'module/lms/v1', 'as' => 'auth.'], function() {
        Route::group(['prefix' => 'auth'], function() {
            Route::get('/login', HelpersRoute::getController('UserController@home'));
        });

        Route::group(['prefix' => 'course', 'as' => 'course.'], function() {
            Route::post('/', HelpersRoute::getController('CourseController@add'))
                ->middleware(BadasoAuthenticate::class)
                ->name('add');
        });
    });
});
