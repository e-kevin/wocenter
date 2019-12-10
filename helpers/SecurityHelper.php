<?php

namespace wocenter\helpers;

/**
 * 安全助手类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class SecurityHelper
{
    
    /**
     * 保密邮箱地址
     *
     * @param string $str 邮箱地址
     *
     * @return string
     */
    public static function markEmail($str): string
    {
        list($first, $second) = explode('@', $str);
        $a = strlen($first) - 3;
        
        return preg_replace('|(?<=.{1})(.{' . $a . '}).*?|', str_pad('', $a, '*'), $first, 1) . '@' . $second;
    }
    
    /**
     * 保密手机号
     *
     * @param string $str 手机号
     *
     * @return string
     */
    public static function markMobile($str): string
    {
        if (!empty($str)) {
            return preg_replace('|(?<=.{3})(.{4}).*?|', str_pad('', 4, '*'), $str, 1);
        }
        
        return $str;
    }
    
    /**
     * 保密字符串
     *
     * @param string $str
     *
     * @return string
     */
    public static function markString($str): string
    {
        $str_len = strlen($str);
        $start = 6 < $str_len ? intval($str_len / 5) : $str_len - 4;
        $end = $str_len - $start * 3;
        
        return preg_replace('|(?<=.{' . $start . '})(.{' . $end . '}).*?|', str_pad('', $end, '*'), $str, 1);
    }
    
    /**
     * 获取哈希值
     *
     * @param string $message
     * @param string $salt
     *
     * @return string
     */
    public static function hash(string $message, $salt = "wocenter"): string
    {
        $s01 = $message . $salt;
        $s02 = md5($s01) . $salt;
        $s03 = sha1($s01) . md5($s02) . $salt;
        $s04 = $salt . md5($s03) . $salt . $s02;
        $s05 = $salt . sha1($s04) . md5($s04) . crc32($salt . $s04);
        
        return md5($s05);
    }
    
    /**
     * 系统解密方法
     *
     * @param string $data 要解密的字符串
     * @param string $key 加密密钥
     *
     * @return string
     */
    public static function decrypt($data = '', $key = ''): string
    {
        $key = md5(empty($key) ? \Yii::$app->getRequest()->cookieValidationKey : $key);
        $data = str_replace(['-', '_'], ['+', '/'], $data);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        $data = base64_decode($data);
        $expire = substr($data, 0, 10);
        $data = substr($data, 10);
        
        if ($expire > 0 && $expire < time()) {
            return '';
        }
        $x = 0;
        $len = strlen($data);
        $l = strlen($key);
        $char = $str = '';
        
        for ($i = 0; $i < $len; $i++) {
            if ($x == $l) {
                $x = 0;
            }
            $char .= substr($key, $x, 1);
            $x++;
        }
        
        for ($i = 0; $i < $len; $i++) {
            if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
                $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
            } else {
                $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
            }
        }
        
        return base64_decode($str);
    }
    
    /**
     * 系统加密方法
     *
     * @param string $data 要加密的字符串
     * @param string $key 加密密钥
     * @param integer $expire 过期时间 单位 秒
     *
     * @return string
     */
    public static function encrypt($data = '', $key = '', $expire = 0): string
    {
        $key = md5(empty($key) ? \Yii::$app->getRequest()->cookieValidationKey : $key);
        $data = base64_encode($data);
        $x = 0;
        $len = strlen($data);
        $l = strlen($key);
        $char = '';
        
        for ($i = 0; $i < $len; $i++) {
            if ($x == $l) {
                $x = 0;
            }
            $char .= substr($key, $x, 1);
            $x++;
        }
        
        $str = sprintf('%010d', $expire ? $expire + time() : 0);
        
        for ($i = 0; $i < $len; $i++) {
            $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1))) % 256);
        }
        
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($str));
    }
    
}
