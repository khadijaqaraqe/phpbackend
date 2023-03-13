<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 36000");
    header("Access-Control-Allow-Headers: Accept, Origin, Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    
    require_once $_SERVER['DOCUMENT_ROOT'] . '/phpbackend/config/database.php';
    include_once '../class/Complaints.php';
    
    $database = new Database();
    $db = $database->getConnection();
    
    $items = new Complaints($db);
    $data = json_decode(file_get_contents("php://input"));

    if(!empty($data->Topic) && !empty($data->PhoneNumber) && !empty($data->ComplaintText) ){     
     
        $items->Name = $data->Name;
        $items->PhoneNumber = $data->PhoneNumber;
        $items->Topic = $data->Topic;	
        $items->ComplaintText = $data->ComplaintText;
       // $items->Email = $data->Email;
        $items->UserId = $data->UserId;	
       
        if($items->create()) {         
            http_response_code(200);          
            echo json_encode(array("message" => "Complaint was added."));
        } else {         
            http_response_code(503);        
            echo json_encode(array("message" => "Unable to add complaint."));
        } 
       
    } else {    
        http_response_code(400);    
        echo json_encode(array("message" => "Unable to create complaint. Data is incomplete."));
    } 
?>
