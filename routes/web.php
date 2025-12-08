<?php

use BehzadHosseinPoor\DatabaseManager\Http\Middleware\ValidateConnection;
use BehzadHosseinPoor\DatabaseManager\Http\Middleware\ValidateTable;
use Illuminate\Support\Facades\Route;

Route::prefix('api/{connection}')->middleware(ValidateConnection::class)->name('database-manager.')->group(function () {
    Route::get('overview', 'DatabaseOverviewController@index')->name('overview.index');
    Route::get('tables', 'DatabaseTablesController@index')->name('tables.index');
    Route::get('query', 'DatabaseQueryController@index')->name('query.index');

    Route::prefix('tables/{table}')->middleware(ValidateTable::class)->group(function () {
        Route::get('structure', 'DatabaseTableStructureController@index')->name('structure.index');
        Route::get('browse', 'DatabaseTableBrowseController@index')->name('browse.index');
        Route::post('truncate', 'DatabaseTableTruncateController@index')->name('truncate.index');
        Route::post('drop', 'DatabaseTableDropController@index')->name('drop.index');
    });
});

Route::get('/{view?}', 'HomeController@index')->where('view', '(.*)')->name('database-manager.index');
