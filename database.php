<?php
class Database {
    private $host = 'maglev.proxy.rlwy.net';
    private $port = 48028;
    private $dbname = 'railway';
    private $username = 'root';
    private $password = 'zBtImbWqqbjTqpcreTzBDcBvzAhLEJxD';
    private $conn;

    public function connect() {
        $this->conn = null;

        try {
            $dsn = "mysql:host=$this->host;port=$this->port;dbname=$this->dbname;charset=utf8";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Verbinding mislukt: " . $e->getMessage();
        }

        return $this->conn;
    }
}
?>
