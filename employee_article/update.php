<?php
 cors();
/* header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
//header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With"); */
 
require_once $_SERVER['DOCUMENT_ROOT'] . '/phpbackend/config/database.php';
include_once '../class/EmployeesArticles.php';
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
 
$items = new EmployeesArticles($db);
 
$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id) && !empty($data->Title) && 
!empty($data->Text)){ 
	
	$items->id = $data->id;
    $items->title = $data->Title;
    $items->text = $data->Text;
	
	if($items->update()){     
		http_response_code(200);   
		echo json_encode(array("message" => "article was updated."));
	} else {    
		http_response_code(503);     
		echo json_encode(array("message" => "Unable to update articles."));
	}
	
} else {
	http_response_code(400);    
    echo json_encode(array("message" => "Unable to update articles. Data is incomplete."));
}

  ?>