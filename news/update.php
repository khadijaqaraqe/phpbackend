<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/phpbackend/config/database.php';
include_once '../class/BreakNews.php';

$BreakNews = new BreakNews;
$newsData = $BreakNews->updateNews(json_decode(file_get_contents("php://input"), true));
 
if($newsData) {         
    http_response_code(200);          
    echo json_encode(array("message" => "News was updated."));
  } else {         
    http_response_code(503);        
    echo json_encode(array("message" => "Unable to update news."));
  } 

?>