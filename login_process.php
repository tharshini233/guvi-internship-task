<?php
header("Content-Type: application/json; charset=utf-8");
include 'db.php'; 

$data = json_decode(file_get_contents("php://input"), true);

if ($data && isset($data['email'], $data['password'])) {
    $email = trim($data['email']);
    $password = trim($data['password']);

    try {
        $stmt = $pdo->prepare("SELECT * FROM userss WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $token = bin2hex(random_bytes(32)); 
            $redis->setex($token, 3600, $email);
            
            echo json_encode([
                "success" => true, 
                "token" => $token,
                "fullname" => $user['fullname']
            ]);
        } else {
            echo json_encode(["success" => false, "message" => "Invalid email or password!"]);
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    }
}
?>