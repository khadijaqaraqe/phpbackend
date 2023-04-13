<?php
cors();
header("Content-Type: application/json; charset=UTF-8");
require_once $_SERVER['DOCUMENT_ROOT'] . '/phpbackend/config/database.php';
include_once '../../class/Publications.php';
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

$Publications = new Publications;
$planData = $Publications->updatePlan(json_decode(file_get_contents("php://input"), true));
 
if($planData) {         
    http_response_code(200);          
    echo json_encode(array("message" => "Plan was updated."));
  } else {         
    http_response_code(503);        
    echo json_encode(array("message" => "Unable to update plan."));
  } 

?>