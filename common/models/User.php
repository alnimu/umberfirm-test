<?php
namespace common\models;

use common\scopes\UsersQuery;
use yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $role
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property string $roleName read-only roleName
 * @property string $statusName read-only statusName
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_BANNED = 2;

    const ROLE_ADMIN = 1;
    const ROLE_MODERATOR = 2;
    const ROLE_USER = 3;
    
    const SCENARIO_CREATE = 'create';

    const EVENT_CREATE = 'create';
    const EVENT_UPDATE = 'update';
    const EVENT_DELETE = 'delete';

    public static $statuses = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_BANNED => 'Banned'
    ];
    
    public static $roles = [
        self::ROLE_ADMIN => 'Admin',
        self::ROLE_MODERATOR => 'Moderator',
        self::ROLE_USER => 'User'
    ];

    public $password;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    public function init()
    {
        parent::init();
        $this->on(self::EVENT_CREATE, [$this, 'updateRbacAssignment']);
        $this->on(self::EVENT_UPDATE, [$this, 'updateRbacAssignment']);
        $this->on(self::EVENT_DELETE, [$this, 'deleteRbacAssignment']);
    }

    public static function find()
    {
        return new UsersQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'email'], 'filter', 'filter' => 'trim'],
            [['username'], 'filter', 'filter' => 'strip_tags'],
            [['username', 'email'], 'required'],
            [['password'], 'required', 'on' => self::SCENARIO_CREATE],

            ['username', 'unique', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'message' => 'This email address has already been taken.'],

            ['password', 'string', 'min' => 6],

            ['role', 'default', 'value' => self::ROLE_USER],
            ['status', 'in', 'range' => [self::ROLE_ADMIN, self::ROLE_MODERATOR, self::ROLE_USER]],

            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_BANNED]],
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->password) {
                $this->setPassword($this->password);
            }

            if ($this->isNewRecord) {
                $this->generateAuthKey();
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns user role name
     * 
     * @return string
     */
    public function getRoleName()
    {
        return isset(self::$roles[$this->role]) ? self::$roles[$this->role] : '';
    }

    /**
     * Returns user status name
     *
     * @return string
     */
    public function getStatusName()
    {
        return isset(self::$statuses[$this->status]) ? self::$statuses[$this->status] : '';
    }

    /**
     * Updating RBAC assignment
     * 
     * @param yii\base\Event $event
     */
    public function updateRbacAssignment(yii\base\Event $event)
    {
        $auth = Yii::$app->authManager;
        
        /** @var User $model */
        $model = $event->sender;

        $auth->revokeAll($model->id);
        $role = $auth->getRole($model->role);

        $auth->assign($role, $model->id);
    }

    /**
     * Deleting RBAC assignment
     * 
     * @param yii\base\Event $event
     */
    public function deleteRbacAssignment(yii\base\Event $event)
    {
        $auth = Yii::$app->authManager;

        /** @var User $model */
        $model = $event->sender;

        $auth->revokeAll($model->id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by username for backend
     * 
     * @param string $username
     * @return null|static
     */
    public static function findByUsernameBackend($username)
    {
        return static::findOne([
            'username' => $username, 
            'role' => [self::ROLE_ADMIN, self::ROLE_MODERATOR],
            'status' => self::STATUS_ACTIVE
        ]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
}
