<?php
class Complaints{   
    
   
    private $complaintTable = "complaint";
    private $complaintAttachementTable ="complaint_attachments";
    private $attachmentTable = "attachments";
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
            "SELECT  `".$this->complaintTable."`.id, ".$this->complaintTable.".name, ".$this->complaintTable.".phone_number, ".$this->complaintTable.".topic, ".$this->complaintTable.".association, ".$this->complaintTable.".complaint_text, ".$this->complaintTable.".complaint_date, ".$this->complaintTable.".email, ".$this->complaintTable.".user_id,  `".$this->attachmentTable."`.path, `".$this->attachmentTable."`.created, `".$this->attachmentTable."`.type, `".$this->attachmentTable."`.modified
			FROM (".$this->complaintTable." LEFT JOIN `".$this->complaintAttachementTable."` ON `".$this->complaintAttachementTable."`.comp_id = ".$this->complaintTable.".id LEFT JOIN `".$this->attachmentTable."` ON `".$this->complaintAttachementTable."`.attach_id = `".$this->attachmentTable."`.id)  
			WHERE ".$this->complaintTable.".id ='".$data->id."'");
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}

	function read(){	
		$stmt = $this->conn->prepare("SELECT id, name, phone_number, topic, association, complaint_text, complaint_date, email, user_id 
        FROM `".$this->complaintTable."`
        ORDER BY `".$this->complaintTable."`.`complaint_date` DESC ;");		
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
        $stmt = $this->conn->prepare("INSERT INTO ".$this->complaintTable." ( name, phone_number, topic, association, complaint_text, complaint_date, email, user_id) 
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
                    
                    $statement = "INSERT INTO `".$this->complaintAttachementTable."` 
                    (`comp_id`, `attach_id`) 
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