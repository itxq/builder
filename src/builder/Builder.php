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
use think\exception\HttpException;
use think\facade\Config;
use think\facade\Request;
use think\View;

/**
 * HTML构建器基类
 * Class Builder
 * @package itxq\builder
 */
abstract class Builder
{
    /**
     * js挂载点名称
     * @var string
     */
    protected $jsHook;
    
    /**
     * css挂载点名称
     * @var string
     */
    protected $cssHook;
    
    /**
     * 构建类型 form | table
     * @var string
     */
    protected $type;
    
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
     * 静态资源默认目录
     * @var string
     */
    protected $assetPath;
    
    /**
     * 模板根目录
     * @var bool|string
     */
    protected $templatePath;
    
    /**
     * 模板目录名
     * @var string
     */
    protected $template;
    
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
     */
    public function __construct(array $config = []) {
        $this->template = get_sub_value('template_name', $config, 'default');
        $this->rootPath = realpath(get_sub_value('template_path', $config, __DIR__ . '/../../template')) . DIRECTORY_SEPARATOR;
        $this->assetPath = realpath(get_sub_value('assets_path', $config, __DIR__ . '/../../assets')) . DIRECTORY_SEPARATOR;
        $this->type = get_sub_value('type', $config, '');
        $this->jsHook = get_sub_value('js_hook', $config, 'hook_js');
        $this->cssHook = get_sub_value('css_hook', $config, 'hook_css');
        if (get_sub_value('jquery', $config, false)) {
            $this->autoloadAssets('jquery', 'js');
        }
        if (get_sub_value('bootstrap', $config, false)) {
            $this->autoloadAssets('bootstrap', 'all');
        }
        $this->getAssets();
        $this->viewInit();
        $assign = ['css_hook' => $this->cssHook, 'js_hook' => $this->jsHook];
        $this->assign($assign);
    }
    
    /**
     * 设置模板名称
     * @param $template
     * @return $this
     */
    public function setTemplateName($template) {
        $this->template = $template;
        return $this;
    }
    
    /**
     * 添加额外的JS
     * @param $js - js代码
     * @return $this | Form | Table | Builder
     */
    public function addJs($js) {
        builder_event_listen($this->jsHook, function () use ($js) {
            return $js;
        });
        return $this;
    }
    
    /**
     * 添加额外的CSS
     * @param $css - css代码
     * @return $this | Form | Table | Builder
     */
    public function addCss($css) {
        builder_event_listen($this->cssHook, function () use ($css) {
            return $css;
        });
        return $this;
    }
    
    /**
     * 自动加载js、css资源
     * @param string $assetsName - 名称
     * @param string $type - 类型（css/js/all）
     */
    protected function autoloadAssets(string $assetsName, string $type = 'all'): void {
        $config = $this->getViewConfig('assets');
        if ($type === 'js') {
            $js = $this->handleAssets($assetsName, $config, 'js');
            builder_event_listen($this->jsHook, function () use ($js) {
                return $js;
            });
        } else if ($type === 'css') {
            $css = $this->handleAssets($assetsName, $config, 'css');
            builder_event_listen($this->cssHook, function () use ($css) {
                return $css;
            });
        } else {
            $js = $this->handleAssets($assetsName, $config, 'js');
            builder_event_listen($this->jsHook, function () use ($js) {
                return $js;
            });
            $css = $this->handleAssets($assetsName, $config, 'css');
            builder_event_listen($this->cssHook, function () use ($css) {
                return $css;
            });
        }
    }
    
