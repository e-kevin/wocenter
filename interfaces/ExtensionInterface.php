<?php

namespace wocenter\interfaces;

/**
 * 扩展接口类
 *
 * @property string $uniqueId 扩展唯一ID，只读属性
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
interface ExtensionInterface
{
    /**
     * 获取扩展唯一ID，不可重复
     *
     * @return string
     */
    public function getUniqueId();
    
    /**
     * 安装
     *
     * @return boolean
     */
    public function install();
    
    /**
     * 卸载
     *
     * @return boolean
     */
    public function uninstall();
    
    /**
     * 升级
     *
     * @return boolean
     */
    public function upgrade();
    
}
