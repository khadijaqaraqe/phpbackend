<?php
 cors();
/* header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
//header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With"); */
 
require_once $_SERVER['DOCUMENT_ROOT'] . '/phpbackend/config/database.php';
include_once '../class/DepartmentArticles.php';
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
 
$items = new DepartmentArticles($db);
 
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



   /*   
    cors();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/phpbackend/config/database.php';
    include_once '../class/Articles.php';
    
    $database = new Database();

    $db = $database->getConnection();
    function cors() {
    
      // Allow from any origin
      if (isset($_SERVER['HTTP_ORIGIN'])) {
          
          header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
          header('Access-Control-Allow-Credentials: true');
          header('Access-Control-Max-Age: 86400');    // cache for 1 day
          header("Content-Type: multipart/form-data");
      }
      
      // Access-Control headers are received during OPTIONS requests
      if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
          
          if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
              // may also be using PUT, PATCH, HEAD etc
              header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT");
          
          if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
              header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
      
          exit(0);
      }
      
    }

    $items = new Articles($db);
    $folderPath = dirname(__DIR__,2)."/images/";
    $postData = file_get_contents("php://input");
    
    
    
    $data = json_decode($postData);

    $items->title = $data->Title;// $_POST['Title'];//$_POST["Title"]; 

    $items->text = $data->Text;//$_POST['Text'];//($_POST["Text"]); */
    
/*

 */
  	/* foreach ($data->fileSource as $key => $value) {	
		$image_parts = explode(";base64,", $value);
		$image_type_aux = explode("image/", $image_parts[0]);
		echo($image_type_aux[2]);
		$image_type = $image_type_aux[1];
		$image_base64 = base64_decode($image_parts[1]);
		$file_name = uniqid() . '.'.'jpeg';
		$file = $folderPath . $file_name;
		$items->Path = "images/".$file_name; 
		$items->Type = "image/jpeg";

		if( $image_type != "jpg" && $image_type != "png" && $image_type != "jpeg"  && $image_type != "gif" ) {
		echo json_encode(array("message" => "Sorry, only JPG, JPEG, PNG & GIF files are allowed."));
		} else {
		file_put_contents($file, $image_base64);
		if ($items->createImages()){
			// echo "Image was added.";
		} else {
			// echo "Unable to add image.";
		}
		}
  	} */
  /* if($items->update()) {         
    http_response_code(200);          
    echo json_encode(array("message" => "Article was updated."));
  } else {         
    http_response_code(503);        
    echo json_encode(array("message" => "Unable to update article."));
  }  */

  ?>