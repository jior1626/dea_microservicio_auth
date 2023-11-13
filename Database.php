<?php
//Dea.php
class Database {
    private $host = "desarrollodea.postgres.database.azure.com";
    private $db_name = "DEA";
    private $username = "administrador";
    private $password = "Urbanik2023*";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("pgsql:host=$this->host;dbname=$this->db_name", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Connection error: " . $e->getMessage();
        }
        return $this->conn;
    }
}

?>
