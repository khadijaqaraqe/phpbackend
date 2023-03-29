<?php
header("Content-Type: application/json; charset=UTF-8");
require_once $_SERVER['DOCUMENT_ROOT'] . '/phpbackend/config/database.php';
include_once '../class/Poll.php';

$poll = new Poll;

$_POST = json_decode(file_get_contents('php://input'), true);
if(isset($_POST['voteSubmit'])){
    $voteData = array(
        'poll_id' => $_POST['pollID'],
        'poll_option_id' => $_POST['voteOpt']
    );
    //insert vote data
    $voteSubmit = $poll->vote($voteData);
    echo $voteSubmit;
    if($voteSubmit) {
        //store in $_COOKIE to signify the user has voted
        setcookie($_POST['pollID'], 1, time()+60*60*24*365);
        http_response_code(200);          
        echo json_encode(array("message" => "Your vote has been submitted successfully."));
    } else {
        http_response_code(404);          
        echo json_encode(array("message" => "Your vote already had submitted."));
    }
}

?>