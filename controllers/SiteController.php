<?php

namespace app\controllers;
include 'BLL/retailDAO.php';
include 'BLL/reviewDAO.php';
include 'BLL/itemsDAO.php';
include 'BLL/userDAO.php';

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\item;
use app\models\retail;
use app\models\review;
use app\models\reviewVM;
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
        return $this->redirect('/PlateIt/site/dashboard');
    }

    public function actionAddreview()
    {
		if(!Yii::$app->user->isGuest) {
			$review = new review();
			$review->userid = $_POST['user_id'];
			$review->itemid = $_POST['item_id'];
			$review->description = $_POST['user_comm'];
			//$review->createReview();
			Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			return [
				'comment' =>  $review->description,
				'name' => $review->getUserFromReview(),
				'timeposted' => "just now",
				'error' => "false",
			];
		}
		else{
			Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			return [
				'comment' =>  "Please login to review this item.",
				'error' => "true",
			];
		}
    }
	
	public function actionMenuitem(){
		$model = new item();
        $loginUser = new LoginForm();
		if(!Yii::$app->user->isGuest){
			$loginUser->username = Yii::$app->user->identity->username;
			$loginUser->getUserInfo();
			$user = ['name' => $loginUser->username,'userid' => Yii::$app->user->getId()];
		}else{
			
			$user = ['name' => "Guest",'userid' => "0"];
		}
		$reviews = array();
        $reviewsVM = array();
		if(isset($_GET['id'])) {
			$model = getItem(Yii::$app->request->get('id'));
			$reviews = getReviewsForItem(Yii::$app->request->get('id'));
               foreach($reviews as $review){
                   $reviewsVM[] = getReviewerForReview($review);
               }
		}

		return $this->render('menuitem', [
			'item' => $model, 'reviews' => $reviewsVM, 'user' => $user,
		]);
	}

    public function actionAccountinfo(){
		
        $this->validateLogin();
		if(!Yii::$app->user->isGuest) {
			if(Yii::$app->session['usertype'] == "Consumer"){
				return $this->redirect('/PlateIt/site/dashboard');
			}
			if(null === Yii::$app->request->get('id')){
				return $this->redirect(array('/site/dashboard'));
			}
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
    }
	
	public function actionUseraccount(){
		
        $this->validateLogin();
		if(!Yii::$app->user->isGuest) {
			$model = new LoginForm();
			if(null === Yii::$app->request->get('id')){
				return $this->redirect(array('/site/dashboard'));
			}
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
		if(!Yii::$app->user->isGuest) {
			//needs work
			$user = new LoginForm();
			$user->fromID(Yii::$app->user->identity->getId());
			if($user->usertype == "Consumer") {
				return $this->redirect(array('/site/dashboard'));
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
    }

    public function actionDashboard()
    {
		$model = new LoginForm();
		if(!Yii::$app->user->isGuest) {
			$model->username = Yii::$app->user->identity->username;
			$model->getUserInfo();
		}
        if($model->usertype == "Consumer" || $model->usertype == "") {
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
        if(Yii::$app->user->isGuest){
            Yii::$app->session->removeAll();
            return $this->redirect('/PlateIt/site/dashboard');
        }
    }

    public function identifyUserType($type){
        Yii::$app->session['usertype'] = $type;
    }

}
