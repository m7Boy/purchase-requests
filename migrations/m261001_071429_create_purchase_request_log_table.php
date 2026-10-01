<?php

use yii\db\Migration;

class m261001_071429_create_purchase_request_log_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%purchase_request_log}}', [
            'id' => $this->primaryKey(),
            'purchase_request_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'old_status' => $this->string(20)->notNull(),
            'new_status' => $this->string(20)->notNull(),
            'comment' => $this->text(),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-purchase_request_log-request',
            '{{%purchase_request_log}}',
            'purchase_request_id',
            '{{%purchase_request}}',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-purchase_request_log-request',
            '{{%purchase_request_log}}'
        );

        $this->dropTable('{{%purchase_request_log}}');
    }
}