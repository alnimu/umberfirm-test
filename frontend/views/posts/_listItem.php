<?php
/**
 * @var \common\models\Post $model
 */
?>

<div class="panel panel-default">
    <div class="panel-heading"><?=\yii\helpers\Html::a($model->title, ['view', 'id' => $model->id])?></div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6"><span class="label label-info"><?=$model->categoriesString?></span></div>
            <div class="col-md-6 text-right"><?=$model->owner->username?> / <?=$model->created_at?></div>
        </div>

        <div class="row" style="margin-top: 20px;">
            <div class="col-md-12">
                <?=$model->shortContent?>
            </div>
        </div>
    </div>
</div>