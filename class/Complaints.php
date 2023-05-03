<?php
class Complaints{   
    
   
  
    public $Name;
    public $id; 
    public $PhoneNumber;
    public $Topic;
    public $ComplaintText;   
    public $UserId;  
    public $Email;  
    public $Association;
    public $Description;

    public $attachmentID= array();
    public $fileId;
    public $Path; 
    public $Type;
    private $conn;
    
	
    public function __construct($db){
        $this->conn = $db;
    }	
	function readOne($data){	
		$stmt = $this->conn->prepare(
            "SELECT  Complaint.ID, Complaint.Name, Complaint.PhoneNumber, Complaint.Topic, Complaint.Association, Complaint.ComplaintText, Complaint.ComplaintDate, Complaint.Email, Complaint.UserId,  Attachments.path, Attachments.created, Attachments.type, Attachments.modified
			FROM (Complaint  LEFT JOIN ComplaintAttachments ON ComplaintAttachments.compId = Complaint.ID LEFT JOIN Attachments ON ComplaintAttachments.attachId = Attachments.id)  
			WHERE Complaint.ID ='".$data->id."'");
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}

	function read(){	
		$stmt = $this->conn->prepare("SELECT ID, Name, PhoneNumber, Topic, Association, ComplaintText, ComplaintDate, Email, UserId FROM Complaint;");		
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}

    function createAttachments() {
		
		$stmt = $this->conn->prepare("INSERT INTO `attachments` ( `description`, `path`, `type`) VALUES 
			( ?, ?, ?);");		
		$this->Description = htmlspecialchars(strip_tags($this->Description));
		$this->Type = htmlspecialchars(strip_tags($this->Type));
		
		$stmt->bind_param("sss", 
			$this->Description, 
			$this->Path, 
			$this->Type 
		);
	
		if($stmt->execute() === true){

			$attachment_id = $stmt->insert_id;
			array_push($this->attachmentID, $attachment_id);
			$this->fileId = $attachment_id;
			return true;
		}
		return false;			
	}
	
	function create() {
        $stmt = $this->conn->prepare("INSERT INTO Complaint ( Name, PhoneNumber, Topic, Association, ComplaintText, ComplaintDate, Email, UserId) 
        VALUES (?, ?, ?, ?, ?, NOW(), ?, ?);");
       
        $this->Name = htmlspecialchars(strip_tags($this->Name));
        $this->Topic = htmlspecialchars(strip_tags($this->Topic));
        $this->ComplaintText = htmlspecialchars(strip_tags($this->ComplaintText));

        $stmt->bind_param("sissssi", 
            $this->Name,
            $this->PhoneNumber,
            $this->Topic, 
            $this->Association,
            $this->ComplaintText,  
            $this->Email, 
            $this->UserId); 
        
        if($stmt->execute()===true) {	
           
            $this->id =  $stmt->insert_id;
            if ($this->attachmentID) {
                foreach($this->attachmentID as $key => $value)
                {
                    
                    $statement = "INSERT INTO `ComplaintAttachments` 
                    (`compID`, `attachId`) 
                    VALUES ";
                    $valuesStmt = "";
                    foreach($this->attachmentID as $key => $value)
                    {
                        $valuesStmt .=  "(  
                            '".$this->id."', 
                            '". $value."'), ";
                    };
                    $updatedValue = substr($valuesStmt, 0, -2);
                    $stmt3 = $this->conn->prepare($statement.$updatedValue.";");
                    
                    if($stmt3->execute()){
                        return true; 
                    }
                    return false;
                }
            } else { 
                return true;}
            
            }
        return false;	
	}
		
}
?>