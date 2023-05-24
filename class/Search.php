<?php
class Search{   
    private $articlesTable = "articles";
    private $imagesTable = "images";  
    private $articleImagesTable = "articles_images";
    public $q;
    public $title;
    public $creator;
    public $text;
    public $category_id;   
    public $created; 
	public $modified; 
    private $conn;
    public $articlesID = array();
    public $imagesID= array();
	
    public function __construct($db){
        $this->conn = $db;
    }	
	function getImages($item) {
        $stmt2 = $this->conn->prepare(" SELECT DISTINCT `".$this->imagesTable."`.`path`, `".$this->imagesTable."`.`type`, `".$this->imagesTable."`.`image`, `".$this->imagesTable."`.`created_date`
        FROM `".$this->imagesTable."` LEFT JOIN `".$this->articleImagesTable."` ON `".$this->articleImagesTable."`.`image_id` = `".$this->imagesTable."`.`id`
        WHERE `".$this->articleImagesTable."`.`image_id` = `".$this->imagesTable."`.id AND `".$this->articleImagesTable."`.`article_id`= '".strval($item)."'");
		if($stmt2->execute() === true){
			$results2 = $stmt2->get_result();
		//print_r($results2);
			return $results2;
		}
		//return false;			
	}
	
	function searchArticles($new_query){
        
      /*   $data = file_get_contents("php://input");
       
 

        $decoded_json = json_decode($data, false);

        if(!empty($decoded_json->query)){  */
           
           // $query =  (isset($decoded_json->query)) ? htmlspecialchars(strip_tags($decoded_json->query)) : "بيت لحم" ;
        
           // $new_query = $query;
                $stmt1 = $this->conn->prepare(
                    "SELECT DISTINCT `".$this->articlesTable."`.id, `".$this->articlesTable."`.title, `".$this->articlesTable."`.text, `".$this->articlesTable."`.modified_date, `".$this->articlesTable."`.created_date 
                    FROM `".$this->articlesTable."`
                   WHERE 
                     ( `".$this->articlesTable."`.title LIKE '%".$new_query."%'
                       OR `".$this->articlesTable."`.text LIKE  '%".$new_query."%')
                   ORDER BY `".$this->articlesTable."`.modified_date DESC, `".$this->articlesTable."`.created_date DESC
                   LIMIT 200 "
                   /*  "SELECT DISTINCT `".$this->articlesTable."`.id, `".$this->articlesTable."`.title, `".$this->articlesTable."`.text, `".$this->articlesTable."`.modified_date, `".$this->articlesTable."`.created_date , `".$this->imagesTable."`.path, `".$this->imagesTable."`.type, `".$this->imagesTable."`.created_date,
                    ROW_NUMBER() OVER (PARTITION BY `".$this->articlesTable."`.id ORDER BY `".$this->articlesTable."`.modified_date)
                    FROM `".$this->articlesTable."` JOIN `".$this->articleImagesTable."` ON `".$this->articlesTable."`.id = `".$this->articleImagesTable."`.article_id JOIN `".$this->imagesTable."` ON `".$this->articleImagesTable."`.image_id = `".$this->imagesTable."`.id 
                
                   WHERE 
                     ( 
                        `".$this->articlesTable."`.title LIKE '%".$new_query."%'
                       OR `".$this->articlesTable."`.text LIKE  '%".$new_query."%')
                   ORDER BY `".$this->articlesTable."`.modified_date DESC, `".$this->articlesTable."`.created_date DESC
                   LIMIT 200" */
                /* "SELECT DISTINCT Articles.ID, Articles.title, Articles.Text, Articles.ModifiedDate, Articles.CreatedDate, Types.Name, `".$this->imagesTable."`.Path, Images.Type, Images.image, Images.CreatedDate
                    FROM Articles LEFT JOIN `".$this->articleImagesTable."` ON `".$this->articleImagesTable."`.articleId = Articles.ID LEFT JOIN Images ON `".$this->articleImagesTable."`.imageId = Images.ID, Types
                    WHERE `".$this->articleImagesTable."`.imageId = Images.ID AND `".$this->articleImagesTable."`.articleId = Articles.ID AND ( 
                        Articles.title LIKE '%".$new_query."%' 
                        OR Articles.Text LIKE '%".$new_query."%' 
                        OR Types.Description LIKE '%".$new_query."%' 
                        OR Types.Name LIKE '%".$new_query."%' )
                    ORDER BY Articles.ModifiedDate DESC, Articles.CreatedDate DESC
                    LIMIT 200" */);
                   // $result = null;
                    $result1 = null; 
                    if($stmt1->execute() === true) { 
                       // $stmt1->execute();			
                        $result1 = $stmt1->get_result();
                        return $result1;
                       /*  foreach ($result1->fetch_assoc() as $key => $value) {
                           // $idea = mysql_real_escape_string($value['id']);
                           //b     $check = $idea->checkIdea($title);
                            if ($stmt1->getImages($value['id'])){
                                print_r($result1);
                                return $result1;
                               } else {
                                return null;
                               }
                        } */
                       /*  if($result1->num_rows > 0){    
                            
                           // $itemRecords=array();
                           // $itemRecords["articles"]=array(); 
                             foreach ($item = $result1->fetch_assoc() as $key => $value) { 	
                              //  print_r($item['id']);
                                extract($item); 
                               $stmt2 = $this->conn->prepare(" SELECT DISTINCT `".$this->imagesTable."`.`path`, `".$this->imagesTable."`.`type`, `".$this->imagesTable."`.`image`, `".$this->imagesTable."`.`created_date`
                               FROM `".$this->imagesTable."` LEFT JOIN `".$this->articleImagesTable."` ON `".$this->articleImagesTable."`.`image_id` = `".$this->imagesTable."`.`id`
                               WHERE `".$this->articleImagesTable."`.`image_id` = `".$this->imagesTable."`.id AND `".$this->articleImagesTable."`.`article_id`= '".$item['id']."'");
                               if($stmt2->execute() === true){
                                $result = $stmt2->get_result();
                               // return $result;
                               /*  while ($item2 = $result->fetch_assoc()) {
                               $itemDetails=array(
                                    "q" => $query,// ,
                                    "id" => $item['id'],
                                    "title" => $item['title'],
                                    "text" => $item['text'],
                                    "path" => $item2['path'],
                                    "category" => $item['name'],            
                                    "created" => $item['created_date'],
                                    "modified" => $item['modified_date']		
                                ); 
                                array_push($itemRecords["articles"], $itemDetails);
                            } * /
                            //echo "result1";
                          //  print_r(array_merge($item, $result->fetch_assoc()));	
                            return(array_merge($item, $result->fetch_assoc()));
                            } 
                            
                        }   
        
                        
                        } */
                        
                        
                    }
                
           	
            //return ($result1);	
      /*   } else {    
            http_response_code(404);    
            echo json_encode(array("message" => "Cannot search for null values"));
        }  */
    }
	
}
?>