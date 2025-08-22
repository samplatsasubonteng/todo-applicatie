<?php
class Database {
    private $host = '127.0.0.1';         
    private $port = 3306;                
    private $dbname = 'todo';           
    private $password = '';             
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
