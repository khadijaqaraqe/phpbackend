<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

header("Access-Control-Allow-Headers: Access-Control-Allow-Headers,Access-Control-Allow-Origin, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers");

require_once $_SERVER['DOCUMENT_ROOT'] . '/phpbackend/config/database.php';
include_once '../class/Directorates.php';

$database = new Database();
$db = $database->getConnection();
 
$items = new Directorates($db);

$items->id = (isset($_GET['id']) && $_GET['id']) ? $_GET['id'] : "";

$result = $items->read();

if($result->num_rows > 0){    
    $itemRecords=array();
    $itemRecords["directorate"]=array(); 
	 while ($item = $result->fetch_assoc()) { 	
        extract($item); 

        $itemDetails=array(
            "id" => $item['id'],
            "title" => $item['title'],
			"text" => $item['text'],
            'description' => $item['description'],
            'director' => $item['director'],
            'url' => $item['url'],
            'phone_number1' => $item['phone_number1'],
            'fax_num' => $item['fax_num'],
            'facebook_url' => $item['facebook_url'],
            'instagram_url' => $item['instagram_url'],
            'phone_number2' => $item['phone_number2'],
            'twitter_url' => $item['twitter_url'],
            'linkedin_url' => $item['linkedin_url'],
            'whatsapp_url' => $item['whatsapp_url'],
            'youtube_url' => $item['youtube_url'],
            "created_at" => $item['created_at'],
            "modified_at" => $item['modified_at'],
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
        array_push($itemRecords["directorate"], $itemDetails);
    }    
   
    echo json_encode($itemRecords);
    http_response_code(200);     
    
} else {     
    http_response_code(404);     
    echo json_encode(
        array("message" => "No directorate found.")
    );
} 