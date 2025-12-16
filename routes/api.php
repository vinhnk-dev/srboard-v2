<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WeeklyReportController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\autoFillController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::get('/weekly-report/{userId}',[WeeklyReportController::class,'index'])->name('index');
Route::post('/sendmail', [HomeController::class, 'sendmail'])->name('sendmail');

Route::post('/check-project-code',[ProjectController::class,'checkProjectCode'])->name('checkPjCode');
Route::get('/auto-fill',[autoFillController::class,'update']);

