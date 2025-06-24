<?php
class Database {
    private $host = 'localhost'; 
    private $dbname = 'todo'; // dit is jouw databanknaam
    private $username = 'root';
    private $password = ''; 
    private $conn;

    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->dbname;charset=utf8", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Verbinding mislukt: " . $e->getMessage();
        }

        return $this->conn;
    }
}
?>
