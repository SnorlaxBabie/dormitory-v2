<?php
$host = 'localhost';
$db = 'dormitory';
$user = 'lstvroot';
$pass = 'Lstv@2016';
$charset = 'utf8mb4';

global $connect;
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $connect = new PDO($dsn, $user, $pass);
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    date_default_timezone_set('Asia/Manila');
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

function isDatabaseConnected() {
    global $connect;
    return isset($connect) && $connect instanceof PDO;
}

?>
