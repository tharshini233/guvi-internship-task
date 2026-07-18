<?php
error_reporting(E_ERROR | E_PARSE);
header("Content-Type: application/json; charset=utf-8");
include 'db.php'; 

$data = json_decode(file_get_contents("php://input"), true);

if ($data && isset($data['fullname'], $data['email'], $data['dob'], $data['password'])) {
    
    $fullname = trim($data['fullname']);
    $email = trim($data['email']);
    $dob = trim($data['dob']);
    $password = trim($data['password']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["success" => false, "field" => "email", "message" => "Thavaraana Email format!"]);
        exit;
    }

    $uppercase = preg_match('@[A-Z]@', $password);
    $number    = preg_match('@[0-9]@', $password);
    $specialChars = preg_match('@[^\w]@', $password);
    if(!$uppercase || !$number || !$specialChars || strlen($password) < 8) {
        echo json_encode(["success" => false, "field" => "password", "message" => "Password Min 8 chars, 1 Uppercase, 1 Number, 1 Special character!"]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT email FROM userss WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            echo json_encode(["success" => false, "field" => "email", "message" => "This email is already registered!"]);
            exit;
        }

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $insertStmt = $pdo->prepare("INSERT INTO userss (fullname, email, dob, password) VALUES (:fullname, :email, :dob, :password)");
        $insertStmt->execute([
            'fullname' => $fullname,
            'email' => $email,
            'dob' => $dob,
            'password' => $hashed_password
        ]);

        $profileCollection->insertOne([
            'email' => $email,
            'updated_at' => new MongoDB\BSON\UTCDateTime()
        ]);

        echo json_encode(["success" => true, "message" => "Account created successfully!"]);

    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "System Error: " . $e->getMessage()]);
    }
    exit;
}
?>