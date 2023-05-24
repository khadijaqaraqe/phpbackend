<?php
cors();
header("Content-Type: application/json; charset=UTF-8");
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
require_once $_SERVER['DOCUMENT_ROOT'] . '/phpbackend/config/database.php';
include_once '../class/Poll.php';

$poll = new Poll;

$_POST = json_decode(file_get_contents('php://input'), true);

    if(isset($_POST['voteSubmit']) && !empty($_POST['pollID']) && !preg_match("/[a-z]/i", strval($_POST['pollID']))){
        $voteData = array(
            'poll_id' => htmlspecialchars(strip_tags($_POST['pollID'])),
            'poll_option_id' => htmlspecialchars(strip_tags($_POST['voteOpt']))
        );
        //insert vote data
        $voteSubmit = $poll->vote($voteData);
        //echo $voteSubmit;
        if($voteSubmit) {
            //store in $_COOKIE to signify the user has voted
            setcookie($_POST['pollID'], 1, time()+60*60*24*365);
            http_response_code(200);          
            echo json_encode(array("message" => "Your vote has been submitted successfully."));
        } else {
            http_response_code(404);          
            echo json_encode(array("message" => "Your vote already had submitted."));
        }
    } else {
        http_response_code(404);          
        echo json_encode(array("message" => "No Matching poll"));
    }

?>