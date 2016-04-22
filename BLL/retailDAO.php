<?php
/**
 * Created by PhpStorm.
 * User: Aaron
 * Date: 4/4/2016
 * Time: 7:58 PM
 */
use \app\models\retail;
use \app\models\DBConnectionHelper;

function getAllRetail(){
    try {
        //check if user exists
        $pdo = DBConnectionHelper::getDBConnection();
        $sql = "select * from retail";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $allRetail = array();
        while($row = $stmt->fetch()){
            $nextRetail = new retail();
            $nextRetail->fromRecord($row);
            $allRetail[] = $nextRetail;
        }
        return $allRetail;
    }
    catch(\PDOException $ex){
        return $ex->getMessage();
    }
}

function getRetail($id){
    try {
        //check if user exists
        $pdo = DBConnectionHelper::getDBConnection();
        $sql = "select * from retail where userid = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1,$id);
        $stmt->execute();
        $row = $stmt->fetch();
        $Retail = new retail();
        $Retail->fromRecord($row);
        return $Retail;
    }
    catch(\PDOException $ex){
        return $ex->getMessage();
    }
}

function updateRetail($retail){
    try {
        //check if user exists
        $pdo = DBConnectionHelper::getDBConnection();
        $sql = "UPDATE retail SET name=?,image=?,address=?,phonenumber=?,description=?,email=?,imagetype=? WHERE userid=?";
        $stmt = $pdo->prepare($sql);
		$retail->image = $_FILES['retail']['tmp_name']['image'];
		$retail->imagetype = $_FILES['retail']['type']['image'];
		$image = file_get_contents($retail->image);
        $stmt->bindValue(1,$retail->name);
        $stmt->bindValue(2,$image);
        $stmt->bindValue(3,$retail->address);
        $stmt->bindValue(4,$retail->phonenumber);
        $stmt->bindValue(5,$retail->description);
        $stmt->bindValue(6,$retail->email);
        $stmt->bindValue(7,$retail->imagetype);
        $stmt->bindValue(8,$retail->userid);
        $stmt->execute();
        return true;
    }
    catch(\PDOException $ex){
        return $ex->getMessage();
    }
}

?>