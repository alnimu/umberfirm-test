<?php

use yii\db\Migration;

class m160519_112823_add_default_users extends Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;

        $this->insert('{{%user}}', [
            'id' => 1,
            'username' => 'admin',
            'password_hash' => Yii::$app->security->generatePasswordHash('gfhjkm'),
            'email' => 'admin@example.com',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'role' => \common\models\User::ROLE_ADMIN,
            'status' => \common\models\User::STATUS_ACTIVE,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        $role = $auth->getRole(\common\models\User::ROLE_ADMIN);
        $auth->assign($role, 1);

        $this->insert('{{%user}}', [
            'id' => 2,
            'username' => 'moderator',
            'password_hash' => Yii::$app->security->generatePasswordHash('gfhjkm'),
            'email' => 'moderator@example.com',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'role' => \common\models\User::ROLE_MODERATOR,
            'status' => \common\models\User::STATUS_ACTIVE,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        $role = $auth->getRole(\common\models\User::ROLE_MODERATOR);
        $auth->assign($role, 2);

        $this->insert('{{%user}}', [
            'id' => 3,
            'username' => 'user',
            'password_hash' => Yii::$app->security->generatePasswordHash('gfhjkm'),
            'email' => 'user@example.com',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'role' => \common\models\User::ROLE_USER,
            'status' => \common\models\User::STATUS_ACTIVE,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        $role = $auth->getRole(\common\models\User::ROLE_USER);
        $auth->assign($role, 3);
    }

    public function down()
    {
        echo "m160519_112823_add_default_users cannot be reverted.\n";
        return false;
    }
}
