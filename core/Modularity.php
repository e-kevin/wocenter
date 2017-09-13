<?php
namespace wocenter\core;

use yii\base\Module;

/**
 * 基础Modularity类
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

}
