<?php

use BehzadHosseinPoor\DatabaseManager\Http\Middleware\ValidateConnection;
use Illuminate\Support\Facades\Route;

Route::prefix('api/{connection}')->middleware(ValidateConnection::class)->group(function () {
    Route::get('overview', 'DatabaseOverviewController@index')->name('database-manager.overview.index');
});

Route::get('/{view?}', 'HomeController@index')->where('view', '(.*)')->name('database-manager.index');