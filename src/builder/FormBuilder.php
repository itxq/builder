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
        $this->formConfig['col_width'] = get_sub_value('width', $config, 2);
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
     * 添加HR分割线
     * @return FormBuilder
     */
    public function addHr() {
        $this->curHtml = '<hr style="height: 0;width: 100%;margin: 0;padding: 0;color: transparent;border: 0;">';
        $this->html .= $this->curHtml;
        return $this;
    }
    
    /**
     * 添加表单验证令牌
     * @param string|array $name - 令牌名称
     * @param mixed $tokenType - 令牌生成方法
     * @return FormBuilder
     */
    public function addToken($name = '__token__', $tokenType = 'md5') {
        if (is_array($name)) {
            $tokenType = get_sub_value('token_type', $name, 'md5');
            $name = get_sub_value('name', $name, '__token__');
        }
        $this->curHtml = Request::token($name, $tokenType);
        $this->html .= $this->curHtml;
        return $this;
    }
    
    /**
     * 添加字体图标选择器
     * @param $name - name
     * @param string $title - 标题
     * @param array $list - 字体图标文件
     * @param array $validate - 字段验证
     * @param string $placeholder - 提示语句
     * @param string $help - 提示语句
     * @param int $itemCol - 默认表单项宽度
     * @throws Exception
     * @return FormBuilder
     */
    public function addIco($name, string $title = '', array $list = [], array $validate = [], string $placeholder = '', string $help = '', int $itemCol = 12) {
        if (is_array($name)) {
            $itemCol = get_sub_value('width', $name, $itemCol);
            $placeholder = get_sub_value('placeholder', $name, '');
            $help = get_sub_value('help', $name, '');
            $validate = get_sub_value('validate', $name, []);
            $title = get_sub_value('title', $name, '');
            $list = $this->unSerialize(get_sub_value('list', $name, ''), false);
            $name = get_sub_value('name', $name, '');
        }
        $value = str_replace('"', '\'', $this->getFormData($name, ''));
        $id = $this->createId($name, $this->formConfig['form_id']);
        $defaultIcoFile = [];
        $icoFile = array_merge($defaultIcoFile, $list);
        $ico = [];
        foreach ($icoFile as $k => $v) {
            //  $v = tpl_replace_string($v);
            if (strpos($v, '//') === 0) {
                $_url = request()->scheme() . ':' . $v;
            } else if (strpos($v, '/') === 0) {
                $_url = request()->domain() . $v;
            } else {
                $_url = $v;
            }
            $_pre = preg_match('/.*?\s([a-zA-Z0-9\-\_]+)$/', $k, $pre) ? $pre[1] : false;
            if ($_pre == false) {
                continue;
            }
            $_preg = "/\." . $_pre . "([a-zA-Z0-9\-\_]+)\:before/";
            $_font = preg_match_all($_preg, @file_get_contents($_url), $icoList) ? $icoList[1] : [];
            if (!is_array($_font) || count($_font) < 1) {
                continue;
            }
            $ico[] = ['pre' => $k, 'ico' => $_font];
            $this->addCss('<link rel="stylesheet" href="' . $_url . '">');
        }
        $assign = [
            'name'        => $name,
            'id'          => $id,
            'value'       => $value,
            'placeholder' => addslashes(strip_tags($placeholder)),
            'ico'         => $ico,
        ];
        $this->assign($assign);
        $content = $this->fetch('ico');
        $this->curHtml = $this->addItem($id, $name, $content, $title, $validate, $help, $itemCol);
        return $this;
    }
    
    /**
     * 添加单文件上传控件
     * @param $name - name
     * @param string $title - 标题
     * @param string $placeholder - 提示语句
     * @param array $validate - 字段验证
     * @param string $help - 提示语句
     * @param int $itemCol - 默认表单项宽度
     * @throws Exception
     * @return FormBuilder
     */
    public function addFile($name, string $title = '', array $validate = [], string $placeholder = '', string $help = '', int $itemCol = 12) {
        $this->autoloadAssets('file', 'all');
        $id = $this->createId($name, $this->formConfig['form_id']);
        $imgExt = ['jpg', 'png', 'gif', 'bmp', 'jpeg', 'ico'];
        $assign = [
            'name'        => $name,
            'id'          => $this->createId($name, $this->formConfig['form_id']),
            'value'       => str_replace('"', '\'', $this->getFormData($name, '')),
            'img_ext'     => implode(',', $imgExt),
            'placeholder' => addslashes(strip_tags($placeholder))
        ];
        $this->assign($assign);
        $content = $this->fetch('file');
        $this->curHtml = $this->addItem($id, $name, $content, $title, $validate, $help, $itemCol);
        return $this;
    }
    
    /**
     * 添加hidden隐藏域
     * @param string $name - name
     * @throws Exception
     * @return FormBuilder
     */
    public function addHidden($name) {
        if (is_array($name)) {
            $name = get_sub_value('name', $name, '');
        }
        $assign = [
            'name'  => $name,
            'id'    => $this->createId($name, $this->formConfig['form_id']),
            'value' => str_replace('"', '\'', $this->getFormData($name, ''))
        ];
        $this->assign($assign);
        $content = $this->fetch('hidden');
        $this->curHtml = $content;
        $this->html .= $content;
        return $this;
    }
    
    /**
     * 添加密码框
     * @param $name - name
     * @param string $title - 标题
     * @param string $placeholder - 提示语句
     * @param array $validate - 字段验证
     * @param string $help - 提示语句
     * @param int $itemCol - 默认表单项宽度
     * @throws Exception
     * @return FormBuilder
     */
    public function addPassword($name, string $title = '', array $validate = [], string $placeholder = '', string $help = '', int $itemCol = 12) {
        if (is_array($name)) {
            $itemCol = get_sub_value('width', $name, $itemCol);
            $placeholder = get_sub_value('placeholder', $name, '');
            $help = get_sub_value('help', $name, '');
            $validate = get_sub_value('validate', $name, []);
            $title = get_sub_value('title', $name, '');
            $name = get_sub_value('name', $name, '');
        }
        $value = str_replace('"', '\'', $this->getFormData($name, ''));
        $id = $this->createId($name, $this->formConfig['form_id']);
        $assign = [
            'name'        => $name,
            'id'          => $id,
            'value'       => $value,
            'placeholder' => addslashes(strip_tags($placeholder))
        ];
        $this->assign($assign);
        $content = $this->fetch('password');
        $this->curHtml = $this->addItem($id, $name, $content, $title, $validate, $help, $itemCol);
        return $this;
    }
    
    /**
     * 添加时间范围选择框
     * @param $name - name
     * @param string $title - 标题
     * @param array $validate - 字段验证
     * @param string $placeholder - 提示语句
     * @param string $help - 提示语句
     * @param int $itemCol - 默认表单项宽度
     * @throws Exception
     * @return FormBuilder
     */
    public function addDateRange($name, string $title = '', array $validate = [], string $placeholder = '', string $help = '', int $itemCol = 12) {
        if (is_array($name)) {
            $itemCol = get_sub_value('width', $name, $itemCol);
            $help = get_sub_value('help', $name, '');
            $placeholder = get_sub_value('placeholder', $name, '');
            $validate = get_sub_value('validate', $name, []);
            $title = get_sub_value('title', $name, '');
            $name = get_sub_value('name', $name, '');
        }
        $this->autoloadAssets('daterangepicker', 'all');
        $value = str_replace('"', '\'', $this->getFormData($name, ''));
        $time = explode('~', $value);
        $id = $this->createId($name, $this->formConfig['form_id']);
        $assign = [
            'name'        => $name,
            'id'          => $id,
            'value'       => $value,
            'placeholder' => addslashes(strip_tags($placeholder)),
            'sTime'       => get_sub_value(0, $time),
            'eTime'       => get_sub_value(1, $time),
        ];
        $this->assign($assign);
        $content = $this->fetch('date-range');
        $this->curHtml = $this->addItem($id, $name, $content, $title, $validate, $help, $itemCol);
        return $this;
    }
    
    /**
     * 添加JSON表单项
     * @param $name - name
     * @param string $title - 标题
     * @param array $validate - 字段验证
     * @param string $help - 提示语句
     * @param int $itemCol - 默认表单项宽度
     * @param string $keyName - 页面显示json键名称
     * @param string $valueName - 页面显示json值名称
     * @return FormBuilder
     * @throws Exception
     */
    public function addJson($name, string $title = '', array $validate = [], string $help = '', string $keyName = '键', string $valueName = '值', int $itemCol = 12) {
        if (is_array($name)) {
            $itemCol = get_sub_value('width', $name, $itemCol);
            $help = get_sub_value('help', $name, $help);
            $validate = get_sub_value('validate', $name, $validate);
            $title = get_sub_value('title', $name, $title);
            $keyName = get_sub_value('key_name', $name, $keyName);
            $valueName = get_sub_value('value_name', $name, $valueName);
            $name = get_sub_value('name', $name, '');
        }
        $value = $this->unSerialize($this->getFormData($name, []), false);
        $id = $this->createId($name, $this->formConfig['form_id']);
        $assign = [
            'name'      => $name,
            'id'        => $id,
            'value'     => $value,
            'title'     => $title,
            'keyName'   => $keyName,
            'valueName' => $valueName,
        ];
        $this->assign($assign);
        $content = $this->fetch('json');
        $this->curHtml = $this->addItem($id, $name, $content, $title, $validate, $help, $itemCol);
        return $this;
    }
    
    /**
     * 添加标签输入框
     * @param $name - name
     * @param string $title - 标题
     * @param array $validate - 字段验证
     * @param string $placeholder - 提示语句
     * @param string $help - 提示语句
     * @param int $itemCol - 默认表单项宽度
     * @throws Exception
     * @return FormBuilder
     */
    public function addTags($name, string $title = '', array $validate = [], string $placeholder = '', string $help = '', int $itemCol = 12) {
        if (is_array($name)) {
            $itemCol = get_sub_value('width', $name, $itemCol);
            $placeholder = get_sub_value('placeholder', $name, '');
            $help = get_sub_value('help', $name, '');
            $validate = get_sub_value('validate', $name, []);
            $title = get_sub_value('title', $name, '');
            $name = get_sub_value('name', $name, '');
        }
        $this->autoloadAssets('tags', 'all');
        $value = str_replace('"', '\'', $this->getFormData($name, ''));
        $id = $this->createId($name, $this->formConfig['form_id']);
        $assign = [
            'name'        => $name,
            'id'          => $id,
            'value'       => $value,
            'title'       => $title,
            'placeholder' => addslashes(strip_tags($placeholder))
        ];
        $this->assign($assign);
        $content = $this->fetch('tags');
        $this->curHtml = $this->addItem($id, $name, $content, $title, $validate, $help, $itemCol);
        return $this;
    }
    
    /**
     * 添加颜色选择器
     * @param $name - name
     * @param string $title - 标题
     * @param string $type - 颜色类型 hex | rgb | rgba.
     * @param array $validate - 字段验证
     * @param string $placeholder - 提示语句
     * @param string $help - 提示语句
     * @param int $itemCol - 默认表单项宽度
     * @throws Exception
     * @return FormBuilder
     */
    public function addColor($name, string $title = '', string $type = 'rgba', array $validate = [], string $placeholder = '', string $help = '', int $itemCol = 12) {
        if (is_array($name)) {
            $itemCol = get_sub_value('width', $name, $itemCol);
            $help = get_sub_value('help', $name, '');
            $placeholder = get_sub_value('placeholder', $name, '');
            $validate = get_sub_value('validate', $name, []);
            $title = get_sub_value('title', $name, '');
            $type = get_sub_value('list', $name, $type);
            $name = get_sub_value('name', $name, '');
        }
        $this->autoloadAssets('color', 'all');
        $value = str_replace('"', '\'', $this->getFormData($name, ''));
        $id = $this->createId($name, $this->formConfig['form_id']);
        $assign = [
            'name'        => $name,
            'id'          => $id,
            'value'       => $value,
            'format'      => $type,
            'placeholder' => addslashes(strip_tags($placeholder))
        ];
        $this->assign($assign);
        $content = $this->fetch('color');
        $this->curHtml = $this->addItem($id, $name, $content, $title, $validate, $help, $itemCol);
        return $this;
    }
    
    /**
     * 添加单行文本框
     * @param $name - name
     * @param string $title - 标题
     * @param array $validate - 字段验证
     * @param string $placeholder - 提示语句
     * @param string $help - 提示语句
     * @param int $itemCol - 默认表单项宽度
     * @throws Exception
     * @return FormBuilder
     */
    public function addText($name, string $title = '', array $validate = [], string $placeholder = '', string $help = '', int $itemCol = 12) {
        if (is_array($name)) {
            $itemCol = get_sub_value('width', $name, $itemCol);
            $help = get_sub_value('help', $name, '');
            $placeholder = get_sub_value('placeholder', $name, '');
            $validate = get_sub_value('validate', $name, []);
            $title = get_sub_value('title', $name, '');
            $name = get_sub_value('name', $name, '');
        }
        $value = str_replace('"', '\'', $this->getFormData($name, ''));
        $id = $this->createId($name, $this->formConfig['form_id']);
        $assign = [
            'name'        => $name,
            'id'          => $id,
            'value'       => $value,
            'placeholder' => addslashes(strip_tags($placeholder))
        ];
        $this->assign($assign);
        $content = $this->fetch('input');
        $this->curHtml = $this->addItem($id, $name, $content, $title, $validate, $help, $itemCol);
        return $this;
    }
    
    /**
     * 添加单行文本框列表
     * @param $name - name
     * @param string $title - 标题
     * @param array $validate - 字段验证
     * @param string $placeholder - 提示语句
     * @param string $help - 提示语句
     * @param int $itemCol - 默认表单项宽度
     * @throws Exception
     * @return FormBuilder
     */
    public function addTexts($name, string $title = '', array $validate = [], string $placeholder = '', string $help = '', int $itemCol = 12) {
        if (is_array($name)) {
            $itemCol = get_sub_value('width', $name, $itemCol);
            $placeholder = get_sub_value('placeholder', $name, '');
            $help = get_sub_value('help', $name, '');
            $validate = get_sub_value('validate', $name, []);
            $title = get_sub_value('title', $name, '');
            $name = get_sub_value('name', $name, '');
        }
        $this->autoloadAssets('sortable', 'js');
        $value = $this->unSerialize($this->getFormData($name, []), false);
        $id = $this->createId($name, $this->formConfig['form_id']);
        $assign = [
            'name'        => $name,
            'id'          => $id,
            'value'       => $value,
            'placeholder' => addslashes(strip_tags($placeholder))
        ];
        $this->assign($assign);
        $content = $this->fetch('input-list');
        $this->curHtml = $this->addItem($id, $name, $content, $title, $validate, $help, $itemCol);
        return $this;
    }
    
    /**
     * 添加多行文本域
     * @param $name - name
     * @param string $title - 标题
     * @param int $rows - 行数
     * @param array $validate - 字段验证
     * @param string $placeholder - 提示语句
     * @param string $help - 提示语句
     * @param int $itemCol - 默认表单项宽度
     * @throws Exception
     * @return FormBuilder
     */
    public function addTextArea($name, string $title = '', array $validate = [], string $placeholder = '', string $help = '', int $rows = 4, int $itemCol = 12) {
        if (is_array($name)) {
            $itemCol = get_sub_value('width', $name, $itemCol);
            $rows = get_sub_value('rows', $name, 4);
            $placeholder = get_sub_value('placeholder', $name, '');
            $help = get_sub_value('help', $name, '');
            $validate = get_sub_value('validate', $name, []);
            $title = get_sub_value('title', $name, '');
            $name = get_sub_value('name', $name, '');
        }
        $value = $this->getFormData($name, '');
        $id = $this->createId($name, $this->formConfig['form_id']);
        $assign = [
            'name'        => $name,
            'id'          => $id,
            'value'       => $value,
            'placeholder' => addslashes(strip_tags($placeholder)),
            'rows'        => $rows
        ];
        $this->assign($assign);
        $content = $this->fetch('textarea');
        $this->curHtml = $this->addItem($id, $name, $content, $title, $validate, $help, $itemCol);
        return $this;
    }
    
    /**
     * 添加多行文本域列表
     * @param $name - name
     * @param string $title - 标题
     * @param int $rows - 行数
     * @param array $validate - 字段验证
     * @param string $placeholder - 提示语句
     * @param string $help - 提示语句
     * @param int $itemCol - 默认表单项宽度
     * @throws Exception
     * @return FormBuilder
     */
    public function addTextAreas($name, string $title = '', array $validate = [], string $placeholder = '', string $help = '', int $rows = 4, int $itemCol = 12) {
        if (is_array($name)) {
            $itemCol = get_sub_value('width', $name, $itemCol);
            $rows = get_sub_value('rows', $name, 4);
            $placeholder = get_sub_value('placeholder', $name, '');
            $help = get_sub_value('help', $name, '');
            $validate = get_sub_value('validate', $name, []);
            $title = get_sub_value('title', $name, '');
            $name = get_sub_value('name', $name, '');
        }
        $this->autoloadAssets('sortable', 'js');
        $value = $this->unSerialize($this->getFormData($name, []), false);
        $id = $this->createId($name, $this->formConfig['form_id']);
        $assign = [
            'name'        => $name,
            'id'          => $id,
            'value'       => $value,
            'placeholder' => addslashes(strip_tags($placeholder)),
            'rows'        => $rows
        ];
        $this->assign($assign);
        $content = $this->fetch('textarea-list');
        $this->curHtml = $this->addItem($id, $name, $content, $title, $validate, $help, $itemCol);
        return $this;
    }
    
    /**
     * 添加单选按钮
     * @param $name - name
     * @param string $title - 标题
     * @param $list - 可选项列表
     * @param array $validate - 字段验证
     * @param string $help - 提示语句
     * @param int $itemCol - 默认表单项宽度
     * @throws Exception
     * @return FormBuilder
     */
    public function addRadio($name, string $title = '', array $list = [], array $validate = [], string $help = '', int $itemCol = 12) {
        if (is_array($name)) {
            $itemCol = get_sub_value('width', $name, $itemCol);
            $help = get_sub_value('help', $name, '');
            $validate = get_sub_value('validate', $name, []);
            $list = get_sub_value('list', $name, []);
            $title = get_sub_value('title', $name, '');
            $name = get_sub_value('name', $name, '');
        }
        $value = $this->getFormData($name, false);
        $list = $this->unSerialize($list);
        $id = $id = $this->createId($name, $this->formConfig['form_id']);
        $list_key = array_keys($list);
        if ($value === false || !in_array($value, $list_key)) {
            $value = $list_key[0];
        }
        $assign = [
            'name'        => $name,
            'value'       => str_replace('"', '\'', $value),
            'placeholder' => addslashes(strip_tags($help)),
            'list'        => $list,
        ];
        $this->assign($assign);
        $content = $this->fetch('radio');
        $this->curHtml = $this->addItem($id, $name, $content, $title, $validate, $help, $itemCol);
        return $this;
    }
    
    /**
     * 添加多选按钮
     * @param $name
     * @param array $list
     * @param string $title
     * @param array $validate - 字段验证
     * @param string $help - 提示语句
     * @param int $itemCol - 默认表单项宽度
     * @throws Exception
     * @return FormBuilder
     */
    public function addCheckbox($name, string $title = '', array $list = [], array $validate = [], string $help = '', int $itemCol = 12) {
        if (is_array($name)) {
            $itemCol = get_sub_value('width', $name, $itemCol);
            $help = get_sub_value('help', $name, '');
            $validate = get_sub_value('validate', $name, []);
            $list = get_sub_value('list', $name, []);
            $title = get_sub_value('title', $name, '');
            $name = get_sub_value('name', $name, '');
        }
        $value = $this->unSerialize($this->getFormData($name, []));
        $list = $this->unSerialize($list);
        $id = $this->createId($name, $this->formConfig['form_id']);
        $assign = [
            'name'        => $name,
            'value'       => $value,
            'placeholder' => addslashes(strip_tags($help)),
            'list'        => $list,
        ];
        $this->assign($assign);
        $content = $this->fetch('checkbox');
        $this->curHtml = $this->addItem($id, $name, $content, $title, $validate, $help, $itemCol);
        return $this;
    }
    
    /**
     * 添加下拉选项框
     * @param $name
     * @param array $list 多选列表
     * @param string $title
     * @param array $validate - 字段验证
     * @param bool $isMultiple - 是否多选
     * @param string $placeholder - 提示语句
     * @param string $help - 提示语句
     * @param int $itemCol - 默认表单项宽度
     * @throws Exception
     * @return FormBuilder
     */
    public function addSelect($name, string $title = '', array $list = [], array $validate = [], string $placeholder = '', string $help = '', bool $isMultiple = false, int $itemCol = 12) {
        if (is_array($name)) {
            $itemCol = get_sub_value('width', $name, $itemCol);
            $placeholder = get_sub_value('placeholder', $name, '');
            $help = get_sub_value('help', $name, '');
            $isMultiple = get_sub_value('is_multiple', $name, false);
            $validate = get_sub_value('validate', $name, []);
            $list = get_sub_value('list', $name, []);
            $title = get_sub_value('title', $name, '');
            $name = get_sub_value('name', $name, '');
        }
        $this->autoloadAssets('select', 'all');
        $value = $this->unSerialize($this->getFormData($name, ''));
        $list = $this->unSerialize($list);
        $id = $this->createId($name, $this->formConfig['form_id']);
        $listValue = array_values($list);
        $isGroup = isset($listValue[0]) && is_array($listValue[0]) ? true : false;
        $assign = [
            'name'        => $name,
            'id'          => $id,
            'value'       => $value,
            'placeholder' => addslashes(strip_tags($placeholder)),
            'list'        => $list,
            'is_multiple' => ($isMultiple == true) ? true : false,
            'is_group'    => $isGroup,
        ];
        $this->assign($assign);
        $content = $this->fetch('select');
        $this->curHtml = $this->addItem($id, $name, $content, $title, $validate, $help, $itemCol);
        return $this;
    }
    
    /**
     * 添加switch开关
     * @param $name - name值
     * @param string $title - 标题
     * @param $list - 可选项列表
     * @param array $validate - 字段验证
     * @param string $help - 提示语句
     * @param int $itemCol - 默认表单项宽度
     * @throws Exception
     * @return FormBuilder
     */
    public function addSwitch($name, string $title = '', array $list = [], array $validate = [], string $help = '', int $itemCol = 12) {
        if (is_array($name)) {
            $itemCol = get_sub_value('width', $name, $itemCol);
            $help = get_sub_value('help', $name, '');
            $validate = get_sub_value('validate', $name, []);
            $list = get_sub_value('list', $name, []);
            $title = get_sub_value('title', $name, '');
            $name = get_sub_value('name', $name, '');
        }
        $this->autoloadAssets('switch', 'all');
        $value = $this->getFormData($name, '');
        $list = $this->unSerialize($list, false);
        $id = $this->createId($name, $this->formConfig['form_id']);
        $off = get_sub_value(0, array_values($list), 0);
        $on = get_sub_value(1, array_values($list), 1);
        $assign = [
            'name'        => $name,
            'id'          => $id,
            'placeholder' => addslashes(strip_tags($help)),
            'value'       => empty($value) ? 0 : $value,
            'off'         => empty($off) ? 0 : $off,
            'on'          => empty($on) ? 0 : $on,
        ];
        $this->assign($assign);
        $content = $this->fetch('switch');
        $this->curHtml = $this->addItem($id, $name, $content, $title, $validate, $help, $itemCol);
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
     * @param string $id
     * @param string $name
     * @param string $content
     * @param string $title
     * @param array $validate - 字段验证
     * @param string $help - 提示语句
     * @param int $itemCol - 默认表单项宽度
     * @throws Exception
     * @return string
     */
    protected function addItem(string $id, string $name, string $content, string $title = '', array $validate = [], string $help = '', int $itemCol = 12) {
        $title = empty($title) ? $name : $title;
        $assign = [
            'id'        => $id,
            'width'     => $this->formConfig['col_width'],
            'col_width' => 12 - $this->formConfig['col_width'],
            'title'     => $title,
            'help'      => $help,
            'help_type' => get_sub_value('help_type', $this->config, 'block'),
            'content'   => $content,
            'item_col'  => $itemCol,
        ];
        $this->assign($assign);
        $html = htmlspecialchars_decode($this->fetch('item'));
        $this->html .= $html;
        if ($validate && is_array($validate) && count($validate) >= 1) {
            $this->addValidate($name, $validate);
        }
        return $html;
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
            'redirect_url'    => $redirectUrl,
            'base_url_upload' => urlencode('uploads'),
            'base_url_static' => urlencode('static'),
        ];
        $this->assign($assign);
        $html = $this->fetch('validate');
        builder_event_listen($this->jsHook, function () use ($html) {
            return htmlspecialchars_decode($html);
        });
    }
}