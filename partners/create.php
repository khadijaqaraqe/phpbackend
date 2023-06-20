<?php
  
  cors();

    require_once $_SERVER['DOCUMENT_ROOT'] . '/phpbackend/config/database.php';
    include_once '../class/Partners.php';
    
    $database = new Database();

    $db = $database->getConnection();
    function cors() {
      // Allow from any origin
      if (isset($_SERVER['HTTP_ORIGIN'])) {
          // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
          // you want to allow, and if so:
          header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
          header('Access-Control-Allow-Credentials: true');
          header('Access-Control-Max-Age: 86400');    // cache for 1 day
          header("Content-Type: multipart/form-data");
      }
      
      // Access-Control headers are received during OPTIONS requests
      if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
          
          if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
              
              header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
          
          if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
              header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
      
          exit(0);
      }
    }
    
    $items = new Partners($db);
    $folderPath = dirname(__DIR__,2)."/attachments/";
    $postData = file_get_contents("php://input");
    
    $people_json = file_get_contents("php://input");
 
    $decoded_json = json_decode($people_json, false);
    
    $data = json_decode($postData);
    $items->name = $data->name;
    $items->minister = $data->minister;
    $items->description = $data->description;
    $items->url = $data->url;
    $items->facebook_url = $data->facebook_url;
    $items->instagram_url = $data->instagram_url;
    $items->youtube_url  = $data->youtube_url;
    $items->image = $data->file;
   
  foreach ($data->fileSource as $key => $value) {
    $image_parts = explode(";base64,", $value);
    
    $image_type_aux = explode("image/", $image_parts[0]);
   
    $image_type = $image_type_aux[1];
    $image_base64 = base64_decode($image_parts[1]);
    $file_name = uniqid() . '.'.'jpeg';
    $file = $folderPath . $file_name;
    $items->path = "attachments/".$file_name; 
    $items->type = "image/jpeg";

    if( $image_type != "jpg" && $image_type != "png" && $image_type != "jpeg"  && $image_type != "gif" ) {
      echo json_encode(array("message" => "Sorry, only JPG, JPEG, PNG & GIF files are allowed."));
    } else {
      file_put_contents($file, $image_base64);
      if ($items->createImages()){
       } else {
        
       }
    }
  }
  if($items->create()) {         
    http_response_code(200);          
    echo json_encode(array("message" => "Partner was created."));
  } else {         
    http_response_code(503);        
    echo json_encode(array("message" => "Unable to create Partner."));
  } 
