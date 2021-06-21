<?php

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;


    Route::namespace('API')->group(function(){

        Route::namespace('Auth')->prefix('auth')->name('auth.')->group(function(){
            // [auth.login]
            Route::any('/login','Login@verifyAccess')->name('login');
        });

        Route::namespace('Performance')->middleware('auth:api')->prefix('performance')->name('performance.')->group(function(){
            Route::post('/graph','PerformanceChat@getChart')->name('graph');
        }); // Route::namespace('Performance')->middleware('auth:api')->prefix('performance')->name('performance.')->group(function(){ ... })

        Route::namespace('Util')->middleware('auth:api')->prefix('util')->name('util.')->group(function(){
            // [util.company]
            Route::any('/company','AccessCompany@getCompanies')->name('company');
            // [util.tasksOptions]
            Route::any('/getUserOptions','GetUserOptions@getOptions')->name('getUserOptions');
            // [util.getPermission]
            Route::any('/getPermission','GetUserOptions@getPermission')->name('getPermission');
        }); // Route::namespace('Util')->middleware('auth:api')->prefix('util')->name('util.')->group(function(){ ... }

        Route::namespace('Task')->middleware('auth:api')->prefix('task')->name('task.')->group(function(){
            // [task.getDataManual]
            Route::any('/getDataManual','GetDataManual@getData')->name('getDataManual');
            // [task.getDataManual]
            Route::any('/getDataAutomatic','GetDataAutomatic@getData')->name('getDataAutomatic');
            // [task.list]
            Route::any('/list','GetDataManual@list')->name('list');
            // [tasks.listAutomatic]
            Route::any('/listAutomatic','TaskList@listAutomatic')->name('listAutomatic');
            // [task.id]
            Route::any('/id','ShowTask@getID')->name('id');
            // [tasks.changeAutomaticStatus]
            Route::any('/changeAutomaticStatus','TaskList@changeAutomaticStatus')->name('changeAutomaticStatus');
            // [tasks.removeScheduling]
            Route::any('/removeScheduling','TaskAutomatic@removeScheduling')->name('removeScheduling');
            // [tasks.editAutomatic]
            Route::any('/editAutomatic','TaskAutomatic@editAutomatic')->name('editAutomatic');
        }); // Route::namespace('Task')->middleware('auth:api')->prefix('task')->name('task.')->group(function(){ ... });
    });