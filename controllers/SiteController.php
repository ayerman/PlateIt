<?php

namespace app\controllers;
include 'BLL/retailDAO.php';
include 'BLL/itemsDAO.php';
include 'BLL/userDAO.php';

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\item;
use app\models\retail;
use app\models\ContactForm;
use yii\web\Session;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className()
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        if(!Yii::$app->user->isGuest) {
            $model = new LoginForm();
            $model->username = Yii::$app->user->identity->username;
            $model->getUserInfo();
            if(!isset(Yii::$app->session['usertype'])){
                $this->identifyUserType($model->usertype);
            }
            if ($model->usertype == "Consumer") {
                return $this->redirect('/PlateIt/site/dashboard');
            } else if ($model->usertype == "Retail") {
                return $this->redirect(array('/site/restaurant?id=' . Yii::$app->user->identity->getId()));
            }
        }
        return $this->render('index');
    }
	
	public function actionMenuitem(){
        $this->validateLogin();
		$model = new item();
        $loginUser = new LoginForm();
        $loginUser->username = Yii::$app->user->identity->username;
        $loginUser->getUserInfo();
		$user = ['name' => $loginUser->username,'userid' => Yii::$app->user->getId()];
		if($loginUser->usertype == "Retail"){
			$loginUser = new retail();
			$loginUser = getRetail(Yii::$app->user->getId());
			$user = ['name' => $loginUser->name,'userid' => Yii::$app->user->getId()];
		}
        if(isset($_GET['id'])) {
			$model = getItem(Yii::$app->request->get('id'));
		}
		
		if(Yii::$app->request->isPost){
			
		}
		$comments = array();
        return $this->render('menuitem', [
            'item' => $model, 'comments' => $comments, 'user' => $user,
        ]);
	}

    public function actionAccountinfo(){
		
        $this->validateLogin();
        $model = new retail();
        if(Yii::$app->request->isGet) {
            $model = getRetail(Yii::$app->request->get('id'));
        }
        else {
            if ($model->load(Yii::$app->request->post())) {
                $model->userid = Yii::$app->user->getId();
                if(updateRetail($model)){
                    Yii::$app->session->setFlash('changeSuccess','You have successfully updated your retail account information');
                }
            }
        }
        return $this->render('accountinfo', [
            'model' => $model,
        ]);
    }
	
	public function actionUseraccount(){
		
        $this->validateLogin();
		$model = new LoginForm();
        if(Yii::$app->request->isGet) {
            $model->fromID(Yii::$app->request->get('id'));
        }
        else {
            if ($model->load(Yii::$app->request->post())) {
                if(updateUser($model,Yii::$app->user->getId())){
					$model->fromID(Yii::$app->user->getId());
					Yii::$app->user->logout();
					$this->identifyUserType($model->usertype);
					$model->login();
                    Yii::$app->session->setFlash('changeSuccess','You have successfully updated your user account information!');
                }else{
					Yii::$app->session->setFlash('changeFail','The username selected currently exists!');
				}
            }
        }
        return $this->render('useraccount', [
            'model' => $model,
        ]);
	}

    public function actionLogin()
    {
        if (!(Yii::$app->user->isGuest)) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['isLogin'])) {
                if ($model->load(Yii::$app->request->post()) && $model->login()) {
					$model->getUserInfo();
                    $this->identifyUserType($model->usertype);
					if($model->usertype == "Consumer") {
						return $this->redirect(array('/site/dashboard'));
					}
					else if($model->usertype == "Retail"){
						return $this->redirect(array('/site/restaurant?id=' . $model->getUser()->getId()));
					}
                }
            }
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionRestaurant()
    {
        $this->validateLogin();
        if(Yii::$app->request->isGet) {
            $Retail = getRetail(Yii::$app->request->get("id"));
            $allItems = getItems($Retail->userid);
            return $this->render('restaurant', ['retail' => $Retail, 'items' => $allItems,]);
        }
        return $this->render('restaurant');
    }

    public function actionAdditem(){
        $this->validateLogin();

        $model = new item();

        //needs work
        $user = new LoginForm();
        $user->fromID(Yii::$app->user->identity->getId());
        if($user->usertype == "Consumer") {
            return $this->render('dashboard');
        }

        if(Yii::$app->request->isPost){
            if ($model->load(Yii::$app->request->post())) {
                $loginUser = Yii::$app->user->getIdentity();
                $model->userid = $loginUser->getId();
                $model->createItem();
                return $this->redirect(array('/site/restaurant?id=' . $model->userid));
            }
            else{
                return $this->goHome();
            }
        }
        return $this->render('additem', [
            'model' => $model,
        ]);
    }

    public function actionDashboard()
    {
        $this->validateLogin();
		$model = new LoginForm();
        $model->username = Yii::$app->user->identity->username;
		$model->getUserInfo();
        if($model->usertype == "Consumer") {
            $allRetail = getAllRetail();
            return $this->render('dashboard', ['model' => $allRetail, 'loginUser' => $model]);
        }
        else if($model->usertype == "Retail"){
            return $this->redirect(array('/site/restaurant?id=' . Yii::$app->user->identity->getId()));
        }
    }

    public function actionRegister()
    {
        if (!(Yii::$app->user->isGuest)) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($model->load(Yii::$app->request->post()) && $model->register()) {
                $model->login();
                $this->identifyUserType($model->usertype);
                if($model->usertype == "Consumer") {
                    return $this->redirect(array('/site/dashboard'));
                }
                else if($model->usertype == "Retail"){
                    $newRetail = new retail();
                    $newRetail->userid = Yii::$app->user->getId();
                    $newRetail->name = $model->username;
                    $newRetail->address = $model->address;
                    $newRetail->email = $model->email;
                    $newRetail->phonenumber = $model->phonenumber;
                    $newRetail->createRetail();
                    return $this->redirect(array('/site/restaurant?id=' . Yii::$app->user->getId()));
                }
            }
			Yii::$app->session->setFlash('userExists', "The username that you selected already exists.");
        }

        return $this->render('register', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        Yii::$app->session->removeAll();
        return $this->goHome();
    }

    public function validateLogin(){
        if(yii::$app->user->isGuest){
            Yii::$app->session->removeAll();
            return $this->goHome();
        }
    }

    public function identifyUserType($type){
        Yii::$app->session['usertype'] = $type;
    }


}
