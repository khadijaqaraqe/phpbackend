<?php
class Generalization{   
    private $generalizationTable = "rules_generalization";
    private $projectsAttachementTable ="projects_attachments";
    private $attachmentTable = "attachments";
    public $id; 
    public $Title;
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
            "SELECT  `".$this->generalizationTable."`.`id`, ".$this->generalizationTable.".`creator`, ".$this->generalizationTable.".`text`, ".$this->generalizationTable.".`title`, ".$this->generalizationTable.".`modified`, ".$this->generalizationTable.".`created`, `".$this->attachmentTable."`.`path`, `".$this->attachmentTable."`.`created`, `".$this->attachmentTable."`.`type`, `".$this->attachmentTable."`.`modified`
			FROM (".$this->generalizationTable." LEFT JOIN `".$this->projectsAttachementTable."` ON `".$this->projectsAttachementTable."`.`project_id` = ".$this->generalizationTable.".`id` LEFT JOIN `".$this->attachmentTable."` ON `".$this->projectsAttachementTable."`.`attach_id` = `".$this->attachmentTable."`.`id`)  
			WHERE ".$this->generalizationTable.".`id` ='".$data->id."'");
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}

	/* function read(){	
		$stmt = $this->conn->prepare("SELECT id, text, title, modified, created, creator 
        FROM `".$this->generalizationTable."`
        ORDER BY `".$this->generalizationTable."`.`modified` DESC;");		
		$stmt->execute();			
		$result = $stmt->get_result();
		return $result;	
	} */

    function read(){
		//
		if($this->id) {
			$stmt = $this->conn->prepare(
                "SELECT  `".$this->generalizationTable."`.`id`, `".$this->generalizationTable."`.`creator`, `".$this->generalizationTable."`.`text`, `".$this->generalizationTable."`.`title`, `".$this->generalizationTable."`.`modified`, `".$this->generalizationTable."`.`created`
                FROM `".$this->generalizationTable."`   
                WHERE `".$this->generalizationTable."`.`id` ='".$this->id."'");
		} else {
			$stmt = $this->conn->prepare("SELECT `id`, `text`, `title`, `modified`, `created`, `creator` 
                FROM `".$this->generalizationTable."`
                ORDER BY `".$this->generalizationTable."`.`modified` DESC;");		
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
		$this->Title = htmlspecialchars(strip_tags($this->Title));
		$this->Type = htmlspecialchars(strip_tags($this->Type));
		$stmt->bind_param("sss", 
			$this->Title, 
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
        $uuid = "generalization-".uniqid();
        $this->id =  $uuid;
        $stmt = $this->conn->prepare("INSERT INTO ".$this->generalizationTable." (`id`, `creator`, `text`, `created`, `modified`, `title`)
        VALUES (?, ?, ?, NOW(), NOW(), ?);");
       
        $this->Text = htmlspecialchars(strip_tags($this->Text));
        $this->Title = htmlspecialchars(strip_tags($this->Title));
       

        $stmt->bind_param("siss", 
            $this->id,
            $this->Creator,
            $this->Text,
            $this->Title); 
        
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
           $this->Title = htmlspecialchars(strip_tags($this->Title));
           $this->Text = htmlspecialchars($this->Text);
           
           $stmt = $this->conn->prepare("UPDATE `".$this->generalizationTable."` 
           SET `title` = ?, `text` = ?
           WHERE `".$this->generalizationTable."`.`id` = ?;");
   
           $stmt->bind_param("sss", 
               $this->Title, 
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
               $stmt2 = $this->conn->prepare("DELETE FROM `".$this->generalizationTable."` WHERE `".$this->generalizationTable."`.`id` = ?");
           
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