<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


require_once $_SERVER['DOCUMENT_ROOT'] . '/phpbackend/config/database.php';
include_once '../class/Tickers.php';

$database = new Database();
$db = $database->getConnection();
 
$items = new Tickers($db);

$result = $items->readTickers();

if($result->num_rows > 0){    
    $itemRecords=array();
    $itemRecords["tickers"]=array(); 
	 while ($item = $result->fetch_assoc()) { 	
        extract($item); 
        $itemDetails=array(
            "id" => $item['ID'],
            "title" => $item['Title'],
			"text" => $item['Text'],
        ); 
       array_push($itemRecords["tickers"], $itemDetails);
    }    
   
    echo json_encode($itemRecords);
    http_response_code(200);     
    
}else{     
    http_response_code(404);     
    echo json_encode(
        array("message" => "No tickers found.")
    );
} 