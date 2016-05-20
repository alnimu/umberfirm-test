<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\Post */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="post-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'visible')->dropDownList(\common\models\Post::$visibility) ?>

    <?= $form->field($model, 'status')->dropDownList(\common\models\Post::$statuses) ?>

    <?= $form->field($model, 'selectedCategories')->widget(Select2::className(), [
        'data' => \yii\helpers\ArrayHelper::map(\common\models\PostCategory::find()->asArray()->all(), 'id', 'name'),
        'language' => 'en',
        'options' => ['placeholder' => 'Select categories ...', 'multiple' => true],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
