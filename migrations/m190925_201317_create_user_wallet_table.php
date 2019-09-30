<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user_wallet`.
 */
class m190925_201317_create_user_wallet_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user_wallet', [
            'id' => $this->primaryKey(),
            'sum' => $this->float(1)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('user_wallet');
    }
}
