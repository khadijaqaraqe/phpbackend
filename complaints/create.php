<?php
  cors();

   //  header("Access-Control-Allow-Origin: *");
   /*  header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST,GET,OPTIONS,DELETE,PUT");
    header("Access-Control-Max-Age: 36000");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Access-Control-Allow-Origin, Accept, Origin, Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With"); */
   /*  header("Content-Type: application/json; charset=UTF-8");
    header('Access-Control-Allow-Origin: *');

    header('Access-Control-Allow-Methods: GET, POST');

    header("Access-Control-Allow-Headers: X-Requested-With"); */
   // header("Access-Control-Allow-Origin: *");
   // header("Access-Control-Allow-Headers: access");
    header("Access-Control-Allow-Methods: POST");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With,access");
    require_once $_SERVER['DOCUMENT_ROOT'] . '/phpbackend/config/database.php';
    include_once '../class/Complaints.php';
    
    $database = new Database();
    $db = $database->getConnection();
    $folderPath = dirname(__DIR__,2)."/attachments/";
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


    $items = new Complaints($db); // class 
    $data = json_decode(file_get_contents("php://input")); // from front end angular app 
    
    if(!empty($data->Topic) && !empty($data->PhoneNumber) && !empty($data->ComplaintText) ){     
     
        $items->Name = $data->Name;
        $items->PhoneNumber = $data->PhoneNumber;
        $items->Topic = $data->Topic;	
        $items->ComplaintText = $data->ComplaintText;
        $items->Email = $data->Email;
        $items->UserId = $data->UserId;	
        $items->Association = ($data->Association ==='others') ? htmlspecialchars(strip_tags($data->ComplaintAssociation)) : htmlspecialchars(strip_tags($data->Association));
        //$items->Association = $data->Association;
        //$items->ComplaintAssociation = $data->ComplaintAssociation;
        if ($data->fileSource) {
            foreach ($data->fileSource as $key => $value) {
                $image_parts = explode(";base64,", $value);
                //echo $image_parts;
                $image_type_aux = explode("image/", $image_parts[0]);
                //echo($image_type_aux[2]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                $file_name = uniqid() . '.'.'jpeg';
                $file = $folderPath . $file_name;
                $items->Path = "attachments/".$file_name; 
                $items->Type = "image/jpeg";
            
                if( $image_type != "jpg" && $image_type != "png" && $image_type != "jpeg"  && $image_type != "gif" ) {
                    echo json_encode(array("message" => "Sorry, only JPG, JPEG, PNG & GIF files are allowed."));
                } else {
                    file_put_contents($file, $image_base64);
                    if ($items->createAttachments()){
                        // echo "Image was added.";
                    } else {
                        // echo "Unable to add image.";
                    }
                }
            }
        }
        if($items->create()) {         
            http_response_code(200);          
            echo json_encode(array("message" => "Complaint was added."));
        } else {         
            http_response_code(503);        
            echo json_encode(array("message" => "Unable to add complaint."));
        } 
       
    } else {    
        http_response_code(400);    
        echo json_encode(array("message" => "Unable to create complaint. Data is incomplete."));
    } 
?>
