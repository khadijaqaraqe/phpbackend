<?php
class Articles{   
    
    private $itemsTable = "Articles";      
    public $id;
    public $title;
    public $creator;
    public $text;
    public $category_id;   
    public $created; 
	public $modified; 
	public $imagesID= array();
    private $conn;
	
    public function __construct($db){
        $this->conn = $db;
    }	
	
	function read(){	
		if($this->id) {
			$stmt = $this->conn->prepare("SELECT Articles.ID, Articles.Title, Articles.Text, Articles.ModifiedDate, Articles.CreatedDate, Users.FirstName, Users.LastName, Types.Name, Images.Path, Images.Type, Images.image, Images.CreatedDate 
			FROM (Articles  JOIN ArticlesImages ON ArticlesImages.articleId = Articles.ID  JOIN Images ON ArticlesImages.imageId = Images.ID), Types, Users  
			WHERE Articles.Category = Types.ID AND Articles.Creator = Users.ID AND Articles.ID = ?
			ORDER BY images.CreatedDate ASC");
			$stmt->bind_param("s", $this->id);					
		} else {
			$stmt = $this->conn->prepare("SELECT Articles.ID, Articles.Title, Articles.Text, Articles.ModifiedDate, Articles.CreatedDate, Users.FirstName, Users.LastName, Types.Name, Images.Path, Images.Type, Images.image, Images.CreatedDate 
			FROM Articles LEFT JOIN ArticlesImages ON ArticlesImages.articleId = Articles.ID LEFT JOIN Images ON ArticlesImages.imageId = Images.ID, Types, Users   
			WHERE Articles.Category = Types.ID AND Articles.Creator = Users.ID 
			GROUP BY Articles.ID
			ORDER BY Articles.ModifiedDate DESC, Articles.CreatedDate DESC 
			LIMIT 5;");		
		}		
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}

	function GetFirst25(){	
		$stmt = $this->conn->prepare("SELECT Articles.ID, Articles.Title, Articles.Text, Articles.ModifiedDate, Articles.CreatedDate, Users.FirstName, Users.LastName, Types.Name, Images.Path, Images.Type, Images.image, Images.CreatedDate 
		FROM Articles LEFT JOIN ArticlesImages ON ArticlesImages.articleId = Articles.ID LEFT JOIN Images ON ArticlesImages.imageId = Images.ID, Types, Users   
		WHERE Articles.Category = Types.ID AND Articles.Creator = Users.ID 
		GROUP BY Articles.ID
		ORDER BY Articles.ModifiedDate DESC, Articles.CreatedDate DESC 
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

	function readTickers() {	
		$stmt = $this->conn->prepare("SELECT Articles.ID, Articles.Title, Articles.Text 
		FROM Articles LEFT JOIN ArticlesImages ON ArticlesImages.articleId = Articles.ID LEFT JOIN Images ON ArticlesImages.imageId = Images.ID, Types, Users   
		WHERE Articles.Category = Types.ID AND Articles.Creator = Users.ID 
		GROUP BY Articles.ID
		ORDER BY Articles.ModifiedDate DESC, Articles.CreatedDate DESC 
		LIMIT 15;");
		$stmt->execute();			
		$result = $stmt->get_result();	
		//$this->conn->close();	
		return $result;	
	}


	function createImages() {
		
		$stmt2 = $this->conn->prepare("INSERT INTO `Images` (`ID`, `AltText`, `CreatorId`, `Path`, `Type`) VALUES 
			(NULL, ?, ?, ?, ?);");		
		$this->AltText = htmlspecialchars(strip_tags($this->AltText));
		$this->CreatorId = htmlspecialchars(strip_tags($this->CreatorId));
		$this->Path = $this->Path;
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
		$uuid = $this->conn->prepare("SELECT UUID();");
		if($uuid->execute() === true) {
			$uuid->store_result();
			$uuid->bind_result($this->article_id);
			$uuid->fetch();
			
			
			//$this->article_id = ($uuid->fetch_assoc())['UUID()'];

			$stmt = $this->conn->prepare("INSERT INTO `Articles` (`ID`, `Category`, `CreatedDate`, `Creator`, `ModifiedDate`, `Text`, `Title`) VALUES 
			(?, ?, NOW(), ?, NOW(), ?, ?);");
			$this->ID =  $this->article_id;
			$this->Category = htmlspecialchars(strip_tags($this->Category));
			$this->Creator = htmlspecialchars(strip_tags($this->CreatorId));
			$this->Text = (strip_tags($this->Text));
			$this->Title = $this->Title;

			$stmt->bind_param("siiss", 
			$this->ID,
			$this->Category,
			$this->Creator, 
			$this->Text,  
			$this->Title); 
			if($stmt->execute()===true) {
				
					/* foreach($this->imagesID as $key => $value)
					{
						echo $key." has the value ". $value;
					} */
					$this->articleId = $this->article_id;
					$this->Description = htmlspecialchars(strip_tags($this->Description));
				foreach($this->imagesID as $key => $value)
				{
					//echo $key." has the value ". $value;
					

					//	echo $value;
				$statement = "INSERT INTO `ArticlesImages` 
				(`id`, `articleId`, `imageId`, `Description`) 
				VALUES ";
				$valuesStmt = "";
				 foreach($this->imagesID as $key => $value)
					{
						$valuesStmt .=  "( NULL, 
							'".$this->articleId."', 
							'". $value."', 	
							'" .$this->Description."'), ";
					};
					$updatedValue = substr($valuesStmt, 0, -2);
					$stmt3 = $this->conn->prepare($statement.$updatedValue.";");
					
					/* "INSERT INTO `ArticlesImages` (`id`, `articleId`, `imageId`, `Description`) VALUES 

						(NULL, ?, ?, ?)"); */

					
					/* $this->imageId = $value;
					
			
					$stmt3->bind_param( "sss", 
						$this->articleId,
						$this->imageId,
						$this->Description
					); */
					if($stmt3->execute()){
						return true; 
					}
					return false;
				  }
					
				/* }
				return false; */
			}
			return false;	
		} 
		//$this->conn-> close();
		return false;	 
		
	}
		
	function update(){
	 
		$stmt = $this->conn->prepare("UPDATE `articles` SET `Title` = ?, `Text` = ? WHERE `articles`.`ID` = ?;");
	 
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
		//DELETE FROM articles WHERE `articles`.`ID` = 'a44adb56-8746-11ed-97ea-dc4a3e462f29'
		//DELETE FROM articlesimages WHERE `articlesimages`.`articleId` = 'cd1fc45c-8756-11ed-97ea-dc4a3e462f29'
		$stmt = $this->conn->prepare("DELETE FROM articlesimages WHERE `articlesimages`.`articleId` = ?");
			
		$this->id = htmlspecialchars(strip_tags($this->id));
	 
		$stmt->bind_param("s", $this->id);
	 
		if($stmt->execute()){
			$stmt2 = $this->conn->prepare("DELETE FROM articles WHERE `articles`.`ID` = ?");
			
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