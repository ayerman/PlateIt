<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Add Item';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php $form = ActiveForm::begin([
    'id' => 'item-form',
    'options' => ['class' => 'form-horizontal','enctype'=>'multipart/form-data'],
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
        'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]); ?>
<div class="row" id="insuranceInfo">

    <div class="col-sm-4" style="margin-left: 0; padding: 0;">
        <h1 style="" align="center"><?= Html::encode($this->title) ?></h1>
    </div>
    <br />
    <div class="col-sm-12" style="margin-left: 0; padding: 0;">
        <div class="form-group">
            <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>
        </div>
        <div class="form-group">
            <?= $form->field($model, 'description')->textarea() ?>
        </div>
        <div class="form-group">
            <?= $form->field($model, 'image')->fileInput() ?>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <div class="col-sm-2 col-sm-offset-3">
            <?= Html::submitButton('Add Item', ['class' => 'btn btn-primary', 'name' => 'item-button']) ?>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>