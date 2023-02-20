<?php
  //ini_set("allow_url_fopen", true);
  cors();
  //ini_set("auto_detect_line_endings", true);
  /*   
      header("Access-Control-Allow-Origin: *");
      header("Content-Type: application/json");//multipart/form-data
      header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT, PATCH, OPTIONS");
      header("Access-Control-Max-Age: 36000");
      header("Access-Control-Allow-Headers: Accept, Origin, Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With"); 
    */
 
    require_once $_SERVER['DOCUMENT_ROOT'] . '/phpbackend/config/database.php';
    include_once '../class/Articles.php';
    
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
              // may also be using PUT, PATCH, HEAD etc
              header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
          
          if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
              header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
      
          exit(0);
      }
      
     // echo "You have CORS!";
    }
    //  $data=file_get_contents('php://input');
    // $data = file_get_contents("php://input");
    // global $_POST_JSON;
    // print_r("datadatadatadatadatadatadatadatadatadata");
    //   print_r($data);
    $items = new Articles($db);
    $folderPath = dirname(__DIR__,2)."/images/";
    $postData = file_get_contents("php://input");
    
    $people_json = file_get_contents("php://input");
 
    $decoded_json = json_decode($people_json, false);
    
    echo $decoded_json->Title;
    // Monty
    
    echo $decoded_json->Text;
    //$data = json_decode($postData);
    //$postData = $_POST["request"];
    $data = json_decode($postData);
    //$data = $postData;
    /*  echo "data";
        echo $data; */
    /* echo "_POST[Title]";
    echo isset($_POST["Title"]);
    echo "_POST[Text]";
    echo isset($_POST["Text"]); */
    //  echo ($_POST["request"]); 
    //echo($data['Title']);
    // echo("F===----------------------------ILES");
    //echo($data);
    //echo($_POST['Title']);
    $items->Title = $data->Title;// $_POST['Title'];//$_POST["Title"]; 
    $items->CreatorId = $data->creatorId;
    $items->Category = 1;	
    $items->Text = $data->Text;//$_POST['Text'];//($_POST["Text"]);
    
    $items->AltText =$data->Title;//($_POST["Title"]);
    $items->Description =$data->Title;//($_POST["Title"]);
    $items->image = $data->file;
    //$array = json_decode(json_encode($data->file), true);
    // $items->image = $array;
    //print_r("F====ILES");
    //print_r($_FILES['fileSource']['size']);
    //$items->image
    // print_r($items);
    //print_r($_FILES);
    
    // echo "_FILES----------------------";
    // print_r( $_FILES['file']);
    //if (is_uploaded_file($_FILES['file']['tmp_name']))  {
    //if (isset($_FILES['file']['name'])) {
    // echo("the File");
    //count($_FILES['file']["tmp_name"]);
    // } else { 
    //   echo "enable to read file";
    // }
    
    //echo "count(array_filter))--------------------------";
    //echo count(array_filter($_FILES['file[]']['name']));
    
    // $countfiles = count($_FILES['file']['name']) ;
   

 /*     $countfiles =  count($data->fileSource); //$_FILES['file'] ?? null
    for($i = 0; $i < $countfiles; $i++) { */

      // $countfiles = count($_FILES['file']['name']); 
      // $folderPath = "uploads/";

   // $target_file = $_FILES["file"][$i];

 /*    $uploadOk = 1;
    // $imageFileType = strtolower(pathinfo($target_file),PATHINFO_EXTENSION);
    $imageFileType = pathinfo($data->fileSource['name'][$i], PATHINFO_EXTENSION);
    // Check if image file is a actual image or fake image
    if(isset($_POST["submit"])) {
      $check = getimagesize($data->fileSource["tmp_name"][$i]);
      if($check !== false) {
       // echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
      } else {
       // echo "File is not an image.";
        $uploadOk = 0;
      }
    }
    
    // Check file size
    if (intval($data->fileSource["size"][$i]) > 900000) {
      echo "Sorry, your file is too large.";
      $uploadOk = 0;
    }
    
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
      echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
      $uploadOk = 0;
    }
    
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
      echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        $imagePath =  uniqid() . '.'.'jpeg';
        $file = $folderPath .$imagePath;
      if (move_uploaded_file($data->fileSource["tmp_name"][$i], $file)) {
        $items->Path = "images/".$imagePath; 
        $items->Type = "image/jpeg";
        //echo $items;
        if ($items->createImages()){
         // echo "Image was added.";
        } else {
         // echo "Unable to add image.";
        }
 
      } else {
      //  echo "Sorry, there was an error uploading your file.";
      }
    }

  } */ 
  foreach ($data->fileSource as $key => $value) {
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
  }
  if($items->create()) {         
    http_response_code(200);          
    echo json_encode(array("message" => "Article was created."));
  } else {         
    http_response_code(503);        
    echo json_encode(array("message" => "Unable to create article."));
  } 


/*
   
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Content-Type: multipart/form-data");
require_once $_SERVER['DOCUMENT_ROOT'] . '/phpbackend/config/database.php';
include_once '../class/Articles.php';

$database = new Database();

$db = $database->getConnection();
//  $data=file_get_contents('php://input');
// $data = file_get_contents("php://input");
// global $_POST_JSON;
// print_r("datadatadatadatadatadatadatadatadatadata");
//   print_r($data);
$items = new Articles($db);
$folderPath = dirname(__DIR__,2)."/images/";

//$folderPath = "images/";
$postdata = file_get_contents("php://input");

$request = json_decode($postdata);

$items->Title =  $request->Title;//$_POST["Title"]; 
$items->CreatorId = 1;
$items->Category = 1;	
$items->Text =$request->Text;//($_POST["Text"]);

$items->AltText = $request->Title;//($_POST["Title"]);
$items->Description = $request->Title;//($_POST["Title"]);
//$items->image = $data->file;
//$array = json_decode(json_encode($data->file), true);
// $items->image = $array;
print_r("F====ILES"); 
print_r($_FILES);
foreach ($request->fileSource as $key => $value) {
    $image_parts = explode(";base64,", $value);
    $image_type_aux = explode("image/", $image_parts[0]);
    $image_type = $image_type_aux[1];
    $image_base64 = base64_decode($image_parts[1]);
    $file = $folderPath . uniqid() . '.' . 'jpeg';
    file_put_contents($file, $image_base64);

    $imagePath =  uniqid() . '.'.'jpeg';
        $file = $folderPath .$imagePath;
      if (move_uploaded_file($_FILES["file"]["tmp_name"][$i], $file)) {
        $items->Path = "images/".$imagePath; 
        $items->Type = "image/jpeg";
        //echo $items;
        if ($items->createImages()){
         // echo "Image was added.";
        } else {
         // echo "Unable to add image.";
        }
 
      } else {
      //  echo "Sorry, there was an error uploading your file.";
      }
      if($items->create()) {         
        http_response_code(200);          
        echo json_encode(array("message" => "Article was created."));
      } else {         
        http_response_code(503);        
        echo json_encode(array("message" => "Unable to create article."));
      }
}

?>
*/