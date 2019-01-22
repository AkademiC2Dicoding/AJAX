<?php

class Inventory{
    private $conn;
    function __construct(PDO &$conn){
        $this->conn = $conn;
    }



    public function getWeaponFromInventory($userId, $weaponId){
        $query  = "SELECT * FROM inventory WHERE weapon_id = ? AND user_id = ?";
        $stmt   = $this->conn->prepare($query);
        $stmt->execute([$weaponId, $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}