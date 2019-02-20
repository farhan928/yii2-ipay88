<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%ipay88_backend}}`.
 */
class m190220_073223_create_ipay88_backend_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%ipay88_backend}}', [
            'id' => $this->primaryKey(),
            'transaction_id' => $this->integer(),
            'ref_no' => $this->string(35)->comment('Unique transaction number'),
            'trans_id' => $this->string(30),
            'content' => $this->json(),            
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%ipay88_backend}}');
    }
}
