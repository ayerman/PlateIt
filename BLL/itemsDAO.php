<?php
/**
 * Created by PhpStorm.
 * User: Aaron
 * Date: 4/4/2016
 * Time: 11:44 PM
 */

use \app\models\item;
use \app\models\DBConnectionHelper;

function getItems($id){
    try {
        //check if user exists
        $pdo = DBConnectionHelper::getDBConnection();
        $sql = "select * from item where userid = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1,$id);
        $stmt->execute();
        $allItems = array();
        while($row = $stmt->fetch()){
            $nextItem = new item();
            $nextItem->fromRecord($row);
            $allItems[] = $nextItem;
        }
        return $allItems;
    }
    catch(\PDOException $ex){
        return $ex->getMessage();
    }
}

?>