<?php
header("Content-Type: text/plain");

$host = "localhost";
$user = "root"; 
$pass = "";     

$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql_db = "CREATE DATABASE IF NOT EXISTS user_sys";
$conn->query($sql_db);
$conn->select_db("user_sys");


$sql_table = "CREATE TABLE IF NOT EXISTS userss (
    email VARCHAR(100) PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    dob DATE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql_table) === TRUE) {
    echo "✔ Table 'userss' created/updated successfully with DOB.\n";
} else {
    echo "❌ Error creating table: " . $conn->error;
}

$conn->close();
?>