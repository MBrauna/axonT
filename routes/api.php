<?php

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;


    Route::namespace('API')->group(function(){
        Route::namespace('Auth')->prefix('auth')->name('auth.')->group(function(){
            // [auth.login]
            Route::any('/login','Login@verifyAccess')->name('login');
        });

        Route::namespace('Util')->middleware('auth:api')->prefix('util')->name('util.')->group(function(){
            // [util.company]
            Route::any('/company','Company@getCompany')->name('company');
            // [util.filterTasks]
            Route::any('/filterTasks','FilterTasks@filter')->name('filterTasks');
        });

        Route::namespace('Performance')->middleware('auth:api')->prefix('performance')->name('performance.')->group(function(){
            Route::post('/graph','Graph@getGraphs')->name('graph');
        });
    });