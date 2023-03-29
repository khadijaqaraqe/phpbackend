<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/phpbackend/config/database.php';
include_once '../class/Poll.php';

$poll = new Poll;
$pollData = $poll->updatePoll(json_decode(file_get_contents("php://input"), true));
 
if($pollData) {         
    http_response_code(200);          
    echo json_encode(array("message" => "Poll was updated."));
  } else {         
    http_response_code(503);        
    echo json_encode(array("message" => "Unable to update poll."));
  } 

?>