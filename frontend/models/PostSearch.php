<?php

namespace frontend\models;

use yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Post;

/**
 * PostSearch represents the model behind the search form about `common\models\Post`.
 */
class PostSearch extends Post
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['categoriesList'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param bool $own
     *
     * @return ActiveDataProvider
     */
    public function search($params, $own=false)
    {
        $query = Post::find();

        // add conditions that should always apply here
        if (!$own) {
            $query->andWhere(['{{%posts}}.status' => [Post::STATUS_ACTIVE]]);
            $query->andWhere(['{{%posts}}.visible' => [Post::VISIBLE]]);
        } else {
            $query->andWhere(['{{%posts}}.ownerId' => Yii::$app->user->getId()]);
        }

        $query->select('{{%posts}}.*');
        $query->with('owner');
        $query->addSelect('(SELECT GROUP_CONCAT(pc.categoryId) 
                            FROM {{%post_has_category}} pc
                            WHERE {{%posts}}.id = pc.postId
                            GROUP BY pc.postId) AS categoriesList');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'like',
            '(SELECT GROUP_CONCAT(pc.categoryId) 
                    FROM {{%post_has_category}} pc 
                    WHERE {{%posts}}.id = pc.postId GROUP BY pc.postId)',
            $this->categoriesList
        ]);

        return $dataProvider;
    }
}
