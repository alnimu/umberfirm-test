<?php

namespace backend\models;

use Yii;
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
            [['id', 'ownerId', 'visible', 'status'], 'integer'],
            [['title', 'content', 'categoriesList', 'created_at', 'updated_at'], 'safe'],
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
            'id' => $this->id,
            'ownerId' => $this->ownerId,
            'visible' => $this->visible,
            'status' => $this->status
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'content', $this->content]);

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
