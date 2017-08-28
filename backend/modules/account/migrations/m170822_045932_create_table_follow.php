<?php
use wocenter\console\Migration;

class m170822_045932_create_table_follow extends Migration
{

    public function safeUp()
    {
        $this->setForeignKeyCheck();

        $this->createTable('{{%follow}}', [
            'id' => $this->primaryKey(11)->unsigned()->comment('ID'),
            'who_follow' => $this->integer()->unsigned()->notNull()->comment('谁关注'),
            'follow_who' => $this->integer()->unsigned()->notNull()->comment('关注谁'),
            'created_at' => $this->integer()->unsigned()->notNull()->comment('创建时间'),
            'alias' => $this->string(40)->notNull()->comment('备注'),
            'group_id' => $this->integer()->unsigned()->notNull()->comment('分组ID'),
        ], $this->tableOptions . $this->buildTableComment('关注表'));

        $this->addForeignKey('fk-follow-who_follow', '{{%follow}}', 'who_follow', '{{%user}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-follow-follow_who', '{{%follow}}', 'follow_who', '{{%user}}', 'id', 'CASCADE');

        $this->setForeignKeyCheck(true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%follow}}');
    }

}
