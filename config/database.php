<?php
class Database{
	
	private $host  = '10.102.70.135';
    private $user  = "gcc10_kqaraqe";
    private $password   = "N2c-v}fSq(,$";
    private $database  = "gcc10_BethlehemGov"; 
    private  $port = 3306;
    
    
    public function getConnection(){		
		$conn = new mysqli($this->host, $this->user, $this->password, $this->database, $this->port);
		if($conn->connect_error){
			die("Error failed to connect to MySQL: " . $conn->connect_error);
		} else {
			return $conn;
		}
    }
}
?>