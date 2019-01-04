<?php
/**
 *  ==================================================================
 *        文 件 名: Table.php
 *        概    要: 表格构建器
 *        作    者: IT小强
 *        创建时间: 2018-12-17 10:40:01
 *        修改时间:
 *        copyright (c) 2016 - 2018 mail@xqitw.cn
 *  ==================================================================
 */

namespace itxq\builder;

use think\Exception;

/**
 * 表格构建器
 * Class Table
 * @package itxq\builder
 */
class Table extends Builder
{
    /**
     * @var string - 表格ID
     */
    protected $tableId;
    
    /**
     * @var string - 表格搜索框ID
     */
    protected $searchId;
    
    /**
     * @var string - 表格工具栏ID
     */
    protected $toolId;
    
    /**
     * @var string - 表格工具栏按钮
     */
    protected $toolBtn = '';
    
    /**
     * @var string - 表格搜索项
     */
    protected $searchItem = '';
    
    /**
     * @var string - 表格操作按钮
     */
    protected $actionItem = '';
    
    /**
     * @var array - 表格列字段信息
     */
    protected $columns = [];
    
    const field = 'field';
    const editable = 'editable';
    const formatter = 'formatter';
    const sort = false;
    const title = 'title';
    const class_list = 'class';
    
    /**
     * Table 构造函数.
     * @param array $config - 配置信息
     */
    public function __construct(array $config = []) {
        $config['type'] = 'table';
        parent::__construct($config);
        $this->autoloadAssets('table', 'all');
        $this->autoloadAssets('builder', 'all');
    }
    
    /**
     * 创建表格头部
     * @param string $tableId - 表格ID
     * @param string $class - class累
     * @return Table
     * @throws Exception
     */
    public function start(string $tableId = 'bootstrap-table', string $class = '') {
        $this->tableId = $tableId;
        $this->searchId = $this->createId('search', $this->tableId);
        $this->toolId = $this->createId('toolbar', $this->tableId);
        $assign = [
            'table_id'    => $this->tableId,
            'table_class' => $class,
        ];
        $this->assign($assign);
        $content = $this->fetch('start');
        $this->curHtml = $content;
        $this->html = $this->curHtml;
        return $this;
    }
    
    /**
     * 创建表格结尾
     * @param string $dataUrl - 数据获取地址
     * @param bool|string $editUrl - 字段修改地址
     * @return Table
     * @throws Exception
     */
    public function end(string $dataUrl, bool $editUrl = false) {
        $search = $this->getBoolVal(get_sub_value('search', $this->config, 'false'));
        $pagination = $this->getBoolVal(get_sub_value('pagination', $this->config, 'true'));
        $cardView = $this->getBoolVal(get_sub_value('card_view', $this->config, 'false'));
        $assign = [
            'columns'         => json_encode($this->columns),
            'table_id'        => $this->tableId,
            'search_id'       => $this->searchId,
            'table_class'     => get_sub_value('class', $this->config, ''),
            'is_search'       => intval($search),
            'query_params'    => $this->createId('getQueryParams'),
            'select_name'     => get_sub_value('select_name', $this->config, 'son[]'),
            'data_url'        => $dataUrl,
            'edit_url'        => $editUrl,
            'editable'        => ($editUrl === false) ? 0 : 1,
            'method'          => get_sub_value('method', $this->config, 'post'),
            'pagination'      => intval($pagination),
            'page_size'       => get_sub_value('page_size', $this->config, '10'),
            'side_pagination' => get_sub_value('side_pagination', $this->config, 'server'),
            'tool_id'         => $this->toolId,
            'card_view'       => intval($cardView),
            'id_field'        => get_sub_value('id_field', $this->config, 'id'),
        ];
        $this->assign($assign);
        $html = htmlspecialchars_decode($this->fetch('end'));
        builder_event_listen($this->jsHook, function () use ($html) {
            return $html;
        });
        $this->curHtml = '</table>';
        $this->html .= $this->curHtml;
        return $this;
    }
    
    /**
     * @return string
     * @throws Exception
     */
    public function returnTable(): string {
        if (!empty($this->toolBtn)) {
            $this->getTableTool();
        }
        return $this->returnHtml();
    }
    /* -------------------------------------------------表格项-------------------------------------------------------- */
    
    /**
     * 添加多选框
     * @param $field - 字段
     * @param string $title - 标题
     * @param array $config - 更多配置
     * @return Table
     */
    public function addColumnCheckbox(string $field, string $title = '', array $config = []) {
        $config = $this->iniTableItemConfig($field, $title, $config);
        $class = 'text-center ' . $config[self::class_list];
        $column = [
            'checkbox' => true,
            'class'    => $class,
        ];
        $this->columns[] = $column;
        return $this;
    }
    
