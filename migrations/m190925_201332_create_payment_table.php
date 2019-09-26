<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%payment}}`.
 */
class m190925_201332_create_payment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%payment}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'sum' => $this->float(1)
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            'idx-payment-user_id',
            'payment',
            'user_id'
        );

        // add foreign key for table `user_wallet`
        $this->addForeignKey(
            'fk-payment-user_id',
            'payment',
            'user_id',
            'user_wallet',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%payment}}');
    }
}
