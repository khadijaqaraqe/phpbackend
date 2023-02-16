<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once 'AuthMiddleware.php';
$allHeaders = getallheaders();
$db_connection = new Database();
$conn = $db_connection->getConnection();
$auth = new Auth($conn, $allHeaders);

echo json_encode($auth->isValid());