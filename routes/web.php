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

            // [task.create] - Criação de solicitacoes, agendamentos
            Route::get('/create','Create@startPage')->name('create');
            // [task.create] - Criação de solicitacoes, agendamentos
            Route::post('/create','Create@createData')->name('create');

            // [task.create] - Sistema para criação das solicitações e agendamentos.
        }); // Route::namespace('Task')->prefix('task')->name('task.')->group(function(){ ... });
    }); // Route::namespace('Page')->middleware(['auth'])->group(function(){ ... });
