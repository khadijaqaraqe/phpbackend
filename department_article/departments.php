<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

header("Access-Control-Allow-Headers: Access-Control-Allow-Headers,Access-Control-Allow-Origin, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers");

require_once $_SERVER['DOCUMENT_ROOT'] . '/phpbackend/config/database.php';
include_once '../class/DepartmentArticles.php';

$database = new Database();
$db = $database->getConnection();
 
$items = new DepartmentArticles($db);

$result = $items->GetDepartments();

if($result->num_rows > 0){    
    $itemRecords=array();
    $itemRecords["departments"]=array(); 
	 while ($item = $result->fetch_assoc()) { 	
        extract($item); 

        $itemDetails=array(
            "id" => $item['id'],
            "name" => $item['name'],
            "manager" => $item['manager']
           	
        ); 
       array_push($itemRecords["departments"], $itemDetails);
    }    
   
    echo json_encode($itemRecords);
    http_response_code(200);     
    
}else{     
    http_response_code(404);     
    echo json_encode(
        array("message" => "No department found.")
    );
} 