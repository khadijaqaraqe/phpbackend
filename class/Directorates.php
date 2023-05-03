<?php
class Directorates{   
    
   
    public $id;
    public $title;
    public $creator;
    public $text;
    public $Name; 
    public $PhoneNumber;
    Public $Email;
    Public $UserId;
    public $category_id;   
    public $created; 
	public $modified; 
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
	
        $stmt = $this->conn->prepare("INSERT INTO Directorates ( Name, PhoneNumber, Topic, AssociationID, ComplaintText, ComplaintDate, Email, UserId) 
        VALUES ( ?, ?, ?, NULL, ?, NOW(), ?, ?);");
        
        $this->Name = htmlspecialchars(strip_tags($this->Name));
        $this->PhoneNumber = $this->PhoneNumber;
        $this->created = htmlspecialchars(strip_tags($this->created));
        $this->creator = $this->creator;
        $this->Email = $this->Email;
        $this->UserId = $this->UserId;

        $stmt->bind_param("sisssi", 
        $this->Name,
        $this->PhoneNumber,
        $this->creator, 
        $this->creator,  
        $this->Email, 
        $this->UserId); 
        
        if($stmt->execute()===true) {	
            return true; 	
        }
        return false;	
	}
		
}
?>