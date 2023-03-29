<?php
class EmpArticles{   
    
    private $Table = "Articles";      
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
    public $imageId;
	private $conn;
	
    public function __construct($db){
        $this->conn = $db;
    }	
	
	function read(){	
		if($this->id) {
			$stmt = $this->conn->prepare("SELECT Articles.ID, Articles.Title, Articles.Text, Articles.ModifiedDate, Articles.CreatedDate, Users.FirstName, Users.LastName, Types.Name, Images.Path, Images.Type, Images.image, Images.CreatedDate 
			FROM (Articles  JOIN ArticlesImages ON ArticlesImages.articleId = Articles.ID  JOIN Images ON ArticlesImages.imageId = Images.ID), Types, Users  
			WHERE Articles.Category = Types.ID AND Articles.Creator = Users.ID AND Articles.ID = ?
			ORDER BY Images.CreatedDate ASC");
			$stmt->bind_param("s", $this->id);					
		} else {
			$stmt = $this->conn->prepare("SELECT Articles.ID, Articles.Title, Articles.Text, Articles.ModifiedDate, Articles.CreatedDate, Users.FirstName, Users.LastName, Types.Name, Images.Path, Images.Type, Images.image, Images.CreatedDate 
			FROM Articles LEFT JOIN ArticlesImages ON ArticlesImages.articleId = Articles.ID LEFT JOIN Images ON ArticlesImages.imageId = Images.ID, Types, Users   
			WHERE Articles.Category = Types.ID AND Articles.Creator = Users.ID
			ORDER BY Articles.ModifiedDate DESC, Articles.CreatedDate DESC 
			LIMIT 5;");		
		}		
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}

	function GetFirst10(){	
		$stmt = $this->conn->prepare("SELECT Articles.ID, Articles.Title, Articles.Text, Articles.ModifiedDate, Articles.CreatedDate, Users.FirstName, Users.LastName, Types.Name, Images.Path, Images.Type, Images.image, Images.CreatedDate 
		FROM Articles LEFT JOIN ArticlesImages ON ArticlesImages.articleId = Articles.ID LEFT JOIN Images ON ArticlesImages.imageId = Images.ID, Types, Users   
		WHERE Articles.Category = Types.ID AND Articles.Creator = Users.ID 
		
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

	


	function createImages() {
		
		$stmt2 = $this->conn->prepare("INSERT INTO `Images` (`ID`, `AltText`, `CreatorId`, `Path`, `Type`) VALUES 
			(NULL, ?, ?, ?, ?);");		
		$this->AltText = htmlspecialchars(strip_tags($this->AltText));
		$this->CreatorId = htmlspecialchars(strip_tags($this->CreatorId));
		//$this->Path = $this->Path;
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
	
			$stmt = $this->conn->prepare("INSERT INTO `Articles` (`ID`, `Category`, `CreatedDate`, `Creator`, `ModifiedDate`, `Text`, `Title`) VALUES 
			(?, ?, NOW(), ?, NOW(), ?, ?);");
			$this->id =  $uuid;//$this->article_id;
			$this->Category = htmlspecialchars(strip_tags($this->Category));
			$this->CreatorId = htmlspecialchars(strip_tags($this->CreatorId));
			$this->Text = (strip_tags($this->Text));

			$stmt->bind_param("siiss", 
			$this->id,
			$this->Category,
			$this->CreatorId, 
			$this->Text,  
			$this->Title); 
			if($stmt->execute()===true) {
				
                $this->articleId = $this->$uuid;
                $this->Description = htmlspecialchars(strip_tags($this->Description));
				foreach($this->imagesID as $key => $value)
				{
					
				$statement = "INSERT INTO `ArticlesImages` 
				(`id`, `articleId`, `imageId`, `Description`) 
				VALUES ";
				$valuesStmt = "";
				 foreach($this->imagesID as $key => $value)
					{
						$valuesStmt .=  "( NULL, 
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
	 
		$stmt = $this->conn->prepare("UPDATE `Articles` SET `Title` = ?, `Text` = ? WHERE `Articles`.`ID` = ?;");
	 
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
		
		$stmt = $this->conn->prepare("DELETE FROM ArticlesImages WHERE `ArticlesImages`.`articleId` = ?");
			
		$this->id = htmlspecialchars(strip_tags($this->id));
	 
		$stmt->bind_param("s", $this->id);
	 
		if($stmt->execute()){
			$stmt2 = $this->conn->prepare("DELETE FROM Articles WHERE `Articles`.`ID` = ?");
			
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