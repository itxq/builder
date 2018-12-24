<?php
/**
 *  ==================================================================
 *        文 件 名: FormBuilder.php
 *        概    要: 表单构建器
 *        作    者: IT小强
 *        创建时间: 2018/11/13 12:50
 *        修改时间:
 *        copyright (c) 2016 - 2018 mail@xqitw.cn
 *  ==================================================================
 */

namespace itxq\builder;

use itxq\tools\FormSubmit;
use think\Exception;
use think\facade\Request;

/**
 * 表单构建器
 * Class FormBuilder
 * @package itxq\builder
 */
class FormBuilder extends Builder
{
    const id = 'id';
    const value = 'value';
    const name = 'name';
    const title = 'title';
    const lists = 'list';
    const placeholder = 'placeholder';
    const tip = 'tip';
    const width = 'width';
    const class_list = 'class';
    const validate = 'validate';
    const type = 'type';
    const disabled = 'disabled';
    const readonly = 'readonly';
    const multiple = 'multiple';
    // ----------------------------------------------------------------
    const form_map = 'map';
    const form_text = 'text';
    const form_file = 'file';
    const form_texts = 'texts';
    const form_password = 'password';
    const form_color = 'color';
    const form_tags = 'tags';
    const form_ico = 'ico';
    const form_radio = 'radio';
    const form_checkbox = 'checkbox';
    const form_select = 'select';
    const form_switch = 'switch';
    const form_hidden = 'hidden';
    const form_hr = 'hr';
    const form_date_range = 'date_range';
    const form_json = 'json';
    const form_text_area = 'text_area';
    const form_text_areas = 'text_areas';
    /**
     * @var string - 多项表单数据
     */
    protected $itemsHtml = '';
    
    /**
     * 表单默认数据
     * @var array|null
     */
    protected $formData = [];
    
    /**
     * 表单配置项
     * @var array
     */
    protected $formConfig = [];
    
    /**
     * 表单验证项
     * @var array
     */
    protected $validateConfig = [];
    
    /**
     * @var string - 表单按钮
     */
    protected $footerBtn = '';
    
    /**
     * FormBuilder 构造函数.
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
        $config['type'] = 'form';
        parent::__construct($config);
        $this->formData = get_sub_value('data', $config, []);
        $this->formConfig['width'] = intval(get_sub_value('width', $config, 2));
        if ($this->formConfig['width'] > 12 || $this->formConfig['width'] <= 0) {
            $this->formConfig['width'] = 12;
        }
        $this->autoloadAssets('form', 'all');
    }
    
    /**
     * 创建表单
     * @param string $url - 表单提交地址
     * @param string $formID - 表单ID
     * @param string $class - css类
     * @throws Exception
     * @return FormBuilder
     */
    public function start(string $url, string $formID = 'validate-form', string $class = 'form-horizontal') {
        $this->formConfig['form_id'] = $formID;
        $this->formConfig['method'] = 'post';
        $assign = [
            'action'  => $url,
            'method'  => $this->formConfig['method'],
            'form_id' => $this->formConfig['form_id'],
            'class'   => $class
        ];
        $this->assign($assign);
        $this->curHtml = $this->fetch('start');
        $this->html = $this->curHtml;
        return $this;
    }
    
    /**
     * 创建表单结尾
     * @param string $redirectUrl -提交成功后的跳转地址（为空不跳转）
     * @throws Exception
     * @return FormBuilder
     */
    public function end(string $redirectUrl = '') {
        // 表单结尾（添加提交/重置按钮等）
        $html = htmlspecialchars_decode($this->fetch('end'));
        $this->curHtml = $html;
        $this->html .= $html;
        $this->addBootstrapValidator($redirectUrl);
        if (Request::isPost()) {
            FormSubmit::submit($this);
            exit();
        }
        return $this;
    }
    
