<?php

namespace App\Http\Controllers;

use App\Application\Telegram\TelegramWebhookManager;
use Illuminate\Http\Request;

class TelegramController extends Controller
{
    public function handle(TelegramWebhookManager $webhookManager, Request $request)
    {
        try {
            $webhookManager->handleRequest($request);
        } catch (\Exception $ex) {
            \Log::error('Исключение при обработке в контроллере', ['exception' => $ex]);
            return 'error!';
        }

        return 'ok';
    }
}
