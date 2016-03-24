<?php
/**
 * Created by PhpStorm.
 * User: Aaron
 * Date: 3/23/2016
 * Time: 9:26 PM
 */

namespace app\models;
include 'dbconfig.php';


class DBConnectionHelper
{
    public static function getDBConnection(){

        return new PDO(DBREMOTECONN, DBREMOTEUSER, DBREMOTEPASS);
        //return new PDO(DBLOCALCONN, DBLOCALUSER, DBLOCALPASS);
    }
}