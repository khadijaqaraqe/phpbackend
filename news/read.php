<?php
header("Content-Type: application/json; charset=UTF-8");
require_once $_SERVER['DOCUMENT_ROOT'] . '/phpbackend/config/database.php';

include_once '../class/BreakNews.php';


$BreakNews = new BreakNews;
$newsData = $BreakNews->getNews();

if ($newsData) {
    echo json_encode($newsData);
    http_response_code(200);  
} else {     
    http_response_code(404);     
    echo json_encode(
        array("message" => "No news found.")
    );
} 
?>