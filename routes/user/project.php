<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\AgileController;
use App\Http\Controllers\MyTaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('index');
Route::get('/mytask', [HomeController::class, 'mytask'])->name('mytask');


$modals = [
    'issues' => ['controller' => IssueController::class , 'parent'=> 'projects']
];

foreach ($modals as $modal => $v) {
    Route::get('/'.$v['parent'] . '/{parentid}/' . $modal, [$v['controller'], 'index'])->name($modal . '.index');
    Route::get('/'.$v['parent'] . '/{parentid}/' . $modal . '/create', [$v['controller'], 'create'])->name($modal . '.create');
    Route::get('/'.$v['parent'] . '/{parentid}/' . $modal . '/excel', [$v['controller'], 'excel'])->name($modal . '.excel');
    Route::get('/'.$v['parent'] . '/{parentid}/' . $modal . '/trash', [$v['controller'], 'trash'])->name($modal .  '.trash');
    Route::get('/'.$v['parent'] . '/{parentid}/' . $modal . '/{id}', [$v['controller'], 'edit'])->name($modal . '.edit');
    Route::get('/'.$v['parent'] . '/{parentid}/' . $modal . '/{id}/view', [$v['controller'], 'view'])->name($modal . '.view');
    Route::get('/'.$v['parent'] . '/{parentid}/' . $modal . '/{id}/delete', [$v['controller'], 'delete'])->name($modal . ".delete");
    Route::get('/'.$v['parent'] . '/{parentid}/' . $modal . '/{id}/restore', [$v['controller'], 'restore'])->name($modal . ".restore");
    Route::get('/'.$v['parent'] . '/{parentid}/' . $modal . '/{id}/deleteforce', [$v['controller'], 'forcesDelete'])->name($modal . ".deleteforce");

    Route::post('/'.$v['parent'] . '/{parentid}/' . $modal . '/create', [$v['controller'], 'store'])->name($modal . '.store');
    Route::post('/'.$v['parent'] . '/{parentid}/' . $modal . '/{id}', [$v['controller'], 'store'])->name($modal . '.update');
    Route::post('/'.$v['parent'] . '/{parentid}/' . $modal . '/{id}/comment', [$v['controller'], 'comment'])->name($modal . '.comment');
    Route::post('/'.$v['parent'] . '/{parentid}/' . $modal . '/comment/{id}', [$v['controller'], 'comment_edit'])->name($modal . '.comment.edit');
    Route::post('/'.$v['parent'] . '/{parentid}/' . $modal . '/comment/{id}/delete', [$v['controller'], 'comment_delete'])->name($modal . '.comment.delete');
}

//Issues management Create/Delete
// Route::get('/projects/{projectId}/issues/', [IssueController::class, 'index'])->name('issue.index');

// Route::get('/projects/{projectId}/agile', [AgileController::class, 'index'])->name('agileboard.index');
// Route::post('/projects/{projectId}/agile/', [AgileController::class, 'update_issue'])->name('agileboard.update_issue');

// Route::get('/projects/{projectId}/issues/{id}', [IssueController::class, 'edit'])->name('issue.edit');
// Route::patch('/projects/{projectId}/issues/{id}', [IssueController::class, 'update'])->name('issue.update');
// Route::get('/projects/{projectId}/issues/create/new', [IssueController::class, 'create'])->name('issue.create');
// Route::post('/projects/{projectId}/issues/create/new', [IssueController::class, 'store'])->name('issue.store');
// Route::get('/projects/{projectId}/issues/delete/{id}', [IssueController::class, 'destroy'])->name('issue.destroy');
// Route::delete('/projects/{projectId}/issues/delete/{id}', [IssueController::class, 'delete'])->name('issue.delete');
// Route::get('/projects/{projectId}/issues/{id}/view', [IssueController::class, 'view'])->name('issue.view');
// Route::post('/projects/{projectId}/issues/{id}/view/comment', [IssueController::class, 'comment'])->name('issue.comment');
// Route::patch('/projects/{projectId}/issues/{issueId}/view/comment/{id}', [IssueController::class, 'editComment'])->name('issue.comment.edit');
// Route::get('/projects/{projectId}/issues/{issueId}/view/comment/{id}/delete', [IssueController::class, 'deleteComment'])->name('issue.comment.delete');
// Route::get('/projects/selected_theme', [ProjectController::class, 'selected_theme']);

Route::post('/projects/{parentid}/issues/{id}/changestt',[IssueController::class,'changeStatus'])->name('issues.changestt');
//Export issue from project
Route::get('/export/projects/{projectId}/', [\App\Http\Controllers\ExportIssueFromProjectController::class, 'export'])->name('export.projects');
