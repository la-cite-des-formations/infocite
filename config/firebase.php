<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Credentials
    |--------------------------------------------------------------------------
    |
    | Spécifiez ici le chemin relatif vers votre fichier JSON de credentials.
    | Il sera automatiquement combiné avec la fonction storage_path().
    | Vous pouvez aussi définir d'autres configurations Firebase ici.
    |
    */

    'credentials' => env('FIREBASE_CREDENTIALS', 'app/firebase_credentials_notifs-ic.json'),
    'default_notification' => [
        'title' => env('APP_NAME', 'Info-Cité'),
        'body' => 'Nouvelle notification',
        'icon' => env('APP_FAVICON', '/img/favicon.png'),
        'url' => env('APP_URL', 'https://info.cite-formations-tours.fr'),
    ]

];
