<?php
header("Content-Type: application/json");
include 'db.php';

$headers = getallheaders();
$token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : '';

if ($token && $redis) {
    $redis->del($token); 
    echo json_encode(["success" => true, "message" => "Logged out successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "No token found"]);
}
?>