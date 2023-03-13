<?php
class Tickers{   
      
    public $id;
    public $title;
    public $text;
    private $conn;
	
    public function __construct($db){
        $this->conn = $db;
    }	
	
	function readTickers(){	
		$stmt = $this->conn->prepare("SELECT Articles.ID, Articles.Title, Articles.Text 
		FROM Articles LEFT JOIN ArticlesImages ON ArticlesImages.articleId = Articles.ID LEFT JOIN Images ON ArticlesImages.imageId = Images.ID, Types, Users   
		WHERE Articles.Category = Types.ID AND Articles.Creator = Users.ID 
		
		ORDER BY Articles.ModifiedDate DESC, Articles.CreatedDate DESC 
		LIMIT 15;");
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}
	
}
?>