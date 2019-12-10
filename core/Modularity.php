<?php

namespace wocenter\core;

use yii\base\Module;

/**
 * 支持系统调度功能（Dispatch）的基础模块类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class Modularity extends Module
{
    
    /**
     * 更改默认路由是为了防止在系统使用调度服务时命名空间不支持`default`|`public`等字符时的问题
     *
     * @inheritdoc
     */
    public $defaultRoute = 'common';
    
    /**
     * @var string 基础命名空间，一般为当前模块的命名空间。
     */
    public $baseNamespace;
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->baseNamespace === null) {
            $class = get_class($this);
            if (($pos = strrpos($class, '\\')) !== false) {
                $this->baseNamespace = substr($class, 0, $pos);
            }
        }
        if ($this->controllerNamespace === null) {
            $this->controllerNamespace = $this->baseNamespace . '\\controllers';
        }
        parent::init();
    }
    
}
