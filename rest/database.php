<?php
// used to get mysql database connection
class Database{
 
    // specify your own database credentials
    private $host = "sql-ibu.adnan.dev:3306";
    private $db_name = "db_kenan14";
    private $username = "user_kenan90";
    private $password = "46F14DEC";
    public $conn;
 
    // get the database connection
    public function getConnection(){
 
        $this->conn = null;
 
        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
 
        return $this->conn;
    }
}
?>