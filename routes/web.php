<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\RubrickController;
use App\Http\Controllers\SchedAuthorController;
use App\Http\Controllers\ScheduleController;
use App\Models\SchedAuthor;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    return Inertia::render('public');
})->name('public');

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth']], function(){
    Route::get('/', function(){
        return Inertia::render('Admin/Index');
    })->name('home');
    Route::get('/participants', function(){
        return Inertia::render('Admin/Participants');
    })->name('participants');
    Route::get('/categories', function(){
        return Inertia::render('Admin/Categories');
    })->name('categories');
    Route::get('/rubricks', function(){
        return Inertia::render('Admin/Rubricks');
    })->name('rubricks');


    //Resources
    Route::apiResource('category', CategoryController::class);
    
    Route::apiResource('participant', ParticipantController::class);
    Route::post('/participant/img/avatar', [ParticipantController::class, 'AvatarUpdate'])->name('participant.img.avatar');
    Route::post('/participant/img/bg', [ParticipantController::class, 'BgUpdate'])->name('participant.img.bg');

    Route::get('/categories/items', [CategoryController::class, 'getItems'])->name('categ.items');
    Route::get('/participants/items', [ParticipantController::class, 'getItems'])->name('participants.items');

    Route::apiResource('schedule', ScheduleController::class)->names('schedule');
    Route::get('/link/schedule/standing', [ScheduleController::class, 'editStanding'])->name('link.schedule.standing');

    Route::apiResource('schedule-auth', SchedAuthorController::class)->names('schedule-auth');
    
    Route::apiResource('rubrick', RubrickController::class)->names('rubrick');
    Route::put('update-rubrick-type/{id}/sched', [RubrickController::class, 'updateRubrickType'])->name('update-rubrick-type');
});

Route::get('/link/schedule/standing', [ScheduleController::class, 'privateStanding'])->name('link.schedule.standing');
Route::put('update/status', [ScheduleController::class, 'UpdateStatus'])->name('update.status');
Route::put('update-schedule-score', [ScheduleController::class, 'UpdateScore'])->name('update-schedule-score');
Route::put('update-schedule-score-auth', [ScheduleController::class, 'UpdateAuthScore'])->name('update-schedule-score-auth');

Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::get('/register', [LoginController::class, 'register']);

Route::post('/login-attempt', [LoginController::class, 'authenticate'])->name('login.authenticate');
Route::post('/register-user', [LoginController::class, 'credRegister'])->name('login.credRegister');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
