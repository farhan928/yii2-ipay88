<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model farhan928\Ipay88\models\Ipay88Config */

$this->title = 'Update Ipay88 Config: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Ipay88 Configs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="ipay88-config-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
