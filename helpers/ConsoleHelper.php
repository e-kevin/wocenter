<?php

namespace wocenter\helpers;

use Yii;
use yii\base\BaseObject;
use yii\helpers\Console;
use yii\web\Application;

/**
 * 控制台助手类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class ConsoleHelper extends BaseObject
{
    
    /**
     * 是否 Windows 系统
     *
     * @return bool
     */
    public static function isRunningOnWindows()
    {
        return DIRECTORY_SEPARATOR === '\\';
    }
    
    /**
     * 获取指定的控制台脚本
     *
     * @param string $cmd
     *
     * @return string
     */
    public static function getCommander($cmd = 'yii')
    {
        return self::isRunningOnWindows() ? "@root/{$cmd}.bat" : "@root/{$cmd}";
    }
    
    /**
     * 执行控制台命令
     *
     * @param string $cmd 需要执行的命令
     * @param bool $show 是否显示输出信息，默认显示
     */
    public static function run($cmd, $show = true)
    {
        if (self::isRunningOnWindows()) {
            $cmd = str_replace("\\", "\\\\", $cmd);
        }
        $handler = popen($cmd, 'r');
        while (!feof($handler)) {
            $show ? self::info(fgets($handler), 1) : fgets($handler);
        }
        pclose($handler);
    }
    
    /**
     * 显示成功信息
     *
     * @param string $message 需要显示的信息
     * @param int $rnCount 换行总数
     */
    public static function success($message, $rnCount = 0)
    {
        if (Yii::$app instanceof Application) {
            self::writeColorMessage($message, 'green', $rnCount);
        } else {
            Console::stdout($message);
        }
    }
    
    /**
     * 显示错误信息
     *
     * @param string $message 需要显示的信息
     * @param int $rnCount 换行总数
     */
    public static function error($message, $rnCount = 0)
    {
        if (Yii::$app instanceof Application) {
            self::writeColorMessage($message, 'red', $rnCount);
        } else {
            Console::stderr($message);
        }
    }
    
    /**
     * 显示提示信息
     *
     * @param string $message 需要显示的信息
     * @param int $rnCount 换行总数
     */
    public static function info($message, $rnCount = 0)
    {
        if (Yii::$app instanceof Application) {
            self::writeColorMessage($message, 'orange', $rnCount);
        } else {
            Console::stdout($message);
        }
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
    private static function writeScript($js)
    {
        echo "<script>$js</script>";
        ob_flush();
        flush();
    }
    
}