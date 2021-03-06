<?php
/**
 *  ==================================================================
 *        文 件 名: Tools.php
 *        概    要: 基础工具类
 *        作    者: IT小强
 *        创建时间: 2018-12-25 20:28:50
 *        修改时间:
 *        copyright (c) 2016 - 2018 mail@xqitw.cn
 *  ==================================================================
 */

namespace itxq\tools;

/**
 * 基础工具类
 * Class Tools
 * @package itxq\tools
 */
class Tools
{
    /**
     * 下划线命名转驼峰命名
     * @param string $str - 下划线命名字符串
     * @param bool $isFirst - 是否为大驼峰（即首字母也大写）
     * @return mixed
     */
    public static function underlineToHump(string $str, bool $isFirst = false): string {
        $str = preg_replace_callback('/([\-\_]+([a-z]{1}))/i', function ($matches) {
            return strtoupper($matches[2]);
        }, $str);
        return $isFirst ? ucfirst($str) : $str;
    }
    
    /**
     * 驼峰命名转下划线命名
     * @param $str
     * @return mixed
     */
    public static function humpToUnderline(string $str): string {
        $str = preg_replace_callback('/([A-Z]{1})/', function ($matches) {
            return '_' . strtolower($matches[0]);
        }, $str);
        return preg_replace('/^\_/', '', $str);
    }
    
    /**
     * 获取数组、对象下标对应值，不存在时返回指定的默认值
     * @param string|integer $name - 下标（键名）
     * @param array|object $data - 原始数组/对象
     * @param mixed $default - 指定默认值
     * @return mixed
     */
    public static function getSubValue(string $name, $data, $default = '') {
        if (is_object($data)) {
            $value = isset($data->$name) ? $data->$name : $default;
        } else if (is_array($data)) {
            $value = isset($data[$name]) ? $data[$name] : $default;
        } else {
            $value = $default;
        }
        return $value;
    }
}