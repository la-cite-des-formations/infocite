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
];
