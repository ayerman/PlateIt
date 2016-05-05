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

function deleteReviews(){
	try {
        //check if user exists
        $pdo = DBConnectionHelper::getDBConnection();
        $sql = "DELETE FROM review WHERE userid=?";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1,Yii::$app->user->identity->getId());
        $stmt->execute();
        return true;
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

function getReviewerForReview($review){
	try{
		$user = new LoginForm();
		$user->fromID($review->userid);
		$reviewVM = new reviewVM();
		$reviewVM->review = $review->description;
		$reviewVM->postedtime = $review->timeposted;
		if($user->usertype == "Retail"){
			$retail = new retail();
			$retail = getRetail($review->userid);
			$reviewVM->reviewer = $retail->name;
		}else{
			$reviewVM->reviewer = $user->username;
		}
		return $reviewVM;
		}
	catch(\PDOException $ex){
         return $ex->getMessage();
	}
}

?>