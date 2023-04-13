<?php
cors();
require_once $_SERVER['DOCUMENT_ROOT'] . '/phpbackend/config/database.php';
include_once '../../class/Publications.php';
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

$Publications = new Publications;
$data = json_decode(file_get_contents("php://input"), true);

$items = new Publications();
$folderPath = realpath(dirname(__DIR__,3))."/pdfs/";
  function cors() { 
    if (isset($_SERVER['HTTP_ORIGIN'])) {
      header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
      header('Access-Control-Allow-Credentials: true');
      header('Access-Control-Max-Age: 86400');    // cache for 1 day
      header("Content-Type: multipart/form-data");
    }

    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
      if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
          header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
      if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
          header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
      exit(0);
    }
  }
 
   
  $file_tmp = $_FILES['file']['tmp_name'];
  
  $file_name = uniqid() . '.'.'pdf';
  $file = $folderPath . $file_name;

  move_uploaded_file($file_tmp, $file);
  
  $items->Title = $_POST["Title"];//$data['Title'];
       
  $items->Creator = $_POST["Creator"];//$data['Creator'];	
  $items->Path = "pdfs/".$file_name;
   
$newsData = $Publications->createPlan((array)$items);
  if($newsData) {         
    http_response_code(200);          
    echo json_encode(array("message" => "Plan was created."));
  } else {         
    http_response_code(503);        
    echo json_encode(array("message" => "Unable to create Plan."));
  } 

?>