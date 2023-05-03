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
	
	function searchArticles($new_query){
        
      /*   $data = file_get_contents("php://input");
       
 

        $decoded_json = json_decode($data, false);

        if(!empty($decoded_json->query)){  */
           
           // $query =  (isset($decoded_json->query)) ? htmlspecialchars(strip_tags($decoded_json->query)) : "بيت لحم" ;
        
           // $new_query = $query;
                $stmt = $this->conn->prepare(
                "SELECT DISTINCT Articles.ID, Articles.Title, Articles.Text, Articles.ModifiedDate, Articles.CreatedDate, Types.Name, Images.Path, Images.Type, Images.image, Images.CreatedDate
                    FROM Articles LEFT JOIN ArticlesImages ON ArticlesImages.articleId = Articles.ID LEFT JOIN Images ON ArticlesImages.imageId = Images.ID, Types
                    WHERE ArticlesImages.imageId = Images.ID AND ArticlesImages.articleId = Articles.ID AND ( 
                        Articles.Title LIKE '%".$new_query."%' 
                        OR Articles.Text LIKE '%".$new_query."%' 
                        OR Types.Description LIKE '%".$new_query."%' 
                        OR Types.Name LIKE '%".$new_query."%' )
                    ORDER BY Articles.ModifiedDate DESC, Articles.CreatedDate DESC
                    LIMIT 200");
                
            $stmt->execute();			
            $result = $stmt->get_result();		
            return $result;	
      /*   } else {    
            http_response_code(404);    
            echo json_encode(array("message" => "Cannot search for null values"));
        }  */
    }
	
}
?>