<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\PostCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Post Categories';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-category-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Post Category', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'ownerId',
                'label' => 'Owner',
                'content'=>function($model){
                    /** @var common\models\PostCategory $model */
                    return $model->owner->username;
                }
            ],
            'name',
            [
                'attribute'=>'status',
                'content'=>function($model){
                    /** @var common\models\PostCategory $model */
                    return $model->statusName;
                },
                'filter'=>\common\models\PostCategory::$statuses,
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
