<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Project Configuration
    |--------------------------------------------------------------------------
    */
    'project_id' => env('FIREBASE_PROJECT_ID', 'vprotect-3e20f'),
    'messaging_sender_id' => env('FIREBASE_MESSAGING_SENDER_ID', '996502919692'),
    'api_key' => env('FIREBASE_API_KEY', 'AIzaSyC24ALS3EZuiX2XkRaAFV6AjvG60n7fRag'),
    'auth_domain' => env('FIREBASE_AUTH_DOMAIN', 'vprotect-3e20f.firebaseapp.com'),
    'storage_bucket' => env('FIREBASE_STORAGE_BUCKET', 'vprotect-3e20f.firebasestorage.app'),
    
    // Web App
    'app_id_web' => env('FIREBASE_APP_ID_WEB', '1:996502919692:web:22f8ffa3a34e4dc6a92c29'),
    'measurement_id' => env('FIREBASE_MEASUREMENT_ID', 'G-595N30P7Y0'),
    'vapid_public_key' => env('FIREBASE_VAPID_PUBLIC_KEY', null),

    // Android App
    'app_id_android' => env('FIREBASE_APP_ID_ANDROID', '1:996502919692:android:e81165d09b6c8c05a92c29'),
    'package_name_android' => env('FIREBASE_ANDROID_PACKAGE', 'neuralinfo.in'),

    // Credentials for Backend push sending:
    // Option A (Recommended): Google Service Account JSON path for Firebase HTTP v1 API
    'credentials_path' => env('FIREBASE_CREDENTIALS', storage_path('app/firebase/service-account.json')),

    // Option B: Legacy Cloud Messaging Server Key (if enabled in Firebase Console)
    'server_key' => env('FIREBASE_SERVER_KEY', null),
];
