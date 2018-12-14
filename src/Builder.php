<?php
/**
 *  ==================================================================
 *        文 件 名: Builder.php
 *        概    要: HTML构建器基类
 *        作    者: IT小强
 *        创建时间: 2018/11/12 18:38
 *        修改时间:
 *        copyright (c) 2016 - 2018 mail@xqitw.cn
 *  ==================================================================
 */

namespace itxq\builder;

use think\Exception;
use think\View;

/**
 * HTML构建器基类
 * Class Builder
 * @package itxq\builder
 */
class Builder
{
    /**
     * js挂载点名称
     * @var string
     */
    protected $jsHook = 'hook_js';
    
    /**
     * css挂载点名称
     * @var string
     */
    protected $cssHook = 'hook_css';
    
    /**
     * 构建类型 form | table
     * @var string
     */
    protected $type = '';
    
    /**
     * 视图类实例
     * @var \think\View
     */
    protected $view;
    
    /**
     * 视图相关配置
     * @var array
     */
    protected $viewConfig = [];
    
    /**
     * 根目录
     * @var string
     */
    protected $rootPath;
    
    /**
     * 模板根目录
     * @var bool|string
     */
    protected $templatePath;
    
    /**
     * 模板目录名
     * @var string
     */
    protected $template = 'default';
    
    /**
     * html字符串
     * @var string
     */
    protected $html = '';
    
    /**
     * 当前字符串
     * @var string
     */
    protected $curHtml = '';
    
    /**
     * @var array - 配置信息
     */
    protected $config = [];
    
    /**
     * Builder 构造函数.
     * @param array $config - 配置信息
     * [
     *
     *  // 通用
     *  'js_hook'  =>  'admin_js', // js挂载点名称
     *  'css_hook'  =>  'admin_css', // css挂载点名称
     *  'template_path'  => __DIR__.'/../template/', // 模板路径
     *  'template_name'  =>  'default', // 使用模板名
     *
     *  // 表单
     *  'data'  =>  [], // 表单默认数据
     *  'width'  =>  2, // 表单label宽度
     * ]
     * @throws Exception
     */
    public function __construct(array $config = []) {
        error_reporting(E_ALL & ~E_NOTICE);
        $this->template = get_sub_value('template_name', $config, 'default');
        $this->rootPath = get_sub_value('template_path', $config, realpath(__DIR__ . '/../template') . DIRECTORY_SEPARATOR);
        $this->type = get_sub_value('type', $config, '');
        if (isset($config['js_hook']) && !empty($config['js_hook'])) {
            $this->jsHook = $config['js_hook'];
        }
        if (isset($config['css_hook']) && !empty($config['css_hook'])) {
            $this->cssHook = $config['css_hook'];
        }
        if (empty($this->template)) {
            throw new Exception('未设置模板名称');
        }
        if (empty($this->rootPath)) {
            throw new Exception('未设置模板根目录');
        }
        $this->viewConfig = $this->getViewConfig();
        $this->view = new View();
        $this->view->init($this->viewConfig);
        $assign = [
            'css_hook' => $this->cssHook,
            'js_hook'  => $this->jsHook,
        ];
        $this->assign($assign);
    }
    
    /**
     * 获取模板完整路径
     * @param $template - 模板名
     * @return string - 完整路径
     */
    protected function getTemplateFilePath(string $template) {
        $path = $this->rootPath . $this->template . DIRECTORY_SEPARATOR;
        if (!is_dir($path)) {
            $path = $this->rootPath . 'default' . DIRECTORY_SEPARATOR;
        }
        if (!empty($this->type)) {
            $path .= $this->type . DIRECTORY_SEPARATOR;
        }
        $path .= $template . '.' . $this->viewConfig['view_suffix'];
        return $path;
    }
    
    /**
     * 添加额外的JS
     * @param $js - js代码
     * @return $this | FormBuilder | TableBuilder | Builder
     */
    public function addJs($js) {
        $this->hookAdd($this->jsHook, function () use ($js) {
            echo $js;
        });
        return $this;
    }
    
    /**
     * 添加额外的CSS
     * @param $css - css代码
     * @return $this | FormBuilder | TableBuilder | Builder
     */
    public function addCss($css) {
        $this->hookAdd($this->cssHook, function () use ($css) {
            echo $css;
        });
        return $this;
    }
    
    /**
     * 自动加载js、css资源
     * @param string $assetsName - 名称
     * @param string $type - 类型（css/js/all）
     * @param bool $isMin - 是否压缩
     * @param string $version - 版本号
     */
    public function autoloadAssets(string $assetsName, string $type = 'all', bool $isMin = true, string $version = ''): void {
        if ($type === 'js') {
            //  $js = autoload_assets($assetsName, 'js', $isMin, $version);
            //  $this->hookAdd($this->jsHook, function () use ($js) {
            //      echo $js;
            //  });
        } else if ($type === 'css') {
            // $css = autoload_assets($assetsName, 'css', $isMin, $version);
            // $this->hookAdd($this->cssHook, function () use ($css) {
            //     echo $css;
            // });
        } else {
            // $js = autoload_assets($assetsName, 'js', $isMin, $version);
            // $this->hookAdd($this->jsHook, function () use ($js) {
            //     echo $js;
            // });
            // $css = autoload_assets($assetsName, 'css', $isMin, $version);
            // $this->hookAdd($this->cssHook, function () use ($css) {
            //     echo $css;
            // });
        }
    }
    
