<?php
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: ","*");

if($_SERVER['REQUEST_METHOD'] != 'GET'){
    echo json_encode([
        'status'    => 400,
        'message'   => 'You have to send a GET request.'
    ]);
    exit();
}


$parts      = explode("/",ltrim($_SERVER['SCRIPT_NAME'],"/"));
$root       = "http://".$_SERVER['HTTP_HOST'];

if($root == "http://localhost"){

    $assetPath  = $root. "/" . $parts[0];
    $assetPath  = str_replace("/index.php","",$assetPath);
    $path       = dirname(__DIR__);
}
else{
    $protocol   = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
    $path       = $_SERVER['DOCUMENT_ROOT'];
    $assetPath  = $protocol.$_SERVER['SERVER_NAME'];
}

require_once($path.'/connect_db.php');
require_once($path.'/models/Weapons.php');

try{
    $Weapons = new Weapons($conn);
    echo json_encode([
        'status'    => 200,
        'data'      => $Weapons->getWeapons()
    ], true);
}

catch(PDOException $e){
    echo json_encode([
        'status'    => $e->getCode(),
        'message'   => $e->getMessage(),
        'file'      => $e->getFile().' line '.$e->getLine()
    ], true);
}

catch(Exception $e)
{
    echo json_encode([
        'status'    => $e->getCode(),
        'message'   => $e->getMessage(),
        'file'      => $e->getFile().' line '.$e->getLine()
    ], true);
}
