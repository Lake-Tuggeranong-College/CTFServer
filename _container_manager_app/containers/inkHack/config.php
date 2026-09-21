<?php
$servername = "SECRET-db";
$username = "secret";
$password = "secret";
$dbname = "secret"; 
$port = "${PORT}:80"; #REMEBER TO FIX THIS LATER

try {
    $dsn = "mysql:host=$servername;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);

    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // echo "Connected successfully using PDO";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>