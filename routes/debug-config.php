<?php

use Illuminate\Support\Facades\Route;

Route::get('/debug-config', function () {
    return response()->json([
        'broadcast_driver' => config('broadcasting.default'),
        'pusher_config' => [
            'key' => config('broadcasting.connections.pusher.key') ? 'SET' : 'NOT SET',
            'secret' => config('broadcasting.connections.pusher.secret') ? 'SET' : 'NOT SET',
            'app_id' => config('broadcasting.connections.pusher.app_id') ? 'SET' : 'NOT SET',
            'cluster' => config('broadcasting.connections.pusher.options.cluster'),
        ],
        'environment' => [
            'BROADCAST_DRIVER' => env('BROADCAST_DRIVER', 'NOT SET'),
            'PUSHER_APP_KEY' => env('PUSHER_APP_KEY') ? 'SET' : 'NOT SET',
            'PUSHER_APP_SECRET' => env('PUSHER_APP_SECRET') ? 'SET' : 'NOT SET',
            'PUSHER_APP_ID' => env('PUSHER_APP_ID') ? 'SET' : 'NOT SET',
            'PUSHER_APP_CLUSTER' => env('PUSHER_APP_CLUSTER', 'NOT SET'),
        ]
    ]);
})->middleware('web');