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


//if(!empty($data->Topic) && !empty($data->PhoneNumber) && !empty($data->ComplaintText) ){     }
$data = file_get_contents("php://input");
       
 

$decoded_json = json_decode($data, false);

if(!empty($decoded_json->query)){ 
$query =  (isset($decoded_json->query)) ? htmlspecialchars(strip_tags($decoded_json->query)) : "بيت لحم" ;

$result = $items->searchArticles($query);

if($result->num_rows > 0){    
    $itemRecords=array();
    $itemRecords["articles"]=array(); 
	 while ($item = $result->fetch_assoc()) { 	
        extract($item); 
       
        $itemDetails=array(
            "q" => $items->q ,
            "id" => $item['ID'],
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
} }    else {    
    http_response_code(404);    
    echo json_encode(array("message" => "Cannot search for null values"));
} 