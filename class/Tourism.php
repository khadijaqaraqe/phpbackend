<?php

class Tourism {   	
	private $tourismTable =  ""; 
    private $places_to_visitTable =  "places_to_visit"; //أماكن زيارة
    private $places_to_stayTable =  "places_to_stay"; //أماكن إقامة
    private $tourism_realityTable =  "tourism_reality"; //الواقع السياحي
    private $presentsTable =  "presents"; // هدايا
    private $restaurantsTable =  "restaurants";    //مطاعم
	private $tourismAttachmentsTable = "tourism_attachments";
	private $attachmentsTable = "attachments";
	
    public $id;

    public $creator;
    public $text;
    public $url;
    public $title;
    public $created; 
    public $modified;
	public $tourism_id;
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
        FROM `".$this->attachmentsTable."` LEFT JOIN `".$this->tourismAttachmentsTable."` ON `".$this->tourismAttachmentsTable."`.`attach_id` = `".$this->attachmentsTable."`.`id`
        WHERE `".$this->tourismAttachmentsTable."`.`attach_id` = `".$this->attachmentsTable."`.id AND `".$this->tourismAttachmentsTable."`.`tourism_id`= '".strval($item)."'");
		if($stmt2->execute() === true){
			$results2 = $stmt2->get_result();
			return $results2;
		}
	}
    
    function TourismTable($item) {
        if(intval($item) == 1) {
            $this->tourismTable = $this->places_to_visitTable;
        } 
        if(intval($item) == 2) {
            $this->tourismTable = $this->places_to_stayTable;
        }
        if(intval($item) == 3) {
            $this->tourismTable = $this->tourism_realityTable;
        }
        if(intval($item) == 4) {
            $this->tourismTable = $this->presentsTable;
        }
        if(intval($item) == 5) {
            $this->tourismTable = $this->restaurantsTable;
        }
    }

    function readTourismTable($item) {
        if(str_contains($item, "places_to_visit") ) {
            $this->tourismTable = $this->places_to_visitTable;
        } else {
            if(str_contains($item, "places_to_stay") ) {
                $this->tourismTable = $this->places_to_stayTable;
            } else {
                if(str_contains($item, "tourism_reality") ) {
                    $this->tourismTable = $this->tourism_realityTable;
                } else {
                    if(str_contains($item, "presents") ) {
                        $this->tourismTable = $this->presentsTable;
                    } else {
                        if(str_contains($item, "restaurants") ) {
                            $this->tourismTable = $this->restaurantsTable;
                        } else {
                            $this->tourismTable = $this->tourism_realityTable;
                        }
                    }
                }
            }
        }
    }
	
	function read() {
        $this->readTourismTable($this->id);
		if($this->id) {
			$stmt = $this->conn->prepare("SELECT 
                `".$this->tourismTable."`.`id`,`".$this->tourismTable."`.`creator`,`".$this->tourismTable."`.`text`,
                `".$this->tourismTable."`.`created`,`".$this->tourismTable."`.`modified`,`".$this->tourismTable."`.`title`,
                `".$this->tourismTable."`.`url`
                FROM `".$this->tourismTable."`  
                WHERE `".$this->tourismTable."`.`id` = '".strval($this->id)."'
                ORDER BY `".$this->tourismTable."`.created ASC");
							
		} else {
			$stmt = $this->conn->prepare("SELECT 
                `".$this->tourismTable."`.`id`,`".$this->tourismTable."`.`creator`,`".$this->tourismTable."`.`text`,
                `".$this->tourismTable."`.`created`,`".$this->tourismTable."`.`modified`,`".$this->tourismTable."`.`title`,
                `".$this->tourismTable."`.`url`
			FROM `".$this->tourismTable."`
			ORDER BY `".$this->tourismTable."`.modified DESC, `".$this->tourismTable."`.created DESC 
			LIMIT 2000;");		
		}	
		if ($stmt->execute()) {
			$result = $stmt->get_result();	
			return $result;	
		} else { 
			return false;
		}
	}

    function readAll() {
        $this->readTourismTable($this->id);
        if(!$this->id) {
			$stmt = $this->conn->prepare("SELECT 
                `".$this->places_to_visitTable."`.`id` AS id,`".$this->places_to_visitTable."`.`creator` AS creator,`".$this->places_to_visitTable."`.`text` AS text,
                `".$this->places_to_visitTable."`.`created` AS created,`".$this->places_to_visitTable."`.`modified` AS modified,`".$this->places_to_visitTable."`.`title` AS title,
                `".$this->places_to_visitTable."`.`url` AS url
			FROM `".$this->places_to_visitTable."`
			
            UNION   
            SELECT 
                `".$this->places_to_stayTable."`.`id` AS id,`".$this->places_to_stayTable."`.`creator` AS creator,`".$this->places_to_stayTable."`.`text` AS text,
                `".$this->places_to_stayTable."`.`created` AS created,`".$this->places_to_stayTable."`.`modified` AS modified,`".$this->places_to_stayTable."`.`title` AS title,
                `".$this->places_to_stayTable."`.`url` AS url
			FROM `".$this->places_to_stayTable."`
			
            UNION   
            SELECT 
                `".$this->tourism_realityTable."`.`id` AS id,`".$this->tourism_realityTable."`.`creator` AS creator,`".$this->tourism_realityTable."`.`text` AS text,
                `".$this->tourism_realityTable."`.`created` AS created,`".$this->tourism_realityTable."`.`modified` AS modified,`".$this->tourism_realityTable."`.`title` AS title,
                `".$this->tourism_realityTable."`.`url` AS url
			FROM `".$this->tourism_realityTable."`
			
            UNION   
            SELECT 
                `".$this->presentsTable."`.`id` AS id,`".$this->presentsTable."`.`creator` AS creator,`".$this->presentsTable."`.`text` AS text,
                `".$this->presentsTable."`.`created` AS created,`".$this->presentsTable."`.`modified` AS modified,`".$this->presentsTable."`.`title` AS title,
                `".$this->presentsTable."`.`url` AS url
			FROM `".$this->presentsTable."`
			
            UNION   
            SELECT 
                `".$this->restaurantsTable."`.`id` AS id,`".$this->restaurantsTable."`.`creator` AS creator,`".$this->restaurantsTable."`.`text` AS text,
                `".$this->restaurantsTable."`.`created` AS created,`".$this->restaurantsTable."`.`modified` AS modified,`".$this->restaurantsTable."`.`title` AS title,
                `".$this->restaurantsTable."`.`url` AS url
			FROM `".$this->restaurantsTable."`
			ORDER BY modified DESC, created DESC 
			LIMIT 2000;");		
		} else {

            $stmt = $this->conn->prepare("SELECT 
                `".$this->tourismTable."`.`id` AS id,`".$this->tourismTable."`.`creator` AS creator,`".$this->tourismTable."`.`text` AS text,
                `".$this->tourismTable."`.`created` AS created,`".$this->tourismTable."`.`modified` AS modified,`".$this->tourismTable."`.`title` AS title,
                `".$this->tourismTable."`.`url` AS url
			FROM `".$this->tourismTable."`
            ORDER BY modified DESC, created DESC 
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
		$this->title = htmlspecialchars(strip_tags($this->title));
		$this->type = htmlspecialchars(strip_tags($this->type));
		
		$stmt2->bind_param("sss", 
			$this->title, 
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
						 
	function create($table) {

        $this->TourismTable($table);
		$uuid = $this->tourismTable.'_'.uniqid();
        $this->id =  $uuid;
        $this->url = htmlspecialchars($this->url);
        $this->creator = htmlspecialchars($this->creator);
        $this->text = htmlspecialchars($this->text);
        $this->title = htmlspecialchars($this->title);

        $stmt = $this->conn->prepare("INSERT INTO `".$this->tourismTable."` 
        (`id`, `creator`, `text`, `title`, `url`, `created`, `modified`) 
            VALUES 
        (?, ?, ?, ?, ?, NOW(), NOW());");
        

        $stmt->bind_param("sisss", 
            $this->id,
            $this->creator,
            $this->text,
            $this->title,
            $this->url
        ); 

        if($stmt->execute()===true) {
        
            if(count($this->imagesID)> 0) {
                $this->title = htmlspecialchars(strip_tags($this->title));
                foreach($this->imagesID as $key => $value)
                {
                    
                    $statement = "INSERT INTO `".$this->tourismAttachmentsTable."` (`tourism_id`, `attach_id`) 
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
            } else {
                return true; 
            }
        } else {
            
        }
        return false;
	}
		
	function update() {
        $this->readTourismTable($this->id);
        $this->id =  htmlspecialchars(strip_tags($this->id));
        $this->url = htmlspecialchars($this->url);        
        $this->text = htmlspecialchars($this->text);
        $this->title = htmlspecialchars($this->title);
      
		$stmt = $this->conn->prepare("UPDATE `".$this->tourismTable."` 
        SET `title` = ?, `url` = ?, `text`= ?
        WHERE `".$this->tourismTable."`.`id` = ?;");

		$stmt->bind_param("ssss", 
            $this->title, 
            $this->url,
            $this->text,
            $this->id
        );
		
		if($stmt->execute()){
			return true;
		}
		return false;
	}
	
	function delete() {
        $this->readTourismTable($this->id);
		$stmt = $this->conn->prepare("DELETE FROM `".$this->tourismAttachmentsTable."` WHERE `".$this->tourismAttachmentsTable."`.`tourism_id` = ?");
			
		$this->id = htmlspecialchars(strip_tags($this->id));
	 
		$stmt->bind_param("s", $this->id);
	 
		if($stmt->execute()){
			$stmt2 = $this->conn->prepare("DELETE FROM `".$this->tourismTable."` WHERE `".$this->tourismTable."`.`id` = ?");
		
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