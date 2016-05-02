<?php
/**
 * Created by PhpStorm.
 * User: Aaron
 * Date: 4/4/2016
 * Time: 7:58 PM
 */
use \app\models\review;
use \app\models\DBConnectionHelper;
use \app\models\LoginForm;
use \app\models\reviewVM;
use \app\models\retail;


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
        $review->fromRecord($row);
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
        $row = $stmt->fetch();
        $review = new review();
        $review->fromRecord($row);
        return $review;
    }
    catch(\PDOException $ex){
        return $ex->getMessage();
    }
}

function getReviewerForReview($review){
    $user = new LoginForm();
    $user->fromID($review->userid);
    $reviewVM = new reviewVM();
    $reviewVM->review = $review->description;
    if($user->usertype == "Retail"){
        $retail = new retail();
        $retail = getRetail($review->userid);
        $reviewVM->reviewer = $retail->userid;
    }else{
        $reviewVM->reviewer = $user->username;
    }
    return $reviewVM;
}

?>