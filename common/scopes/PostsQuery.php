<?php

namespace common\scopes;

use common\models\Post;
use yii\db\ActiveQuery;

class PostsQuery extends ActiveQuery
{
    public function active()
    {
        return $this->andWhere(['{{%posts}}.status', Post::STATUS_ACTIVE]);
    }

    public function notDeleted()
    {
        return $this->andWhere(['<>', '{{%posts}}.status', Post::STATUS_DELETED]);
    }
    
    public function withCategoriesList()
    {
        return $this->addSelect('(SELECT GROUP_CONCAT(pc.categoryId) 
                            FROM {{%post_has_category}} pc
                            WHERE {{%posts}}.id = pc.postId
                            GROUP BY pc.postId) AS categoriesList');
    }
}