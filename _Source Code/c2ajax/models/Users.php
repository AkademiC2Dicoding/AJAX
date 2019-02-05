<?php

class Users{
    private $conn;
    function __construct(PDO &$conn){
        $this->conn = $conn;
    }

    public function getUser($username){
        $query  = "SELECT * FROM users WHERE username = ?";
        $stmt   = $this->conn->prepare($query);
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function find($userId){
        $query  = "SELECT * FROM users WHERE user_id = ?";
        $stmt   = $this->conn->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createUser($username, $password){
        $query = "INSERT INTO users (username,password,money) VALUES (?,?,?)";
        $this->conn->prepare($query)->execute([
            $username,
            md5($password),
            1000
        ]);
    }

    public function login($username, $password){
        $query  = "SELECT * FROM users WHERE username = ? AND password = ?";
        $stmt   = $this->conn->prepare($query);
        $stmt->execute([$username,$password]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function setRole($userId, $roleId){
        $query  = "UPDATE users SET role_id = ? WHERE user_id = ?";
        $stmt   = $this->conn->prepare($query);
        $stmt->execute([
            $roleId,
            $userId

        ]);

        return $stmt->rowCount();
    }

    public function getUserInfo($userId){
        $generalInfo = $this->find($userId);
        unset($generalInfo['password']);
        $generalInfo['inventory']['weapons'] = $this->getInventory($userId);

        return $generalInfo;
    }

    public function buyWeapon($user,$weaponData, $amount){
        $Inventory  = new Inventory($this->conn);
        $weapon = $Inventory->getWeaponFromInventory($user['user_id'],$weaponData['weapon_id']);

        if(is_array($weapon)){
            $query  = "UPDATE inventory SET amount = ? WHERE user_id = ? AND weapon_id = ?";
            $stmt   = $this->conn->prepare($query);
            $stmt->execute([$amount + $weapon['amount'], $user['user_id'], $weaponData['weapon_id']]);
        }
        else{
            $query  = "INSERT INTO inventory (user_id, weapon_id, amount) VALUES (?,?,?)";
            $stmt   = $this->conn->prepare($query);
            $stmt->execute([$user['user_id'], $weaponData['weapon_id'], $amount]);
        }
        $totalPrice = $weaponData['weapon_price']*$amount*-1;
        $userData = new Users($this->conn);
        $userData->setMoney($user['user_id'], max(0,$user['money']+$totalPrice));

        return $this->getUserInfo($user['user_id']);
    }

    public function setMoney($userId, $balance){
        $query  = "UPDATE users SET money = ? WHERE user_id = ?";
        $stmt   = $this->conn->prepare($query);
        $stmt->execute([$balance, $userId]);
    }

    public function getInventory($userId){
        $query  = "
            SELECT w.weapon_id, w.weapon_name, i.amount FROM weapons as w
            JOIN inventory as i ON w.weapon_id = i.weapon_id
            JOIN users as u ON u.user_id = i.user_id
            WHERE u.user_id = ?";
        $stmt   = $this->conn->prepare($query);
        $stmt->execute([$userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}