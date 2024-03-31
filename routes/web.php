<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\ScheduleController;
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

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function(){
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
});

