<?php

namespace wocenter\traits;

use wocenter\{
    helpers\FileHelper, helpers\ConsoleHelper
};
use Yii;
use yii\base\InvalidArgumentException;
use yii\db\Connection;

/**
 * Class ExtensionTrait
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
trait ExtensionTrait
{
    
    /**
     * @var string|Connection DB连接对象或DB连接的应用程序组件ID，主要是为扩展提供操作数据库功能
     */
    public $db = 'db';
    
    /**
     * @return Connection
     */
    public function getDb()
    {
        if (!Yii::$app->has($this->db,true)) {
            $this->db = Yii::$app->get($this->db);
            $this->db->getSchema()->refresh();
            $this->db->enableSlaves = false;
        }
        
        return $this->db;
    }
    
    /**
     * 执行migrate操作
     *
     * @param string $type 操作类型
     */
    protected function runMigrate($type)
    {
        if (FileHelper::isDir(Yii::getAlias($this->migrationPath))) {
            $action = "migrate/";
            switch ($type) {
                case 'up':
                    $action .= 'up';
                    break;
                case 'down':
                    $action .= 'down';
                    break;
                default:
                    throw new InvalidArgumentException('The "type" property is invalid.');
            }
            $cmd = "%s {$action} --migrationPath=%s --interactive=0 all";
            //执行
            ConsoleHelper::run(sprintf($cmd,
                Yii::getAlias(ConsoleHelper::getCommander()),
                Yii::getAlias($this->migrationPath)
            ), false);
        }
    }
    
}
