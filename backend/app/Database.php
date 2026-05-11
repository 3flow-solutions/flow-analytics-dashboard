<?php

namespace App;

class Database {
    private static $connection = null;
    private static $store_id = null;
    private static $store_url = null;

    public static function getConnection() {
        if (self::$connection === null) {
            $config = require __DIR__ . '/../../config/database.php';
            self::$connection = $config['connection'];
            self::$store_id = $config['store_id'];
            self::$store_url = $config['store_url'];
        }
        return self::$connection;
    }

    public static function getStoreId() {
        self::getConnection();
        return self::$store_id;
    }

    public static function getStoreUrl() {
        self::getConnection();
        return self::$store_url;
    }

    public static function query($sql) {
        $conn = self::getConnection();
        return mysqli_query($conn, $sql);
    }

    public static function escape($value) {
        $conn = self::getConnection();
        return mysqli_real_escape_string($conn, $value);
    }

    public static function fetchArray($result) {
        return mysqli_fetch_array($result);
    }

    public static function fetchAssoc($result) {
        return mysqli_fetch_assoc($result);
    }
}
