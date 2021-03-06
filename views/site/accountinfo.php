<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Retail Information';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
        'id' => 'account-form',
        'options' => ['class' => 'form-horizontal','enctype'=>'multipart/form-data'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]); ?>
    <?php if(Yii::$app->session->hasFlash('changeSuccess')):?>
        <div class="alert alert-success">
            <?php echo Yii::$app->session->getFlash('changeSuccess');?>
        </div>
    <?php endif; ?>
    <?php if(Yii::$app->session->hasFlash('newAccount')):?>
        <div class="alert alert-success">
            <?php echo Yii::$app->session->getFlash('newAccount');?>
        </div>
    <?php endif; ?>
    <p>This information is for the retail information and not your login account.</p>
    <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>

    <?= $form->field($model, 'description')->textInput() ?>

    <?= $form->field($model, 'image')->fileInput() ?>

    <?= $form->field($model, 'address')->textInput() ?>

    <?= $form->field($model, 'phonenumber')->textInput() ?>

    <?= $form->field($model, 'email')->textInput() ?>


    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'account-button']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
