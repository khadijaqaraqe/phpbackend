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
	
	function searchArticles(){
        // $q -> bind_param("q", $this->q);
        $data = file_get_contents("php://input");
        
        if(empty($data) == 1){ $data = json_decode("{'query': 'بيت لحم'}"); } else {$data = json_decode(file_get_contents("php://input")); }///* file_get_contents("php://input") */ ? json_decode(file_get_contents("php://input")) : json_decode("{'query': 'بيت لحم'}");
        
        //echo(empty($data) == 1);
       // echo("data");
       
       // echo($data);
        //$this->q -> $data->query;
        //$new_query -> $data->query;
       // $query = is_null($data) ? "بيت لحم" : htmlspecialchars(strip_tags($data->query));
        $query = /* (isset($data)) ? */  htmlspecialchars(strip_tags($data->query))/*  : "بيت لحم" */;
      //  $this->q = $query;
        $new_query = $query;//htmlspecialchars(strip_tags($data->query)) ;// (strlen($query) > 0 /* && !is_null($data->query) */) ?  : "بيت لحم"; 
		/* *
          SELECT Articles.ID, Articles.Title, Articles.Text, Articles.ModifiedDate, Articles.CreatedDate, Users.FirstName, Users.LastName, Types.Name, Images.Path, Images.Type, Images.image, Images.CreatedDate 
		FROM Articles LEFT JOIN ArticlesImages ON ArticlesImages.articleId = Articles.ID LEFT JOIN Images ON ArticlesImages.imageId = Images.ID, Types, Users
         */
        /* SELECT Articles.ID, Articles.Title, Articles.Text, Articles.ModifiedDate, Articles.CreatedDate, Types.Name, Images.Path, Images.Type, Images.image, Images.CreatedDate 
			FROM Articles LEFT JOIN ArticlesImages ON ArticlesImages.articleId = Articles.ID LEFT JOIN Images ON ArticlesImages.imageId = Images.ID, Types    
			WHERE Articles.Category = Types.ID 
			GROUP BY Articles.ID
			ORDER BY Articles.ModifiedDate DESC, Articles.CreatedDate DESC 
			LIMIT 50; */
			$stmt = $this->conn->prepare(
               "SELECT Articles.ID, Articles.Title, Articles.Text, Articles.ModifiedDate, Articles.CreatedDate, Types.Name, Images.Path, Images.Type, Images.image, Images.CreatedDate
                FROM Articles LEFT JOIN ArticlesImages ON ArticlesImages.articleId = Articles.ID LEFT JOIN Images ON ArticlesImages.imageId = Images.ID, Types
                WHERE Articles.Category = Types.ID AND ArticlesImages.imageId = Images.ID AND ArticlesImages.articleId = Articles.ID AND ( 
                    Articles.Title LIKE '%".$new_query."%' 
                    OR Articles.Text LIKE '%".$new_query."%' 
                    OR Types.Description LIKE '%".$new_query."%' 
                    OR Types.Name LIKE '%".$new_query."%' )
                GROUP BY Articles.ID
                ORDER BY Articles.ModifiedDate DESC, Articles.CreatedDate DESC
                LIMIT 200");
			//$stmt->bind_param("q", $this->q);			
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}
}
?>