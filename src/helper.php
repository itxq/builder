<?php
/**
 *  ==================================================================
 *        文 件 名: helper.php
 *        概    要: 助手函数
 *        作    者: IT小强
 *        创建时间: 2018-11-13 13:14
 *        修改时间:
 *        copyright (c) 2016 - 2018 mail@xqitw.cn
 *  ==================================================================
 */

if (!function_exists('config')) {
    /**
     * 获取和设置配置参数
     * @param string|array $name 参数名
     * @param mixed $value 参数值
     * @return mixed
     */
    function config($name = '', $value = null) {
        if (is_null($value) && is_string($name)) {
            if ('.' == substr($name, -1)) {
                return \think\facade\Config::pull(substr($name, 0, -1));
            }
            
            return 0 === strpos($name, '?') ? \think\facade\Config::has(substr($name, 1)) : \think\facade\Config::get($name);
        }
        
        return \think\facade\Config::set($name, $value);
    }
}