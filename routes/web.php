<?php

use BehzadHosseinPoor\DatabaseManager\Http\Middleware\ValidateConnection;
use Illuminate\Support\Facades\Route;

Route::prefix('api/{connection}')->middleware(ValidateConnection::class)->name('database-manager.')->group(function () {
    Route::get('overview', 'DatabaseOverviewController@index')->name('overview.index');
    Route::get('tables', 'DatabaseTablesController@index')->name('tables.index');
});

Route::get('/{view?}', 'HomeController@index')->where('view', '(.*)')->name('database-manager.index');