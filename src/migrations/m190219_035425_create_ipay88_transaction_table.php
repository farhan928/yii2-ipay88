<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%ipay88_transaction}}`.
 */
class m190219_035425_create_ipay88_transaction_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%ipay88_transaction}}', [
            'id' => $this->primaryKey(),
            'ref_no' => $this->string(35)->comment('Unique transaction number'),
            'entity_id' => $this->integer(),
            'merchant_code' => $this->string(25),
            'payment_id' => $this->smallInteger()->comment('Refer Appendix I or II'),
            'amount' => $this->float(15,2),
            'currency' => $this->string(5)->comment('Refer Appendix I or II'),
            'prod_desc' => $this->string(255),
            'user_name' => $this->string(100),
            'user_email' => $this->string(255),
            'user_contact' => $this->string(20),
            'status' => $this->smallInteger()->defaultValue(2)->comment('0 - Failed, 1 - Success, 2 - Pending'),
            'err_desc' => $this->string(100),
            'remark' => $this->string(255),
            'lang' => $this->string(20),
            'signature_type' => $this->string(10),
            'signature' => $this->string(100),
            'response_url' => $this->string(200),
            'backend_url' => $this->string(200),
            'redirect_url' => $this->string(200),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%ipay88_transaction}}');
    }
}
