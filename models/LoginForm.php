<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\mssql\PDO;
include 'dbconfig.php';
include 'DBConnectionHelper.php';

/**
 * LoginForm is the model behind the login form.
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $address;
    public $phonenumber;
    public $email;
    public $type;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        }
        return false;
    }

    public function register(){
        try {
            $pdo = DBConnectionHelper::getDBConnection();
            $sql = "INSERT INTO `Users`(`username`, `password`) VALUES (?,?);";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(1, $this->username);
            $stmt->bindValue(2, $this->password);
 //           $stmt->bindValue(3, $this->address);
 //           $stmt->bindValue(4, $this->phonenumber);
 //           $stmt->bindValue(5, $this->email);
 //           $stmt->bindValue(6, $this->type);
            $stmt->execute();
            return true;
        }
        catch(\PDOException $ex){
            return $ex->getMessage();
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
