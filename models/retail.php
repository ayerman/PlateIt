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
class retail extends Model
{
    public $userid;
    public $name;
    public $description;
    public $image;
    public $address;
    public $email;
    public $phonenumber;

    function __construct()
    {
    }

    public function fromRecord($record)
    {
        $this->userid = $record['userid'];
        $this->name = $record['name'];
        $this->description = $record['description'];
        $this->image = $record['image'];
        $this->address = $record['address'];
        $this->email = $record['email'];
        $this->phonenumber = $record['phonenumber'];
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['name', 'userid'], 'required'],
            ['description', 'safe'],
            ['image', 'safe'],
            ['address', 'safe'],
            ['email', 'safe'],
            ['phonenumber', 'safe'],
        ];
    }

    public function createRetail(){
        try {
            //check if user exists
            $pdo = DBConnectionHelper::getDBConnection();
            $sql = "INSERT INTO retail(userid, name, description, image, address, email, phonenumber) VALUES (?,?,?,?,?,?,?);";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(1, $this->userid);
            $stmt->bindValue(2, $this->name);
            $stmt->bindValue(3, $this->description);
            $stmt->bindValue(4, $this->image);
            $stmt->bindValue(5, $this->address);
            $stmt->bindValue(6, $this->email);
            $stmt->bindValue(7, $this->phonenumber);
            $stmt->execute();
            return;
        }
        catch(\PDOException $ex){
            return $ex->getMessage();
        }
    }
}