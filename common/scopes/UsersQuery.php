<?php

namespace common\scopes;

use common\models\User;
use yii\db\ActiveQuery;

class UsersQuery extends ActiveQuery
{
    public function active()
    {
        return $this->andWhere(['{{%user}}.status', User::STATUS_ACTIVE]);
    }

    public function notDeleted()
    {
        return $this->andWhere(['<>', '{{%user}}.status', User::STATUS_DELETED]);
    }
}