<?php
/**
 * Created by PhpStorm.
 * User: Aaron
 * Date: 4/4/2016
 * Time: 7:58 PM
 */
use \app\models\LoginForm;
use \app\models\DBConnectionHelper;

function getUser($id){
    try {
        $user = new LoginForm();
        $user->fromID($id);
        return $user;
    }
    catch(\PDOException $ex){
        return $ex->getMessage();
    }
}

function isUserTaken($username){
	try {
        //check if user exists
        $pdo = DBConnectionHelper::getDBConnection();
        $sql = "select * from users where username = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1,$username);
        $stmt->execute();
        if($stmt->rowCount()>0){
			return true;
		}else{
			return false;
		}
    }
    catch(\PDOException $ex){
        return $ex->getMessage();
    }
}

function updateUser($user,$id){
    try {
        //check if user exists
        $pdo = DBConnectionHelper::getDBConnection();
		$currentUser = getUser($id);
		if($currentUser->username != $user->username){
			if(isUserTaken($user->username)){
				return false;
			}
		}
        $sql = "UPDATE users SET username=?,password=?,address=?,phonenumber=?,email=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1,$user->username);
        $stmt->bindValue(2,$user->password);
        $stmt->bindValue(3,$user->address);
        $stmt->bindValue(4,$user->phonenumber);
        $stmt->bindValue(5,$user->email);
        $stmt->bindValue(6,$id);
        $stmt->execute();
        return true;
    }
    catch(\PDOException $ex){
        return $ex->getMessage();
    }
}

?>