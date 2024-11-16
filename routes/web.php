<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    dd(\App\Models\PatientParamValue::first()->patientParam);
});

Route::get('/test', function (\App\Application\Telegram\Actions\SendCheckupMessageAction $action) {
    $action->execute(\App\Models\Checkup::find(3));
});
/*Route::get('/test', function (\App\Application\Telegram\Conversation\Actions\CheckForConversations $checkForConversations) {
    $checkForConversations->execute(\App\Models\Patient::find(6));
});*/


