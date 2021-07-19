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
            // [tasks.createObject] - Criação de solicitacoes, agendamentos
            Route::any('/createObject','Create@createObject')->name('createObject');
            // [tasks.editObject] - Criação de solicitacoes, agendamentos
            Route::any('/editObject','Create@editObject')->name('editObject');
            // [task.list] - Sistema para criação das solicitações e agendamentos.
            Route::any('/list','ListSS@listPage')->name('list');
            // [task.listAutomatic] - Sistema para troca de objetos
            Route::any('/listAutomatic','ListSS@listAutomatic')->name('listAutomatic');
            // [task.idTask]
            Route::any('/{idTask}','ShowTask@getID')->name('idTask');
            // [task.allTasks]
            Route::any('/allTasks','TaskData@listTask')->name('allTasks');
        }); // Route::namespace('Task')->prefix('task')->name('task.')->group(function(){ ... });


        Route::namespace('Cards')->prefix('card')->name('card.')->group(function(){
            // [card.mainPage]
            Route::any('/', function(){
                return redirect()->route('card.list');
            })->name('mainPage');

            // [card.list] - Sistema para criação das cards
            Route::any('/list','CardsData@listCard')->name('list');
            // [card.edit] - Alteração de dados de cartões
            Route::any('/edit','CardsData@saveCard')->name('edit');
        }); // Route::namespace('Task')->prefix('task')->name('task.')->group(function(){ ... });

        Route::namespace('Admin')->prefix('admin')->name('admin.')->group(function(){
            // [admin.mainPage]
            Route::any('/', function(){
                return redirect()->route('performance.graph');
            })->name('mainPage');

            // [admin.company]
            Route::any('company','Company@pageCompany')->name('company');
        }); // Route::namespace('Admin')->prefix('admin')->name('admin.')->group(function(){ ... }
    }); // Route::namespace('Page')->middleware(['auth'])->group(function(){ ... });
