<?php

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;


    Route::namespace('API')->group(function(){
        Route::namespace('Auth')->prefix('auth')->name('auth')->group(function(){
            // [auth.login]
            Route::any('/login','Login@verifyAccess')->name('login');
        });

        Route::namespace('Performance')->middleware('auth:api')->prefix('performance')->name('performance.')->group(function(){
            Route::get('/teste','Graph@getGraphs')->name('graph');
        });
    });
