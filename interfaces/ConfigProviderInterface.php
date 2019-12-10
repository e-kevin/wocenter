<?php

namespace wocenter\interfaces;

/**
 * 系统配置提供者接口类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
interface ConfigProviderInterface
{
    
    /**
     * @var string 网站标题
     */
    const WEB_SITE_TITLE = 'WEB_SITE_TITLE';
    
    /**
     * @var string 网站描述
     */
    const WEB_SITE_DESCRIPTION = 'WEB_SITE_DESCRIPTION';
    
    /**
     * @var string 网站关键词
     */
    const WEB_SITE_KEYWORD = 'WEB_SITE_KEYWORD';
    
    /**
     * @var string 网站ICP
     */
    const WEB_SITE_ICP = 'WEB_SITE_ICP';
    
    /**
     * @var string 关闭网站
     */
    const WEB_SITE_CLOSE = 'WEB_SITE_CLOSE';
    
    /**
     * @var string 关闭网站提示语
     */
    const WEB_SITE_CLOSE_TIPS = 'WEB_SITE_CLOSE_TIPS';
    
    /**
     * @var int 字符串类型
     */
    const TYPE_STRING = 1;
    
    /**
     * @var int 文本类型
     */
    const TYPE_TEXT = 2;
    
    /**
     * @var int 下拉框类型
     */
    const TYPE_SELECT = 3;
    
    /**
     * @var int 选择框类型
     */
    const TYPE_CHECKBOX = 4;
    
    /**
     * @var int 单选项类型
     */
    const TYPE_RADIO = 5;
    
    /**
     * @var int 看板类型
     */
    const TYPE_KANBAN = 6;
    
    /**
     * @var int 日期时间类型
     */
    const TYPE_DATETIME = 7;
    
    /**
     * @var int 日期类型
     */
    const TYPE_DATE = 8;
    
    /**
     * @var int 时间类型
     */
    const TYPE_TIME = 9;
    
    /**
     * @var int 不分组
     */
    const CATEGORY_NONE = 0;
    
    /**
     * @var int 基础配置
     */
    const CATEGORY_BASE = 1;
    
    /**
     * @var int 内容配置
     */
    const CATEGORY_CONTENT = 2;
    
    /**
     * @var int 注册配置
     */
    const CATEGORY_REGISTRATION = 3;
    
    /**
     * @var int 系统配置
     */
    const CATEGORY_SYSTEM = 4;
    
    /**
     * @var int 安全配置
     */
    const CATEGORY_SECURITY = 5;
    
    /**
     * 获取所有配置项，数组键名以配置ID为索引
     *
     * @example
     * ```php
     * [
     *      'WEB_SITE_TITLE' => [],
     *      'WEB_SITE_DESCRIPTION' => [],
     * ]
     * ```
     *
     * @return array
     */
    public function getAll();
    
    /**
     * 删除缓存
     */
    public function clearCache();
    
}
