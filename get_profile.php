<?php
error_reporting(E_ERROR | E_PARSE);
header('Content-Type: application/json; charset=utf-8');

include 'db.php'; 
$headers = getallheaders();
$auth_token = null;

if (isset($headers['Authorization'])) {
    $auth_token = str_replace('Bearer ', '', $headers['Authorization']);
} elseif (isset($headers['authorization'])) {
    $auth_token = str_replace('Bearer ', '', $headers['authorization']);
}

if (!$auth_token) {
    echo json_encode(['status' => 'unauthorized', 'message' => 'No token provided']);
    exit();
}

try {
    $user_email = $redis->get($auth_token);

    if (!$user_email) {
        echo json_encode(['status' => 'unauthorized', 'message' => 'Session expired. Please login again.']);
        exit();
    }
    $stmt = $pdo->prepare("SELECT fullname, email, dob, profile_pic FROM userss WHERE email = :email");
    $stmt->execute(['email' => $user_email]);
    $user = $stmt->fetch();

    if ($user) {
        $profile_pic = !empty($user['profile_pic']) ? $user['profile_pic'] : 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=150&q=80';

        echo json_encode([
            'status' => 'success',
            'fullname' => $user['fullname'],
            'email' => $user['email'],
            'dob' => $user['dob'] ?? '',
            'profile_pic' => $profile_pic
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User data not found.']);
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database Error: ' . $e->getMessage()]);
}
exit();
?>