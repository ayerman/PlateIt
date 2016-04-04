<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\item;
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
        return $this->render('index');
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm(null);
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['isLogin'])) {
                if ($model->load(Yii::$app->request->post()) && $model->login()) {
					$model->getUserInfo();
                    $this->identifyUserType($model->usertype);
					if($model->usertype == "Consumer") {
						return $this->redirect(array('/site/dashboard'));
					}
					else if($model->usertype == "Retail"){
						return $this->redirect(array('/site/restaurant'));
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
        return $this->render('restaurant');
    }

    public function actionAdditem(){
        $this->validateLogin();

        $model = new item();
        $user = new LoginForm(Yii::$app->user->identity->username);
        if($user->usertype == "Consumer") {
            return $this->render('dashboard');
        }

        if(Yii::$app->request->isPost){
            if ($model->load(Yii::$app->request->post())) {
                $loginUser = Yii::$app->user->getIdentity();
                $model->userid = $loginUser->getId();
                $model->createItem();
                return $this->redirect(array('/site/restaurant'));
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
		$model = new LoginForm(Yii::$app->user->identity->username);
		$model->getUserInfo();
        if($model->usertype == "Consumer") {
            return $this->render('dashboard');
        }
        else if($model->usertype == "Retail"){
            return $this->redirect(array('/site/restaurant'));
        }
    }

    public function actionRegister()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm(null);
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($model->load(Yii::$app->request->post()) && $model->register()) {
                $model->login();
                $this->identifyUserType($model->usertype);
                if($model->usertype == "Consumer") {
                    return $this->redirect(array('/site/dashboard'));
                }
                else if($model->usertype == "Retail"){
                    return $this->redirect(array('/site/restaurant'));
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
