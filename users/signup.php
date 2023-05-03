<?php
/*  header("Access-Control-Allow-Origin: *");
 header("Content-Type: application/json; charset=UTF-8");
 
 
// get database connection
include_once '../config/database.php';
 
// instantiate user object
include_once '../class/Users.php';
 
$database = new Database();
$db = $database->getConnection();
 
$user = new Users($db);
$_POST = json_decode(file_get_contents('php://input'), true);
// set user property values
$user->username = $_POST['username']; //$_POST['username'];
//$user->password = $_POST['password'];//
$user->password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$user->email = $_POST['email'];
$user->DateOfBirth = date('Y-m-d H:i:s');
$user->PhoneNumber = $_POST['PhoneNumber'];
$user->FirstName = $_POST['FirstName'];
$user->LastName =  $_POST['LastName'];
$user->DepartmentID =  $_POST['DepartmentID'];

// create the user

if(!empty($user->username) && !empty($user->password) && !empty($user->email) && !empty($user->PhoneNumber)){     
   // if($user->isAlreadyExist($user->username, $user->email) -> num_rows > 0) {
        $user_arr=array(
            "status" => false,
            "message" => "Username or email already exists!"
        );
 //   } else {
        if($user->signup()){
            $user_arr=array(
                "status" => true,
                "message" => "Successfully Signup!",
                "id" => $user->id,
                "username" => $user->username,
                "email" => $user->email,
                "FirstName" => $user->FirstName, 
                "LastName" => $user->LastName,
                "DateOfBirth" => $user->DateOfBirth,
                "PhoneNumber" => $user->PhoneNumber,
                "DepartmentID" => $user->DepartmentID
            );
        }
        else {
            $user_arr=array(
                "status" => false,
                "message" => "Username already exists!"
            );
        } 
   // } 
} else {
        $user_arr=array(
            "status" => false,
            "message" => "Missing Credintials!"
        );
}
print_r(json_encode($user_arr));
?>



<?php */
cors();
header("Content-Type: application/json; charset=UTF-8");

/* 
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 */

include_once '../config/database.php';

$db_connection = new Database();
$conn = $db_connection->getConnection();
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
function msg($success, $status, $message, $extra = [])
{
    return array_merge([
        'success' => $success,
        'status' => $status,
        'message' => $message
    ], $extra);
}

// DATA FORM REQUEST
$data = json_decode(file_get_contents("php://input"));

$returnData = [];

if ($_SERVER["REQUEST_METHOD"] != "POST") :

    $returnData = msg(0, 404, 'Page Not Found!');

elseif (
    !isset($data->username)
    || !isset($data->email)
    || !isset($data->password)
    || empty(trim($data->username))
    || empty(trim($data->email))
    || empty(trim($data->password))
) :
    
    $fields = ['fields' => ['username', 'email', 'password']];
    $returnData = msg(0, 422, 'Please Fill in all Required Fields!', $fields);

// IF THERE ARE NO EMPTY FIELDS THEN-
else :

    $name            = trim($data->username);
    $firstname       = trim($data->firstname);
    $lastname        = trim($data->lastname);
    $email           = trim($data->email);
    $phonenumber     = (int)trim($data->PhoneNumber);
    $password        = trim($data->password);
    $departmentId    = (int)trim($data->DepartmentID);
    $dateofbirth     = trim($data->dateofbirth);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) :
        $returnData = msg(0, 422, 'Invalid Email Address!');

    elseif (strlen($password) < 8) :
        $returnData = msg(0, 422, 'Your password must be at least 8 characters long!');

    elseif (strlen($name) < 3) :
        $returnData = msg(0, 422, 'Your name must be at least 3 characters long!');

    elseif (strlen($phonenumber) >= 10 &&  strlen($phonenumber) <= 14) :
        $returnData = msg(0, 422, 'Your Phone number must be at least 10 characters long!');
    else :
        try {

            $check_email = "SELECT `email` FROM `users` WHERE `email`=?";
            $check_email_stmt = $conn->prepare($check_email);
            $check_email_stmt->bind_param('s', $email);
            $check_email_stmt->execute();
            $result = $check_email_stmt->get_result();
            if ($result->num_rows > 0) :
                $returnData = msg(0, 422, 'This E-mail already in use!');

            else :
                $insert_query = "INSERT INTO `Users` ( `UserName`,`Email`,`Password`, `DepartmentID`, `PhoneNumber`, `FirstName`, `LastName`, `DateOfBirth`) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?)";

                $insert_stmt = $conn->prepare($insert_query);
                // INSERT INTO `users` (`ID`, `FirstName`, `LastName`,  
                // `Email`, `PhoneNumber`, `Password`, `DateOfBirth`, `DepartmentID`, `UserName`, `createdAt`, `modifiedAt`)
                // VALUES (NULL, 'dsfsdf', 'fdgh', 'ewe324', '345', 'dg435', NULL, NULL, 'retertret', current_timestamp(), current_timestamp());
                // DATA BINDING
                //$datetime = date("Y-m-d H:i:s", mktime(10, 30, 0, 6, 10, 2015));
                $newName = htmlspecialchars(strip_tags($name));
                $hashedPass = password_hash($password, PASSWORD_DEFAULT);
                $insert_stmt->bind_param('sssiisss', $newName, $email, $hashedPass, $departmentId, $phonenumber, $firstname, $lastname, $dateofbirth);

                $insert_stmt->execute();

                $returnData = msg(1, 201, 'You have successfully registered.');

            endif;
        } catch (PDOException $e) {
            $returnData = msg(0, 500, $e->getMessage());
        }
    endif;
endif;

echo json_encode($returnData);