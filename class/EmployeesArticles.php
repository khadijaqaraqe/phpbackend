<?php
class EmployeesArticles{   
    
    private $ArticleTable = "employee_articles";     
    private $ImagesTable = "employee_images";   
    private $ArticleImagesTable = "employee_articlesimages";    
    private $usersTable = 'users';
    public $id;
    public $Title;
    public $CreatorId;
    public $Text;
    public $articleId;
    public $Category;   
	public $AltText;
	public $Description;
    public $created; 
	public $modified; 
	public $image;
	public $imagesID= array();
	public $Path;

	public $Type;
	public $FirstRow;
	public $LastRow;
	public $Writer;
    public $imageId;
	private $conn;
	
    public function __construct($db){
        $this->conn = $db;
    }	
	
	function read(){	
		if($this->id) {
			$stmt = $this->conn->prepare("SELECT ".$this->ArticleTable.".id, ".$this->ArticleTable.".title, ".$this->ArticleTable.".text, ".$this->ArticleTable.".modified_date, ".$this->ArticleTable.".created_date, ".$this->usersTable.".first_name, ".$this->usersTable.".last_name, types.name, `".$this->ImagesTable."`.path, `".$this->ImagesTable."`.type, `".$this->ImagesTable."`.image, `".$this->ImagesTable."`.created_date 
			FROM (".$this->ArticleTable." JOIN `".$this->ArticleImagesTable."` ON `".$this->ArticleImagesTable."`.article_id = ".$this->ArticleTable.".id  JOIN `".$this->ImagesTable."` ON `".$this->ArticleImagesTable."`.image_id = `".$this->ImagesTable."`.id), types, ".$this->usersTable."  
			WHERE ".$this->ArticleTable.".category = types.id AND ".$this->ArticleTable.".creator = `".$this->usersTable."`.id AND ".$this->ArticleTable.".id = ?
			ORDER BY `".$this->ImagesTable."`.created_date ASC");
			$stmt->bind_param("s", $this->id);					
		} else {
			$stmt = $this->conn->prepare("SELECT ".$this->ArticleTable.".id, ".$this->ArticleTable.".title, ".$this->ArticleTable.".text, ".$this->ArticleTable.".modified_date, ".$this->ArticleTable.".created_date, ".$this->usersTable.".first_name, ".$this->usersTable.".last_name, types.name, `".$this->ImagesTable."`.path, `".$this->ImagesTable."`.type, `".$this->ImagesTable."`.image, `".$this->ImagesTable."`.created_date 
			FROM ".$this->ArticleTable." LEFT JOIN `".$this->ArticleImagesTable."` ON `".$this->ArticleImagesTable."`.article_id = ".$this->ArticleTable.".id LEFT JOIN `".$this->ImagesTable."` ON `".$this->ArticleImagesTable."`.image_id = `".$this->ImagesTable."`.id, types, ".$this->usersTable."   
			WHERE ".$this->ArticleTable.".category = types.id AND ".$this->ArticleTable.".creator = `".$this->usersTable."`.id 
			ORDER BY ".$this->ArticleTable.".modified_date DESC, ".$this->ArticleTable.".created_date DESC 
			LIMIT 5;");		
		}		
		if ($stmt->execute()) {
			$result = $stmt->get_result();	
			return $result;	
		} else { 
			return false;
		}			
			
	}

	function GetFirst5(){	
		$stmt = $this->conn->prepare("SELECT ".$this->ArticleTable.".id, ".$this->ArticleTable.".title, ".$this->ArticleTable.".text, ".$this->ArticleTable.".modified_date, ".$this->ArticleTable.".created_date, `".$this->usersTable."`.first_name, ".$this->usersTable.".last_name, types.name, `".$this->ImagesTable."`.path, `".$this->ImagesTable."`.type, `".$this->ImagesTable."`.image, `".$this->ImagesTable."`.created_date 
		FROM ".$this->ArticleTable." LEFT JOIN `".$this->ArticleImagesTable."` ON `".$this->ArticleImagesTable."`.article_id = ".$this->ArticleTable.".id LEFT JOIN images ON `".$this->ArticleImagesTable."`.image_id = `".$this->ImagesTable."`.id, types, ".$this->usersTable."   
		WHERE ".$this->ArticleTable.".category = types.id AND ".$this->ArticleTable.".creator = `".$this->usersTable."`.id 
		ORDER BY ".$this->ArticleTable.".modified_date DESC, ".$this->ArticleTable.".created_date DESC 
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




	function createImages() {
		
		$stmt2 = $this->conn->prepare("INSERT INTO `".$this->ImagesTable."` ( `alt_text`, `creator_id`, `path`, `type`) VALUES 
			( ?, ?, ?, ?);");		
		$this->AltText = htmlspecialchars(strip_tags($this->AltText));
		$this->CreatorId = htmlspecialchars(strip_tags($this->CreatorId));
		
		$this->Type = htmlspecialchars(strip_tags($this->Type));
		
		$stmt2->bind_param("siss", 
			$this->AltText, 
			$this->CreatorId,
			$this->Path, 
			$this->Type 
		);
		if($stmt2->execute() === true){

			$image_id = $stmt2->insert_id;;
			array_push($this->imagesID, $image_id);
			$this->imageId = $image_id;
			
			return true;
		}
		return false;			
	}
						 
	function create() {
		$uuid = uniqid(date("Y").date("n").'0');

			$stmt = $this->conn->prepare("INSERT INTO `".$this->ArticleTable."` (`id`, `category`, `created_date`, `creator`, `modified_date`, `text`, `title`, `writer`) VALUES 
			(?, ?, NOW(), ?, NOW(), ?, ?, ?);");
			$this->id =  $uuid;

			$this->Category = htmlspecialchars(strip_tags($this->Category));
			$this->CreatorId = htmlspecialchars(strip_tags($this->CreatorId));
			$this->Writer = htmlspecialchars(strip_tags($this->Writer));
			$this->Text = (strip_tags($this->Text));
			

			$stmt->bind_param("siisss", 
			$this->id,
			$this->Category,
			$this->CreatorId, 
			$this->Text,  
			$this->Title, 
			$this->Writer); 

			if($stmt->execute()===true) {
				
					$this->Description = htmlspecialchars(strip_tags($this->Description));
				foreach($this->imagesID as $key => $value)
				{
					
				$statement = "INSERT INTO ``".$this->ArticleImagesTable."`` 
				( `article_id`, `image_id`, `description`) 
				VALUES ";
				$valuesStmt = "";
				 foreach($this->imagesID as $key => $value)
					{
						$valuesStmt .=  "(  
							'".$uuid."', 
							'". $value."', 	
							'" .$this->Description."'), ";
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
	 
		$stmt = $this->conn->prepare("UPDATE `".$this->ArticleTable."` SET `title` = ?, `text` = ? WHERE `".$this->ArticleTable."`.`id` = ?;");
	 
		$this->id = htmlspecialchars(strip_tags($this->id));
		$this->Title = htmlspecialchars(strip_tags($this->Title));
		$this->Text = htmlspecialchars(strip_tags($this->Text));
		
		$stmt->bind_param("sss", $this->Title, $this->Text, $this->id);
		
		if($stmt->execute()){
			return true;
		}
	 
		return false;
	}
	
	function delete(){
		
		$stmt = $this->conn->prepare("DELETE FROM `".$this->ArticleImagesTable."` WHERE ``".$this->ArticleImagesTable."``.`article_id` = ?");
			
		$this->id = htmlspecialchars(strip_tags($this->id));
	 
		$stmt->bind_param("s", $this->id);
	 
		if($stmt->execute()){
			$stmt2 = $this->conn->prepare("DELETE FROM ".$this->ArticleTable." WHERE `".$this->ArticleTable."`.`id` = ?");
		
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