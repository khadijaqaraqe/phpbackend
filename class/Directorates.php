<?php

class Directorates{   
    
	
	private $directorateTable =  "directorate";    
	private $directorateAttachmentsTable = "directorate_attachments";
	private $usersTable = "users";
	private $attachmentsTable = "attachments";
	
    public $id;
    public $title;
    public $director;
    public $url;
    public $phone_number1;
    public $fax_num;   
	public $facebook_url;
	public $instagram_url;
    public $phone_number2; 
	public $twitter_url;
    public $linkedin_url;
    public $whatsapp_url; 
    public $youtube_url;
    public $created_at; 
    public $modified_at;
    public $text;
	public $directorate_id;
    public $attach_id;
    public $image;
	public $imagesID= array();
	public $path;
    public $description;
	public $type;
    public $image_id;
	public $writer;
	private $conn;
	
    public function __construct($db){
        $this->conn = $db;
    }	

	function getImages($item) {
        $stmt2 = $this->conn->prepare(" SELECT DISTINCT `".$this->attachmentsTable."`.`path`, `".$this->attachmentsTable."`.`type`, `".$this->attachmentsTable."`.`description`, `".$this->attachmentsTable."`.`created`
        FROM `".$this->attachmentsTable."` LEFT JOIN `".$this->directorateAttachmentsTable."` ON `".$this->directorateAttachmentsTable."`.`attach_id` = `".$this->attachmentsTable."`.`id`
        WHERE `".$this->directorateAttachmentsTable."`.`attach_id` = `".$this->attachmentsTable."`.id AND `".$this->directorateAttachmentsTable."`.`directorate_id`= '".strval($item)."'");
		if($stmt2->execute() === true){
			$results2 = $stmt2->get_result();
			return $results2;
		}
	}

	
	function read(){
		//
		if($this->id) {
			$stmt = $this->conn->prepare("SELECT 
                `".$this->directorateTable."`.`id`,`".$this->directorateTable."`.`title`,`".$this->directorateTable."`.`director`,
                `".$this->directorateTable."`.`url`,`".$this->directorateTable."`.`phone_number1`,`".$this->directorateTable."`.`fax_num`,
                `".$this->directorateTable."`.`facebook_url`,`".$this->directorateTable."`.`instagram_url`,`".$this->directorateTable."`.`phone_number2`,
                `".$this->directorateTable."`.`twitter_url`,`".$this->directorateTable."`.`linkedin_url`,`".$this->directorateTable."`.`whatsapp_url`,
                `".$this->directorateTable."`.`youtube_url`,`".$this->directorateTable."`.`created_at`,`".$this->directorateTable."`.`modified_at`,
                `".$this->directorateTable."`.`text`, `".$this->directorateTable."`.`description`
                FROM `".$this->directorateTable."`  
                WHERE `".$this->directorateTable."`.`id` = '".strval($this->id)."'
                ORDER BY `".$this->directorateTable."`.created_at ASC");
			//$updatedID = "'".$this->id."'";
			//$stmt->bind_param("s", $this->id);					
		} else {
			// .$this->usersTable."`.first_name, `".$this->usersTable."`.last_name, `".$this->typesTable."`.name, `".$this->attachmentsTable."`.path, `".$this->attachmentsTable."`.`type`, `".$this->attachmentsTable."`.image, `".$this->attachmentsTable."`.created_at 
			// ` LEFT JOIN `".$this->directorateAttachmentsTable."` ON `".$this->directorateAttachmentsTable."`.directorate_id = `".$this->directorateTable."`.id LEFT JOIN `".$this->attachmentsTable."` ON `".$this->directorateAttachmentsTable."`.image_id = `".$this->attachmentsTable."`.id, `".$this->typesTable."`, `".$this->usersTable."` 
			// AND `".$this->directorateTable."`.creator = `".$this->usersTable."`.id
			$stmt = $this->conn->prepare("SELECT 
            `".$this->directorateTable."`.`id`,`".$this->directorateTable."`.`title`,`".$this->directorateTable."`.`director`,
            `".$this->directorateTable."`.`url`,`".$this->directorateTable."`.`phone_number1`,`".$this->directorateTable."`.`fax_num`,
            `".$this->directorateTable."`.`facebook_url`,`".$this->directorateTable."`.`instagram_url`,`".$this->directorateTable."`.`phone_number2`,
            `".$this->directorateTable."`.`twitter_url`,`".$this->directorateTable."`.`linkedin_url`,`".$this->directorateTable."`.`whatsapp_url`,
            `".$this->directorateTable."`.`youtube_url`,`".$this->directorateTable."`.`created_at`,`".$this->directorateTable."`.`modified_at`,
            `".$this->directorateTable."`.`text`, `".$this->directorateTable."`.`description`
			FROM `".$this->directorateTable."`
			ORDER BY `".$this->directorateTable."`.modified_at DESC, `".$this->directorateTable."`.created_at DESC 
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
		
		$stmt2 = $this->conn->prepare("INSERT INTO `".$this->attachmentsTable."` ( `description`, `path`, `type`) VALUES 
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
		$uuid = "directorate-".uniqid();
        $this->id               =   $uuid;
        $this->title            =   htmlspecialchars(strip_tags($this->title));
        $this->director         =   htmlspecialchars(strip_tags($this->director));
        $this->url              =   htmlspecialchars($this->url);
        $this->phone_number1    =   htmlspecialchars($this->phone_number1);
        $this->fax_num          =   htmlspecialchars($this->fax_num);
        $this->facebook_url     =   htmlspecialchars($this->facebook_url);
        $this->instagram_url    =   htmlspecialchars($this->instagram_url);
        $this->phone_number2    =   htmlspecialchars($this->phone_number2);
        $this->twitter_url      =   htmlspecialchars($this->twitter_url);
        $this->linkedin_url     =   htmlspecialchars($this->linkedin_url);
        $this->whatsapp_url     =   htmlspecialchars($this->whatsapp_url);
        $this->youtube_url      =   htmlspecialchars($this->youtube_url);
        $this->text             =   htmlspecialchars($this->text);
        $this->description      =   htmlspecialchars($this->description);

			$stmt = $this->conn->prepare("INSERT INTO `".$this->directorateTable."` 
            (`id`, `title`, `director`, `url`, `phone_number1`, `fax_num`, `facebook_url`, `instagram_url`, 
            `phone_number2`, `twitter_url`, `linkedin_url`, `whatsapp_url`, `youtube_url`, `created_at`, `modified_at`, `text`, `description`) 
             VALUES 
			(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), ?, ?);");
			

			$stmt->bind_param("ssssiississssss", 
                $this->id,
                $this->title,
                $this->director,
                $this->url,
                $this->phone_number1,
                $this->fax_num,
                $this->facebook_url,
                $this->instagram_url,
                $this->phone_number2,
                $this->twitter_url,
                $this->linkedin_url,
                $this->whatsapp_url,
                $this->youtube_url,
                $this->text, //address
                $this->description
			); 
			if($stmt->execute()===true) {
					$this->description = htmlspecialchars(strip_tags($this->description));
				foreach($this->imagesID as $key => $value)
				{
					
                    $statement = "INSERT INTO `".$this->directorateAttachmentsTable."` ( `directorate_id`, `attach_id`) 
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
	 //`title`, `director`, `url`, `phone_number1`, `fax_num`, `facebook_url`, `instagram_url`, 
     //`phone_number2`, `twitter_url`, `linkedin_url`, `whatsapp_url`, `youtube_url`,  `text`
        $this->id = htmlspecialchars(strip_tags($this->id));
		$this->title = htmlspecialchars(strip_tags($this->title));
		$this->text = htmlspecialchars($this->text);
		$this->director = htmlspecialchars(strip_tags($this->director));
        $this->url = htmlspecialchars($this->url);
        $this->phone_number1 = htmlspecialchars($this->phone_number1);
        $this->fax_num = htmlspecialchars($this->fax_num);
        $this->facebook_url = htmlspecialchars($this->facebook_url);
        $this->instagram_url = htmlspecialchars($this->instagram_url);
        $this->phone_number2 = htmlspecialchars($this->phone_number2);
        $this->twitter_url = htmlspecialchars($this->twitter_url);
        $this->linkedin_url = htmlspecialchars($this->linkedin_url);
        $this->whatsapp_url = htmlspecialchars($this->whatsapp_url);
        $this->youtube_url  = htmlspecialchars($this->youtube_url);
        $this->description  = htmlspecialchars($this->description);
		$stmt = $this->conn->prepare("UPDATE `".$this->directorateTable."` 
        SET `title` = ?, `text` = ?, `director`= ?, `url`= ?, `phone_number1`= ?, `fax_num`= ?, `facebook_url`= ?, `instagram_url`= ?, 
        `phone_number2`= ?, `twitter_url`= ?, `linkedin_url`= ?, `whatsapp_url`= ?, `youtube_url`= ?, `description` = ?
        WHERE `".$this->directorateTable."`.`id` = ?;");
		

		$stmt->bind_param("ssssiississssss", 
            $this->title, 
            $this->text,
            $this->director,
            $this->url,
            $this->phone_number1,
            $this->fax_num,
            $this->facebook_url,
            $this->instagram_url,
            $this->phone_number2,
            $this->twitter_url,
            $this->linkedin_url,
            $this->whatsapp_url,
            $this->youtube_url,
            $this->description, 
            $this->id
        );
		
		if($stmt->execute()){
			return true;
		}
	 
		return false;
	}
	
	function delete(){
		
		$stmt = $this->conn->prepare("DELETE FROM `".$this->directorateAttachmentsTable."` WHERE `".$this->directorateAttachmentsTable."`.`directorate_id` = ?");
			
		$this->id = htmlspecialchars(strip_tags($this->id));
	 
		$stmt->bind_param("s", $this->id);
	 
		if($stmt->execute()){
			$stmt2 = $this->conn->prepare("DELETE FROM `".$this->directorateTable."` WHERE `".$this->directorateTable."`.`id` = ?");
		
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