<?php
  
  cors();

    require_once $_SERVER['DOCUMENT_ROOT'] . '/phpbackend/config/database.php';
    include_once '../class/GovLetter.php';
    
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
    $folderPath = dirname(__DIR__,2)."/attachments/";
    $items = new GovLetter($db);
   
    $data = json_decode(file_get_contents("php://input")); // from front end angular app 
    
    if(!empty($data->text)){     
     
       
        $items->Text = $data->text;
        $items->Creator = $data->creator;
    
       /*  if ($data->fileSource) {
            foreach ($data->fileSource as $key => $value) {
                
                $image_parts = explode(";base64,", $value);
                
                $image_type_aux = explode("image/", $image_parts[0]);
                $pdf_type_aux = explode("application/", $image_parts[0]);

                $image_type = isset($image_type_aux[1]) ? $image_type_aux[1] : null ;
                $pdf_type =isset($pdf_type_aux[1]) ? $pdf_type_aux[1] : null ;

                // echo $pdf_type;
                $image_base64 = base64_decode($image_parts[1]);
                $items->Type = $pdf_type === 'pdf' ? 'pdf' : 'jpeg';

                //echo $items->Type;
                $file_name = uniqid() . '.'. $items->Type ;
                $file = $folderPath . $file_name;
                $items->Path = "attachments/".$file_name; 
                
                if($image_type != "jpg" && $image_type != "png" && $image_type != "jpeg"  && $image_type != "gif" && $pdf_type != "pdf") {
                    echo json_encode(array("message" => "Sorry, only JPG, JPEG, PNG, PDF & GIF files are allowed."));
                } else {
                    file_put_contents($file, $image_base64);
                    if ($items->createAttachments()){
                        // echo "Image was added.";
                    } else {
                        // echo "Unable to add image.";
                    }
                }
            }
        } */
        if($items->create()) {         
            http_response_code(200);          
            echo json_encode(array("message" => "Item was added."));
        } else {         
            http_response_code(503);        
            echo json_encode(array("message" => "Unable to add Item."));
        } 
       
    } else {    
        http_response_code(400);    
        echo json_encode(array("message" => "Unable to create Item. Data is incomplete."));
    }
