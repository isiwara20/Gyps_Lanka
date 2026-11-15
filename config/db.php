<?php
// ==============================================================
// GYPS LANKA - Database Configuration & Auto-Setup
// ==============================================================

if (!ob_get_level()) {
    ob_start();
}

if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'gyps_lanka_db');

function get_db() {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    try {
        // Attempt connection directly to the database
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        // If database does not exist (code 1049) or connection refused, attempt auto-creation
        if ($e->getCode() == 1049 || strpos($e->getMessage(), 'Unknown database') !== false) {
            try {
                $serverDsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4";
                $serverPdo = new PDO($serverDsn, DB_USER, DB_PASS);
                $serverPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                
                // Now reconnect to new db and run database.sql
                $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);

                $sqlFile = __DIR__ . '/../database.sql';
                if (file_exists($sqlFile)) {
                    $sqlContent = file_get_contents($sqlFile);
                    // Split and execute SQL queries
                    $queries = array_filter(array_map('trim', explode(';', $sqlContent)));
                    foreach ($queries as $query) {
                        if (!empty($query)) {
                            try {
                                $pdo->exec($query);
                            } catch (Exception $ex) {
                                // Ignore non-critical duplicate errors during auto-import
                            }
                        }
                    }
                }
                return $pdo;
            } catch (Exception $setupEx) {
                error_log("Database auto-setup error: " . $setupEx->getMessage());
            }
        }
        
        // Log error and return null if MySQL service is not reachable
        error_log("Database connection error: " . $e->getMessage());
        return null;
    }
}

// Global cached settings helper
function get_setting($key, $default = '') {
    static $settings = null;
    if ($settings === null) {
        $db = get_db();
        if ($db) {
            try {
                $stmt = $db->query("SELECT setting_key, setting_value FROM settings");
                $settings = [];
                while ($row = $stmt->fetch()) {
                    $settings[$row['setting_key']] = $row['setting_value'];
                }
            } catch (Exception $e) {
                $settings = [];
            }
        } else {
            $settings = [];
        }
    }
    return isset($settings[$key]) ? $settings[$key] : $default;
}

