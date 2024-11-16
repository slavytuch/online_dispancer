<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(\App\Http\Controllers\PatientController::class)->prefix('patient')->group(function () {
    Route::get('/{id}', 'getById');
});
