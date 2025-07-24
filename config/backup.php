<?php

return [
    'backup' => [
        'name' => env('APP_NAME', 'Rental-backup'),

        'source' => [
            'files' => [
                'include'       => [],
                'exclude'       => [],
                'relative_path' => base_path(),
            ],
            'databases' => ['mysql'],
        ],

        'database_dump_compressor' => null,

        'destination' => [
            'disks' => [
                'public_db',
            ],
        ],

        'backup' => [
            'filename' => 'database_backup_{date}.sql',
        ],
    ],
];
