<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%ipay88_response}}`.
 */
class m190219_045843_create_ipay88_response_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%ipay88_response}}', [
            'id' => $this->primaryKey(),
            'transaction_id' => $this->integer(),
            'ref_no' => $this->string(35)->comment('Unique transaction number'),
            'trans_id' => $this->string(30),
            'content' => $this->json(),            
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp(),
        ]);

        $this->addForeignKey(
            'fk-response-transaction_id',
            '{{%ipay88_response}}',
            'transaction_id',
            '{{%ipay88_transaction}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-response-request_id',
            '{{%ipay88_response}}'
        );

        $this->dropTable('{{%ipay88_response}}');
    }
}
