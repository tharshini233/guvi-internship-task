<?php
header('Content-Type: application/json; charset=utf-8');
include 'db.php'; 

$headers = getallheaders();
$auth_token = str_replace('Bearer ', '', $headers['Authorization'] ?? '');

$user_email = $redis->get($auth_token);
if (!$user_email) { 
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']); 
    exit(); 
}

$username = $_POST['username'] ?? '';
$dob = $_POST['dob'] ?? '';

$stmt = $pdo->prepare("UPDATE userss SET fullname = :fullname, dob = :dob WHERE email = :email");
$result = $stmt->execute(['fullname' => $username, 'dob' => $dob, 'email' => $user_email]);

if ($result) {
    try {
        $collection = $mongoClient->selectCollection('user_sys_mongo', 'user_profiles');
        
        $collection->updateOne(
            ['email' => $user_email],
            ['$set' => [
                'fullname' => $username,
                'dob' => $dob,
                'updated_at' => date('Y-m-d H:i:s')
            ]],
            ['upsert' => true]
        );
        
        echo json_encode(['status' => 'success', 'message' => 'Updated in both DBs']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'MongoDB Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'MySQL Update Failed']);
}
?>