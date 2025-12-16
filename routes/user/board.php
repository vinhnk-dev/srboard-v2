<?php
use App\Http\Controllers\UserController;
use App\Http\Controllers\BoardController;
use Illuminate\Support\Facades\Route;

Route::get('/profile', [UserController::class, 'show'])->name('user.profile');
Route::get('/profile/{mode}', [UserController::class, 'profile'])->name('user.profile_edit');
Route::post('/profile', [UserController::class, 'store'])->name('user.edit_submit');

Route::get('/notice/{id}/view', [BoardController::class, 'view'])->name("notice.view");
Route::post('/notice/{id}/view/comment', [BoardController::class, 'comment'])->name("notice.comment");
Route::post('/notice/{boardId}/view/comment/{id}/edit', [BoardController::class, 'comment_edit'])->name("notice.edit.comment");
Route::get('/notice/{boardId}/view/comment/{id}/delete', [BoardController::class, 'comment_delete'])->name("notice.delete.comment");