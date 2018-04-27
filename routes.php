<?php

    Route::group([
        'domain' => env('API_DOMAIN'),
        'prefix' => env('API_PREFIX', 'api') .'/v1',
        'namespace' => 'Octobro\SocialLoginAPI\Controllers',
        'middleware' => 'cors'
        ], function() {
            Route::post('sociallogin/facebook', 'SocialLogin@facebook');
    });
