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




?>