<?php
/**
 * Created by PhpStorm.
 * User: Aaron
 * Date: 4/3/2016
 * Time: 10:24 PM
 */


namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\mssql\PDO;
require_once('DBConnectionHelper.php');

/**
 * LoginForm is the model behind the login form.
 */
class review extends Model
{
	public $username;
    public $userid;
	public $timeposted;
	public $itemid;
    public $description;

    function __construct()
    {
    }

    public function fromRecord($record)
    {
		try {
        //check if user exists
        $pdo = DBConnectionHelper::getDBConnection();
        $sql = "select * from users where id = ?;";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1, $record['userid']);
        $stmt->execute();
		$row = $stmt->fetch();
		$this->username = $row['username'];
		}
		catch(\PDOException $ex){
			return $ex->getMessage();
		}
        $this->userid = $record['userid'];
        $this->description = $record['description'];
        $this->itemid = $record['itemid'];
        $this->timeposted = $record['timeposted'];
    }
	
	public function createReview(){
		try {
        //check if user exists
        $pdo = DBConnectionHelper::getDBConnection();
        $sql = "INSERT INTO `review`(`timeposted`, `itemid`, `userid`, `description`) VALUES (NOW(),?,?,?)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1, $this->itemid);
        $stmt->bindValue(2, $this->userid);
        $stmt->bindValue(3, $this->description);
        $stmt->execute();
		}
		catch(\PDOException $ex){
			return $ex->getMessage();
		}
	}

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['userid','timeposted','itemid'], 'required'],
            ['description', 'safe'],
        ];
    }
}