<?php

namespace common\models;

use common\scopes\PostCategoriesQuery;
use yii;

/**
 * This is the model class for table "{{%post_categories}}".
 *
 * @property integer $id
 * @property integer $ownerId
 * @property string $name
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * 
 * @property \common\models\User $owner
 * @property string $statusName read-only statusName
 */
class PostCategory extends yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_INVISIBLE = 2;

    public static $statuses = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INVISIBLE => 'Invisible'
    ];
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%post_categories}}';
    }

    public static function find()
    {
        return new PostCategoriesQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => yii\behaviors\TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
                'value' => date('Y-m-d H:i:s'),
            ],
            [
                'class' => 'common\behaviours\Owner',
                'ownerIdAttribute' => 'ownerId'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'filter', 'filter' => 'trim'],
            ['name', 'filter', 'filter' => 'strip_tags'],

            [['name', 'status'], 'required'],

            [['name'], 'string', 'max' => 255],
            [['name'], 'unique'],

            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INVISIBLE]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ownerId' => 'Owner ID',
            'name' => 'Name',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Owner relation
     * 
     * @return yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(User::className(), ['id' => 'ownerId']);
    }

    /**
     * Returns status name
     *
     * @return string
     */
    public function getStatusName()
    {
        return isset(self::$statuses[$this->status]) ? self::$statuses[$this->status] : '';
    }
}
