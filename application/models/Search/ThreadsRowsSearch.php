<?php

namespace app\models\Search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ThreadsRows;

/**
 * ThreadsRowsSearch represents the model behind the search form about `app\models\ThreadsRows`.
 */
class ThreadsRowsSearch extends ThreadsRows
{
    public $author;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['author', 'body_text', 'body_embedded', 'sent_at', 'created_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'author' => 'Autor',
            'body_text' => 'Treść wpisu',
            'body_embedded' => 'Link do obrazu',
            'body_embedded_file' => 'Uploadowany obraz',
            'status' => 'Status',
            'sent_at' => 'Data wysłania',
            'created_at' => 'Data utworzenia',
            'updated_at' => 'Data ost. zmiany'
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
     * @param array $threadRowsId
     * @param $params
     * @return ActiveDataProvider
     */
    public function search(array $threadRowsId, $params)
    {
        $query = ThreadsRows::find();

        $query->where(['threads_rows.id' => $threadRowsId]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);


        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->joinWith('author');

        // grid filtering conditions
        $query->andFilterWhere([
            'author.login' => $this->author,
            'threads_rows.status' => $this->status,
            'threads_rows.sent_at' => $this->sent_at,
            'threads_rows.created_at' => $this->created_at,
        ]);


        $query->andFilterWhere(['like', 'threads_rows.body_text', $this->body_text])
            ->andFilterWhere(['like', 'threads_rows.body_embedded', $this->body_embedded]);


        return $dataProvider;
    }
}