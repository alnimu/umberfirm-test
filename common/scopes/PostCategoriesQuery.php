<?php
/**
 * Created by PhpStorm.
 * User: alnimu
 * Date: 19.05.16
 * Time: 22:15
 */

namespace common\scopes;

use common\models\PostCategory;
use yii\db\ActiveQuery;

class PostCategoriesQuery extends ActiveQuery
{
    public function active()
    {
        return $this->andWhere(['{{%post_categories}}.status' => PostCategory::STATUS_ACTIVE]);
    }
    
    public function notDeleted()
    {
        return $this->andWhere(['<>', '{{%post_categories}}.status', PostCategory::STATUS_DELETED]);
    }
}