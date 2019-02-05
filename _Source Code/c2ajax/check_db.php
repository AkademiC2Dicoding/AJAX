<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: ","*");

$host='localhost';
$db = 'myrpg';
$username = 'root';
$password = '';

try {

    $conn = new PDO("mysql:host=$host;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo json_encode([
        'status'    => 200,
        'message'   => 'connected to database'
    ], true);

}
catch(PDOException $e)
{
    echo json_encode([
        'status'    => $e->getCode(),
        'message'   => $e->getMessage()
    ], true);
}