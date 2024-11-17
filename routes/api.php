<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(\App\Http\Controllers\PatientController::class)->prefix('patient')->group(function () {
    Route::get('/{id}', 'getById');
    Route::get('/', 'getList');
    Route::post('/update/{id}', 'update');
    Route::put('/', 'add');
});

Route::controller(\App\Http\Controllers\UserController::class)->prefix('user')->group(function () {
    Route::get('/{id}', 'getById');
    Route::get('/', 'getList');
    Route::post('/update/{id}', 'update');
    Route::put('/', 'add');
});

Route::controller(\App\Http\Controllers\QuestionsController::class)->prefix('question')->group(function (){
    Route::get('/{id}', 'getById');
    Route::get('/', 'getList');
    Route::post('/update/{id}', 'update');
});

Route::any('/telegram-webhook', [\App\Http\Controllers\TelegramController::class, 'handle']);
