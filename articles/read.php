<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

header("Access-Control-Allow-Headers: Access-Control-Allow-Headers,Access-Control-Allow-Origin, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers");

require_once $_SERVER['DOCUMENT_ROOT'] . '/phpbackend/config/database.php';
include_once '../class/Articles.php';

$database = new Database();
$db = $database->getConnection();
 
$items = new Articles($db);

$items->id = (isset($_GET['id']) && $_GET['id']) ? $_GET['id'] : "";

$result = $items->read();

if($result->num_rows > 0){    
    $itemRecords=array();
    $itemRecords["articles"]=array(); 
    $ImagesDetails["images"]= array();
	 while ($item = $result->fetch_assoc()) { 	
        extract($item); 
       //print_r($item);
        $itemDetails=array(
            "id" => $item['id'],
            //"firstName" => $item['first_name'],
            //"lastName" => $item['last_name'],
            "title" => $item['title'],
			"text" => $item['text'],
           // "path" => $item['path'],
            //"category" => $item['name'],            
			"created" => $item['created_date'],
            "modified" => $item['modified_date'],
            "images"=> array()			
        ); 
        foreach ($item as $key => $value) {
            // $idea = mysql_real_escape_string($value['id']);
           $result2 = $items->getImages($value);

            //b     $check = $idea->checkIdea($title);
            //print_r($value);
             if ($result2->num_rows > 0){
                while ($item = $result2->fetch_assoc()) { 	
                    extract($item); 
                $ImagesItemDetails = array();
               // array_push($ImagesItemDetails, $item['path']); //json_encode
                //    "path" => $item['path'],
                    //"category" => $item['name']            
              //  ); 
             array_push($itemDetails['images'], $item['path']);   
            }
               
                } else {
                 
                }
            }
       array_push($itemRecords["articles"], $itemDetails);
    }    
   
    echo json_encode($itemRecords);
    http_response_code(200);     
    
}else{     
    http_response_code(404);     
    echo json_encode(
        array("message" => "No article found.")
    );
} 