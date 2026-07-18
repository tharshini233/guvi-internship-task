<?php
$host = "localhost";
$dbname = "user_sys";
$dbuser = "root";
$dbpass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    die(json_encode(["success" => false, "message" => "MySQL Connection Failed: " . $e->getMessage()]));
}

try {
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
} catch (Exception $e) {
    header('Content-Type: application/json');
    die(json_encode(["success" => false, "message" => "Redis Connection Failed: " . $e->getMessage()]));
}

require 'vendor/autoload.php'; 
try {
    $mongoClient = new MongoDB\Client("mongodb://localhost:27017");
    $mongoDb = $mongoClient->selectDatabase('user_sys_mongo');
    $profileCollection = $mongoDb->selectCollection('user_profiles');
} catch (Exception $e) {
    header('Content-Type: application/json');
    die(json_encode(["success" => false, "message" => "MongoDB Connection Failed: " . $e->getMessage()]));
}
?>