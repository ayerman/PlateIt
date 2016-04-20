<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'PlateIt',
        'brandUrl' =>  Yii::$app->request->baseUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    if(Yii::$app->user->isGuest) {
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => [
                ['label' => 'Home', 'url' => ['/site/index']],
                ['label' => 'Register', 'url' => ['/site/register']],
                ['label' => 'Login', 'url' => ['/site/login']],
            ],
        ]);
    }
    else if(Yii::$app->session['usertype'] == "Consumer"){
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => [
                ['label' => 'Home', 'url' => ['/site/dashboard']],
                ['label' => 'Account', 'url' => ['/site/useraccount?id=' . Yii::$app->user->identity->getId()]],
                ['label' => 'Logout (' . Yii::$app->user->identity->username . ')', 'url' => ['/site/logout']]
            ],
        ]);
    }
    else if(Yii::$app->session['usertype'] == "Retail"){
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => [
                ['label' => 'Home', 'url' => ['/site/restaurant?id=' . Yii::$app->user->identity->getId()]],
                ['label' => 'Add Item', 'url' => ['/site/additem']],
                ['label' => 'User Account', 'url' => ['/site/useraccount?id=' . Yii::$app->user->identity->getId()]],
                ['label' => 'Retail Account', 'url' => ['/site/accountinfo?id=' . Yii::$app->user->identity->getId()]],
                ['label' => 'Logout (' . Yii::$app->user->identity->username . ')', 'url' => ['/site/logout']]
            ],
        ]);
    }
    NavBar::end();
    ?>

    <div class="container">
        <?= $content ?>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
