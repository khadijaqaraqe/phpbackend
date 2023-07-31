<?php
class Vision{   
    private $govTable = "vision";
    private $projectsAttachementTable ="projects_attachments";
    private $attachmentTable = "attachments";
    public $id; 
    
    public $Text;   
    Public $Creator;
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
            "SELECT  `".$this->govTable."`.`id`, ".$this->govTable.".`creator`, ".$this->govTable.".`text`, ".$this->govTable.".`modified`, ".$this->govTable.".`created`, `".$this->attachmentTable."`.`path`, `".$this->attachmentTable."`.`created`, `".$this->attachmentTable."`.`type`, `".$this->attachmentTable."`.`modified`
			FROM (".$this->govTable." LEFT JOIN `".$this->projectsAttachementTable."` ON `".$this->projectsAttachementTable."`.`project_id` = ".$this->govTable.".`id` LEFT JOIN `".$this->attachmentTable."` ON `".$this->projectsAttachementTable."`.`attach_id` = `".$this->attachmentTable."`.`id`)  
			WHERE ".$this->govTable.".`id` ='".$data->id."'");
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}
    function readLastAdded(){
		
        $stmt = $this->conn->prepare("SELECT `id`, `text`, `modified`, `created`, `creator` 
            FROM `".$this->govTable."`
            ORDER BY `".$this->govTable."`.`modified` DESC LIMIT 1;");		
				
		if ($stmt->execute()) {
			$result = $stmt->get_result();	
			return $result;	
		} else { 
			return false;
		}			
			
	}

    function read(){
		//
		if($this->id) {
			$stmt = $this->conn->prepare(
                "SELECT  `".$this->govTable."`.`id`, `".$this->govTable."`.`creator`, `".$this->govTable."`.`text`, `".$this->govTable."`.`modified`, `".$this->govTable."`.`created`
                FROM `".$this->govTable."`   
                WHERE `".$this->govTable."`.`id` ='".$this->id."'");
		} else {
			$stmt = $this->conn->prepare("SELECT `id`, `text`, `modified`, `created`, `creator` 
                FROM `".$this->govTable."`
                ORDER BY `".$this->govTable."`.`modified` DESC;");		
		}		
		if ($stmt->execute()) {
			$result = $stmt->get_result();	
			return $result;	
		} else { 
			return false;
		}			
			
	}
    function getImages($item) {
        $stmt2 = $this->conn->prepare(" SELECT DISTINCT `".$this->attachmentTable."`.`path`, `".$this->attachmentTable."`.`type`, `".$this->attachmentTable."`.`description`, `".$this->attachmentTable."`.`created`
        FROM `".$this->attachmentTable."` LEFT JOIN `".$this->projectsAttachementTable."` ON `".$this->projectsAttachementTable."`.`attach_id` = `".$this->attachmentTable."`.`id`
        WHERE `".$this->projectsAttachementTable."`.`attach_id` = `".$this->attachmentTable."`.id AND `".$this->projectsAttachementTable."`.`project_id`= '".strval($item)."'");
		if($stmt2->execute() === true){
			$results2 = $stmt2->get_result();
			return $results2;
		}
	}

    function createAttachments() {
		$stmt = $this->conn->prepare("INSERT INTO `attachments` (`description`, `path`, `type`) VALUES 
			( ?, ?, ?);");		
		$this->id = htmlspecialchars(strip_tags($this->id));
		$this->Type = htmlspecialchars(strip_tags($this->Type));
		$stmt->bind_param("sss", 
			$this->id, 
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
        $uuid = "vision-".uniqid();
        $this->id =  $uuid;
        $stmt = $this->conn->prepare("INSERT INTO ".$this->govTable." (`id`, `creator`, `text`, `created`, `modified`)
        VALUES (?, ?, ?, NOW(), NOW());");
       
        $this->Text = htmlspecialchars(strip_tags($this->Text));
       
       

        $stmt->bind_param("sis", 
            $this->id,
            $this->Creator,
            $this->Text); 
        
        if($stmt->execute()===true) {	
           
           
            if ($this->attachmentID) {
                foreach($this->attachmentID as $key => $value)
                {
                    
                    $statement = "INSERT INTO `".$this->projectsAttachementTable."` 
                    (`project_id`, `attach_id`) 
                    VALUES ";
                    $valuesStmt = "";
                    foreach($this->attachmentID as $key => $value)
                    {
                        echo $this->id;
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

    function update(){
       
           $this->id = htmlspecialchars(strip_tags($this->id));
           $this->Text = htmlspecialchars($this->Text);
           
           $stmt = $this->conn->prepare("UPDATE `".$this->govTable."` 
           SET `text` = ?
           WHERE `".$this->govTable."`.`id` = ?;");
   
           $stmt->bind_param("ss", 
           
               $this->Text,
               $this->id
           );
           
           if($stmt->execute()){
               return true;
           }

           return false;
       }
       
       function delete(){
           
           $stmt = $this->conn->prepare("DELETE FROM `".$this->projectsAttachementTable."` WHERE `".$this->projectsAttachementTable."`.`project_id` = ?");
               
           $this->id = htmlspecialchars(strip_tags($this->id));
        
           $stmt->bind_param("s", $this->id);
        
           if($stmt->execute()){
               $stmt2 = $this->conn->prepare("DELETE FROM `".$this->govTable."` WHERE `".$this->govTable."`.`id` = ?");
           
               $stmt2->bind_param("s", $this->id);
   
               if ($stmt2->execute()) {
                   return true;
               } else {
                   return false;
               }
           } else {
               return false;		
           }
            
       }
		
}
?>