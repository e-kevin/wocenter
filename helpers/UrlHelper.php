<?php
namespace wocenter\helpers;

/**
 * Url地址助手类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class UrlHelper
{

    /**
     * 将URL中的参数取出来放到数组里
     *
     * @param string $query
     *
     * @return array $params
     */
    public static function convertUrlQuery($query)
    {
        if (empty($query)) {
            return [];
        }
        $queryParts = explode('&', $query);
        $params = [];
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            // FIXED:2016-07-13 解析数组型参数
            $pos = strpos($item[0], '[');
            if ($pos) {
                $params[substr($item[0], 0, $pos)][substr($item[0], $pos + 1, -1)] = $item[1];
            } else {
                $params[$item[0]] = $item[1];
            }
        }

        return $params;
    }

    /**
     * 将 参数数组 变回 字符串形式的参数格式
     *
     * @param array $array_query
     *
     * @return string
     */
    public static function getUrlQuery(array $array_query)
    {
        $tmp = [];
        foreach ($array_query as $name => $value) {
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    $tmp[] = "{$name}[{$k}]={$v}";
                }
            } else {
                $tmp[] = "{$name}={$value}";
            }
        }

        return implode('&', $tmp);
    }

    /**
     * 删除url地址里指定的参数
     *
     * @param string $url
     * @param string $params 待删除参数名，不支持删除数组参数
     * @param boolean $format 是否返回格式化后干净的url地址，默认格式化
     *
     * @return string|array 干净的url或包含path、query等信息的数组
     */
    public static function unsetParams($url = '', $params = '', $format = true)
    {
        if (strpos($url, '?') === false) {
            return $url;
        }
        $parseUrl = static::parseUrl($url);
        if (isset($parseUrl['query'])) {
            foreach (explode(',', $params) as $param) {
                if (isset($parseUrl['query'][$param])) {
                    unset($parseUrl['query'][$param]);
                }
            }
            if ($format) {
                // 删除参数后，存在其他参数则重新组装URL，否则参数为空
                $parseUrl['query'] = !empty($parseUrl['query']) ? ('?' . static::getUrlQuery($parseUrl['query'])) : '';
            }
        } else {
            $parseUrl['query'] = '';
        }

        return $format ? ($parseUrl['path'] . $parseUrl['query']) : $parseUrl;
    }

    /**
     * 解析url地址，返回的query请求参数为数组格式
     *
     * @param string $url
     *
     * @return array ['path', 'query']
     */
    public static function parseUrl($url)
    {
        $parseUrl = parse_url($url);
        if (isset($parseUrl['query'])) {
            // 修复Pjax参数。该参数带#符号，导致该参数后的其他参数被标识为fragment
            if (strpos($parseUrl['query'], '_pjax') !== false && isset($parseUrl['fragment'])) {
                $parseUrl['query'] .= $parseUrl['fragment'];
                unset($parseUrl['fragment']);
            }
            $parseUrl['query'] = self::convertUrlQuery($parseUrl['query']);
        }

        return $parseUrl;
    }

}
