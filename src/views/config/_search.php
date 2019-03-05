<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model farhan928\Ipay88\models\Ipay88ConfigSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ipay88-config-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => ['class'=>'form-inline mb-3']
    ]); ?>

    <?= $form->field($model, 'id', ['options' => ['class' => 'form-group mb-2']])->textInput(['placeholder'=>$model->getAttributeLabel('id')])->label(false) ?>

    <?= $form->field($model, 'entity_id', ['options' => ['class' => 'form-group ml-sm-2 mb-2']])->textInput(['placeholder'=>$model->getAttributeLabel('entity_id')])->label(false) ?>

    <?= $form->field($model, 'merchant_code', ['options' => ['class' => 'form-group ml-sm-2 mb-2']])->textInput(['placeholder'=>$model->getAttributeLabel('merchant_code')])->label(false) ?>

    <?= $form->field($model, 'merchant_key', ['options' => ['class' => 'form-group ml-sm-2 mb-2']])->textInput(['placeholder'=>$model->getAttributeLabel('merchant_key')])->label(false) ?>

    <?= $form->field($model, 'description', ['options' => ['class' => 'form-group mx-sm-2 mb-2']])->textInput(['placeholder'=>$model->getAttributeLabel('description')])->label(false) ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary mr-sm-2 mb-2']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-light mb-2']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<hr/>
