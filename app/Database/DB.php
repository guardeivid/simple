<?php

namespace App\Database;

use \PDO;

class DB
{
    

    public function __construct()
    {

    }

    private static function connect()
    {
        $controller = 'pgsql';
        $dbname = 'ada';
        $host = 'localhost';
        $port = '5432';
        $user = 'postgres';
        $pass = 'ale';

        try {
            $pdo = new PDO("$controller:dbname=$dbname;host=$host;port=$port", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_EMPTY_STRING);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            //$dbh->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
            return $pdo;
        } catch (PDOException $e) {
            //echo "{\"error\": \"" . $e->getMessage() . "\"}";
            exit();
        }
    }

    public static function query($query, $params = [], $all = true)
    {
        $stmt = self::connect()->prepare($query);
        $params = is_array($params) ? $params : [$params];
        $stmt->execute($params);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            $error = $e->getMessage();
        }
        
        if ($all) {
        // Return all
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
        // Return one
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }

}