    /**
     * 字符串、数组转换为格式化的数组
     * @param string|array|mixed $list - 原始字符串，可以为数组或者json、字符串、序列化字符串
     * @param bool $isJsonType - 是否转换为json键值对的形式
     * @return array
     */
    public function unSerialize($list, bool $isJsonType = false): array {
        // 对普通字符串进行简析
        if (is_string($list)) {
            $list = $this->tplReplaceString($list);
            if (empty($list)) {
                $list = [];
            } else if (preg_match('/^{{.*?}}$/', $list)) {
                $list = $this->unSerialize($this->display($list));
            } else if (preg_match('/^{.*?}$/', $list) || preg_match('/^[.*?]$/', $list)) {
                $list = json_decode($list, true);
            } else if (preg_match('/^a:.*?(})$/', $list)) {
                $list = unserialize($list);
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
    public function getCurHtml(): string {
        return $this->curHtml;
    }
    
    /**
     * 返回全部HTML字符串
     * @return string
     */
    public function returnHtml(): string {
        return $this->html;
    }
    
    /**
     * 布尔值获取
     * @param string|int|bool $val - 原始变量
     * @return bool 布尔值
     */
    protected function getBoolVal($val): bool {
        if ($val === '1' || $val === true || $val === 1 || $val === 'true') {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 创建ID
     * @param string $name - 名称
     * @param bool|string $isRound - 是否添加随机数(true添加，为字符串时表示前缀)
     * @return string
     */
    protected function createId(string $name, $isRound = true): string {
        $prefix = is_string($isRound) ? $isRound : 'builder_auto_create_';
        $search = ['[', ']'];
        $replace = ['_', '_'];
        $name = $prefix . str_replace($search, $replace, $name);
        return $isRound === true ? ($name . $this->cmRound(4, 'all')) : $name;
    }
    
    /**
     * 渲染内容输出
     * @param $data
     * @return string
     */
    protected function display(string $data): string {
        $data = htmlspecialchars_decode($data);
        $data = $this->view->display($data);
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
    protected function fetch(string $template = '', array $vars = []): string {
        $template = $this->getTemplateFilePath($template);
        if (!is_file($template)) {
            throw new Exception('模板文件{' . $template . '}不存在');
        }
        return $this->view->fetch($template, $vars, [], false);
    }
    
    /**
     * 模板变量赋值
     * @param  array $vars 要显示的模板变量
     * @return void
     */
    protected function assign(array $vars): void {
        $this->view->assign($vars);
    }
    
    /**
     * 加载模板配置
     * @param string $name - 文件名
     * @return array
     */
    protected function getViewConfig(string $name = 'template'): array {
        $path = $this->rootPath . $this->template . DIRECTORY_SEPARATOR;
        if (!is_dir($path)) {
            $path = $this->rootPath . 'default' . DIRECTORY_SEPARATOR;
        }
        $file = realpath($path . $name . '.php');
        if (!is_file($file)) {
            return [];
        }
        $config = include $file;
        return is_array($config) ? $config : [];
    }
    
    /**
     * 拼装css & js 资源
     * @param string $assetsName
     * @param array $config
     * @param string $type
     * @return string
     */
    protected function handleAssets(string $assetsName, array $config, string $type): string {
        $assetsHtml = '';
        $defineName = 'AUTOLOAD_ASSETS_' . strtoupper($assetsName) . '_' . strtoupper($type) . '_0';
        if (defined($defineName)) {
            return $assetsHtml;
        }
        define($defineName, true);
        $assets = get_sub_value($assetsName . '.' . $type, $config, []);
        if ($type === 'js') {
            foreach ($assets as $v) {
                $assetsHtml .= '<script type="text/javascript" src="' . $this->getTrueUrl($v) . '"></script>';
            }
        } else if ($type === 'css') {
            foreach ($assets as $v) {
                $assetsHtml .= '<link rel="stylesheet" type="text/css" href="' . $this->getTrueUrl($v) . '">';
            }
        }
        return $assetsHtml;
    }
    
    /**
     * 获取完整的URL路径
     * @param string $url
     * @return string
     */
    protected function getTrueUrl(string $url): string {
        $url = $this->tplReplaceString($url);
        if (empty($url)) {
            $trueUrl = '';
        } else if (strpos($url, '//') === 0) {
            $trueUrl = Request::scheme() . ':' . $url;
        } else if (strpos($url, '/') === 0) {
            $trueUrl = Request::domain() . $url;
        } else if (strpos($url, '__builder_assets__/') === 0) {
            $base = Request::scheme() . '://' . Request::server('HTTP_HOST') . Request::server('PHP_SELF');
            $trueUrl = $base . '?action=get_assets_common&create_builder_url=' . $url;
        } else if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
            $trueUrl = $url;
        } else {
            $base = Request::scheme() . '://' . Request::server('HTTP_HOST') . Request::server('PHP_SELF');
            $trueUrl = $base . '?action=get_assets&create_builder_url=' . $url;
        }
        return $trueUrl;
    }
    
    /**
     * 输出资源文件
     */
    protected function getAssets() {
        $action = strip_tags(strval(Request::get('action', '')));
        $url = strip_tags(strval(Request::get('create_builder_url', '')));
        $allowAction = ['get_assets', 'get_assets_common'];
        if (empty($action) || empty($url) || !in_array($action, $allowAction)) {
            return false;
        }
        $url = strtolower($url);
        $path = $this->rootPath;
        if ($action === 'get_assets_common') {
            $url = str_replace('__builder_assets__/', '', $url);
            $path = $this->assetPath;
        }
        $ext = pathinfo($url, PATHINFO_EXTENSION);
        if ($ext === 'css') {
            $type = 'text/css';
        } else if ($ext === 'js') {
            $type = 'text/javascript';
        } else if ($ext === 'png') {
            $type = 'image/png';
        } else if ($ext === 'gif') {
            $type = 'image/gif';
        } else if ($ext === 'jpg' || $ext === 'jpeg') {
            $type = 'image/jpeg';
        } else {
            $type = 'text/html';
        }
        header('Content-type:' . $type);
        $path = realpath($path . $url);
        if (!is_file($path)) {
            throw new HttpException(404, '资源文件不存在');
        }
        exit(file_get_contents($path));
    }
    
    /**
     * 生成随机字符串
     * @param int $length - 指定生成字符串的长度
     * @param string $type - 指定生成字符串的类型（all-全部，num-纯数字，letter-纯字母）
     * @return string
     */
    protected function cmRound(int $length = 4, string $type = 'all'): string {
        $str = '';
        $strUp = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $strLow = 'abcdefghijklmnopqrstuvwxyz';
        $number = '0123456789';
        switch ($type) {
            case 'num':
                $strPol = $number;
                break;
            case 'letter':
                $strPol = $strUp . $strLow;
                break;
            default:
                $strPol = $strUp . $number . $strLow;
        }
        $max = strlen($strPol) - 1;
        for ($i = 0; $i < $length; $i++) {
            $str .= $strPol[mt_rand(0, $max)];
        }
        return $str;
    }
    
    /**
     * 模板字符串替换
     * @param string $string - 原始字符串
     * @return string - 模板变量替换之后的字符串
     */
    protected function tplReplaceString(string $string): string {
        $tplReplaceString = array_merge(get_sub_value('tpl_replace_string', $this->viewConfig, []), (array)Config::get('template.tpl_replace_string'));
        $string = str_replace(array_keys($tplReplaceString), array_values($tplReplaceString), $string);
        return $string;
    }
    
    /**
     * 获取模板完整路径
     * @param $template - 模板名
     * @return string - 完整路径
     */
    protected function getTemplateFilePath(string $template): string {
        $path = $this->rootPath . $this->template . DIRECTORY_SEPARATOR;
        if (!is_dir($path)) {
            $path = $this->rootPath . 'default' . DIRECTORY_SEPARATOR;
        }
        if (!is_dir($path)) {
            $path = realpath(__DIR__ . '/../../template/default') . DIRECTORY_SEPARATOR;
        }
        if (!empty($this->type)) {
            $path .= $this->type . DIRECTORY_SEPARATOR;
        }
        $path .= $template . '.' . $this->viewConfig['view_suffix'];
        return $path;
    }
    
    /**
     * 初始化模板引擎
     * @return void
     */
    protected function viewInit(): void {
        $this->viewConfig = $this->getViewConfig('template');
        // 模板引擎普通标签开始标记
        $this->viewConfig['tpl_begin'] = '{{';
        // 模板引擎普通标签结束标记
        $this->viewConfig['tpl_end'] = '}}';
        // 标签库标签开始标记
        $this->viewConfig['taglib_begin'] = '{{';
        // 标签库标签结束标记
        $this->viewConfig['taglib_end'] = '}}';
        $this->view = new View();
        $this->view->init($this->viewConfig);
    }
}