<?php

namespace farhan928\Ipay88\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use farhan928\Ipay88\models\Ipay88Config;

/**
 * Ipay88ConfigSearch represents the model behind the search form of `farhan928\Ipay88\models\Ipay88Config`.
 */
class Ipay88ConfigSearch extends Ipay88Config
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'entity_id'], 'integer'],
            [['merchant_code', 'merchant_key', 'description', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = Ipay88Config::find();

        // add conditions that should always apply here

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
            'entity_id' => $this->entity_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'merchant_code', $this->merchant_code])
            ->andFilterWhere(['like', 'merchant_key', $this->merchant_key])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