    /**
     * 添加单行文本框
     * @param string|array $name - 表单元素name属性名
     * @param string $title - 标题
     * @param array $config - 更多配置
     * @throws Exception
     * @return FormBuilder
     */
    public function addText($name, string $title = '', array $config = []) {
        $config = $this->iniFormItemConfig($name, $title, $config);
        $config[self::value] = addslashes($this->getFormData($config[self::name], $config[self::value]));
        $config[self::type] = self::form_text;
        $this->assign($config);
        $content = $this->fetch('text');
        $this->curHtml = $this->addItem($content, $config);
        return $this;
    }
    
    /**
     * 添加HR分割线
     * @param string|array $name - 表单元素name属性名
     * @return FormBuilder
     */
    public function addHr($name = '') {
        $this->curHtml = '<hr style="height: 0;width: 100%;margin: 0;padding: 0;color: transparent;border: 0;">';
        $this->html .= $this->curHtml;
        return $this;
    }
    
    /**
     * 添加表单验证令牌
     * @param string|array $name - 令牌名称
     * @param mixed $tokenType - 令牌生成方法
     * @param array $config - 更多配置
     * @return FormBuilder
     */
    public function addToken($name = '__token__', $tokenType = 'md5', array $config = []) {
        $config = $this->iniFormItemConfig($name, $tokenType, $config);
        $this->curHtml = Request::token($config[self::name], $config[self::title]);
        $this->html .= $this->curHtml;
        return $this;
    }
    
    /**
     * 添加字体图标选择器
     * @param string|array $name - 表单元素name属性名
     * @param string $title - 标题
     * @param array $config - 更多配置
     * @throws Exception
     * @return FormBuilder
     */
    public function addIco($name, string $title = '', array $config = []) {
        $config = $this->iniFormItemConfig($name, $title, $config);
        $config[self::lists] = $this->unSerialize($config[self::lists], false);
        $config[self::value] = addslashes($this->getFormData($config[self::name], $config[self::value]));
        $config[self::type] = self::form_ico;
        $defaultIcoFile = [];
        $icoFile = array_merge($defaultIcoFile, $config[self::lists]);
        $list = [];
        foreach ($icoFile as $k => $v) {
            $_url = $this->getTrueUrl($v);
            $_pre = preg_match('/.*?\s([a-zA-Z0-9\-\_]+)$/', $k, $pre) ? $pre[1] : false;
            if ($_pre == false) {
                continue;
            }
            $_preg = "/\." . $_pre . "([a-zA-Z0-9\-\_]+)\:before/";
            $_font = preg_match_all($_preg, @file_get_contents($_url), $icoList) ? $icoList[1] : [];
            if (!is_array($_font) || count($_font) < 1) {
                continue;
            }
            $list[] = ['pre' => $k, 'ico' => $_font];
            $this->addCss('<link rel="stylesheet" href="' . $_url . '">');
        }
        $config[self::lists] = $list;
        $this->assign($config);
        $content = $this->fetch('ico');
        $this->curHtml = $this->addItem($content, $config);
        return $this;
    }
    
    /**
     * 添加单文件上传控件
     * @param string|array $name - 表单元素name属性名
     * @param string $title - 标题
     * @param array $config - 更多配置
     * @throws Exception
     * @return FormBuilder
     */
    public function addFile($name, string $title = '', array $config = []) {
        $this->autoloadAssets('file', 'all');
        $config = $this->iniFormItemConfig($name, $title, $config);
        $config[self::value] = addslashes($this->getFormData($config[self::name], $config[self::value]));
        $config[self::type] = self::form_file;
        $config['url'] = $this->getTrueUrl(get_sub_value('url', $config, ''));
        $config['img_ext'] = ['jpg', 'png', 'gif', 'bmp', 'jpeg', 'ico'];
        $this->assign($config);
        $content = $this->fetch('file');
        $this->curHtml = $this->addItem($content, $config);
        return $this;
    }
    
