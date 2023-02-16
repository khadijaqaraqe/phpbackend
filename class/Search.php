<?php
class Search{   
    
    public $q;
    public $title;
    public $creator;
    public $text;
    public $category_id;   
    public $created; 
	public $modified; 
    private $conn;
	
    public function __construct($db){
        $this->conn = $db;
    }	
	
	function searchArticles($query){	
       // $q -> bind_param("q", $this->q);
      
		
			$stmt = $this->conn->prepare(
            "SELECT Articles.ID, Articles.Title, Articles.Text, Articles.ModifiedDate, Articles.CreatedDate, Users.FirstName, Users.LastName, Types.Name, Images.Path, Images.Type, Images.image, Images.CreatedDate
                FROM Articles, Types, Users, ArticlesImages, Images
                WHERE Articles.Category = Types.ID AND Articles.Creator = Users.ID AND ArticlesImages.imageId = Images.ID AND ArticlesImages.articleId = Articles.ID AND ( 
                    Articles.Title LIKE '%".$query."%' 
                    OR Articles.Text LIKE '%".$query."%' 
                    OR Types.Description LIKE '%".$query."%' 
                    OR Types.Name LIKE '%".$query."%' )
                ORDER BY Articles.ModifiedDate DESC 
                LIMIT 20");
			//$stmt->bind_param("q", $this->q);			
			
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}
}
?>