<?php

// +----------------------------------------------------------------------
// | 助手函数
// +----------------------------------------------------------------------
if (!function_exists('get_think_view')) {
    /**
     * 实例化View
     * @param array $config 配置
     * @return \think\View
     */
    function get_think_view($config): \think\View
    {
        $view = new \think\View();
        $view->init($config);
        return $view;
    }
}

if (!function_exists('builder_event_trigger')) {
    /**
     * 触发事件
     * @param string|object $event 事件名称
     * @return string
     */
    function builder_event_trigger($event): string
    {
        $event = \think\facade\Hook::listen($event);
        $html = '';
        if (is_array($event)) {
            foreach ($event as $v) {
                $html .= $v;
            }
        } else {
            $html = (string)$event;
        }
        return $html;
    }
}

if (!function_exists('builder_event_listen')) {
    /**
     * 动态添加行为扩展到某个标签
     * @param string $event 事件名称
     * @param mixed $listener 监听操作（或者类名）
     * @return void
     */
    function builder_event_listen(string $event, $listener): void
    {
        \think\facade\Hook::add($event, $listener, false);
    }
}

if (!function_exists('get_sub_value')) {
    /**
     * 获取数组、对象下标对应值，不存在时返回指定的默认值
     * @param string|integer $name - 下标（键名）
     * @param array|object $data - 原始数组/对象
     * @param mixed $default - 指定默认值
     * @return mixed
     */
    function get_sub_value(string $name, $data, $default = '')
    {
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

if (!function_exists('create_form')) {
    
    /**
     * 创建表单
     * @param array $data - 表单默认数据
     * @param int $width - 表单label宽度
     * @param string $template - 使用模板名
     * @param array $config - 更多配置项
     * @return \itxq\builder\Form
     */
    function create_form(array $data = [], int $width = 0, string $template = 'default', array $config = [])
    {
        $config['data'] = $data;
        $config['width'] = $width;
        $formBuilder = new \itxq\builder\Form($config);
        $formBuilder->setTemplateName($template);
        return $formBuilder;
    }
}
