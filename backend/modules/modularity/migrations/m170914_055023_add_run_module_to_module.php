<?php
use wocenter\console\Migration;

class m170914_055023_add_run_module_to_module extends Migration
{

    public function safeUp()
    {
        $this->addColumn('{{%module}}', 'run_module', $this->boolean()->unsigned()->notNull()
            ->defaultValue(0)->comment('运行模块 0:核心 1:开发者'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%module}}', 'run_module');
    }

}
