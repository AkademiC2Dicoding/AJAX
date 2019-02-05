<?php
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: ","*");
header("Access-Control-Allow-Methods: ", "PATCH");

if($_SERVER['REQUEST_METHOD'] != 'PATCH'){
    echo json_encode([
        'status'    => 400,
        'message'   => 'You have to send a PATCH request.'
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
require_once($path.'/models/Weapons.php');
require_once($path.'/models/Inventory.php');

try{
    $input      = json_decode(file_get_contents('php://input'),true);

    $userId     = $input['user_id'];
    $weaponId   = $input['weapon_id'];
    $amount     = $input['amount'];

    $Users      = new Users($conn);
    $Weapons    = new Weapons($conn);


    $userData   = $Users->find($userId);
    $weaponData = $Weapons->getWeapon($weaponId);

    if($weaponData['weapon_price']*$amount > $userData['money']){
        echo json_encode([
           'status'     => 400,
           'message'    => 'insufficient money'
        ], true);
    }

    else{

        echo json_encode([
            'status'    => 200,
            'message'   => 'Bought!',
            'data'      => $Users->buyWeapon($userData, $weaponData, $amount)
        ], true);
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
