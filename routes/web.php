<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| EuPago Routes
|--------------------------------------------------------------------------
*/

Route::get('callback', 'EuPagoController@callback')->name('callback');
