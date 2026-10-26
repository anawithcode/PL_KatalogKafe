<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use RuntimeException;

final class Database
{
    private static ?PDO $connection = null;

    public static function connection(array $config): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $driver = $config['driver'];
        if ($driver === 'mysql') {
            $mysql = $config['mysql'];
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $mysql['host'],
                $mysql['port'],
                $mysql['database'],
                $mysql['charset']
            );
            $pdo = new PDO($dsn, $mysql['username'], $mysql['password'], self::options());
        } elseif ($driver === 'sqlite') {
            $path = $config['sqlite']['path'];
            $directory = dirname($path);
            if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
                throw new RuntimeException('Folder database tidak dapat dibuat.');
            }
            $isNew = !file_exists($path);
            $pdo = new PDO('sqlite:' . $path, null, null, self::options());
            $pdo->exec('PRAGMA foreign_keys = ON');
            if ($isNew) {
                self::initializeSqlite($pdo);
            }
        } else {
            throw new RuntimeException('Driver database tidak didukung.');
        }

        return self::$connection = $pdo;
    }

    private static function options(): array
    {
        return [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
    }

    private static function initializeSqlite(PDO $pdo): void
    {
        $schema = file_get_contents(dirname(__DIR__, 2) . '/database/schema.sqlite.sql');
        if ($schema === false) {
            throw new RuntimeException('Skema SQLite tidak ditemukan.');
        }
        $pdo->exec($schema);
    }
}
