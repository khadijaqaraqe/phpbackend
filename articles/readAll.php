<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


require_once $_SERVER['DOCUMENT_ROOT'] . '/phpbackend/config/database.php';
include_once '../class/Articles.php';

$database = new Database();
$db = $database->getConnection();
 
$items = new Articles($db);

$items->FirstRow = (isset($_GET['FirstRow']) && $_GET['FirstRow']) ? $_GET['FirstRow'] : "";
$items->LastRow = (isset($_GET['LastRow']) && $_GET['LastRow']) ? $_GET['LastRow'] : "";

$result = $items->GetFirst25();

if($result->num_rows > 0){    
    $itemRecords=array();
    $itemRecords["articles"]=array(); 
	 while ($item = $result->fetch_assoc()) { 	
        extract($item); 

        $itemDetails=array(
            "id" => $item['ID'],
            "firstName" => $item['FirstName'],
            "lastName" => $item['LastName'],
            "title" => $item['Title'],
			"text" => $item['Text'],
            "path" => $item['Path'],
            //"image" => $item['image'],
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
        array("message" => "No article found.")
    );
} 