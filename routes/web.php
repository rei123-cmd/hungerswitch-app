<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarketplaceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('index');
})->name('home');

Route::get('/marketplace', [MarketplaceController::class, 'index'])
    ->name('marketplace');

Route::get('/login', function () {
    return view('login_unified_blade');
})->name('login');

Route::get('/donasi', function () {
    return view('donasi');
})->name('donasi');
