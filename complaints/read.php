<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


require_once $_SERVER['DOCUMENT_ROOT'] . '/phpbackend/config/database.php';
include_once '../class/Complaints.php';

$database = new Database();
$db = $database->getConnection();
 
$items = new Complaints($db);

$items->id = (isset($_GET['id']) && $_GET['id']) ? $_GET['id'] : "";

$result = $items->read();

if($result->num_rows > 0){    
    $itemRecords=array();
    $itemRecords["complaints"]=array(); 
	 while ($item = $result->fetch_assoc()) { 	
        extract($item); 

        $itemDetails=array(
            "id" => $item['id'],
            "Name" => $item['name'],
            "PhoneNumber" => $item['phone_number'],
            "Topic" => $item['topic'],
			"Association" => $item['association'],
            "ComplaintText" => $item['complaint_text'],            
			"ComplaintDate" => $item['complaint_date'],
            "Email" => $item['email'],
            "UserId" => $item['user_id']		
        ); 
       array_push($itemRecords["complaints"], $itemDetails);
    }    
   
    echo json_encode($itemRecords);
    http_response_code(200);     
    
}else{     
    http_response_code(404);     
    echo json_encode(
        array("message" => "No complaint found.")
    );
} 