<?php

    use Illuminate\Support\Facades\Route;

    Auth::routes();

    Route::namespace('Page')->middleware(['auth'])->group(function(){
        // [mainPage] Tela principal para o sistema AxionT
        Route::any('/', function(){
            return redirect()->route('performance.graph');
        })->name('mainPage');

        Route::namespace('Performance')->prefix('performance')->name('performance.')->group(function(){
            // [performance.mainPage] - Página principal para o sistema de gráficos
            Route::any('/', function(){
                return redirect()->route('performance.graph');
            })->name('mainPage');

            // [performance.graph] - Página de gráficos
            Route::any('/graph','Graph@startPage')->name('graph');
            // [performance.report] - Página de relatórios
            Route::any('/report','Report@startPage')->name('report');
        }); // Route::namespace('Performance')->prefix('performance')->name('performance.')->group(function(){ ... });

        Route::namespace('Task')->prefix('task')->name('task.')->group(function(){
            // [task.mainPage]
            Route::any('/', function(){
                return redirect()->route('task.list');
            })->name('mainPage');

            // [tasks.create] - Criação de solicitacoes, agendamentos
            Route::any('/create','Create@startPage')->name('create');
            // [tasks.createSS] - Criação de solicitacoes, agendamentos
            Route::any('/createSS','Create@createData')->name('createSS');
            // [task.list] - Sistema para criação das solicitações e agendamentos.
            Route::any('/list','ListSS@listPage')->name('list');
            // [task.idTask]
            Route::any('/{idTask}','ShowTask@getID')->name('idTask');

            // [tasks.listAutomatic] - Sistema para troca de objetos
            Route::any('/listAutomatic','ListSS@listAutomatic')->name('listAutomatic');
            // [task.editAutomatic]
            Route::any('/editAutomatic','ListSS@editAutomatic')->name('editAutomatic');
        }); // Route::namespace('Task')->prefix('task')->name('task.')->group(function(){ ... });
    }); // Route::namespace('Page')->middleware(['auth'])->group(function(){ ... });
