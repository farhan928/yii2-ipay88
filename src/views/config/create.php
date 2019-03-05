<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model farhan928\Ipay88\models\Ipay88Config */

$this->title = 'Create Ipay88 Config';
$this->params['breadcrumbs'][] = ['label' => 'Ipay88 Configs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ipay88-config-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
