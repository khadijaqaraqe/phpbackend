<?php
  cors();
  header("Content-Type: application/json; charset=UTF-8");
/* header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Access-Control-Allow-Origin, Origin, X-Requested-With, Content-Type, Accept"); */
/* header("Access-Control-Allow-Origin: *");
    
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 36000");
    header("Access-Control-Allow-Headers: Accept, Origin, Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With"); */
//  header("Access-Control-Allow-Headers: Access-Control-Allow-Headers,Access-Control-Allow-Origin, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers");

require_once $_SERVER['DOCUMENT_ROOT'] . '/phpbackend/config/database.php';
include_once '../class/Search.php';
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
$database = new Database();
$db = $database->getConnection();

$items = new Search($db);

$items->q = (isset($_GET['q']) && $_GET['q']) ? $_GET['q'] : '0';


//if(!empty($data->Topic) && !empty($data->PhoneNumber) && !empty($data->ComplaintText) ){     }
$data = file_get_contents("php://input");
       
 

$decoded_json = json_decode($data, false);

if(!empty($decoded_json->query)){ 
$query =  (isset($decoded_json->query)) ? htmlspecialchars(strip_tags($decoded_json->query)) : "بيت لحم" ;

$result = $items->searchArticles($query);

if($result->num_rows > 0){    
    $itemRecords=array();
    $itemRecords["articles"]=array(); 
    $ImagesDetails["images"]= array();
	 while ($item = $result->fetch_assoc()) { 	
        extract($item); 
        $itemDetails=array(
            "q" => $query,// ,
            "id" => $item['id'],
            "title" => $item['title'],
			"text" => $item['text'],   
			"created" => $item['created_date'],
            "modified" => $item['modified_date'], 
            "images"=> array()	
        ); 
        foreach ($item as $key => $value) {
            // $idea = mysql_real_escape_string($value['id']);
           $result2 = $items->getImages($value);

            //b     $check = $idea->checkIdea($title);
            //print_r($value);
             if ($result2->num_rows > 0){
                while ($item = $result2->fetch_assoc()) { 	
                    extract($item); 
                $ImagesItemDetails = array();
               // array_push($ImagesItemDetails, $item['path']); //json_encode
                //    "path" => $item['path'],
                    //"category" => $item['name']            
              //  ); 
             array_push($itemDetails['images'], $item['path']);   
            }
               
                } else {
                 
                }
            }
           // array_push($itemRecords, $itemDetails['images']);
            array_push($itemRecords["articles"], $itemDetails);
    }    
    //array_push($itemRecords["articles"], $itemRecords);
    echo json_encode($itemRecords);
    http_response_code(200);     
    
} else {     
    http_response_code(404);     
    echo json_encode(
        array("message" => "No article found." )
    );
} }    else {    
    http_response_code(404);    
    echo json_encode(array("message" => "Cannot search for null values"));
} 