    /**
     * 添加通用字段列
     * @param $field - 字段
     * @param string $title - 标题
     * @param array $config - 更多配置
     * @return Table
     */
    public function addColumnDefault(string $field, string $title = '', array $config = []) {
        $config = $this->iniTableItemConfig($field, $title, $config);
        $this->setColumn($config[self::field], $config[self::title], $config[self::class_list], $config[self::editable], $config[self::sort], $config[self::formatter]);
        return $this;
    }
    
    /**
     * 表格元素初始化
     * @param $field - 字段名
     * @param string $title - 标题
     * @param array $config - 更多配置
     * @return array
     */
    protected function iniTableItemConfig($field, string $title = '', array $config = []) {
        $defaultConfig = [
            self::class_list => '',
            self::formatter  => '',
            self::editable   => [],
            self::sort       => false,
        ];
        if (is_array($field)) {
            $config = $field;
        } else {
            $config[self::field] = $field;
            $config[self::title] = $title;
        }
        $config = array_merge($defaultConfig, $config);
        return $config;
    }
    
    /**
     * 设置编辑字段
     * @param $field - 字段
     * @param string $title - 标题
     * @param string $class - class类
     * @param array $edit - 行内编辑
     * @param bool $sort - 是否排序
     * @param string $formatter - 格式化数据(js回调函数名)
     * @return array
     */
    protected function setColumn(string $field, string $title = '', string $class = '', array $edit = [], bool $sort = false, $formatter = ''): array {
        $title = empty($title) ? $field : $title;
        $column = [
            'field'    => $field,
            'title'    => $title,
            'sortable' => $this->getBoolVal($sort),
        ];
        if (!empty($formatter)) {
            $column['formatter'] = $formatter;
        }
        if (!empty($class)) {
            $column['class'] = $class;
        }
        if (is_array($edit) && count($edit) >= 1) {
            $column['editable'] = $edit;
        }
        $this->columns[] = $column;
        return $column;
    }
    
    
    /* -------------------------------------------------工具栏-------------------------------------------------------- */
    
    /**
     * 添加工具栏普通按钮
     * @param string $type - 按钮类型
     * @param string $title - 标题
     * @param string $class -额外的class类
     * @return Table
     * @throws Exception
     */
    public function addToolBtn(string $title = '', string $type = 'button', string $class = 'btn-info') {
        $assign = [
            'type'  => $type,
            'title' => $title,
            'class' => $class,
        ];
        $this->assign($assign);
        $this->toolBtn .= $this->fetch('tool-btn-default');
        return $this;
    }
    
    /**
     * 添加工具栏链接按钮
     * @param $url - 链接地址
     * @param string $title - 标题
     * @param string $class -额外的class类
     * @param string $target - a标签target属性
     * @return Table
     * @throws Exception
     */
    public function addToolLink(string $url = '', string $title = '', string $class = 'btn-info', string $target = '_self') {
        $assign = [
            'url'    => $url,
            'title'  => $title,
            'class'  => $class,
            'target' => $target,
        ];
        $this->assign($assign);
        $this->toolBtn .= $this->fetch('tool-btn-link');
        return $this;
    }
    
    /**
     * 添加工具栏AJAX操作全部按钮
     * @param string $title - 标题
     * @param string $url - AJAX提交地址
     * @param string $successUrl - AJAX完成跳转地址
     * @param string $confirmTitle - 确认信息
     * @param string $class -额外的class类
     * @return Table
     * @throws Exception
     */
    public function addToolAll($title, $url, $successUrl = '', $confirmTitle = '确定要执行此操作吗？', $class = 'btn-danger') {
        $id = $this->createId('ajax_btn', true);
        $assign = [
            'id'            => $id,
            'title'         => $title,
            'url'           => $url,
            'success_url'   => ($successUrl === false) ? 'false' : $successUrl,
            'confirm_title' => $confirmTitle,
            'class'         => $class,
        ];
        $this->assign($assign);
        $this->toolBtn .= $this->fetch('tool-btn-all');
        return $this;
    }
    
    /**
     * 获取表格工具栏
     * @return string
     * @throws Exception
     */
    protected function getTableTool(): string {
        $assign = [
            'tool_id'  => $this->toolId,
            'tool_btn' => $this->toolBtn
        ];
        $this->assign($assign);
        $this->curHtml = $this->fetch('table-tool');
        $this->html .= $this->curHtml;
        return $this->curHtml;
    }
}