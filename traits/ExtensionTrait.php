<?php

namespace wocenter\traits;

use wocenter\{
    helpers\FileHelper, helpers\WebConsoleHelper
};
use Yii;
use yii\base\InvalidParamException;

/**
 * Class ExtensionTrait
 * 主要为模块扩展和功能扩展提供支持
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
trait ExtensionTrait
{
    
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
                    throw new InvalidParamException('The "type" property is invalid.');
            }
            $cmd = "%s {$action} --migrationPath=%s --interactive=0 all";
            //执行
            WebConsoleHelper::run(sprintf($cmd,
                Yii::getAlias(WebConsoleHelper::getYiiCommand()),
                Yii::getAlias($this->migrationPath)
            ), false);
        }
    }
    
}
