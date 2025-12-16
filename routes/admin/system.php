<?php

use App\Http\Controllers\StatusController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GroupController;
use Illuminate\Support\Facades\Route;

$modals = [
    'status' => StatusController::class,
    'projects' => ProjectController::class,
    'users' => UserController::class,
    'group' => GroupController::class
];

foreach ($modals as $modal => $controller) {
    Route::get('/' . $modal, [$controller, 'index'])->name($modal . '.index')->middleware(['can:permission.*']);
    Route::get('/' . $modal . '/create', [$controller, 'create'])->name($modal . '.create')->middleware(['can:permission.*']);
    Route::get('/' . $modal . '/excel', [$controller, 'excel'])->name($modal . '.excel')->middleware(['can:permission.*']);
    Route::get('/' . $modal . '/trash', [$controller, 'trash'])->name($modal .  '.trash')->middleware(['can:permission.*']);
    Route::get('/' . $modal . '/{id}', [$controller, 'edit'])->name($modal . '.edit')->middleware(['can:permission.*']);
    Route::get('/' . $modal . '/{id}/delete', [$controller, 'delete'])->name($modal . ".delete")->middleware(['can:permission.*']);
    Route::get('/' . $modal . '/{id}/restore', [$controller, 'restore'])->name($modal . ".restore")->middleware(['can:permission.*']);
    Route::get('/' . $modal . '/{id}/deleteforce', [$controller, 'forcesDelete'])->name($modal . ".deleteforce")->middleware(['can:permission.*']);

    Route::post('/' . $modal . '/create', [$controller, 'store'])->name($modal . '.store')->middleware(['can:permission.*']);
    Route::post('/' . $modal . '/{id}', [$controller, 'update'])->name($modal . '.update')->middleware(['can:permission.*']);
}
Route::get('/check-project-code',[ProjectController::class,'checkProjectCode'])->name('checkPjCode');
Route::get('/group/{id}/member', [GroupController::class, 'show'])->name('group.show')->middleware(['can:permission.*']);
Route::get('/group/{id}/member/delete/{member_id}', [GroupController::class, 'remove'])->name('group.remove')->middleware(['can:permission.*']);
Route::get('/group/{id}/member/add', [GroupController::class, 'add'])->name('group.add')->middleware(['can:permission.*']);
Route::delete('/group/{id}/member/delete/{member_id}', [GroupController::class, 'remove'])->name('group.remove')->middleware(['can:permission.*']);
