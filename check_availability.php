<?php
header("Content-Type: application/json");
include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

if ($data && isset($data['email'])) {
    $email = $conn->real_escape_string($data['email']);
    $result = $conn->query("SELECT email FROM userss WHERE email='$email'");
    if ($result->num_rows > 0) {
        echo json_encode(["exists" => true, "message" => "Email already registered!"]);
    } else {
        echo json_encode(["exists" => false]);
    }
    exit;
}
echo json_encode(["exists" => false]);
?>