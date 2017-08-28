<?php
use wocenter\console\Migration;

class m170821_143853_create_table_backend_user extends Migration
{

    public function safeUp()
    {
        $this->setForeignKeyCheck();

        $this->createTable('{{%backend_user}}', [
            'id' => $this->primaryKey()->unsigned()->comment('ID'),
            'user_id' => $this->integer()->unsigned()->unique()->notNull()->comment('用户ID'),
            'status' => $this->boolean()->unsigned()->notNull()->defaultValue(0)->comment('管理员状态'),
        ], $this->tableOptions . $this->buildTableComment('后台用户表'));

        $this->insert('{{%backend_user}}', ['id' => 1, 'user_id' => 1, 'status' => 1]);

        $this->createIndex('idx-backend_user-status', '{{%backend_user}}', 'status');

        $this->addForeignKey('fk-backend_user-user_id', '{{%backend_user}}', 'user_id', '{{%user}}', 'id', 'CASCADE');

        $this->setForeignKeyCheck(true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%backend_user}}');
    }

}
