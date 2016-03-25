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

        $model = new LoginForm();
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['isLogin'])) {
                if ($model->load(Yii::$app->request->post()) && $model->login()) {
                    if($model->type == "Consumer") {
                        return $this->redirect(array('site/restaurants'));
                    }
                    else{
                        return $this->redirect(array('site/review'));
                    }
                }
            }
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionRestaurants()
    {
        return $this->render('restaurants');
    }

    public function actionReview()
    {
        return $this->render('review');
    }

    public function actionRegister()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($model->load(Yii::$app->request->post()) && $model->register()) {
                $model->login();
                if($model->type == "Consumer") {
                    return $this->redirect(array('site/restaurants'));
                }
                else{
                    return $this->redirect(array('site/review'));
                }
            }
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
