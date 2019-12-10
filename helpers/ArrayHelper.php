<?php

namespace wocenter\helpers;

use yii\helpers\ArrayHelper as baseArrayHelper;

/**
 * 数组助手类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class ArrayHelper extends baseArrayHelper
{
    
    /**
     * 数组转换为字符串，主要用于把分隔符调整到第二个参数
     *
     * @param array $arr 要连接的数组
     * @param string $glue 分割符
     *
     * @return string
     */
    public static function arrayToString($arr, $glue = ','): string
    {
        return implode($glue, $arr);
    }
    
    /**
     * 不区分大小写的in_array实现
     *
     * @param string $key 待查询的key
     * @param mixed $data 被查询的数据
     * @param boolean $validate_string 是否验证包含字符串，默认不验证
     * @param boolean $inverse 参数反转，仅在$validate_string为true时生效。
     * 当$inverse参数为true时，即在$key里查询$array里的值。
     * 例如：ArrayHelper::inArrayCase('administrators', ['admin', 'guest], true, true)结果为true
     *
     * @return boolean
     */
    public static function inArrayCase($key, $data, $validate_string = false, $inverse = false): bool
    {
        if (!is_array($data)) {
            $data = explode(',', $data);
        }
        foreach ($data as $k) {
            if (strcasecmp($key, $k) === 0) {
                return true;
            }
            if ($validate_string) {
                return $inverse ? (strpos($k, strtolower($key)) !== false) : (strpos(strtolower($key), $k) !== false);
            }
            continue;
        }
        
        return false;
    }
    
    /**
     * 浏览器友好的变量输出
     *
     * @param mixed $var 变量
     * @param boolean $echo 是否输出，默认为`true`。如果为`false`，则返回输出字符串
     * @param string $label 标签，默认为空
     * @param boolean $strict 是否严谨，默认为`true`
     *
     * @return string|null
     */
    public static function dump($var, $echo = true, $label = null, $strict = true)
    {
        $label = ($label === null) ? '' : rtrim($label) . ' ';
        if (!$strict) {
            if (ini_get('html_errors')) {
                $output = print_r($var, true);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            } else {
                $output = $label . print_r($var, true);
            }
        } else {
            ob_start();
            var_dump($var);
            $output = ob_get_clean();
            if (!extension_loaded('xdebug')) {
                $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            }
        }
        if ($echo) {
            echo($output);
            
            return null;
        } else {
            return $output;
        }
    }
    
    /**
     * 把返回的数据集转换成Tree
     *
     * @param array $list 要转换的数据集
     * @param string $pk 供pid索引的id字段
     * @param string $pid parent标记字段
     * @param string $child 子类标记字段
     * @param integer $root 顶级菜单数值，默认为`0`，表示显示到最顶级菜单
     *
     * @return array
     */
    public static function listToTree($list, $pk = 'id', $pid = 'parent_id', $child = 'items', $root = 0): array
    {
        if (empty($list)) {
            return [];
        }
        // 创建Tree
        $tree = [];
        if (is_array($list)) {
            // 创建基于主键的数组引用
            $refer = [];
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] = &$list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[] = &$list[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent = &$refer[$parentId];
                        $parent[$child][] = &$list[$key];
                    }
                }
            }
        }
        
        return $tree;
    }
    
    /**
     * 将listToTree的树还原成列表
     *
     * @param array $tree 原来的树
     * @param string $child 子类节点的键
     * @param string|null $order 排序显示的键，一般是主键 升序排列，默认为`null`，表示不排序
     * @param array $list 过渡用的中间数组，
     *
     * @return array 返回排过序的列表数组
     */
    public static function treeToList($tree, $child = 'items', $order = null, &$list = []): array
    {
        if (is_array($tree)) {
            foreach ($tree as $key => $value) {
                $refer = $value;
                if (isset($refer[$child])) {
                    unset($refer[$child]);
                    self::treeToList($value[$child], $child, $order, $list);
                }
                $list[] = $refer;
            }
            if ($order !== null) {
                $list = self::listSortBy($list, $order, 'asc');
            }
        }
        
        return $list;
    }
    
    /**
     * 在数组列表中搜索
     *
     * @param array $list 数据列表
     * @param mixed $condition 查询条件，支持数组、操作符 ['neq', 'eq', 'not in', 'in']、字符串、todo 正则
     * 1: ['name'=>$value]
     * 2: 'name=value&name1=value1'
     * 3: ['name' => ['not in' => ['value1', 'value2']]]
     *
     * @return array
     */
    public static function listSearch($list = [], $condition = '')
    {
        if (empty($list) || empty($condition)) {
            return [];
        }
        if (is_string($condition)) {
            parse_str($condition, $condition);
        }
        
        // 返回的结果集合
        $resultSet = [];
        foreach ($list as $key => $data) {
            $find = false;
            foreach ($condition as $field => $value) {
                // 排除数组里不存在的搜索条件
                if (!isset($data[$field])) {
                    continue;
                }
                if (is_array($value)) {
                    switch ($value[0]) {
                        // 不等于
                        case 'neq':
                            $find = $data[$field] !== $value[1];
                            break;
                        // 等于
                        case 'eq':
                            $find = $data[$field] == $value[1];
                            break;
                        // 不包含
                        case 'not in':
                            $find = !in_array($data[$field], $value[1]);
                            break;
                        // 包含
                        case 'in':
                            $find = in_array($data[$field], $value[1]);
                            break;
                        // 大于
                        case 'gt':
                            $find = $data[$field] > $value[1];
                            break;
                        // 小于
                        case 'lt':
                            $find = $data[$field] < $value[1];
                            break;
                    }
                } elseif ($data[$field] == $value) {
                    $find = true;
                } else {
                    $find = false;
                }
                // 多条件查询时，只要有一个条件不满足，则标记为false，并跳过此次循环
                // todo 是否根据第三个参数$filter来判断查询条件是OR或AND
                if ($find == false) {
                    break;
                }
            }
            if ($find) {
                $resultSet[] = $list[$key];
            }
        }
        
        return $resultSet;
    }
    
    /**
     * 对查询结果集进行排序
     *
     * @param array $list 查询结果
     * @param string $field 排序的字段名
     * @param string $sortBy 排序类型
     * asc正向排序 desc逆向排序 nat自然排序
     *
     * @return array
     */
    public static function listSortBy($list, $field, $sortBy = 'asc'): array
    {
        if (is_array($list)) {
            $refer = $resultSet = [];
            
            foreach ($list as $i => $data) {
                $refer[$i] = &$data[$field];
            }
            
            switch ($sortBy) {
                // 正向排序
                case 'asc':
                    asort($refer);
                    break;
                // 逆向排序
                case 'desc':
                    arsort($refer);
                    break;
                // 自然排序
                case 'nat':
                    natcasesort($refer);
                    break;
            }
            foreach ($refer as $key => $val) {
                $resultSet[] = &$list[$key];
            }
            
            return $resultSet;
        }
        
        return [];
    }
    
}
