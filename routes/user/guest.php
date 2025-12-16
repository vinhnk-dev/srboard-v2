<?php
use App\Http\Controllers\UserController;
use App\Http\Controllers\CleannerController;

use Illuminate\Support\Facades\Route;

//TrashCleaning ,leave it!!!
Route::get('/trashcleaning', [CleannerController::class, 'destroy'])->name('cleanner');

Route::get('/user/login', [UserController::class, 'login'])->name('user.login');
Route::post('/user/login', [UserController::class, 'login_submit'])->name('user.login_submit');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

