<?php
namespace Src\Config;

class DatabaseConnector
{
    private static $connection = null;

    private function __construct()
    {
    }

    public static function getConnection()
    {
        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT');
        $db = getenv('DB_DATABASE');
        $user = getenv('DB_USERNAME');
        $pass = getenv('DB_PASSWORD');

        if (!self::$connection) {
            try {
                self::$connection = new \PDO(
                    "pgsql:host=$host;port=$port;dbname=$db",
                    $user,
                    $pass
                );
            } catch (\PDOException $e) {
                die($e->getMessage());
            }
        }

        return self::$connection;
    }
}
?>
