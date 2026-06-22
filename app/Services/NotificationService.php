<?php

namespace App\Services;

use GuzzleHttp\Client;

class NotificationService
{
    public static function send($title, $message)
    {
        // 1. Lógica do WhatsApp (a que você já tem funcionando)
        \Log::info("Enviando WhatsApp..."); 
        
        // 2. Lógica do OneSignal
        $client = new Client();
        $client->post('https://onesignal.com/api/v1/notifications', [
            'headers' => [
                'Authorization' => 'Basic ' . env('ONESIGNAL_REST_API_KEY'),
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'app_id' => env('ONESIGNAL_APP_ID'),
                'contents' => ['en' => $message],
                'headings' => ['en' => $title],
                'included_segments' => ['All'], // Ou filtrar por usuário
            ]
        ]);
        
        \Log::info("Notificação Mobile enviada: $title");
    }
}