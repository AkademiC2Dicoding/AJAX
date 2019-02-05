<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: ","*");

$host='localhost';
$db = 'myrpg';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $username, $password, [PDO::MYSQL_ATTR_FOUND_ROWS => true]);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e)
{
    echo json_encode([
        'status'    => $e->getCode(),
        'message'   => $e->getMessage()
    ], true);
}