    /**
     * 添加hidden隐藏域
     * @param string|array $name - 表单元素name属性名
     * @param string $title - 标题
     * @param array $config - 更多配置
     * @throws Exception
     * @return FormBuilder
     */
    public function addHidden($name, string $title = '', array $config = []) {
        $config = $this->iniFormItemConfig($name, $title, $config);
        $config[self::value] = addslashes($this->getFormData($config[self::name], $config[self::value]));
        $this->assign($config);
        $content = $this->fetch('hidden') . '<input type="hidden" name="build_form_type[' . $config[self::name] . ']" value="' . self::form_hidden . '">';
        $this->curHtml = $content;
        $this->html .= $content;
        return $this;
    }
    
    /**
     * 添加密码框
     * @param string|array $name - 表单元素name属性名
     * @param string $title - 标题
     * @param array $config - 更多配置
     * @throws Exception
     * @return FormBuilder
     */
    public function addPassword($name, string $title = '', array $config = []) {
        $config = $this->iniFormItemConfig($name, $title, $config);
        $config[self::value] = addslashes($this->getFormData($config[self::name], $config[self::value]));
        $config[self::type] = self::form_password;
        $this->assign($config);
        $content = $this->fetch('password');
        $this->curHtml = $this->addItem($content, $config);
        return $this;
    }
    
    /**
     * 添加时间范围选择框
     * @param string|array $name - 表单元素name属性名
     * @param string $title - 标题
     * @param array $config - 更多配置
     * @throws Exception
     * @return FormBuilder
     */
    public function addDateRange($name, string $title = '', array $config = []) {
        $config = $this->iniFormItemConfig($name, $title, $config);
        $config[self::value] = addslashes($this->getFormData($config[self::name], $config[self::value]));
        $config[self::type] = self::form_date_range;
        $this->autoloadAssets('daterangepicker', 'all');
        $time = explode('~', $config[self::value]);
        $config['sTime'] = get_sub_value(0, $time);
        $config['eTime'] = get_sub_value(1, $time);
        $this->assign($config);
        $content = $this->fetch('date-range');
        $this->curHtml = $this->addItem($content, $config);
        return $this;
    }
    
    /**
     * 添加JSON表单项
     * @param string|array $name - 表单元素name属性名
     * @param string $title - 标题
     * @param array $config - 更多配置
     * @return FormBuilder
     * @throws Exception
     */
    public function addJson($name, string $title = '', array $config = []) {
        $config = $this->iniFormItemConfig($name, $title, $config);
        $config[self::value] = $this->unSerialize($this->getFormData($config[self::name], $config[self::value]), false);
        $config[self::type] = self::form_json;
        $config['keyName'] = get_sub_value('key_name', $config, '键');
        $config['valueName'] = get_sub_value('value_name', $config, '值');
        $this->assign($config);
        $content = $this->fetch('json');
        $this->curHtml = $this->addItem($content, $config);
        return $this;
    }
    
    /**
     * 添加标签输入框
     * @param string|array $name - 表单元素name属性名
     * @param string $title - 标题
     * @param array $config - 更多配置
     * @throws Exception
     * @return FormBuilder
     */
    public function addTags($name, string $title = '', array $config = []) {
        $this->autoloadAssets('tags', 'all');
        $config = $this->iniFormItemConfig($name, $title, $config);
        $config[self::value] = addslashes($this->getFormData($config[self::name], $config[self::value]));
        $config[self::type] = self::form_tags;
        $this->assign($config);
        $content = $this->fetch('tags');
        $this->curHtml = $this->addItem($content, $config);
        return $this;
    }
    
    /**
     * 添加颜色选择器
     * @param string|array $name - 表单元素name属性名
     * @param string $title - 标题
     * @param array $config - 更多配置
     * @throws Exception
     * @return FormBuilder
     */
    public function addColor($name, string $title = '', array $config = []) {
        $this->autoloadAssets('color', 'all');
        $config = $this->iniFormItemConfig($name, $title, $config);
        $config[self::value] = addslashes($this->getFormData($config[self::name], $config[self::value]));
        $config[self::type] = self::form_color;
        //  颜色类型 hex | rgb | rgba
        $config['format'] = get_sub_value('format', $config, 'rgba');
        $this->assign($config);
        $content = $this->fetch('color');
        $this->curHtml = $this->addItem($content, $config);
        return $this;
    }
    
