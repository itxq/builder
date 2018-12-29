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
    
    /**
     * Table 构造函数.
     * @param array $config - 配置信息
     */
    public function __construct(array $config = []) {
        $config['type'] = 'table';
        parent::__construct($config);
        $this->autoloadAssets('table', 'all');
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
}