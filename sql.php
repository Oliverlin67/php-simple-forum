<?php

include_once(__DIR__."/config.php");

class Sql {
    private $conn;

    public function __construct() {
        try {
            $this->conn = new \PDO("mysql:host=".Config::$db_host.";dbname=".Config::$db_name.";charset=utf8mb4", Config::$db_user, Config::$db_password);
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, 
                    \PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("SET time_zone = '+08:00'");
        } catch(\PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function query($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function insert($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $this->conn->lastInsertId();
    }

    public function update($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
    }

    public function delete($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
    }

    public function close() {
        $this->conn = null;
    }
}