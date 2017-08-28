<?php
use wocenter\console\Migration;

class m170822_065758_create_table_module extends Migration
{

    public function safeUp()
    {
        $this->createTable('{{%module}}', [
            'id' => $this->string(64)->notNull()->comment('模块标识ID'),
            'app' => $this->char(15)->notNull()->comment('所属应用'),
            'is_system' => $this->boolean()->unsigned()->notNull()->defaultValue(0)->comment('系统模块 0:否 1:是'),
            'status' => $this->boolean()->unsigned()->notNull()->defaultValue(0)->comment('状态 0:禁用 1:启用'),
        ], $this->tableOptions . $this->buildTableComment('系统模块'));

        $this->addPrimaryKey('unique', '{{%module}}', ['id', 'app']);
        
        $this->batchInsert('{{%module}}', ['id', 'app', 'is_system', 'status'], [
            ['account', 'backend', 1, 1],
            ['action', 'backend', 1, 1],
            ['core', 'backend', 1, 1],
            ['data', 'backend', 1, 1],
            ['log', 'backend', 1, 1],
            ['menu', 'backend', 1, 1],
            ['modularity', 'backend', 1, 1],
            ['notification', 'backend', 1, 1],
            ['operate', 'backend', 1, 1],
            ['passport', 'backend', 1, 1],
            ['system', 'backend', 1, 1],
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%module}}');
    }

}
