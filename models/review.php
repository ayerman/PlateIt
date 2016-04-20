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
    public $userid;
    public $description;

    function __construct()
    {
    }

    public function fromRecord($record)
    {
        $this->userid = $record['userid'];
        $this->description = $record['description'];
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            ['userid', 'required'],
            ['description', 'safe'],
        ];
    }
}