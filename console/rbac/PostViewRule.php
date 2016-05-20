<?php

namespace console\rbac;

use common\models\Post;
use yii\rbac\Rule;

class PostViewRule extends Rule
{
    public $name = 'canView';

    /**
     * @param string|integer $user the user ID.
     * @param \yii\rbac\Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return boolean a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        $can = false;
        if (isset($params['post'])) {
            if ($params['post']->ownerId == $user)
                $can = true;
            elseif ($params['post']->status != Post::STATUS_DELETED)
                $can = true;
        }

        return $can;
    }
}