<?php

namespace backend\models;

use common\models\Post;
use yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

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
            [['id', 'visible', 'status'], 'integer'],
            [['title', 'categoriesList', 'ownerId'], 'safe'],
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
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Post::find();

        // add conditions that should always apply here
        $query->select('{{%posts}}.*');
        $query->joinWith(['owner']);
        $query->addSelect('(SELECT GROUP_CONCAT(pc.categoryId) 
                            FROM {{%post_has_category}} pc
                            WHERE {{%posts}}.id = pc.postId
                            GROUP BY pc.postId) AS categoriesList');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['ownerId'] = [
            'asc' => ['{{%user}}.username' => SORT_ASC],
            'desc' => ['{{%user}}.username' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'visible' => $this->visible,
            'status' => $this->status
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', '{{%user}}.username', $this->ownerId]);

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
