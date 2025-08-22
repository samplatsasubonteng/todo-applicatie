<?php
class Database {
    private $host = 'localhost';
    private $dbname = 'todo'; // pas aan naar jouw lokale database
    private $username = 'root';
    private $password = '';
    private $conn;

    public function connect() {
        $this->conn = null;

        try {
            $dsn = "mysql:host=$this->host;dbname=$this->dbname;charset=utf8";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Verbinding mislukt: " . $e->getMessage();
        }

        return $this->conn;
    }
}
?>
