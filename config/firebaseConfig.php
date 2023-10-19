<?php

if (env('APP_ENV', 'development') === 'development') {
    return [
        'FIREBASE_CONFIG' => [
            'firebaseConfig' => [
                'apiKey' => 'AIzaSyBMSKsBzEFtfBHkudjHuLr9brCuRUJQYX4',
                'authDomain' => 'alaa-office.firebaseapp.com',
                'databaseURL' => 'https://alaa-office.firebaseio.com',
                'projectId' => 'alaa-office',
                'storageBucket' => 'alaa-office.appspot.com',
                'messagingSenderId' => '300754869233',
                'appId' => '1:300754869233:web:c730b68385257132ed8250',
                'measurementId' => 'G-V614DM1FRK',
            ],
            'VapidKey' => 'BKJlaTO0dnXtHHFho3i53VF_mGMkyxSv0dnC7ldF1wTZ8sRgXQIzYu2P4O3l2n0yKQ0H8BYcq86VOjbHAKAIFZY',
            'ConsoleReport' => true,
        ],
    ];
} elseif (env('APP_ENV', 'development') === 'production') {
    return [
        'FIREBASE_CONFIG' => [
            'firebaseConfig' => null,
            'VapidKey' => null,
            'ConsoleReport' => false,
        ],
    ];
}
