<?php
class Database {
    // Gegevens rechtstreeks uit je Railway MYSQL vars
    private $host = 'shortline.proxy.rlwy.net';
    private $port = 40104; // dit is het stukje na de :
    private $dbname = 'railway'; 
    private $username = 'root';
    private $password = 'SRrXpAvTyXFdpQniREtlIbqwBLDthm'; // exact uit MYSQL_ROOT_PASSWORD
    private $conn;

    public function connect() {
        $this->conn = null;

        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Verbinding mislukt: " . $e->getMessage();
        }

        return $this->conn;
    }
}
?>
