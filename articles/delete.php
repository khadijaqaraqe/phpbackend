<?php
 	cors();

	require_once $_SERVER['DOCUMENT_ROOT'] . '/phpbackend/config/database.php';
	include_once '../class/Articles.php';
	function cors() {
    
		// Allow from any origin
		if (isset($_SERVER['HTTP_ORIGIN'])) {
			// Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
			// you want to allow, and if so:
			header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
			header('Access-Control-Allow-Credentials: true');
			header('Access-Control-Max-Age: 86400');    // cache for 1 day
			header("Content-Type: application/json");
		}
		
		// Access-Control headers are received during OPTIONS requests
		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
			
			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
				// may also be using PUT, PATCH, HEAD etc
				header("Access-Control-Allow-Methods: GET, OPTIONS, PUT");
			
			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
				header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
		
			exit(0);
		}	
	}

	$database = new Database();
	$db = $database->getConnection();
	
	$items = new Articles($db);
	$data = json_decode(file_get_contents("php://input"));// (isset($_GET['DeleteArticleID']) && $_GET['DeleteArticleID']) ? $_GET['DeleteArticleID'] : "";
	//$data = $_GET['DeleteArticleID'];// (isset($_GET['id']) && $_GET['id']) ? $_GET['id'] : "";//$_GET['id']//json_decode(file_get_contents("php://input"));
//	print_r ("_GET['DeleteArticleID']");
//	print_r ($_GET['DeleteArticleID']);

	if(!empty($data->id )){ 
		
		$items->id = $data->id;
		

		if($items->delete()) {     
			http_response_code(200);   
			echo json_encode(array("message" => "article was delete."));
		} else {    
			http_response_code(503);     
			echo json_encode(array("message" => "Unable to article articles."));
		}
		
	} else {
		http_response_code(400);    
		echo json_encode(array("message" => "Unable to delete articles. Data is incomplete."));
	}
  ?>