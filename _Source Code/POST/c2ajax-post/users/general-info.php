<?php
header("Access-Control-Allow-Origin: ","*");
ini_set('display_errors', 1);

if($_SERVER['REQUEST_METHOD'] != 'POST'){
    echo json_encode([
        'status'    => 400,
        'message'   => 'You have to send a POST request.'
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
require_once($path.'/models/Users.php');

try{
    $input      = json_decode(file_get_contents('php://input'),true);
    $userId     = $input['user_id'];

    $Users      = new Users($conn);
    $userData   = $Users->getUserInfo($userId);

    if(is_array($userData)){
        echo json_encode([
            'status'    => 200,
            'message'   => 'success',
            'data'      => $userData
        ]);
    }
    else{
        echo json_encode([
            'status'    => 400,
            'message'  => 'User not found'
        ]);
    }
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
