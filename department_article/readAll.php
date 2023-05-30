<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers,Access-Control-Allow-Origin, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers");


require_once $_SERVER['DOCUMENT_ROOT'] . '/phpbackend/config/database.php';
include_once '../class/EmpArticles.php';

$database = new Database();
$db = $database->getConnection();
 
$items = new DepartmentArticles($db);

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
            "firstName" => $item['first_name'],
            "lastName" => $item['last_name'],
            "title" => $item['title'],
			"text" => $item['text'],
            "path" => $item['path'],
            //"image" => $item['image'],
            "category" => $item['name'],            
			"created" => $item['created_date'],
            "modified" => $item['modified_date']		
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