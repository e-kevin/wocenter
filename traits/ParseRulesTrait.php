<?php

namespace wocenter\traits;

/**
 * Class ParseRulesTrait
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
trait ParseRulesTrait
{
    
    /**
     * 解析验证规则
     *
     * @param string $rules 验证规则 e.g. required;string,max:30;string,length:1-3
     * @param string $field 需要验证的字段
     *
     * @return array e.g.
     * [
     *      [$field, 'required'],
     *      [$field, 'string', 'max' => 30],
     *      [$field, 'string', 'length' => [1,3]]
     * ]
     */
    protected function parseRulesToArray($rules, $field)
    {
        $value = [];
        if (!empty($rules)) {
            // 获取所有规则
            $array = preg_split('/[;\r\n]+/', trim($rules, ";\r\n"));
            if (!empty($array)) {
                foreach ($array as $key => $val) {
                    $value[$key][] = $field; // [$field]
                    $tmp = explode(',', $val);
                    $value[$key][] = $tmp[0]; // [$field, 'required']|[$field, 'string']
                    array_shift($tmp);  // $tmp = max:30|length:1-3
                    foreach ($tmp as $k => $v) {
                        if (strpos($v, ':')) {
                            $t = explode(':', $v);
                            if (strpos($t[1], '-')) {
                                $value[$key][$t[0]] = explode('-', $t[1]); // [$field, 'string', 'length' => [1,3]]
                            } else {
                                $value[$key][$t[0]] = intval($t[1]); // [$field, 'string', 'max' => 30]
                            }
                        }
                    }
                }
            }
        }
        
        return $value;
    }
    
}