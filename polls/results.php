<?php
cors();
header("Content-Type: application/json; charset=UTF-8");
require_once $_SERVER['DOCUMENT_ROOT'] . '/phpbackend/config/database.php';
include_once '../class/Poll.php';
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

$poll = new Poll;

$_POST = json_decode(file_get_contents('php://input'), true);
if(isset($_POST['pollID']) && preg_match('/^[0-9]+$/', strval($_POST['pollID'])) > 0) {
   //get poll result data
    $pollResult = $poll->getResult($_POST['pollID']);
    if($pollResult){
        if(!empty($pollResult['options']) && !empty($pollResult['total_votes'])) { 
            $i = 0;
            //Option bar color class array
            $barColorArr = array('azure','emerald','violet','yellow','red');
            //Generate option bars with votes count
            $optionsVoteArray = array();
            foreach($pollResult['options'] as $opt=>$vote){
                //Calculate vote percent
                $votePercent = round(($vote/$pollResult['total_votes'])*100);
                $votePercent = !empty($votePercent)?$votePercent.'%':'0%';
                //Define bar color class
                if(!array_key_exists($i, $barColorArr)){
                    $i = 0;
                }
                $barColor = $barColorArr[$i];
            // $barColor; 
                //$opt;
                //$votePercent; 
                array_push($optionsVoteArray, Array($opt, $barColor, $votePercent));
                $i++ ;
            } 
            echo json_encode($optionsVoteArray);
            http_response_code(200);          
            
        } else {
            http_response_code(404);          
            echo json_encode(array("message" => "There are no Votes for this poll"));
        }
    } else {
        http_response_code(404);          
        echo json_encode(array("message" => "Please enter poll id"));
    }
} else {
    http_response_code(404);          
    echo json_encode(array("message" => "Please Enter Valid Poll ID"));
}


?>