    /**
     * 字符串、数组转换为格式化的数组
     * @param string|array|mixed $list - 原始字符串，可以为数组或者json、字符串、序列化字符串
     * @param bool $isJsonType - 是否转换为json键值对的形式
     * @return array
     */
    public function unSerialize($list, bool $isJsonType = false) {
        // 对普通字符串进行简析
        if (is_string($list)) {
            if (preg_match('/^\{.*?\}$/', $list) || preg_match('/^\[.*?\]$/', $list)) {
                $list = json_decode($list, true);
            } else if (preg_match('/^a:.*?(})$/', $list)) {
                $list = unserialize($list);
            } else if (preg_match('/^\<\{\:serialize.*?\}\>$/', $list) || preg_match('/^\<\{.*?\}\>$/', $list)) {
                $list = $this->unSerialize($this->display($list));
            } else {
                $list = explode(',', $list);
            }
        }
        if (!is_array($list) || count($list) < 1) {
            return [];
        }
        if (!isset($list['json_key']) || !isset($list['json_value'])) {
            return $list;
        }
        $returnList = [];
        foreach ($list['json_value'] as $k => $v) {
            if (empty($list['json_key'][$k]) && empty($v)) {
                continue;
            }
            if ($isJsonType === true) {
                $returnList[] = ['key' => $list['json_key'][$k], 'value' => $v];
            } else {
                $returnList[$list['json_key'][$k]] = $v;
            }
        }
        if (count($returnList) === 1 && isset($returnList[0]) && $returnList[0] === '') {
            return [];
        }
        return $returnList;
    }
    
    /**
     * 返回当前字符串
     * @return string
     */
    public function getCurHtml() {
        return $this->curHtml;
    }
    
    /**
     * 返回全部HTML字符串
     * @return string
     */
    public function returnHtml() {
        return $this->html;
    }
    
    /**
     * 动态添加行为扩展到某个标签
     * @param  string|array $name 事件别名
     * @param  mixed $event 事件名称
     * @return void
     */
    protected function eventBind($name, $event = null) {
        \think\facade\Event::bind($name, $event);
    }
    
    /**
     * 布尔值获取
     * @param string|int|bool $val - 原始变量
     * @return bool 布尔值
     */
    protected function getBoolVal($val) {
        if ($val === '1' || $val === true || $val === 1 || $val === 'true') {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 创建ID
     * @param string $name - 名称
     * @param bool $isRound - 是否添加随机数
     * @return mixed
     */
    protected function createId(string $name, bool $isRound = true) {
        $prefix = 'builder_auto_create_';
        $search = ['[', ']'];
        $replace = ['_', '_'];
        $name = $prefix . str_replace($search, $replace, $name);
        return $isRound ? ($name . cm_round(4, 'all')) : $name;
    }
    
    /**
     * 渲染内容输出
     * @param $data
     * @return mixed
     */
    protected function display(string $data) {
        $data = htmlspecialchars_decode($data);
        if (preg_match('/^\<\{\:serialize.*?\}\>$/', $data)) {
            $data = unserialize($this->view->display($data));
        } else if (preg_match('/^\<\{\:json_encode.*?\}\>$/', $data)) {
            $data = json_decode($this->view->display($data), true);
        } else if (preg_match('/^\<\{.*?\}\>$/', $data)) {
            $data = $this->view->display($data);
        }
        return $data;
    }
    
    /**
     * 解析和获取模板内容 用于输出
     * @param  string $template 模板文件名或者内容
     * @param  array $vars 模板输出变量
     * @return string
     * @throws Exception
     * @throws \Exception
     */
    protected function fetch(string $template = '', array $vars = []) {
        $template = $this->getTemplateFilePath($template);
        if (!is_file($template)) {
            throw new Exception('模板文件{' . $template . '}不存在');
        }
        return $this->view->fetch($template, $vars, [], false);
    }
    
    /**
     * 模板变量赋值
     * @param  array $vars 要显示的模板变量
     * @return $this
     */
    protected function assign(array $vars) {
        $this->view->assign($vars);
        return $this;
    }
    
    /**
     * 加载模板配置
     * @return array
     */
    protected function getViewConfig() {
        $path = $this->rootPath . $this->template . DIRECTORY_SEPARATOR;
        if (!is_dir($path)) {
            $path = $this->rootPath . 'default' . DIRECTORY_SEPARATOR;
        }
        return include realpath($path . 'template.php');
    }
}