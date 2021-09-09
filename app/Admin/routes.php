<?php

Route::group(
    [
        'prefix' => env('ADMIN_PATH', '/admin/'),
        'middleware' => ['admin.bootstrap', 'admin.auth'],
        'namespace' => '\App\Admin\Controller',
    ], function () {

     Route::match(['get', 'post'], '', 'IndexController@index');

});
