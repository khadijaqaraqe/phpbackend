<?php
class Users{   
   
    // database connection and table name
    private $conn;
    private $table_name = "Users";
 
    // object properties
    public $id;
    public $username;
    public $email;
    public $password;
    public $LastName;
    public $FirstName;
    public $DateOfBirth;
    public $PhoneNumber;
    public $DepartmentID;
 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    // signup user
    function signup(){

        // query to insert record

        $query = 
        "INSERT INTO
        " . $this->table_name . "
                SET
                    `ID`=?,
                    `UserName`=?, 
                    `Password`=?, 
                    `Email`=?, 
                    `FirstName`=?, 
                    `LastName`=?, 
                    `PhoneNumber`=?, 
                    `DateOfBirth`=?, 
                    `DepartmentID`=?";
        
        /*  "INSERT INTO `Users` (`ID`, `FirstName`, `LastName`, `Email`, `PhoneNumber`, `Password`, `DateOfBirth`, `DepartmentID`, `UserName) 
        VALUES (NULL, ':username', ':LastName', ':email', ':PhoneNumber', ':password', ':DateOfBirth', ':DepartmentID', ':username')"; */
         /* "INSERT INTO
                    " . $this->table_name . "
                SET
                    UserName=:username', 
                    Password=:password, 
                    Email=:email, 
                    FirstName=:FirstName, 
                    LastName=:LastName, 
                    PhoneNumber=:PhoneNumber, 
                    DateOfBirth=:DateOfBirth, 
                    DepartmentID=:DepartmentID
                    "; */
     
        // prepare query

        $uuid = $this->conn->prepare("SELECT UUID();");

        
		if($uuid->execute() === true) {
			$uuid->store_result();
			//$uuid->bind_result($this->user_id);
			$uuid->fetch();
			
			//$this->article_id = ($uuid->fetch_assoc())['UUID()'];

            $stmt = $this->conn->prepare($query);
        
            // sanitize3
            //$this->id               =   $this->user_id;
            $this->username         =   htmlspecialchars(strip_tags($this->username));
            $this->password         =   htmlspecialchars(strip_tags($this->password));
            $this->email            =   htmlspecialchars(strip_tags($this->email));
            $this->FirstName        =   htmlspecialchars(strip_tags($this->FirstName));
            $this->LastName         =   htmlspecialchars(strip_tags($this->LastName));
           // $this->PhoneNumber      =   ($this->PhoneNumber);
            $this->DateOfBirth      =   htmlspecialchars(strip_tags($this->DateOfBirth));
           // $this->DepartmentID     =   ($this->DepartmentID);
           
            // bind values

            /*    
                $stmt->bindParam("username", $this->username);
                $stmt->bindParam(":password", $this->password);
                $stmt->bindParam(":email", $this->email);
                $stmt->bindParam(":FirstName", $this->FirstName);
                $stmt->bindParam(":LastName", $this->LastName);
                $stmt->bindParam(":DateOfBirth", $this->DateOfBirth);
                $stmt->bindParam(":PhoneNumber", $this->PhoneNumber);
                $stmt->bindParam(":DepartmentID", $this->DepartmentID); 
            */

           /*  if($this->isAlreadyExist($this->username, $this->email)->num_rows > 0){

                return false;
            } else { */

                $stmt->bind_param('ssssssisi', $this->id, $this->username, $this->password, $this->email, $this->FirstName, 
                                $this->LastName, $this->PhoneNumber, $this->DateOfBirth, $this->DepartmentID);
                // execute query
                if($stmt->execute()){
                    
                    //$this->id = $this->conn->lastInsertId();
                    return true;
                }
         //  } 
        }
        return false;
    } 

