<?php
/**
 * Created by PhpStorm.
 * User: Aaron
 * Date: 4/4/2016
 * Time: 7:58 PM
 */
use \app\models\review;
use \app\models\DBConnectionHelper;


function getReview($id){
    try {
        //check if user exists
        $pdo = DBConnectionHelper::getDBConnection();
        $sql = "select * from review where id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1,$id);
        $stmt->execute();
        $row = $stmt->fetch();
		$review = new review();
        return $review;
    }
    catch(\PDOException $ex){
        return $ex->getMessage();
    }
}

function getReviewsForItem($itemid){
    try {
        //check if user exists
        $pdo = DBConnectionHelper::getDBConnection();
        $sql = "select * from review where itemid = ? ORDER BY timeposted DESC;";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1, $itemid);
        $stmt->execute();
		$allReviews = array();
        while($row = $stmt->fetch()){
            $nextReview = new review();
            $nextReview->fromRecord($row);
            $allReviews[] = $nextReview;
        }
        return $allReviews;
    }
    catch(\PDOException $ex){
        return $ex->getMessage();
    }
}

?>