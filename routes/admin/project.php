<?php
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\StatusController;

use Illuminate\Support\Facades\Route;

// CHECKED!

// project management
// Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index')->middleware(['can:permission.*']);
// Route::post('/projects/search/{search_text}', [ProjectController::class, 'search'])->name('project.search')->middleware(['can:permission.*']);
// Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create')->middleware(['can:permission.*']);
// Route::post('/projects/create', [ProjectController::class, 'store'])->name('projects.store')->middleware(['can:permission.*']);
// Route::get('/projects/create/status', [ProjectController::class, 'addStatus'])->name('project.addStatus')->middleware(['can:permission.*']);
// Route::post('/projects/create/status', [ProjectController::class, 'addStatus_submit'])->name('project.addStatus_submit')->middleware(['can:permission.*']);
// Route::get('/projects/{id}', [ProjectController::class, 'edit'])->name('projects.edit')->middleware(['can:permission.*']);
// Route::post('/projects/{id}', [ProjectController::class, 'update'])->name('project.update')->middleware(['can:permission.*']);
// Route::get('/projects/delete/{id}', [ProjectController::class, 'delete'])->name('projects.delete')->middleware(['can:permission.*']);

//user management
// Route::get('/users', [UserController::class, 'index'])->name('users.index')->middleware(['can:permission.*']);
// Route::get('/users/create', [UserController::class, 'create'])->name('users.create')->middleware(['can:permission.*']);
// Route::post('/users/create', [UserController::class, 'store'])->name('users.store')->middleware(['can:permission.*']);
// // Route::get('/users/delete/{id}', [UserController::class, 'destroy'])->name('user.destroy')->middleware(['can:permission.*']);
// Route::get('/users/delete/{id}', [UserController::class, 'delete'])->name('user.delete')->middleware(['can:permission.*']);
// Route::get('/users/{id}', [UserController::class, 'edit'])->name('user.edit')->middleware(['can:permission.*']);
// // Route::patch('/users/{id}', [UserController::class, 'userProfile_submit'])->name('user.userProfile_submit')->middleware(['can:permission.*']);

//Group Management
// Route::get('/group', [GroupController::class, 'index'])->name('group.index')->middleware(['can:permission.*']);
// Route::get('/group/create/new', [GroupController::class, 'create'])->name('group.create')->middleware(['can:permission.*']);
// Route::post('/group/create/new', [GroupController::class, 'store'])->name('group.store')->middleware(['can:permission.*']);
// Route::get('/group/{id}', [GroupController::class, 'edit'])->name('group.edit')->middleware(['can:permission.*']);
// Route::get('/group/delete/{id}', [GroupController::class, 'delete'])->name('group.delete')->middleware(['can:permission.*']);
// Route::post('/group/search/{search_text}', [GroupController::class, 'search'])->name('group.search')->middleware(['can:permission.*']);


//Export
// Route::get('/export/issues', [\App\Http\Controllers\ExportIssueController::class, 'export']);
