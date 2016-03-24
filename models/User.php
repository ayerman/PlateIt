<?php

namespace app\models;

use yii\db\mssql\PDO;
require_once('DBConnectionHelper.php');

class User extends \yii\base\Object implements \yii\web\IdentityInterface
{
    public $id;
    public $username;
    public $password;
    public $authKey;
    public $accessToken;

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        $pdo = DBConnectionHelper::getDBConnection();
        $sql = "select * from users where id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();
        while ($row = $stmt->fetch()){
            return new static(['id' => $row['id'],
                'username' => $row['username'],
                'password' => $row['password'],
                'authKey' => 'test'.$row['id'].'key',
                'accessToken' => $row['id'].'-token']);
        }
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $pdo = DBConnectionHelper::getDBConnection();
        $sql = "select * from users where accesskey = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1, $token);
        $stmt->execute();
        while ($row = $stmt->fetch()){
            return new static(['id' => $row['id'],
                'username' => $row['username'],
                'password' => $row['password'],
                'authKey' => 'test'.$row['id'].'key',
                'accessToken' => $row['id'].'-token']);
        }
        return null;
    }

    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        $pdo = DBConnectionHelper::getDBConnection();
        $sql = "select * from users where username = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1, $username);
        $stmt->execute();
        while ($row = $stmt->fetch()){
            return new static(['id' => $row['id'],
                                'username' => $row['username'],
                                'password' => $row['password'],
                                'authKey' => 'test'.$row['id'].'key',
                                'accessToken' => $row['id'].'-token']);
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->password === $password;
    }
}
