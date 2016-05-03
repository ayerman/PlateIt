<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'User Account Information';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
        'id' => 'account-form',
        'options' => ['class' => 'form-horizontal'],
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
	<?php if(Yii::$app->session->hasFlash('changeFail')):?>
        <div class="alert alert-danger">
            <?php echo Yii::$app->session->getFlash('changeFail');?>
        </div>
    <?php endif; ?>
    <p>Changing this information will change your login account information</p>
    <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

    <?= $form->field($model, 'password')->passwordInput() ?>

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
