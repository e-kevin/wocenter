<?php

namespace wocenter\core;

use wocenter\interfaces\ConfigProviderInterface;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;

/**
 * 文件形式的配置提供者实现类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class ConfigProvider extends BaseObject implements ConfigProviderInterface
{
    
    /**
     * 配置数据
     *
     * @var array
     */
    public $config = [];
    
    /**
     * 默认配置数据
     *
     * @var array
     */
    protected $_defaultConfig = [
        self::WEB_SITE_TITLE => [
            'name' => self::WEB_SITE_TITLE,
            'title' => '网站名称',
            'remark' => '网站名称',
            'value' => 'WC后台管理系统',
            'extra' => '',
        ],
        self::WEB_SITE_DESCRIPTION => [
            'name' => self::WEB_SITE_DESCRIPTION,
            'title' => '网站简介',
            'remark' => '搜索引擎描述',
            'value' => '',
            'extra' => '',
        ],
        self::WEB_SITE_KEYWORD => [
            'name' => self::WEB_SITE_KEYWORD,
            'title' => '网站关键词',
            'remark' => '搜索引擎关键词',
            'value' => '',
            'extra' => '',
        ],
        self::WEB_SITE_ICP => [
            'name' => self::WEB_SITE_ICP,
            'title' => '网站备案号',
            'remark' => '网站备案号，如：沪ICP备12345678号-9',
            'value' => '',
            'extra' => '',
        ],
        self::WEB_SITE_CLOSE => [
            'name' => self::WEB_SITE_CLOSE,
            'title' => '关闭站点',
            'remark' => '站点关闭后其他用户不能访问，管理员可以正常访问',
            'value' => 1,
            'extra' => '0:关闭,1:开启',
        ],
        self::WEB_SITE_CLOSE_TIPS => [
            'name' => self::WEB_SITE_CLOSE_TIPS,
            'title' => '站点关闭提示语',
            'remark' => '站点关闭后显示的提示信息',
            'value' => '网站正在更新维护，请稍候再试～',
            'extra' => '',
        ],
    ];
    
    /**
     * @inheritdoc
     */
    public function __construct(array $config = [])
    {
        $config['config'] = ArrayHelper::merge($this->_defaultConfig, $config['config'] ?? []);
        parent::__construct($config);
    }
    
    /**
     * @inheritdoc
     */
    public function getAll()
    {
        return $this->config;
    }
    
    /**
     * @inheritdoc
     */
    public function clearCache()
    {
    }
    
}
