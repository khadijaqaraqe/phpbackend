<?php
/* header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Access-Control-Allow-Origin, Origin, X-Requested-With, Content-Type, Accept"); */
header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 36000");
    header("Access-Control-Allow-Headers: Accept, Origin, Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
require_once $_SERVER['DOCUMENT_ROOT'] . '/phpbackend/config/database.php';
include_once '../class/Search.php';

$database = new Database();
$db = $database->getConnection();
 
$items = new Search($db);

$items->q = (isset($_GET['q']) && $_GET['q']) ? $_GET['q'] : '0';

$result = $items->searchArticles($items->q);

if($result->num_rows > 0){    
    $itemRecords=array();
    $itemRecords["articles"]=array(); 
	 while ($item = $result->fetch_assoc()) { 	
        extract($item); 
       
        $itemDetails=array(
            "q" => $items->q ,
            "id" => $item['ID'],
            //"firstName" => $item['FirstName'],
           // "lastName" => $item['LastName'],
            "title" => $item['Title'],
			"text" => $item['Text'],
            "path" => $item['Path'],
            "category" => $item['Name'],            
			"created" => $item['CreatedDate'],
            "modified" => $item['ModifiedDate']		
        ); 
       array_push($itemRecords["articles"], $itemDetails);
    }    
   
    echo json_encode($itemRecords);
    http_response_code(200);     
    
} else {     
    http_response_code(404);     
    echo json_encode(
        array("message" => "No article found." )
    );
} 