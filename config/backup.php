<?php

return [
    'backup' => [
        'name' => env('APP_NAME', 'Rental-backup'),

        'source' => [
            'files' => [
                'include' => [], // ✅ Exclude all files
                'exclude' => [],
                'relative_path' => base_path(),
            ],
            'databases' => ['mysql'], // ✅ Backup only the database
        ],

        'database_dump_compressor' => null, // ✅ Ensure raw SQL is stored

        'destination' => [
            'disks' => [
                'public_db', // ✅ Store SQL backups separately
            ],
        ],

        'backup' => [
            'filename' => 'database_backup_{date}.sql', // ✅ Ensure raw SQL filename
        ],
    ],
];
