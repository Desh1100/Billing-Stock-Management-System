<?php
if(!defined('DB_SERVER')){
    require_once("../initialize.php");
}
class DBConnection{

    private $host = h40lg7qyub2umdvb.cbetxkdyhwsb.us-east-1.rds.amazonaws.com;
    private $username = bzsh8yszz4s9m78o;
    private $password = mmpdegiotfn2enmm;
    private $database = kytnlimt8p7iponx;
    
    public $conn;
    
    public function __construct(){

        if (!isset($this->conn)) {
            
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
            
            if (!$this->conn) {
                echo 'Cannot connect to database server';
                exit;
            }            
        }    
        
    }
    public function __destruct(){
        $this->conn->close();
    }
}
?>
