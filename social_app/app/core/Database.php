<?php
/**
 * Database - PDO singleton. Models call Database::connect().
 */
class Database
{
    private static ?PDO $pdo = null;

    public static function connect(): PDO
    {
        if (self::$pdo === null) {
            $c   = require __DIR__ . '/../../config/database.php';
            $dsn = "mysql:host={$c['host']};dbname={$c['dbname']};charset={$c['charset']}";
            try {
                self::$pdo = new PDO($dsn, $c['user'], $c['pass'], [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '+08:00'",
                ]);
            } catch (PDOException $e) {
                http_response_code(500);
                exit('Database connection failed. Check config/database.php and make sure you imported sql/social_app.sql.');
            }
        }
        return self::$pdo;
    }
}
