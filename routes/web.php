<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//could group all this

Route::controller(TicketController::class)->group(function () {
    Route::get('unprocessed', 'unprocessed')->name('tickets.unprocessed');
    Route::get('processed', 'processed')->name('tickets.processed');
    Route::get('for-user', 'forUser')->name('tickets.for-user');
    Route::get('stats', 'stats')->name('tickets.stats');
});



