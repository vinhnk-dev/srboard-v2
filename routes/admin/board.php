<?php
use App\Http\Controllers\BoardController;
use App\Http\Controllers\FAQController;

use Illuminate\Support\Facades\Route;

//Board
Route::get('/boards', [BoardController::class, 'index'])->name("board.index")->middleware(['can:permission.*']);

//Notice
Route::get('/notice/create', [BoardController::class, 'create'])->name("notice.create")->middleware(['can:permission.*']);
Route::post('/notice/create', [BoardController::class, 'store'])->name("notice.store")->middleware(['can:permission.*']);
Route::get('/notice/{id}', [BoardController::class, 'edit'])->name("notice.edit")->middleware(['can:permission.*']);
Route::post('/notice/{id}', [BoardController::class, 'update'])->name("notice.update")->middleware(['can:permission.*']);
Route::get('/boards/{id}/delete', [BoardController::class, 'delete'])->name("board.delete")->middleware(['can:permission.*']);

//FAQ
Route::get('/faq/create', [FAQController::class, 'create'])->name("faq.create")->middleware(['can:permission.*']);
Route::post('/faq/create', [BoardController::class, 'store'])->name("faq.store")->middleware(['can:permission.*']);
Route::get('/faq/{id}', [FAQController::class, 'edit'])->name("faq.edit")->middleware(['can:permission.*']);
Route::post('/faq/{id}', [BoardController::class, 'update'])->name("faq.update")->middleware(['can:permission.*']);
Route::post('/faq/categories/create', [FAQController::class, 'category_store'])->name("category.create")->middleware(['can:permission.*']);
Route::post('/faq/categories/{id}', [FAQController::class, 'category_edit'])->name("category.edit")->middleware(['can:permission.*']);
Route::get('/faq/categories/{id}/delete', [FAQController::class, 'category_delete'])->name("category.delete")->middleware(['can:permission.*']);