<?php
return [
    // Required only when each initial account is created for the first time.
    'passwords'=>[
        'admin'=>env('INITIAL_ADMIN_PASSWORD'),
        'petugas'=>env('INITIAL_PETUGAS_PASSWORD'),
        'pimpinan'=>env('INITIAL_PIMPINAN_PASSWORD'),
    ],
];
