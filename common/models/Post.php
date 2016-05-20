<?php

namespace common\models;

use common\scopes\PostsQuery;
use HtmlTruncator\Truncator;
use voskobovich\behaviors\ManyToManyBehavior;
use yii;

/**
 * This is the model class for table "{{%posts}}".
 *
 * @property integer $id
 * @property integer $ownerId
 * @property string $title
 * @property string $content
 * @property integer $visible
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property \common\models\User $owner
 * @property \common\models\PostHasCategory[] $hasCategories
 * @property \common\models\PostCategory[] $categories
 * @property string $statusName read-only statusName
 * @property string $visibilityName read-only statusName
 * @property string $categoriesString
 * @property string $shortContent
 */
class Post extends yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_MODERATE = 2;
    
    const VISIBLE = 1;
    const INVISIBLE = 2;

    public static $statuses = [
        self::STATUS_ACTIVE => 'New',
        self::STATUS_MODERATE => 'Moderate',
        self::STATUS_DELETED => 'Deleted',
    ];

    public static $visibility = [
        self::VISIBLE => 'Yes',
        self::INVISIBLE => 'No'
    ];

    public $categoriesList;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%posts}}';
    }

    public static function find()
    {
        return new PostsQuery(get_called_class());
    }

    public function getHasCategories()
    {
        return $this->hasMany(PostHasCategory::className(), ['postId' => 'id']);
    }

    public function getCategories()
    {
        return $this->hasMany(PostCategory::className(), ['id' => 'categoryId'])
            ->via('hasCategories');
    }

    private static $_postCategories = [];

    /**
     * Return categories names separated by ","
     *
     * @return array
     */
    public function getCategoriesString()
    {
        $categoriesList = [];

        if ($this->categoriesList) {
            $categoryIds = explode(',', $this->categoriesList);

            foreach ($categoryIds as $categoryId) {
                if (!isset(self::$_postCategories[$categoryId]))
                    self::$_postCategories[$categoryId] = PostCategory::findOne($categoryId)->toArray();

                if (null !== self::$_postCategories[$categoryId])
                    $categoriesList[] = self::$_postCategories[$categoryId];
            }
        }

        return implode(', ', yii\helpers\ArrayHelper::getColumn($categoriesList, 'name'));
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => 'common\behaviours\Owner',
                'ownerIdAttribute' => 'ownerId'
            ],
            [
                'class' => yii\behaviors\TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
                'value' => date('Y-m-d H:i:s'),
            ],
            [
                'class' => ManyToManyBehavior::className(),
                'relations' => [
                    'selectedCategories' => 'categories',
                ],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'content'], 'filter', 'filter' => 'trim'],
            [['title', 'content'], 'filter', 'filter' => 'strip_tags'],

            [['selectedCategories'], 'each', 'rule' => ['integer']],
            [['title', 'content', 'selectedCategories'], 'required'],
            [['visible', 'status'], 'integer'],
            [['content'], 'string'],
            [['title'], 'string', 'max' => 255],

            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_MODERATE, self::STATUS_DELETED]],

            ['visible', 'default', 'value' => self::VISIBLE],
            ['visible', 'in', 'range' => [self::VISIBLE, self::INVISIBLE]],
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
            'title' => 'Title',
            'content' => 'Content',
            'visible' => 'Visible',
            'status' => 'Status',
            'selectedCategories' => 'Categories',
            'categoriesList' => 'Categories',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function beforeSave($insert)
    {
        if (Yii::$app->user->identity->role == User::ROLE_USER and $this->visible == self::VISIBLE) {
            $this->status = self::STATUS_MODERATE;
        }

        return parent::beforeSave($insert);
    }

    public function afterDelete()
    {
        parent::afterDelete();
        foreach ($this->categories as $category) {
            $this->unlink('categories', $category, true);
        }
    }

    public function getOwner()
    {
        return $this->hasOne(User::className(), ['id' => 'ownerId']);
    }

    public function getShortContent()
    {
        return Truncator::truncate($this->content, 20);
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

    /**
     * Returns visibility name
     *
     * @return string
     */
    public function getVisibilityName()
    {
        return isset(self::$visibility[$this->visible]) ? self::$visibility[$this->visible] : '';
    }
}