    /**
     * 添加单行文本框列表
     * @param string|array $name - 表单元素name属性名
     * @param string $title - 标题
     * @param array $config - 更多配置
     * @throws Exception
     * @return FormBuilder
     */
    public function addTexts($name, string $title = '', array $config = []) {
        $this->autoloadAssets('sortable', 'all');
        $config = $this->iniFormItemConfig($name, $title, $config);
        $config[self::value] = $this->unSerialize($this->getFormData($config[self::name], $config[self::value]), false);
        $config[self::type] = self::form_texts;
        $this->assign($config);
        $content = $this->fetch('texts');
        $this->curHtml = $this->addItem($content, $config);
        return $this;
    }
    
    /**
     * 添加多行文本域
     * @param string|array $name - 表单元素name属性名
     * @param string $title - 标题
     * @param array $config - 更多配置
     * @throws Exception
     * @return FormBuilder
     */
    public function addTextArea($name, string $title = '', array $config = []) {
        $config = $this->iniFormItemConfig($name, $title, $config);
        $config[self::value] = $this->getFormData($name, $config[self::value]);
        $config[self::type] = self::form_text_area;
        $config['rows'] = get_sub_value('rows', $config, 4);
        $this->assign($config);
        $content = $this->fetch('text_area');
        $this->curHtml = $this->addItem($content, $config);
        return $this;
    }
    
    /**
     * 添加多行文本域列表
     * @param string|array $name - 表单元素name属性名
     * @param string $title - 标题
     * @param array $config - 更多配置
     * @throws Exception
     * @return FormBuilder
     */
    public function addTextAreas($name, string $title = '', array $config = []) {
        $this->autoloadAssets('sortable', 'all');
        $config = $this->iniFormItemConfig($name, $title, $config);
        $config[self::value] = $this->unSerialize($this->getFormData($config[self::name], $config[self::value]), false);
        $config[self::type] = self::form_text_areas;
        $config['rows'] = get_sub_value('rows', $config, 4);
        $this->assign($config);
        $content = $this->fetch('text_areas');
        $this->curHtml = $this->addItem($content, $config);
        return $this;
    }
    
    /**
     * 添加单选按钮
     * @param string|array $name - 表单元素name属性名
     * @param string $title - 标题
     * @param array $config - 更多配置
     * @throws Exception
     * @return FormBuilder
     */
    public function addRadio($name, string $title = '', array $config = []) {
        $config = $this->iniFormItemConfig($name, $title, $config);
        $list = $config[self::lists] = $this->unSerialize($config[self::lists], false);
        $value = $config[self::value] = addslashes($this->getFormData($config[self::name], $config[self::value]));
        $config[self::type] = self::form_radio;
        $listKey = array_keys($list);
        if (!in_array($value, $listKey)) {
            $value = $listKey[0];
        }
        $config[self::value] = $value;
        $this->assign($config);
        $content = $this->fetch('radio');
        $this->curHtml = $this->addItem($content, $config);
        return $this;
    }
    
    /**
     * 添加多选按钮
     * @param string|array $name - 表单元素name属性名
     * @param string $title - 标题
     * @param array $config - 更多配置
     * @throws Exception
     * @return FormBuilder
     */
    public function addCheckbox($name, string $title = '', array $config = []) {
        $config = $this->iniFormItemConfig($name, $title, $config);
        $config[self::lists] = $this->unSerialize($config[self::lists], false);
        $config[self::value] = $this->unSerialize($this->getFormData($config[self::name], $config[self::value]));
        $config[self::type] = self::form_checkbox;
        $this->assign($config);
        $content = $this->fetch('checkbox');
        $this->curHtml = $this->addItem($content, $config);
        return $this;
    }
    
