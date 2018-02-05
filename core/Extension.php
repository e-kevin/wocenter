<?php

namespace wocenter\core;

use wocenter\interfaces\ExtensionInterface;
use yii\base\{
    InvalidConfigException, BaseObject
};
use yii\db\Connection;
use yii\di\Instance;

/**
 * 基础扩展实现类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class Extension extends BaseObject implements ExtensionInterface
{
    
    /**
     * @var Connection|array|string DB连接对象或DB连接的应用程序组件ID，主要是为扩展提供操作数据库功能
     */
    public $db = 'db';
    
    /**
     * @var string 扩展唯一ID，不可重复
     */
    private $_uniqueId;
    
    /**
     * @var string 扩展唯一名称，不可重复
     */
    private $_uniqueName;
    
    /**
     * @var string 版本
     */
    private $_version;
    
    /**
     * @var string 所属应用
     */
    public $app;
    
    /**
     * @var string 扩展ID
     */
    public $id;
    
    /**
     * @var string 名称
     */
    public $name;
    
    /**
     * @var string 描述
     */
    public $description;
    
    /**
     * @var string 扩展备注信息
     */
    public $remark;
    
    /**
     * @var string 扩展网址
     */
    public $url;
    
    /**
     * @var string 开发者
     */
    public $developer = 'WoCenter';
    
    /**
     * @var string 开发者网站
     */
    public $webSite;
    
    /**
     * @var string 开发者邮箱
     */
    public $email;
    
    /**
     * @var boolean 是否系统扩展
     */
    public $isSystem = false;
    
    /**
     * @var boolean 是否可安装
     */
    public $canInstall = false;
    
    /**
     * @var boolean 是否可卸载
     */
    public $canUninstall = false;
    
    /**
     * @var array 必须设置的属性值
     */
    protected $mustBeSetProps = ['app', 'id'];
    
    /**
     * @var array 扩展所需依赖
     */
    protected $depends = [];
    
    /**
     * @var array 扩展所需的composer包
     */
    protected $requirePackages = [];
    
    /**
     * @inheritdoc
     */
    public function __construct($uniqueId, $uniqueName, $version, array $config = [])
    {
        $this->_uniqueId = $uniqueId;
        $this->_uniqueName = $uniqueName;
        $this->_version = $version;
        parent::__construct($config);
    }
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        foreach ($this->mustBeSetProps as $prop) {
            if ($this->{$prop} === null) {
                throw new InvalidConfigException(get_called_class() . ": The {$prop} property must be set.");
            }
        }
        $this->db = Instance::ensure($this->db, Connection::className());
        $this->db->getSchema()->refresh();
        $this->db->enableSlaves = false;
    }
    
    /**
     * @inheritdoc
     */
    public function install()
    {
        return true;
    }
    
    /**
     * @inheritdoc
     */
    public function uninstall()
    {
        return true;
    }
    
    /**
     * @inheritdoc
     */
    public function upgrade()
    {
        return true;
    }
    
    /**
     * @inheritdoc
     */
    public function getUniqueId()
    {
        return $this->_uniqueId;
    }
    
    /**
     * @inheritdoc
     */
    public function getUniqueName()
    {
        return $this->_uniqueName;
    }
    
    /**
     * @inheritdoc
     */
    public function getVersion()
    {
        return $this->_version;
    }
    
    /**
     * @inheritdoc
     */
    public function getDepends()
    {
        return $this->depends;
    }
    
    /**
     * @inheritdoc
     */
    public function getRequirePackages()
    {
        return $this->requirePackages;
    }
    
}
