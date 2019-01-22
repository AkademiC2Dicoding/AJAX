<?php

class Weapons{
    private $conn;
    function __construct(PDO &$conn){
        $this->conn = $conn;
    }

    public function getWeapon($weaponId){
        $query  = "SELECT * FROM weapons WHERE weapon_id = ?";
        $stmt   = $this->conn->prepare($query);
        $stmt->execute([$weaponId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getWeapons(){
        $query  = "SELECT * FROM weapons";
        $stmt   = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}