    /**
     * 添加下拉选项框
     * @param string|array $name - 表单元素name属性名
     * @param string $title - 标题
     * @param array $config - 更多配置
     * @throws Exception
     * @return FormBuilder
     */
    public function addSelect($name, string $title = '', array $config = []) {
        $this->autoloadAssets('select', 'all');
        $config = $this->iniFormItemConfig($name, $title, $config);
        $config[self::lists] = $this->unSerialize($config[self::lists], false);
        $config[self::value] = $this->unSerialize($this->getFormData($config[self::name], $config[self::value]));
        $config[self::type] = self::form_select;
        $listValue = array_values($config[self::lists]);
        $isGroup = isset($listValue[0]) && is_array($listValue[0]) ? true : false;
        $config['group'] = $isGroup;
        $config[self::multiple] = $this->getBoolVal(get_sub_value(self::multiple, $config, false));
        $this->assign($config);
        $content = $this->fetch('select');
        $this->curHtml = $this->addItem($content, $config);
        return $this;
    }
    
    /**
     * 添加switch开关
     * @param string|array $name - 表单元素name属性名
     * @param string $title - 标题
     * @param array $config - 更多配置
     * @throws Exception
     * @return FormBuilder
     */
    public function addSwitch($name, string $title = '', array $config = []) {
        $this->autoloadAssets('switch', 'all');
        $config = $this->iniFormItemConfig($name, $title, $config);
        $list = $config[self::lists] = $this->unSerialize($config[self::lists], false);
        $value = addslashes($this->getFormData($config[self::name], $config[self::value]));
        $off = get_sub_value(0, array_values($list), 0);
        $on = get_sub_value(1, array_values($list), 1);
        $config[self::type] = self::form_switch;
        $config[self::value] = empty($value) ? 0 : $value;
        $config['off'] = empty($off) ? 0 : $off;
        $config['on'] = empty($on) ? 0 : $on;
        $this->assign($config);
        $content = $this->fetch('switch');
        $this->curHtml = $this->addItem($content, $config);
        return $this;
    }
    
    /**
     * 添加坐标拾取器
     * @param string|array $name - 表单元素name属性名
     * @param string $title - 标题
     * @param array $config - 更多配置
     * @throws Exception
     * @return FormBuilder
     */
    public function addMap($name, string $title = '', array $config = []) {
        $this->autoloadAssets('map', 'all');
        $config = $this->iniFormItemConfig($name, $title, $config);
        $value = $config[self::value] = $this->unSerialize($this->getFormData($config[self::name], $config[self::value]), false);
        $config[self::type] = self::form_map;
        $config['location'] = get_sub_value('location', $value, implode(',', $value));
        $config['address'] = get_sub_value('address', $value, '');
        $content = $this->fetch('map');
        $this->curHtml = $this->addItem($content, $config);
        return $this;
    }
    
    /**
     * 添加 表单提交按钮
     * @param string $btnTitle - 按钮标题
     * @param string $btnType - 按钮类型(submit/button)
     * @throws Exception
     * @return FormBuilder
     */
    public function addSubmitBtn(string $btnTitle = '提交表单', string $btnType = 'submit') {
        $id = $this->createId($btnType, $this->formConfig['form_id']);
        $assign = [
            'btn_title' => $btnTitle,
            'btn_type'  => $btnType,
            'id'        => $id
        ];
        $this->assign($assign);
        $html = $this->fetch('btn-submit');
        $this->curHtml = $html;
        $this->footerBtn .= $html;
        $this->assign(['btn_footer' => $this->footerBtn]);
        return $this;
    }
    
    /**
     * 添加 表单重置按钮
     * @param string $btnTitle
     * @throws Exception
     * @return FormBuilder
     */
    public function addResetBtn(string $btnTitle = '重置表单') {
        $id = $this->createId('reset', $this->formConfig['form_id']);
        $this->assign(['btn_title' => $btnTitle, 'id' => $id]);
        $html = $this->fetch('btn-reset');
        $this->curHtml = $html;
        $this->footerBtn .= $html;
        $this->assign(['btn_footer' => $this->footerBtn]);
        return $this;
    }
    
