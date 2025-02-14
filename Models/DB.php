<?php

namespace App\Models;

use Dotenv\Dotenv;
use Exception;
use PDO;
use RuntimeException;

/**
 * The DB class provides a singleton pattern for managing database connections using PDO.
 * It ensures a single connection instance throughout the application lifecycle and
 * handles connection configurations automatically.
 */
class DB
{
    /**
     * Holds the single instance of the PDO connection.
     *
     * @var PDO|null
     */
    private static ?PDO $pdo = null;

    /**
     * Private constructor to prevent direct instantiation.
     */
    private function __construct()
    {
    }

    /**
     * Default PDO connection options for error handling, fetch mode, and auto-commit.
     *
     * @var array
     */
    private static array $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        //PDO::ATTR_AUTOCOMMIT => false,
    ];

    /**
     * Establishes a MySQL connection using PDO.
     *
     * This method ensures a single instance of the PDO connection is created and reused.
     * It retrieves database configuration values from the environment and applies
     * pre-defined connection options.
     *
     * @return PDO The existing or newly created PDO instance.
     * @throws RuntimeException If any required database configuration variables are missing.
     */
    public static function getPDO(): PDO
    {
        // Check if a PDO connection is already established.
        if (isset(self::$pdo) && !empty(self::$pdo)) {
            return self::$pdo;
        }

        // Include the database configuration file
        include_once "config/database.php";

        $host = DB_HOST;
        $port = DB_PORT;
        $dbname = DB_NAME;
        $username = DB_USER;
        $password = DB_PASS;

        if (file_exists(".env")) {

            // Debug
            Logger::log("Dotenv file found successfully", __METHOD__, Level::DEBUG);

            $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
            $dotenv->load();

            // Load database configuration from environment variables.
            $host = $_ENV['DB_HOST'];
            $port = $_ENV['DB_PORT'];
            $dbname = $_ENV['DB_NAME'];
            $username = $_ENV['DB_USER'];
            $password = $_ENV['DB_PASS'];
        } else {
            // LOGGING
            Logger::log("Dotenv not found", __METHOD__, Level::WARNING);
        }

        // Debug
        Logger::log("mysql:host=$host;port=$port;dbname=$dbname;username=$username", __METHOD__, Level::DEBUG);

        // Ensure all required configuration variables are defined.
        if (!isset($host, $port, $dbname, $username, $password)) {

            // LOGGING -> Log the missing database configuration problem.
            Logger::log('Missing database configuration variables', __METHOD__);

            // Throw an exception to prevent the application from proceeding without valid settings.
            throw new RuntimeException('Missing database configuration variables');
        }

        // Create a Data Source Name (DSN) string for the MySQL connection.
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname";

        try {// Create a new PDO instance with the DSN, username, password, and connection options.
            self::$pdo = new PDO($dsn, $username, $password, self::$options);
        } catch (Exception $e) {

            // LOGGING
            Logger::log('Error connecting to database: ' . $e->getMessage(), __METHOD__);
            header('HTTP/1.0 500 Internal Server Error');
            die();
        }

        // Return the established PDO connection.
        return self::$pdo;
    }
}