<?php

namespace models;

class DB
{
    private static $options = [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_AUTOCOMMIT => false,
    ];

    /**
     * Establishes a MySQL connection using PDO.
     *
     * @return \PDO The existing or newly created PDO instance
     */
    public static function new(): \PDO
    {
        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT');
        $dbname = getenv('DB_NAME');
        $username = getenv('DB_USERNAME');
        $password = getenv('DB_PASSWORD');

        if (!\defined('DB_NAME') || !\defined('DB_USERNAME') || !\defined('DB_PASSWORD')) {
            throw new \RuntimeException('Missing database configuration variables');
        }

        $dsn = "mysql:host=$host;port=$port;dbname=$dbname";
        return new \PDO($dsn, $username, $password, DB::$options);
    }
}