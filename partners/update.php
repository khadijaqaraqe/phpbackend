<?php
	 cors();
	require_once $_SERVER['DOCUMENT_ROOT'] . '/phpbackend/config/database.php';
	include_once '../class/Partners.php';
	function cors() {
    
	// Allow from any origin
	if (isset($_SERVER['HTTP_ORIGIN'])) {
		// Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
		// you want to allow, and if so:
		header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
		header('Access-Control-Allow-Credentials: true');
		header('Access-Control-Max-Age: 86400');    // cache for 1 day
		header("Content-Type: application/json; charset=UTF-8");
	}
	
	// Access-Control headers are received during OPTIONS requests
	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
		
		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
			// may also be using PUT, PATCH, HEAD etc
			header("Access-Control-Allow-Methods: PUT, GET, POST, OPTIONS");
		
		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
			header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
	
		exit(0);
	}
	
   // echo "You have CORS!";
  }

$database = new Database();
$db = $database->getConnection();
 
$items = new Partners($db);
 
$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id) && !empty($data->name) && 
	!empty($data->minister)  &&!empty($data->url) && !empty($data->facebook_url) && !empty($data->instagram_url) 
	&& !empty($data->youtube_url) && !empty($data->description)){ 
	
	$items->id = $data->id;
    $items->name = $data->name;
    $items->minister = $data->minister;
    $items->description = $data->description;
    $items->url = $data->url;
    $items->facebook_url = $data->facebook_url;
    $items->instagram_url = $data->instagram_url;
    $items->youtube_url  = $data->youtube_url;

	if($items->update()){     
		http_response_code(200);   
		echo json_encode(array("message" => "Partner was updated."));
	} else {    
		http_response_code(503);     
		echo json_encode(array("message" => "Unable to update Partner."));
	}
	
} else {
	http_response_code(400);    
    echo json_encode(array("message" => "Unable to update Partner. Data is incomplete."));
}

  ?>