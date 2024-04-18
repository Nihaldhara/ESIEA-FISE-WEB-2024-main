<?php

class Database {
    private $host = "localhost";      // Your database host, usually 'localhost'
    private $dbname = "esiea_web";  // Your database name
    private $user = "root";  // Your database username, default is 'root' in WAMP
    private $password = "";  // Your database password, default is '' (empty) in WAMP

    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbname, $this->user, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Database connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
