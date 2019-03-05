<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel farhan928\Ipay88\models\Ipay88ConfigSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Ipay88 Configs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ipay88-config-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Ipay88 Config', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'entity_id',
            'merchant_code',
            'merchant_key',
            'description',
            //'created_at',
            //'updated_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Actions',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                'template' => '{view}{update}{delete}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('View', $url, [
                            'title' => Yii::t('app', 'View'),
                            'class' => 'btn btn-info btn-sm',
                        ]).' ';
                    },
        
                    'update' => function ($url, $model) {
                        return Html::a('Edit', $url, [
                            'title' => Yii::t('app', 'Edit'),
                            'class' => 'btn btn-success btn-sm',
                        ]).' ';
                    },
                    'delete' => function ($url, $model) {
                        return Html::a('Delete', $url, [
                            'title' => Yii::t('app', 'Delete'),
                            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            'data-method'  => 'post',
                            'class' => 'btn btn-danger btn-sm',
                        ]);
                    }
        
                  ],                 
            ],
        ],
    ]); ?>
</div>
