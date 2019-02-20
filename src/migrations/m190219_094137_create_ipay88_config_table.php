<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%ipay88_config}}`.
 */
class m190219_094137_create_ipay88_config_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%ipay88_config}}', [
            'id' => $this->primaryKey(),
            'entity_id' => $this->integer(),
            'merchant_code' => $this->string(30),
            'merchant_key' => $this->string(50),
            'description' => $this->string(100),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%ipay88_config}}');
    }
}
