<?php
header("Content-Type: application/json; charset=UTF-8");
require_once $_SERVER['DOCUMENT_ROOT'] . '/phpbackend/config/database.php';

include_once '../class/Poll.php';

//$database = new Database();
//$db = $database->getConnection();
 
//$items = new Poll();

$poll = new Poll;
$pollData = $poll->getPolls();
//$result = $items->getPolls();
//$jsonPollData = json_encode($pollData);
if ($pollData) {
    echo json_encode($pollData);
    http_response_code(200);  
}else{     
    http_response_code(404);     
    echo json_encode(
        array("message" => "No poll found.")
    );
} 
//print_r($pollData);
/* if($$pollData->num_rows > 0){    
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
    
}else{     
    http_response_code(404);     
    echo json_encode(
        array("message" => "No article found.")
    );
}  */
?>
