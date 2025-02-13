<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Broadcaster
    |--------------------------------------------------------------------------
    |
    | This option controls the default broadcaster that will be used by the
    | framework when an event needs to be broadcast. You may set this to
    | any of the connections defined in the "connections" array below.
    |
    | Supported: "pusher", "redis", "log", "null"
    |
    */

    'default' => env('BROADCAST_DRIVER', 'null'),

    /*
    |--------------------------------------------------------------------------
    | Broadcast Connections
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the broadcast connections that will be used
    | to broadcast events to other systems or over websockets. Samples of
    | each available type of connection are provided inside this array.
    |
    */

    'connections' => [

        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true, // Active TLS pour HTTPS
                // Pour laravel-websockets :
                'host' => env('LARAVEL_WEBSOCKETS_HOST', 'infocite.test'),
                'port' => env('LARAVEL_WEBSOCKETS_PORT', 6001),
                'scheme' => env('LARAVEL_WEBSOCKETS_SCHEME', 'https'), // Utilisation de HTTPS
                'encrypted' => env('LARAVEL_WEBSOCKETS_ENCRYPTED', true), // Assurez-vous que cette option est activée
                'verify' => false,
                'curl_options' => [
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_CAINFO => env('LARAVEL_WEBSOCKETS_SSL_LOCAL_CERT', 'C:/laragon/etc/ssl/laragon.crt'),
                ],
            ],
        ],

        // 'redis' => [
        //     'driver' => 'redis',
        //     'connection' => 'default',
        // ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],

    ],

];
