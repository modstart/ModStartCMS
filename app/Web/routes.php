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
    Route::match(['get', 'post'], 'testx', 'IndexController@testx');
//    Route::match(['get', 'post'], 'member', 'MemberController@index');
    Route::match(['get', 'post'], 'member/{id}', 'MemberController@show');
    Route::match(['get', 'post'], 'member_profile', 'MemberProfileController@index');

});


