<?php
class Articles{   
    
    private $articlesTable = "articles";    
	private $articleImagesTable = "articles_images";
	private $usersTable = "users";
	private $imagesTable = "images";
	private $typesTable ="types";
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
	private $conn;
	
    public function __construct($db){
        $this->conn = $db;
    }	
	
	function read(){	
		if($this->id) {
			$stmt = $this->conn->prepare("SELECT `".$this->articlesTable."`.id, `".$this->articlesTable."`.title, `".$this->articlesTable."`.text, `".$this->articlesTable."`.modified_date, `".$this->articlesTable."`.created_date, `".$this->usersTable."`.first_name, `".$this->usersTable."`.last_name, `".$this->typesTable."`.name, `".$this->imagesTable."`.path, `".$this->imagesTable."`.type, `".$this->imagesTable."`.image, `".$this->imagesTable."`.created_date 
			FROM (`".$this->articlesTable."` JOIN `".$this->articleImagesTable."` ON `".$this->articleImagesTable."`.article_id = `".$this->articlesTable."`.id  JOIN `".$this->imagesTable."` ON `".$this->articleImagesTable."`.image_id = `".$this->imagesTable."`.id), `".$this->typesTable."`, `".$this->usersTable."`  
			WHERE `".$this->articlesTable."`.category = `".$this->typesTable."`.id AND `".$this->articlesTable."`.creator = `".$this->usersTable."`.id AND `".$this->articlesTable."`.id = ?
			ORDER BY `".$this->imagesTable."`.created_date ASC");
			$stmt->bind_param("s", $this->id);					
		} else {
			$stmt = $this->conn->prepare("SELECT `".$this->articlesTable."`.id, `".$this->articlesTable."`.title, `".$this->articlesTable."`.text, `".$this->articlesTable."`.modified_date, `".$this->articlesTable."`.created_date, `".$this->usersTable."`.first_name, `".$this->usersTable."`.last_name, `".$this->typesTable."`.name, `".$this->imagesTable."`.path, `".$this->imagesTable."`.`type`, `".$this->imagesTable."`.image, `".$this->imagesTable."`.created_date 
			FROM `".$this->articlesTable."` LEFT JOIN `".$this->articleImagesTable."` ON `".$this->articleImagesTable."`.article_id = `".$this->articlesTable."`.id LEFT JOIN `".$this->imagesTable."` ON `".$this->articleImagesTable."`.image_id = `".$this->imagesTable."`.id, `".$this->typesTable."`, `".$this->usersTable."`   
			WHERE `".$this->articlesTable."`.category = `".$this->typesTable."`.id AND `".$this->articlesTable."`.creator = `".$this->usersTable."`.id 
			
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
		$stmt = $this->conn->prepare("SELECT `".$this->articlesTable."`.id, `".$this->articlesTable."`.title, `".$this->articlesTable."`.text, `".$this->articlesTable."`.modified_date, `".$this->articlesTable."`.created_date, `".$this->usersTable."`.first_name, `".$this->usersTable."`.last_name, `".$this->typesTable."`.name, `".$this->imagesTable."`.path, `".$this->imagesTable."`.type, `".$this->imagesTable."`.image, `".$this->imagesTable."`.created_date 
		FROM `".$this->articlesTable."` LEFT JOIN `".$this->articleImagesTable."` ON `".$this->articleImagesTable."`.article_id = `".$this->articlesTable."`.id LEFT JOIN Images ON `".$this->articleImagesTable."`.image_id = `".$this->imagesTable."`.id, `".$this->typesTable."`, `".$this->usersTable."`   
		WHERE `".$this->articlesTable."`.category = `".$this->typesTable."`.id AND `".$this->articlesTable."`.creator = `".$this->usersTable."`.id 
		
		ORDER BY `".$this->articlesTable."`.modified_date DESC, `".$this->articlesTable."`.created_date DESC 
		LIMIT ?, ?;");		
		$this->FirstRow = htmlspecialchars(strip_tags($this->FirstRow));
		$this->LastRow = htmlspecialchars(strip_tags($this->LastRow));
		$stmt->bind_param(
			"ii",
			$this->FirstRow, 
			$this->LastRow
		);
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}

	/* function readTickers() {	
		$stmt = $this->conn->prepare("SELECT `".$this->articlesTable."`.id, `".$this->articlesTable."`.title, `".$this->articlesTable."`.text 
		FROM `".$this->articlesTable."` LEFT JOIN `".$this->articleImagesTable."` ON `".$this->articleImagesTable."`.article_id = `".$this->articlesTable."`.id LEFT JOIN Images ON `".$this->articleImagesTable."`.image_id = `".$this->imagesTable."`.id, types, `".$this->usersTable."`   
		WHERE `".$this->articlesTable."`.category = `".$this->typesTable."`.id AND `".$this->articlesTable."`.creator = `".$this->usersTable."`.id 
		
		ORDER BY `".$this->articlesTable."`.modified_date DESC, `".$this->articlesTable."`.created_date DESC 
		LIMIT 15;");
		$stmt->execute();			
		$result = $stmt->get_result();	
		//$this->conn->close();	
		return $result;	
	} */


	function createImages() {
		
		$stmt2 = $this->conn->prepare("INSERT INTO `".$this->imagesTable."` ( `alt_text`, `creator_id`, `path`, `type`) VALUES 
			(?, ?, ?, ?);");		
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
		$uuid = uniqid(date("Y").date("n").'0');//$this->conn->prepare("SELECT uniqid();");
	//	if($uuid->execute() === true) {
		//	$uuid->store_result();
		//	$uuid->bind_result($this->article_id);
		//	$uuid->fetch();
			
			
			//$this->article_id = ($uuid->fetch_assoc())['UUID()'];

			$stmt = $this->conn->prepare("INSERT INTO `".$this->articlesTable."` (`id`, `category`, `created_date`, `creator`, `modified_date`, `text`, `title`) VALUES 
			(?, ?, NOW(), ?, NOW(), ?, ?);");
			$this->id =  $uuid;//$this->article_id;
			$this->category = htmlspecialchars(strip_tags($this->category));
			$this->creator_id = htmlspecialchars(strip_tags($this->creator_id));
			$this->text = (strip_tags($this->text));
			//$this->title = $this->title;

			$stmt->bind_param("siiss", 
			$this->id,
			$this->category,
			$this->creator_id, 
			$this->text,  
			$this->title); 
			if($stmt->execute() === true) {
				
					/* foreach($this->imagesID as $key => $value)
					{
						echo $key." has the value ". $value;
					} */
					//$this->article_id = $this->$uuid;
					$this->description = htmlspecialchars(strip_tags($this->description));
				foreach($this->imagesID as $key => $value)
				{
					//echo $key." has the value ". $value;
					

					//	echo $value;
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
					
					/* "INSERT INTO `".$this->articleImagesTable."` (`id`, `article_id`, `image_id`, `description`) VALUES 

						(NULL, ?, ?, ?)"); */

					
					/* $this->image_id = $value;
					
			
					$stmt3->bind_param( "sss", 
						$this->article_id,
						$this->image_id,
						$this->description
					); */
					if($stmt3->execute()){
						return true; 
					}
					return false;
				  }
					
				/* }
				return false; */
			}
			//return false;	
		//} 
		//$this->conn-> close();
		return false;	 
		
	}
		
	function update(){
	 
		$stmt = $this->conn->prepare("UPDATE `".$this->articlesTable."` SET `title` = ?, `text` = ? WHERE `".$this->articlesTable."`.`id` = ?;");
	 
		$this->id = htmlspecialchars(strip_tags($this->id));
		$this->title = htmlspecialchars(strip_tags($this->title));
		$this->text = htmlspecialchars(strip_tags($this->text));
		
		$stmt->bind_param("sss", $this->title, $this->text, $this->id);
		
		if($stmt->execute()){
			return true;
		}
	 
		return false;
	}
	
	function delete(){
		//cd1fc45c-8756-11ed-97ea-dc4a3e462f29
		//DELETE FROM articlesimages WHERE `articlesimages`.`id` = 311"
		//DELETE FROM articles WHERE `articles`.`id` = 'a44adb56-8746-11ed-97ea-dc4a3e462f29'
		//DELETE FROM articlesimages WHERE `articlesimages`.`article_id` = 'cd1fc45c-8756-11ed-97ea-dc4a3e462f29'
		$stmt = $this->conn->prepare("DELETE FROM `".$this->articleImagesTable."` WHERE `".$this->articleImagesTable."`.`article_id` = ?");
			
		$this->id = htmlspecialchars(strip_tags($this->id));
	 
		$stmt->bind_param("s", $this->id);
	 
		if($stmt->execute()){
			$stmt2 = $this->conn->prepare("DELETE FROM `".$this->articlesTable."` WHERE `".$this->articlesTable."`.`id` = ?");
			
			//$this->id = htmlspecialchars(strip_tags($this->id));
		
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