<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

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
        $car = "fun";
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
        return $this->render('restaurant');
    }

    public function actionDashboard()
    {
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

}
