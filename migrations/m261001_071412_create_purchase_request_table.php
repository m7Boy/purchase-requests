<?php

use yii\db\Migration;

class m261001_071412_create_purchase_request_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%purchase_request}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'description' => $this->text(),
            'amount' => $this->decimal(10, 2)->notNull(),
            'status' => $this->string(20)->notNull()->defaultValue('draft'),
            'created_by' => $this->integer()->notNull(),
            'approved_by' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex(
            'idx-purchase_request-status',
            '{{%purchase_request}}',
            'status'
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%purchase_request}}');
    }
}