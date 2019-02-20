<?php

namespace farhan928\Ipay88\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%ipay88_response}}".
 *
 * @property int $id
 * @property int $transaction_id
 * @property string $ref_no Unique transaction number
 * @property string $trans_id
 * @property array $content
 * @property string $created_at
 * @property string $updated_at
 */
class Ipay88Response extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%ipay88_response}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['transaction_id'], 'integer'],
            [['content', 'created_at', 'updated_at'], 'safe'],
            [['ref_no'], 'string', 'max' => 35],
            [['trans_id'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'transaction_id' => 'Transaction ID',
            'ref_no' => 'Ref No',
            'trans_id' => 'Trans ID',
            'content' => 'Content',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function behaviors()
    {        	
		return [			
			[
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    parent::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    parent::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
				'value' => new Expression('NOW()'),
            ], 	             
        ];
    }
}
