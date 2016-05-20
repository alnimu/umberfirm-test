<?php
namespace console\controllers;

use common\models\User;
use console\rbac\AuthorRule;
use console\rbac\PostViewRule;
use yii;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();

        // create roles
        $admin = $auth->createRole(User::ROLE_ADMIN);
        $admin->description = 'Admin';
        $auth->add($admin);

        $moderator = $auth->createRole(User::ROLE_MODERATOR);
        $moderator->description = 'Moderator';
        $auth->add($moderator);

        $user = $auth->createRole(User::ROLE_USER);
        $user->description = 'User';
        $auth->add($user);

        // add the rules
        $authorRule = new AuthorRule();
        $auth->add($authorRule);

        $postViewRule = new PostViewRule();
        $auth->add($postViewRule);

        $updateOwnPost = $auth->createPermission('updateOwnPost');
        $updateOwnPost->description = 'Update own post';
        $updateOwnPost->ruleName = $authorRule->name;
        $auth->add($updateOwnPost);

        // allow "author" to update their own posts
        $auth->addChild($user, $updateOwnPost);

        $viewPost = $auth->createPermission('postView');
        $viewPost->description = 'View post';
        $viewPost->ruleName = $postViewRule->name;
        $auth->add($viewPost);

        $auth->addChild($user, $viewPost);
    }
}