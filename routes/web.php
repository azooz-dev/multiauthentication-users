<?php

use App\Http\Controllers\Backend\Admin\AdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::controller(AdminController::class)->prefix('admin')->group(function () {
    Route::get('/login', 'create')->name('admin.login');
    Route::get('/forget-password', 'forget_password')->name('admin.forget_password');
    Route::post('/forget-password-submit', 'forget_password_submit')->name('admin.forget_password_submit');
    Route::get('/reset-password/{token}/{email}', 'reset_password')->name('admin.reset_password');
    Route::post('/reset-password-submit', 'reset_password_submit')->name('admin.reset_password_submit');
    Route::post('/login-submit', 'store')->name('admin.login_submit');
    Route::middleware('admin')->group(function () {
        Route::get('/dashboard', 'index')->name('admin.dashboard');
        Route::get('/logout', 'destroy')->name('admin.logout');
    });
});
require __DIR__.'/auth.php';
