<?php
class Complaints{   
    
   
  
    public $Name;
    public $PhoneNumber;
    public $Topic;
    public $ComplaintText;   
    public $UserId;  

    private $conn;
	
    public function __construct($db){
        $this->conn = $db;
    }	
	
	function read(){	
		
		$stmt = $this->conn->prepare("SELECT ID, Name, PhoneNumber, Topic, AssociationID, ComplaintText, ComplaintDate, Email, UserId FROM Complaint;");		
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}
	
	function create() {
	
        $stmt = $this->conn->prepare("INSERT INTO Complaint (ID, Name, PhoneNumber, Topic, AssociationID, ComplaintText, ComplaintDate, Email, UserId) 
        VALUES (NULL, ?, ?, ?, NULL, ?, NOW(), NULL, ?);");
        
        $this->Name = htmlspecialchars(strip_tags($this->Name));
        $this->PhoneNumber = $this->PhoneNumber;
        $this->Topic = htmlspecialchars(strip_tags($this->Topic));
        $this->ComplaintText = $this->ComplaintText;
        //$this->Email = $this->Email;
        $this->UserId = $this->UserId;

        $stmt->bind_param("sissi", 
        $this->Name,
        $this->PhoneNumber,
        $this->Topic, 
        $this->ComplaintText,  
       // $this->Email, 
        $this->UserId); 
        
        if($stmt->execute()===true) {	
            return true; 	
        }
        return false;	
	}
		
}
?>