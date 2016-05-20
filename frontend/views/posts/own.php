<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\PostSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Posts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Create Post', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'ownerId',
                'label' => 'Owner',
                'content'=>function($model){
                    /** @var common\models\Post $model */
                    return $model->owner->username;
                }
            ],
            'title',
            [
                'attribute'=>'categoriesList',
                'content'=>function($model){
                    /** @var common\models\Post $model */
                    return $model->categoriesString;
                },
                'filter'=>\yii\helpers\ArrayHelper::map(\common\models\PostCategory::find()->all(), 'id', 'name'),
            ],
            [
                'attribute'=>'status',
                'content'=>function($model){
                    /** @var common\models\Post $model */
                    return $model->statusName;
                },
                'filter'=>\common\models\Post::$statuses,
            ],
            [
                'attribute'=>'visible',
                'content'=>function($model){
                    /** @var common\models\Post $model */
                    return $model->visibilityName;
                },
                'filter'=>\common\models\Post::$visibility,
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}',
                'buttons'=> [
                    'update' => function ($url, $model, $key) {
                        return $model->status != \common\models\Post::STATUS_DELETED ? Html::a('Update', $url) : '';
                    },
                ]
            ],
        ],
    ]); ?>
</div>
