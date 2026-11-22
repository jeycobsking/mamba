<?php
/**
 * Database Connection Manager (PDO)
 * Implements prepared statement enforcement and robust error handling
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'mamba_village_db');
define('DB_CHARSET', 'utf8mb4');

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                // If database doesn't exist, try connecting without DB to allow installer
                try {
                    $dsnNoDb = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
                    $tempPdo = new PDO($dsnNoDb, DB_USER, DB_PASS, $options);
                    $tempPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                    self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
                } catch (PDOException $innerException) {
                    die("Database connection failed. Please ensure MySQL is running in XAMPP: " . htmlspecialchars($innerException->getMessage()));
                }
            }
        }
        return self::$instance;
    }
}

// Global shortcut helper
function getDB(): PDO {
    return Database::getConnection();
}
