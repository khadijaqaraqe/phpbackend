<?php

class Partners{   	
	private $partnerTable =  "partners";    
	private $partnersAttachmentsTable = "partners_attachments";
	private $usersTable = "users";
	private $attachmentsTable = "attachments";
	
    public $id;

    public $name;
    public $minister;
    public $url;
    public $description;
    public $created_at; 
    public $modified_at;
    public $facebook_url;
	public $instagram_url;
    public $youtube_url;
	public $partner_id;
    public $attach_id;
    public $image;
	public $imagesID= array();
	public $path;
	public $type;
    public $image_id;

	private $conn;
	
    public function __construct($db){
        $this->conn = $db;
    }	

	function getImages($item) {
        $stmt2 = $this->conn->prepare(" SELECT DISTINCT `".$this->attachmentsTable."`.`path`, `".$this->attachmentsTable."`.`type`, `".$this->attachmentsTable."`.`description`, `".$this->attachmentsTable."`.`created`
        FROM `".$this->attachmentsTable."` LEFT JOIN `".$this->partnersAttachmentsTable."` ON `".$this->partnersAttachmentsTable."`.`attach_id` = `".$this->attachmentsTable."`.`id`
        WHERE `".$this->partnersAttachmentsTable."`.`attach_id` = `".$this->attachmentsTable."`.id AND `".$this->partnersAttachmentsTable."`.`partner_id`= '".strval($item)."'");
		if($stmt2->execute() === true){
			$results2 = $stmt2->get_result();
			return $results2;
		}
	}

	
	function read(){
		//
		if($this->id) {
			$stmt = $this->conn->prepare("SELECT 
                `".$this->partnerTable."`.`id`,`".$this->partnerTable."`.`name`,`".$this->partnerTable."`.`url`,
                `".$this->partnerTable."`.`minister`,`".$this->partnerTable."`.`description`,`".$this->partnerTable."`.`facebook_url`,
                `".$this->partnerTable."`.`instagram_url`,`".$this->partnerTable."`.`youtube_url`,`".$this->partnerTable."`.`created_at`,
                `".$this->partnerTable."`.`modified_at`
                FROM `".$this->partnerTable."`  
                WHERE `".$this->partnerTable."`.`id` = '".strval($this->id)."'
                ORDER BY `".$this->partnerTable."`.created_at ASC");
							
		} else {
			
			$stmt = $this->conn->prepare("SELECT 
                `".$this->partnerTable."`.`id`,`".$this->partnerTable."`.`name`,`".$this->partnerTable."`.`url`,
                `".$this->partnerTable."`.`minister`,`".$this->partnerTable."`.`description`,`".$this->partnerTable."`.`facebook_url`,
                `".$this->partnerTable."`.`instagram_url`,`".$this->partnerTable."`.`youtube_url`,`".$this->partnerTable."`.`created_at`,
                `".$this->partnerTable."`.`modified_at`
			FROM `".$this->partnerTable."`
			ORDER BY `".$this->partnerTable."`.modified_at DESC, `".$this->partnerTable."`.created_at DESC 
			LIMIT 2000;");		
		}		
		if ($stmt->execute()) {
			$result = $stmt->get_result();	
			return $result;	
		} else { 
			return false;
		}			
			
	}

	
    

	function createImages() {
		
		$stmt2 = $this->conn->prepare("INSERT INTO `".$this->attachmentsTable."` (`description`, `path`, `type`) VALUES 
			( ?, ?, ?);");		
		$this->description = htmlspecialchars(strip_tags($this->description));
		$this->type = htmlspecialchars(strip_tags($this->type));
		
		$stmt2->bind_param("sss", 
			$this->description, 
			$this->path, 
			$this->type 
		);
	
		if($stmt2->execute() === true){

			$image_id = $stmt2->insert_id;;
			array_push($this->imagesID, $image_id);
			$this->image_id = $image_id;
			
			return true;
		}
		return false;			
	}
						 
	function create() {
		$uuid = "partner-".uniqid();
        $this->id =  $uuid;
        $this->name = htmlspecialchars($this->name);
        $this->minister = htmlspecialchars($this->minister);
        $this->url = htmlspecialchars($this->url);
        $this->description = htmlspecialchars($this->description);

			$stmt = $this->conn->prepare("INSERT INTO `".$this->partnerTable."` 
            (`id`, `name`, `url`, `minister`, `description`,`youtube_url`, `facebook_url`, `instagram_url`, `created_at`, `modified_at`) 
             VALUES 
			(?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW());");
			

			$stmt->bind_param("ssssssss", 
                $this->id,
                $this->name,
                $this->url,
                $this->minister,
                $this->description,
                $this->youtube_url,
                $this->facebook_url,
                $this->instagram_url
			); 
			if($stmt->execute()===true) {
					$this->description = htmlspecialchars(strip_tags($this->description));
				foreach($this->imagesID as $key => $value)
				{
					
                    $statement = "INSERT INTO `".$this->partnersAttachmentsTable."` (`partner_id`, `attach_id`) 
                    VALUES ";
                    $valuesStmt = "";
                    foreach($this->imagesID as $key => $value)
                        {
                            $valuesStmt .=  "(  
                                '".$uuid."', 
                                '". $value."'), ";
                        };
                        $updatedValue = substr($valuesStmt, 0, -2);
                        $stmt3 = $this->conn->prepare($statement.$updatedValue.";");
                    
                        if($stmt3->execute()){
                            return true; 
                        }
                        return false;
				}
			}
			
		return false;	 
		
	}
		
	function update(){
	
        $this->id 				= htmlspecialchars(strip_tags($this->id));
		$this->name 			= htmlspecialchars(strip_tags($this->name));
        $this->url 				= htmlspecialchars($this->url);
        $this->minister 		= htmlspecialchars($this->minister);
        $this->description  	= htmlspecialchars($this->description);
        $this->facebook_url 	= htmlspecialchars($this->facebook_url);
        $this->instagram_url 	= htmlspecialchars($this->instagram_url);
        $this->youtube_url  	= htmlspecialchars($this->youtube_url);
		$stmt = $this->conn->prepare("UPDATE `".$this->partnerTable."` 
        SET `name` = ?, `url` = ?, `minister`= ?, `description`= ?,
        `facebook_url` = ?, `instagram_url`= ?, `youtube_url`= ?
        WHERE `".$this->partnerTable."`.`id` = ?;");

		$stmt->bind_param("ssssssss", 
            $this->name, 
            $this->url,
            $this->minister,
            $this->description, 
            $this->facebook_url,
            $this->instagram_url,
            $this->youtube_url,
            $this->id
        );
		
		if($stmt->execute()){
			return true;
		}
	 
		return false;
	}
	
	function delete(){
		
		$stmt = $this->conn->prepare("DELETE FROM `".$this->partnersAttachmentsTable."` WHERE `".$this->partnersAttachmentsTable."`.`partner_id` = ?");
			
		$this->id = htmlspecialchars(strip_tags($this->id));
	 
		$stmt->bind_param("s", $this->id);
	 
		if($stmt->execute()){
			$stmt2 = $this->conn->prepare("DELETE FROM `".$this->partnerTable."` WHERE `".$this->partnerTable."`.`id` = ?");
		
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