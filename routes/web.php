<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

Route::middleware(['auth:sanctum', 'verified'])->group(function (){
    Route::resource('inbox', \App\Http\Controllers\ChatController::class);
    Route::get('get_conversation/{id}', [\App\Http\Controllers\ChatController::class, 'getConversation']);
});

Route::put('users/{user}/update', [App\Http\Controllers\UsersOnlineController::class, 'updateStatus'])->name('user_status');
Route::put('users/{user}/offline', [App\Http\Controllers\UserOfflineController::class, 'updateStatus'])->name('user_status_offline');
