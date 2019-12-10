<?php

namespace wocenter\core;

use wocenter\interfaces\ExtensionInfoInterface;
use Yii;
use yii\base\{
    InvalidConfigException, BaseObject
};

/**
 * 基础扩展信息实现类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class ExtensionInfo extends BaseObject implements ExtensionInfoInterface
{
    
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
     * @var string 扩展的仓库网址，例如：https://github.com/Wonail/wocenter
     */
    public $repositoryUrl;
    
    /**
     * @var string|array 开发者
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
     * [
     *      'wocenter/yii2-module-system' => 'dev-master',
     * ]
     */
    protected $depends = [];
    
    /**
     * @var array 扩展所需的composer包
     * [
     *      'wonail/wocenter' => '~0.3',
     * ]
     */
    protected $requirePackages = [];
    
    /**
     * @var string|null 获取扩展所属类型
     */
    protected $category;
    
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
                throw new InvalidConfigException(get_called_class() . ': The `$' . $prop . '` property must be set.');
            }
        }
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
    
    /**
     * @inheritdoc
     */
    public function getCategory()
    {
        return $this->category;
    }
    
    /**
     * @inheritdoc
     */
    public function getConfigKey()
    {
        return ['components', 'params'];
    }
    
    /**
     * @inheritdoc
     */
    public function getConfig()
    {
        return [];
    }
    
    /**
     * @inheritdoc
     */
    public function getCommonConfigKey()
    {
        return ['components', 'params'];
    }
    
    /**
     * @inheritdoc
     */
    public function getCommonConfig()
    {
        return [];
    }
    
    /**
     * Returns url to repository for creation of new issue.
     *
     * @param string $path
     *
     * @return string
     */
    final public function getIssueUrl($path = '/issues/new')
    {
        return self::getRepoUrl() ? (self::getRepoUrl() . $path) : '';
    }
    
    /**
     * Returns url of official repository.
     *
     * @return string
     */
    final public function getRepoUrl()
    {
        return $this->repositoryUrl;
    }
    
}
