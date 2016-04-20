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
class item extends Model
{
	public $id;
    public $userid;
    public $name;
    public $description;
    public $image;

    function __construct(){

    }

    public function fromRecord($record)
    {
        $this->id = $record['id'];
        $this->userid = $record['userid'];
        $this->name = $record['name'];
        $this->description = $record['description'];
        $this->image = $record['image'];
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['name', 'description', 'userid','id'], 'required'],
            ['image', 'safe'],
        ];
    }



    public function createItem(){
        try {
            //check if user exists
            $pdo = DBConnectionHelper::getDBConnection();
            $sql = "INSERT INTO item(userid, name, description, image) VALUES (?,?,?,?);";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(1, $this->userid);
            $stmt->bindValue(2, $this->name);
            $stmt->bindValue(3, $this->description);
            $stmt->bindValue(4, $this->image);
            $stmt->execute();
            return;
        }
        catch(\PDOException $ex){
            return $ex->getMessage();
        }
    }
}