<?php

namespace farhan928\Ipay88\models;

use Yii;

/**
 * This is the model class for table "{{%ipay88_config}}".
 *
 * @property int $id
 * @property int $entity_id
 * @property string $merchant_code
 * @property string $merchant_key
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 */
class Ipay88Config extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%ipay88_config}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['entity_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['merchant_code'], 'string', 'max' => 30],
            [['merchant_key'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'entity_id' => 'Entity ID',
            'merchant_code' => 'Merchant Code',
            'merchant_key' => 'Merchant Key',
            'description' => 'Description',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
