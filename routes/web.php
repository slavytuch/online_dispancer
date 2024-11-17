<?php

use Illuminate\Support\Facades\Route;

Route::get('/test', function (\App\Application\Telegram\Actions\SendPatientAnswerAction $action) {
    $action->execute(\App\Models\Question::find(7));
});
