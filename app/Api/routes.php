<?php
Route::group(
    [
        'middleware' => [
            'api.bootstrap',
            \Module\Member\Middleware\ApiAuthMiddleware::class,
        ],
        'namespace' => '\App\Api\Controller',
        'prefix' => 'api',
    ], function () {

    Route::match(['get', 'post'], 'config_app', 'ConfigController@app');
    Route::match(['get', 'post'], 'config_constant', 'ConfigController@constant');

});