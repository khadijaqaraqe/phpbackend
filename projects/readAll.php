<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers,Access-Control-Allow-Origin, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers");


require_once $_SERVER['DOCUMENT_ROOT'] . '/phpbackend/config/database.php';
include_once '../class/Projects.php';

$database = new Database();
$db = $database->getConnection();
 
$items = new Projects($db);


$result = $items->read();

if($result->num_rows > 0){    
    $itemRecords=array();
    $itemRecords["project"]=array(); 
	 while ($item = $result->fetch_assoc()) { 	
        extract($item); 

        $itemDetails=array(
            "id" => $item['id'],
            "title" => $item['title'],
			"text" => $item['text'],
            'creator' => $item['creator'],
            "created" => $item['created'],
            "modified" => $item['modified'], 
            "images"=> array()		
        ); 
        foreach ($item as $key => $value) {
            $result2 = $items->getImages($value);
            if ($result2->num_rows > 0) {
                while ($item = $result2->fetch_assoc()) { 	
                    extract($item); 
                    $ImagesItemDetails = array();
                    array_push($itemDetails['images'], $item['path']);   
                }
            } else { }
        }
       array_push($itemRecords["project"], $itemDetails);
    }    
   
    echo json_encode($itemRecords);
    http_response_code(200);     
    
} else {     
    http_response_code(404);     
    echo json_encode(
        array("message" => "No Projects found.")
    );
} 