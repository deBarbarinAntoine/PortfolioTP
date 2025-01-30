<?php

namespace models;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../.env');
$dotenv->load();
use PDO;

class DB
{
    private static ?PDO $pdo = null;

    private function __construct() {}
    private static array $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_AUTOCOMMIT => false,
    ];

    /**
     * Establishes a MySQL connection using PDO.
     *
     * @return PDO The existing or newly created PDO instance
     */
    public static function getPDO(): PDO
    {
        if (isset(self::$pdo) && !empty(self::$pdo)) {
            return self::$pdo;
        }

        $host = $_ENV['DB_HOST'];
        $port = $_ENV['DB_PORT'];
        $dbname = $_ENV['DB_NAME'];
        $username = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASS'];

        if (!\defined('DB_NAME') || !\defined('DB_USER') || !\defined('DB_PASS')) {
            throw new \RuntimeException('Missing database configuration variables');
        }

        $dsn = "mysql:host=$host;port=$port;dbname=$dbname";

        self::$pdo = new PDO($dsn, $username, $password, self::$options);

        return self::$pdo;
    }
}