<?php

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

Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('test', function () {
    event(new App\Events\MessageSent('duraibytes'));
    return "Event has been sent!";
});

Auth::routes();


Route::group(['middleware' => ['auth']], function() {
    Route::get('/', [App\Http\Controllers\ProjectController::class, 'index'])->name('home');

    Route::prefix('project')->group(function(){
        Route::get('/', [App\Http\Controllers\ProjectController::class, 'index'])->name('project');
        Route::any('/task/{project_id?}', [App\Http\Controllers\ProjectController::class, 'taskView'])->name('project.task');
        Route::post('/add_edit', [App\Http\Controllers\ProjectController::class, 'add_edit'])->name('project.add_edit');
        Route::post('/save', [App\Http\Controllers\ProjectController::class, 'save'])->name('project.save');
        Route::post('/status', [App\Http\Controllers\ProjectController::class, 'changeStatus'])->name('project.status');
        Route::post('/delete', [App\Http\Controllers\ProjectController::class, 'delete'])->name('project.delete');
    });

    Route::get('/task/{project_id?}', [App\Http\Controllers\TaskController::class, 'index'])->name('task');
    Route::post('/task/add_edit', [App\Http\Controllers\TaskController::class, 'add_edit'])->name('task.add_edit');
    Route::post('/task/save', [App\Http\Controllers\TaskController::class, 'save'])->name('task.save');
    Route::post('/task/delete', [App\Http\Controllers\TaskController::class, 'delete'])->name('task.delete');
    Route::post('/task/status', [App\Http\Controllers\TaskController::class, 'changeStatus'])->name('task.status');
    Route::post('/task/project/collabarators', [App\Http\Controllers\TaskController::class, 'gerProjectCollabarators'])->name('task.get_project_collabarators');



});
