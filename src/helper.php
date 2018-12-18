<?php

// +----------------------------------------------------------------------
// | 助手函数
// +----------------------------------------------------------------------

if (!function_exists('get_sub_value')) {
    /**
     * 获取数组、对象下标对应值，不存在时返回指定的默认值
     * @param string|integer $name - 下标（键名）
     * @param array|object $data - 原始数组/对象
     * @param mixed $default - 指定默认值
     * @return mixed
     */
    function get_sub_value(string $name, $data, $default = '') {
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
     * @param array|null $data - 表单默认数据
     * @param int $colWidth - 表单label宽度
     * @param string $template - 使用模板名
     * @param array $config - 更多配置项
     * @return \itxq\builder\FormBuilder
     * @throws \think\Exception
     */
    function create_form($data = null, int $colWidth = 0, string $template = 'default', array $config = []) {
        $config['data'] = $data;
        $config['width'] = $colWidth;
        $config['template_name'] = $template;
        $formBuilder = new \itxq\builder\FormBuilder($config);
        return $formBuilder;
    }
}

if (!function_exists('builder_event_listen')) {
    /**
     * 动态添加行为扩展到某个标签
     * @param  string $event 事件名称
     * @param  mixed $listener 监听操作（或者类名）
     * @param  bool $first 是否优先执行
     * @return void
     */
    function builder_event_listen(string $event, $listener, bool $first = false): void {
        \think\facade\Event::listen($event, $listener, $first);
    }
}

if (!function_exists('builder_event_trigger')) {
    /**
     * 触发事件
     * @param  string|object $event 事件名称
     * @param  mixed $params 传入参数
     * @param  bool $once 只获取一个有效返回值
     * @return mixed
     */
    function builder_event_trigger($event, $params = null, bool $once = false) {
        $event = \think\facade\Event::trigger($event, $params, $once);
        $html = '';
        if (is_array($event)) {
            foreach ($event as $v) {
                $html .= $v;
            }
        } else {
            $html = strval($event);
        }
        return $html;
    }
}