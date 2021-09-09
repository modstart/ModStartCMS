<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'namespace' => '\App\Web\Controller',
        'middleware' => [
            'web.bootstrap',
            \Module\Member\Middleware\WebAuthMiddleware::class,
        ],
    ], function () {

    Route::match(['get', 'post'], '', 'IndexController@index');
    Route::match(['get', 'post'], 'member', 'MemberController@index');
    Route::match(['get', 'post'], 'member/{id}', 'MemberController@show');
    Route::match(['get', 'post'], 'member_profile', 'MemberProfileController@index');

});


Route::group(
    [
        'namespace' => '\App\Web\Controller',
        'middleware' => [
            \Module\Vendor\Middleware\StatelessRouteMiddleware::class,
        ]
    ], function () {

    Route::match(['get', 'post'], 'license-logo.png', 'LicenseLogoController@index');

});
