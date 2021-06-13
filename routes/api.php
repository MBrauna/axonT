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
            // [task.createSS]
            Route::any('/createSS','CreateSolicitation@setData')->name('createSS');
            // [tasks.list]
            Route::any('/list','GetDataManual@list')->name('list');
        }); // Route::namespace('Task')->middleware('auth:api')->prefix('task')->name('task.')->group(function(){ ... });










        Route::namespace('Tasks')->middleware('auth:api')->prefix('tasks')->name('tasks.')->group(function(){
            // [tasks.getDataManual]
            Route::any('/getDataManual','GetUserOptions@getPermission')->name('getDataManual');
            // [tasks.search]
            Route::any('/verifyTaskAutomatic','TaskAutomatic@verifyTaskAutomatic')->name('verifyTaskAutomatic');
            // [tasks.resp]
            Route::any('/resp','TaskAutomatic@dataOrignDestiny')->name('resp');
            // [tasks.objType]
            Route::any('/objType','TaskAutomatic@filterObjectType')->name('objType');
            // [tasks.question]
            Route::any('/question','TaskAutomatic@filterQuestion')->name('question');
            // [tasks.validateManual]
            Route::any('/validateManual','TaskCreate@validateDataManual')->name('validateManual');
            // [tasks.list]
            Route::any('/list','TaskList@list')->name('list');
            // [tasks.listAutomatic]
            Route::any('/listAutomatic','TaskList@listAutomatic')->name('listAutomatic');
            // [tasks.changeAutomaticStatus]
            Route::any('/changeAutomaticStatus','TaskList@changeAutomaticStatus')->name('changeAutomaticStatus');
            // [tasks.removeScheduling]
            Route::any('/removeScheduling','TaskAutomatic@removeScheduling')->name('removeScheduling');
            // [tasks.editAutomatic]
            Route::any('/editAutomatic','TaskAutomatic@editAutomatic')->name('editAutomatic');
        });

        

    });