    // login user
    function login() {
        $query1 = "SELECT `Password`
                    FROM   `Users`
                    WHERE (Users.UserName = ?) OR ( Users.Email = ?)
                    LIMIT 1;";
        $stmt1 = $this->conn->prepare($query1);
        
        $this->username         =   htmlspecialchars(strip_tags($this->username));
        $this->email            =   htmlspecialchars(strip_tags($this->email));
        $stmt1->bind_param( 'ss', $this->username, $this->email );
/* $row = $stmt->get_result()->fetch_assoc();
    if($row){

        // create array
        $user_arr=array(
            "status" => true,
            "message" => "Successfully Login!",
            "id" => $row['ID'],
            "username" => $row['Username'], 
            "email"=> $row['Email']
        );
        http_response_code(200); 
        echo json_encode($user_arr);
    } else {
        $user_arr=array(
            "status" => false,
            "message" => "Invalid Credintials!"
        );
        echo json_encode($user_arr);
        http_response_code(401); 
    }
     */
        
        if($stmt1->execute()) {    
            $hashPassword = $stmt1->get_result()->fetch_assoc()['Password'];  
            
            // select all query
            $query = 
                    "SELECT
                        `ID`, `Username`, `createdAt`, `FirstName`, `LastName`, `Email`, `PhoneNumber`, `DepartmentID`, `DateOfBirth`
                    FROM
                    `Users`
                    WHERE
                        (Users.UserName = ? AND Users.Password = ?) OR ( Users.Email = ? AND Users.Password = ?)
                        
                    LIMIT  1;";
                
            // prepare query statement
            $stmt = $this->conn->prepare($query);
            
            $this->username         =   htmlspecialchars(strip_tags($this->username));

            $this->password         =   htmlspecialchars(strip_tags($this->password)); 
            
            $this->email            =   htmlspecialchars(strip_tags($this->email));
            $isPasswordCorrect = password_verify($this->password, $hashPassword);
         //echo $hashPassword ."Pas";
            if($isPasswordCorrect == 1) { 
            // execute query
            $stmt->bind_param( 'ssss', $this->username, $hashPassword, $this->email, $hashPassword );

            $stmt->execute();
            if($stmt->execute()) {



                 $row = $stmt->get_result()->fetch_assoc();
                //if($row){

                    // create array
                    $user_arr=array(
                        "status" => true,
                        "message" => "Successfully Login!",
                        "id" => $row['ID'],
                        "username" => $row['Username'], 
                        "email"=> $row['Email']
                    );
                    http_response_code(200); 
                    echo json_encode($user_arr);
                /* } else {
                    $user_arr=array(
                        "status" => false,
                        "message" => "Invalid Credintials!"
                    );
                    echo json_encode($user_arr);
                    http_response_code(401); 
                }
 */


                return $stmt;
            }
            else {
                return false;
            } 
        } else {
            $user_arr=array(
                "status" => false,
                "message" => "Invalid Credintials!"
            );
            echo json_encode($user_arr);
            http_response_code(401); ;
            }
        } else {
            return false;
        } 
    }
    
    // a function to check if username already exists
    function isAlreadyExist() {

        $query = "SELECT *
            FROM
                " . $this->table_name . " 
            WHERE
            Users.UserName='?' OR Users.Email ='?'";
             
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ss',  $this->username, $this->email);
        // execute query
        $stmt->execute();
        if($stmt->num_rows > 0) {
            return true;
        }
        else {
            return false;
        } 
    }
	
	/* 
    function readUsers(){	
		$stmt = $this->conn->prepare("SELECT `ID`, `FirstName`, `LastName`, `Email`, `PhoneNumber`, `Password`, `DateOfBirth`, `DepartmentID` FROM Users");
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}

    function create(){
        $stmt = $this->conn->prepare(
            "INSERT INTO `Users` (`ID`, `FirstName`, `LastName`, `Email`, `PhoneNumber`, `Password`, `DateOfBirth`, `DepartmentID`) 
                VALUES (NULL, ?, ?, ?, ?, ?, ?, ?)");
       
        $this->FirstName = htmlspecialchars(strip_tags($this->FirstName));
        $this->LastName = htmlspecialchars(strip_tags($this->LastName));
        $this->Email = htmlspecialchars(strip_tags($this->Email));
        $this->PhoneNumber = htmlspecialchars(strip_tags($this->PhoneNumber));
        $this->Password = htmlspecialchars(strip_tags($this->Password));
        $this->DateOfBirth = htmlspecialchars(strip_tags($this->DateOfBirth));
        $this->DepartmentID = htmlspecialchars(strip_tags($this->DepartmentID));
        $stmt->bind_param( "sssissi", 
            $this->FirstName,
            $this->LastName,
            $this->Email,
            $this->PhoneNumber,
            $this->Password,
            $this->DateOfBirth,
            $this->DepartmentID
        );
        if($stmt->execute()){
            return true; 
        }
        ret urn false;
	}*/
}
?>