    /**
     * 构建表单Item样式
     * @param string $content - 表单HTML内容
     * @param array $config - 整合后的配置
     * @throws Exception
     * @return string
     */
    protected function addItem(string $content, array $config) {
        $itemWidth = 12 - $this->formConfig['width'];
        $assign = [
            'content'     => $content,
            'id'          => $config[self::id],
            'label_width' => $this->formConfig['width'],
            'item_width'  => $itemWidth == 0 ? 12 : $itemWidth,
            'title'       => $config[self::title],
            'help'        => $config[self::tip],
            'width'       => $config[self::width],
            'type'        => $config[self::type],
        ];
        $this->assign($assign);
        $html = htmlspecialchars_decode($this->fetch('item'));
        $this->html .= $html;
        return $html;
    }
    
    /**
     * 表单元素配置初始化
     * @param string|array $name - 表单元素name属性名
     * @param string $title - 标题
     * @param array $config - 更多配置
     * @return array
     */
    protected function iniFormItemConfig($name, string $title = '', array $config = []) {
        $defaultConfig = [
            self::tip         => '',
            self::placeholder => '',
            self::width       => 12,
            self::class_list  => '',
            self::value       => '',
            self::lists       => [],
            self::disabled    => false,
            self::readonly    => false,
        ];
        if (is_array($name)) {
            $config = $name;
        } else {
            $config[self::name] = $name;
            $config[self::title] = $title;
        }
        $config[self::id] = $this->createId($config[self::name], $this->formConfig['form_id']);
        $config[self::placeholder] = addslashes(strip_tags($config[self::placeholder]));
        $config[self::disabled] = $this->getBoolVal($config[self::disabled]);
        $config[self::readonly] = $this->getBoolVal($config[self::readonly]);
        if (isset($config[self::validate]) && is_array($config[self::validate]) && count($config[self::validate]) >= 1) {
            $this->addValidate($config[self::name], $config[self::validate]);
            unset($config[self::validate]);
        }
        return array_merge($defaultConfig, $config);
    }
    
    /**
     * 添加字段验证
     * @param string $name -表达name
     * @param array $config - 验证
     * @return FormBuilder
     */
    public function addValidate(string $name, array $config) {
        if (!isset($this->validateConfig[$name]['validators'])) {
            $this->validateConfig[$name]['validators'] = [];
        }
        $this->validateConfig[$name]['validators'] = array_merge($this->validateConfig[$name]['validators'], $config);
        return $this;
    }
    
    /**
     * 自行获取表单默认数据
     * @param string $name - name属性
     * @param mixed $default - 默认值
     * @return mixed
     */
    protected function getFormData(string $name, $default = '') {
        return get_sub_value($name, $this->formData, $default);
    }
    
    /**
     * 添加表达验证
     * @param string $redirectUrl -提交成功后的跳转地址（为空不跳转）
     * @throws Exception
     */
    protected function addBootstrapValidator(string $redirectUrl = '') {
        /* 以下为 bootstrapValidator 处理 */
        if (!empty($this->validateConfig)) {
            $validateConfig = json_encode($this->validateConfig, JSON_UNESCAPED_UNICODE);
            $validateConfig = stripslashes($validateConfig);
            $patten = '/({"regexp":")(.*?)(",)/i';
            $replacement = "{\"regexp\":\$2,";
            $validateConfig = preg_replace($patten, $replacement, $validateConfig);
        } else {
            $validateConfig = '{}';
        }
        $assign = [
            'validate_config' => $validateConfig,
            'redirect_url'    => $redirectUrl
        ];
        $this->assign($assign);
        $html = $this->fetch('validate');
        builder_event_listen($this->jsHook, function () use ($html) {
            return htmlspecialchars_decode($html);
        });
    }
}