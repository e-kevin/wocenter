<?php

namespace wocenter\helpers;

use yii\base\BaseObject;

/**
 * Web控制台助手类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class WebConsoleHelper extends BaseObject
{
    
    /**
     * 是否 Win 系统
     *
     * @return bool
     */
    public static function isWin()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }
    
    /**
     * 获取 Yii 控制台脚本
     *
     * @return string
     */
    public static function getYiiCommand()
    {
        return self::isWin() ? '@root/yii.bat' : '@root/yii';
    }
    
    /**
     * 执行控制台命令
     *
     * @param string $cmd
     * @param bool $show 是否显示输出信息，默认显示
     */
    public static function run($cmd, $show = true)
    {
        if (self::isWin()) {
            $cmd = str_replace("\\", "\\\\", $cmd);
        }
        $handler = popen($cmd, 'r');
        while (!feof($handler)) {
            $show ? self::writeInfoMessage(fgets($handler), 1) : fgets($handler);
        }
        pclose($handler);
    }
    
    /**
     * 显示成功信息
     *
     * @param string $message 需要显示的信息
     * @param int $rnCount 换行总数
     */
    public static function writeSuccessMessage($message, $rnCount = 0)
    {
        self::writeColorMessage($message, 'green', $rnCount);
    }
    
    /**
     * 显示错误信息
     *
     * @param string $message 需要显示的信息
     * @param int $rnCount 换行总数
     */
    public static function writeErrorMessage($message, $rnCount = 0)
    {
        self::writeColorMessage($message, 'red', $rnCount);
    }
    
    /**
     * 显示提示信息
     *
     * @param string $message 需要显示的信息
     * @param int $rnCount 换行总数
     */
    public static function writeInfoMessage($message, $rnCount = 0)
    {
        self::writeColorMessage($message, 'orange', $rnCount);
    }
    
    /**
     * 显示彩色的信息
     *
     * @param string $message 需要显示的信息
     * @param string $color 文字颜色
     * @param int $rnCount 换行总数
     */
    public static function writeColorMessage($message, $color, $rnCount = 0)
    {
        $message = "<span style='color:{$color}'>$message</span>";
        self::writeMessage($message, $rnCount);
    }
    
    /**
     * 显示信息
     *
     * @param string $message 需要显示的信息
     * @param int $rnCount 换行总数
     */
    public static function writeMessage($message, $rnCount = 0)
    {
        $message = str_replace(["'", "\n"], ["\"", ""], $message) . str_repeat("<br />", $rnCount);
        self::writeScript("parent.writeMessage('$message')");
    }
    
    /**
     * 显示js脚本
     *
     * @param string $js
     */
    public static function writeScript($js)
    {
        echo "<script>$js</script>";
        ob_flush();
        flush();
    }
    
}
