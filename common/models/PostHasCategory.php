<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%post_has_category}}".
 *
 * @property integer $id
 * @property integer $postId
 * @property integer $categoryId
 */
class PostHasCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%post_has_category}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['postId', 'categoryId'], 'required'],
            [['postId', 'categoryId'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'postId' => 'Post ID',
            'categoryId' => 'Category ID',
        ];
    }
}
