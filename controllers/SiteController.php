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
		$this->identifyUserType();
        if(!Yii::$app->user->isGuest) {
            $model = new LoginForm();
            $model->fromID(Yii::$app->user->identity->getId());
            if ($model->usertype == "Consumer") {
                return $this->redirect( Yii::$app->request->baseUrl . '/site/dashboard');
            } else if ($model->usertype == "Retail") {
                return $this->redirect(array('/site/restaurant?id=' . Yii::$app->user->identity->getId()));
            }
        }
        return $this->redirect( Yii::$app->request->baseUrl . '/site/dashboard');
    }

    public function actionAddreview()
    {
		$this->identifyUserType();
		if(!Yii::$app->user->isGuest) {
			$review = new review();
			$review->userid = $_POST['user_id'];
			$review->itemid = $_POST['item_id'];
			$review->description = $_POST['user_comm'];
			$review->createReview();
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
		$this->identifyUserType();
		$model = new item();
        $loginUser = new LoginForm();
		if(!Yii::$app->user->isGuest){
			$loginUser->fromID(Yii::$app->user->identity->getId());
			$user = ['name' => $loginUser->username,'userid' => Yii::$app->user->identity->getId()];
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
		}else{
			return $this->redirect(array('/site/dashboard'));
		}

		return $this->render('menuitem', [
			'item' => $model, 'reviews' => $reviewsVM, 'user' => $user,
		]);
	}

    public function actionAccountinfo(){
		$this->identifyUserType();
        $this->validateLogin();
		if(!Yii::$app->user->isGuest) {
			if(Yii::$app->session['usertype'] == "Consumer"){
				return $this->redirect(Yii::$app->request->baseUrl . '/site/dashboard');
			}
			$model = new retail();
			if(Yii::$app->request->isGet) {
				if(null === Yii::$app->request->get('id')){
					return $this->redirect(Yii::$app->request->baseUrl . '/site/dashboard');
				}
				$model = getRetail(Yii::$app->request->get('id'));
			}
			else {
				if ($model->load(Yii::$app->request->post())) {
					$model->userid = Yii::$app->user->identity->getId();
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
		$this->identifyUserType();
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
					if(updateUser($model,Yii::$app->user->identity->getId())){
						$model->fromID(Yii::$app->user->identity->getId());
						Yii::$app->user->logout();
						$model->login();
						$this->identifyUserType();
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
					$model->fromID(Yii::$app->user->identity->getId());
					$this->identifyUserType();
					if($model->usertype == "Consumer") {
						return $this->redirect(array('/site/dashboard'));
					}
					else if($model->usertype == "Retail"){
                        return $this->redirect(array('/site/restaurant?id=' . Yii::$app->user->identity->getId()));
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
		$this->identifyUserType();
		if(null === Yii::$app->request->get('id')){
			return $this->redirect(array('/site/dashboard'));
		}
        if(Yii::$app->request->isGet) {
            $Retail = getRetail(Yii::$app->request->get("id"));
            $allItems = getItems($Retail->userid);
            return $this->render('restaurant', ['retail' => $Retail, 'items' => $allItems,]);
        }
        return $this->render('restaurant');
    }

    public function actionAdditem(){
		$this->identifyUserType();
        $this->validateLogin();
        $model = new item();
		if(!Yii::$app->user->isGuest) {
			$user = new LoginForm();
			$user->fromID(Yii::$app->user->identity->getId());
			if($user->usertype == "Consumer") {
				return $this->redirect(array('/site/dashboard'));
			}
	
			if(Yii::$app->request->isPost){
				if ($model->load(Yii::$app->request->post())) {
					$model->userid = Yii::$app->user->identity->getId();
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
		$this->identifyUserType();
		$model = new LoginForm();
		if(!Yii::$app->user->isGuest) {
			$model->fromID(Yii::$app->user->identity->getId());
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
		$this->identifyUserType();
        if (!(Yii::$app->user->isGuest)) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($model->load(Yii::$app->request->post()) && $model->register()) {
                $model->login();
                $this->identifyUserType($model->usertype);
                if($model->usertype == "Consumer") {
                    return $this->redirect(array(Yii::$app->request->baseUrl . '/site/dashboard'));
                }
                else if($model->usertype == "Retail"){
                    $newRetail = new retail();
                    $newRetail->userid = Yii::$app->user->identity->getId();
                    $newRetail->name = $model->username;
                    $newRetail->address = $model->address;
                    $newRetail->email = $model->email;
                    $newRetail->phonenumber = $model->phonenumber;
                    $newRetail->createRetail();
                    Yii::$app->session->setFlash('newAccount','Now please edit your retail information!');
                    return $this->redirect(array('/site/accountinfo?id=' . $model->getUser()->getId()));
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
            return $this->redirect(Yii::$app->request->baseUrl . '/site/dashboard');
        }
    }

    public function identifyUserType(){
        if(!Yii::$app->user->isGuest){
			$model = new LoginForm();
			$model->fromID(Yii::$app->user->identity->getId());
			if(!isset(Yii::$app->session['usertype'])){
				Yii::$app->session['usertype'] = $model->usertype;
			}
		}
    }

}
