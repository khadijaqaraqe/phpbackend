<?php
	 cors();
	require_once $_SERVER['DOCUMENT_ROOT'] . '/phpbackend/config/database.php';
	include_once '../class/Directorates.php';
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
 
$items = new Directorates($db);
 
$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id) && !empty($data->title) && 
	!empty($data->text) &&!empty($data->director) &&!empty($data->url) &&!empty($data->phone_number1) && !empty($data->fax_num) 
	&& !empty($data->facebook_url) && !empty($data->instagram_url) && !empty($data->phone_number2) 
	&& !empty($data->twitter_url) && !empty($data->linkedin_url) && !empty($data->whatsapp_url) && !empty($data->youtube_url) && !empty($data->description)){ 
	
	$items->id = $data->id;
    $items->title = $data->title;
    $items->text = $data->text;
    $items->description = $data->description;
    $items->director = $data->director;
    $items->url = $data->url;
    $items->phone_number1 = $data->phone_number1;
    $items->fax_num = $data->fax_num;
    $items->facebook_url = $data->facebook_url;
    $items->instagram_url = $data->instagram_url;
    $items->phone_number2 = $data->phone_number2;
    $items->twitter_url = $data->twitter_url;
    $items->linkedin_url = $data->linkedin_url;
    $items->whatsapp_url = $data->whatsapp_url;
    $items->youtube_url  = $data->youtube_url;

	if($items->update()){     
		http_response_code(200);   
		echo json_encode(array("message" => "Directorate was updated."));
	} else {    
		http_response_code(503);     
		echo json_encode(array("message" => "Unable to update Directorate."));
	}
	
} else {
	http_response_code(400);    
    echo json_encode(array("message" => "Unable to update Directorate. Data is incomplete."));
}

  ?>