<?php

declare(strict_types=1);

return [
    'driver' => getenv('DB_DRIVER') ?: 'sqlite',
    'mysql' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => getenv('DB_PORT') ?: '3306',
        'database' => getenv('DB_DATABASE') ?: 'katalog_kafe',
        'username' => getenv('DB_USERNAME') ?: 'root',
        'password' => getenv('DB_PASSWORD') ?: '',
        'charset' => 'utf8mb4',
    ],
    'sqlite' => [
        'path' => getenv('DB_PATH') ?: dirname(__DIR__) . '/storage/katalog_kafe.sqlite',
    ],
];
