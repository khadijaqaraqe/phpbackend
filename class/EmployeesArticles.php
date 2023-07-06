<?php
class EmployeesArticles{   
    
	
	private $articlesTable =  "employee_articles";    
	private $articleImagesTable = "employee_articles_images";
	private $usersTable = "users";
	private $imagesTable = "employee_images";
	
    public $id;
    public $title;
    public $creator_id;
    public $text;
    public $article_id;
    public $category;   
	public $alt_text;
	public $description;
    public $created; 
	public $modified; 
	public $image;
	public $imagesID= array();
	public $path;

	public $type;
	public $FirstRow;
	public $LastRow;
    public $image_id;
	public $writer;
	private $conn;
	
    public function __construct($db){
        $this->conn = $db;
    }	

	function getImages($item) {
        $stmt2 = $this->conn->prepare(" SELECT DISTINCT `".$this->imagesTable."`.`path`, `".$this->imagesTable."`.`type`, `".$this->imagesTable."`.`image`, `".$this->imagesTable."`.`created_date`
        FROM `".$this->imagesTable."` LEFT JOIN `".$this->articleImagesTable."` ON `".$this->articleImagesTable."`.`image_id` = `".$this->imagesTable."`.`id`
        WHERE `".$this->articleImagesTable."`.`image_id` = `".$this->imagesTable."`.id AND `".$this->articleImagesTable."`.`article_id`= '".strval($item)."'");
		if($stmt2->execute() === true){
			$results2 = $stmt2->get_result();
			return $results2;
		}
	}

	
	function read(){
		//



		if($this->id) {
			$stmt = $this->conn->prepare("SELECT `".$this->articlesTable."`.`id`, `".$this->articlesTable."`.`title`, `".$this->articlesTable."`.`text`, 
			`".$this->articlesTable."`.`writer`, `".$this->articlesTable."`.`modified_date`, `".$this->articlesTable."`.`created_date`
			FROM `".$this->articlesTable."`  
			WHERE `".$this->articlesTable."`.`id` = '".strval($this->id)."'
			ORDER BY `".$this->articlesTable."`.created_date ASC");
			//$updatedID = "'".$this->id."'";
			//$stmt->bind_param("s", $this->id);					
		} else {
			// .$this->usersTable."`.first_name, `".$this->usersTable."`.last_name, `".$this->typesTable."`.name, `".$this->imagesTable."`.path, `".$this->imagesTable."`.`type`, `".$this->imagesTable."`.image, `".$this->imagesTable."`.created_date 
			// ` LEFT JOIN `".$this->articleImagesTable."` ON `".$this->articleImagesTable."`.article_id = `".$this->articlesTable."`.id LEFT JOIN `".$this->imagesTable."` ON `".$this->articleImagesTable."`.image_id = `".$this->imagesTable."`.id, `".$this->typesTable."`, `".$this->usersTable."` 
			// AND `".$this->articlesTable."`.creator = `".$this->usersTable."`.id
			$stmt = $this->conn->prepare("SELECT `".$this->articlesTable."`.id, `".$this->articlesTable."`.title, `".$this->articlesTable."`.text, 
			`".$this->articlesTable."`.writer, `".$this->articlesTable."`.modified_date, `".$this->articlesTable."`.created_date
			FROM `".$this->articlesTable."`
			ORDER BY `".$this->articlesTable."`.modified_date DESC, `".$this->articlesTable."`.created_date DESC 
			LIMIT 5;");		
		}		
		if ($stmt->execute()) {
			$result = $stmt->get_result();	
			return $result;	
		} else { 
			return false;
		}			
			
	}

	function GetFirst25(){	
	/* 	$stmt = $this->conn->prepare("SELECT `".$this->articlesTable."`.`id`, `".$this->articlesTable."`.`title`, `".$this->articlesTable."`.text, `".$this->articlesTable."`.writer, `".$this->articlesTable."`.modified_date, `".$this->articlesTable."`.created_date, `".$this->usersTable."`.first_name, `".$this->usersTable."`.last_name, `".$this->imagesTable."`.path, `".$this->imagesTable."`.type, `".$this->imagesTable."`.image, `".$this->imagesTable."`.created_date 
		FROM `".$this->articlesTable."` LEFT JOIN `".$this->articleImagesTable."` ON `".$this->articleImagesTable."`.`article_id` = `".$this->articlesTable."`.id LEFT JOIN Images ON `".$this->articleImagesTable."`.image_id = `".$this->imagesTable."`.id,  `".$this->usersTable."`   
		WHERE `".$this->articlesTable."`.creator = `".$this->usersTable."`.`id` 
		
		ORDER BY `".$this->articlesTable."`.`modified_date` DESC, `".$this->articlesTable."`.`created_date` DESC 
		LIMIT ?, ?;");	 */
		$stmt = $this->conn->prepare("SELECT `".$this->articlesTable."`.id, `".$this->articlesTable."`.title, `".$this->articlesTable."`.text, 
			`".$this->articlesTable."`.writer, `".$this->articlesTable."`.modified_date, `".$this->articlesTable."`.created_date
			FROM `".$this->articlesTable."`
			ORDER BY `".$this->articlesTable."`.modified_date DESC, `".$this->articlesTable."`.created_date DESC 
			LIMIT 25;");		
		/* $this->FirstRow = htmlspecialchars(strip_tags($this->FirstRow));
		$this->LastRow = htmlspecialchars(strip_tags($this->LastRow));
		$stmt->bind_param(
			"ii",
			$this->FirstRow, 
			$this->LastRow
		); */
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}

	function createImages() {
		
		$stmt2 = $this->conn->prepare("INSERT INTO `".$this->imagesTable."` ( `alt_text`, `creator_id`, `path`, `type`) VALUES 
			( ?, ?, ?, ?);");		
		$this->alt_text = htmlspecialchars(strip_tags($this->alt_text));
		$this->creator_id = htmlspecialchars(strip_tags($this->creator_id));
		//$this->path = $this->path;
		$this->type = htmlspecialchars(strip_tags($this->type));
		
		$stmt2->bind_param("siss", 
			$this->alt_text, 
			$this->creator_id,
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
		$uuid = "employee-".uniqid(date("Y").date("n").'0');

			$stmt = $this->conn->prepare("INSERT INTO `".$this->articlesTable."` (`id`, `category`, `created_date`, `creator`, `modified_date`, `text`, `title`, `writer`) VALUES 
			(?, ?, NOW(), ?, NOW(), ?, ?, ?);");
			$this->id =  $uuid;//$this->article_id;
			$this->category = htmlspecialchars(strip_tags($this->category));
			$this->creator_id = htmlspecialchars(strip_tags($this->creator_id));
			$this->writer = htmlspecialchars(strip_tags($this->writer));
			$this->text = htmlspecialchars($this->text);
			//$this->title = $this->title;

			$stmt->bind_param("siisss", 
			$this->id,
			$this->category,
			$this->creator_id, 
			$this->text,  
			$this->title,
			$this->writer); 
			if($stmt->execute()===true) {
				
					
					$this->description = htmlspecialchars(strip_tags($this->description));
				foreach($this->imagesID as $key => $value)
				{
					
				$statement = "INSERT INTO `".$this->articleImagesTable."` 
				( `article_id`, `image_id`, `description`) 
				VALUES ";
				$valuesStmt = "";
				 foreach($this->imagesID as $key => $value)
					{
						$valuesStmt .=  "(  
							'".$uuid."', 
							'". $value."', 	
							'" .$this->description."'), ";
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
	 
		$stmt = $this->conn->prepare("UPDATE `".$this->articlesTable."` SET `title` = ?, `text` = ? WHERE `".$this->articlesTable."`.`id` = ?;");
	 
		$this->id = htmlspecialchars(strip_tags($this->id));
		$this->title = htmlspecialchars(strip_tags($this->title));
		$this->text = htmlspecialchars($this->text);
		
		$stmt->bind_param("sss", $this->title, $this->text, $this->id);
		
		if($stmt->execute()){
			return true;
		}
	 
		return false;
	}
	
	function delete(){
		
		$stmt = $this->conn->prepare("DELETE FROM `".$this->articleImagesTable."` WHERE `".$this->articleImagesTable."`.`article_id` = ?");
			
		$this->id = htmlspecialchars(strip_tags($this->id));
	 
		$stmt->bind_param("s", $this->id);
	 
		if($stmt->execute()){
			$stmt2 = $this->conn->prepare("DELETE FROM `".$this->articlesTable."` WHERE `".$this->articlesTable."`.`id` = ?");
		
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