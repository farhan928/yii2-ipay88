<?php

namespace farhan928\Ipay88\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\AttributesBehavior;

/**
 * This is the model class for table "{{%ipay88_request}}".
 *
 * @property int $id
 * @property string $ref_no Unique transaction number
 * @property string $merchant_code
 * @property int $payment_id Refer Appendix I or II
 * @property double $amount
 * @property string $currency Refer Appendix I or II
 * @property string $prod_desc
 * @property string $user_name
 * @property string $user_email
 * @property string $user_contact
 * @property string $remark
 * @property string $lang
 * @property string $signature_type
 * @property string $signature
 * @property string $response_url
 * @property string $backend_url
 * @property string $redirect_url
 * @property string $created_at
 * @property string $updated_at
 */
class Ipay88Transaction extends \yii\db\ActiveRecord
{
    const SCENARIO_REQUEST = 'REQUEST';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%ipay88_transaction}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['payment_id', 'entity_id', 'status'], 'integer'],
            [['amount'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['ref_no'], 'string', 'max' => 35],
            [['merchant_code'], 'string', 'max' => 25],
            [['currency'], 'string', 'max' => 5],
            [['prod_desc', 'user_email', 'remark'], 'string', 'max' => 255],
            [['user_name', 'signature', 'err_desc'], 'string', 'max' => 100],
            [['user_contact', 'lang'], 'string', 'max' => 20],
            [['signature_type'], 'string', 'max' => 10],
            [['response_url', 'backend_url', 'redirect_url'], 'string', 'max' => 200],
            [['user_email'], 'email'],

            [['ref_no', 'merchant_code', 'amount', 'prod_desc', 'user_name', 'user_contact', 'user_email', 'signature', 'redirect_url'], 'required', 'on' => self::SCENARIO_REQUEST]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ref_no' => 'Ref No',
            'merchant_code' => 'Merchant Code',
            'entity_id' => 'Entity ID',
            'payment_id' => 'Payment ID',
            'amount' => 'Amount',
            'currency' => 'Currency',
            'prod_desc' => 'Prod Desc',
            'user_name' => 'User Name',
            'user_email' => 'User Email',
            'user_contact' => 'User Contact',
            'remark' => 'Remark',
            'lang' => 'Lang',
            'signature_type' => 'Signature Type',
            'signature' => 'Signature',
            'response_url' => 'Response URL',
            'backend_url' => 'Backend URL',
            'redirect_url' => 'Redirect URL',
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
            [
				'class' => AttributesBehavior::className(),
				'attributes' => [
					'ref_no' => [
						parent::EVENT_BEFORE_INSERT => function ($event, $attribute) {
							if(!$this->$attribute) return Yii::$app->security->generateRandomString(20);
							else return $this->$attribute;
						},						
					],								
				],
            ],           
        ];
    }

    public function generateRefNo($chars_limit = 20){
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';     
        
        $ref_no = substr(str_shuffle($permitted_chars), 0, $chars_limit);
        
        //ensure ref no is unique
        while(self::findOne(['ref_no'=>$ref_no])){
            $ref_no = substr(str_shuffle($permitted_chars), 0, $chars_limit);
        }    
        
        return $ref_no;
    }
}
