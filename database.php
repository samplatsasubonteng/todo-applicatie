<?php
class Database {
    private string $host = 'ID474795_toDoApp.db.webhosting.be'; 
    private string $dbname = 'ID474795_toDoApp';   
    private string $username = 'ID474795_toDoApp'; 
    private string $password = 'E0rR3Iw6Ko5Q26Ua5k2m';      
    private ?PDO   $conn = null;

    public function connect(): ?PDO {
        if ($this->conn instanceof PDO) {
            return $this->conn;
        }

        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            echo "Verbinding mislukt: " . htmlspecialchars($e->getMessage());
            $this->conn = null;
        }

        return $this->conn;
    }
}
