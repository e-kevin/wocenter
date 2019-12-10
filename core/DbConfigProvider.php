<?php

namespace wocenter\core;

use wocenter\interfaces\ConfigProviderInterface;
use yii\base\BaseObject;
use yii\db\Connection;
use yii\db\Query;
use yii\di\Instance;

/**
 * 数据库形式的配置提供者实现类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class DbConfigProvider extends BaseObject implements ConfigProviderInterface
{
    
    /**
     * @var Connection|string|array 数据库组件配置
     */
    public $db = 'db';
    
    /**
     * @var string 配置数据库表名
     */
    public $tableName;
    
    /**
     * @var string 配置键名
     */
    public $nameField = 'name';
    
    /**
     * @var string 配置值字段
     */
    public $valueField = 'value';
    
    /**
     * @var string 额外数据字段
     */
    public $extraField = 'extra';
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->db = Instance::ensure($this->db, Connection::class);
    }
    
    /**
     * @inheritdoc
     */
    public function getAll()
    {
        return (new Query())
            ->select([$this->nameField, $this->valueField, $this->extraField])
            ->from($this->tableName)
            ->indexBy($this->nameField)
            ->all($this->db);
    }
    
    /**
     * @inheritdoc
     */
    public function clearCache()
    {
    }
    
}
