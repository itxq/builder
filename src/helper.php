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
    function create_form($data = null, int $colWidth = 2, string $template = 'default', array $config = []) {
        $config['data'] = $data;
        $config['width'] = $colWidth;
        $config['template'] = $template;
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