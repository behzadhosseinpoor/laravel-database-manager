<?php

use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {

});

// Catch-all Route...
Route::get('/{view?}', 'HomeController@index')->where('view', '(.*)')->name('database-manager.index');