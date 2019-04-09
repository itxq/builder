<?php

// +----------------------------------------------------------------------
// | 静态资源设置
// +----------------------------------------------------------------------

return [
    /* ------------------------------- jquery ---------------------------------------------------------- */
    'jquery.js'           => [
        '/builder-assets/assets/jquery/jquery-3.3.1.min.js'
    ],
    /* ------------------------------- bootstrap ---------------------------------------------------------- */
    'bootstrap.css'       => [
        '/builder-assets/assets/bootstrap/css/bootstrap.min.css'
    ],
    'bootstrap.js'        => [
        '/builder-assets/assets/bootstrap/js/bootstrap.bundle.min.js'
    ],
    /* ------------------------------- 公共 ---------------------------------------------------------- */
    'builder.css'         => [
        '/builder-assets/css/style.min.css',
    ],
    'builder.js'          => [
        '/builder-assets/assets/layer/layer.js',
        // '/builder-assets/assets/bootstrap-notify/bootstrap-notify.min.js',
        '/builder-assets/js/script.min.js',
    ],
    /* ------------------------------- 表单 ---------------------------------------------------------- */
    'form.css'            => [
        '/builder-assets/assets/bootstrapvalidator/css/bootstrapValidator.min.css',
    ],
    'form.js'             => [
        '/builder-assets/assets/bootstrapvalidator/js/bootstrapValidator.min.js',
        '/builder-assets/assets/bootstrapvalidator/js/language/zh_CN.js',
    ],
    /* ------------------------------- 拖动排序 ---------------------------------------------------------- */
    'sortable.js'         => [
        '/builder-assets/assets/sortable/sortable.min.js',
        '/builder-assets/assets/sortable/script.min.js',
    ],
    /* ------------------------------- select --------------------------------------------------- */
    'select.js'           => [
        '/builder-assets/assets/bootstrap-select/js/bootstrap-select.min.js',
        '/builder-assets/assets/bootstrap-select/js/i18n/defaults-zh_CN.min.js',
    ],
    'select.css'          => [
        '/builder-assets/assets/bootstrap-select/css/bootstrap-select.min.css',
    ],
    /* ------------------------------- 时间、日期范围选择 ------------------------------------------ */
    'daterangepicker.css' => [
        '/builder-assets/assets/daterangepicker/daterangepicker.css'
    ],
    'daterangepicker.js'  => [
        '/builder-assets/assets/daterangepicker/moment.min.js',
        '/builder-assets/assets/daterangepicker/daterangepicker.js',
    ],
    /* ------------------------------- bootstrap-switch ------------------------------------------ */
    'switch.css'          => [
        '/builder-assets/assets/bootstrap-switch/css/bootstrap-switch.min.css'
    ],
    'switch.js'           => [
        '/builder-assets/assets/bootstrap-switch/js/bootstrap-switch.min.js',
    ],
    /* ------------------------------- 颜色选择器 ------------------------------------------ */
    'color.css'           => [
        '/builder-assets/assets/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css'
    ],
    'color.js'            => [
        '/builder-assets/assets/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js'
    ],
    /* ------------------------------- 标签输入框插件 ------------------------------------------------- */
    'tags.js'             => [
        '/builder-assets/assets/bootstrap-tagsinput/bootstrap-tagsinput.min.js',
    ],
    'tags.css'            => [
        '/builder-assets/assets/bootstrap-tagsinput/bootstrap-tagsinput.min.css',
    ],
    /* ------------------------------------ 文件上传 ------------------------------------------------- */
    'file.css'            => [],
    'file.js'             => [
        '/builder-assets/assets/ckfinder/ckfinder.js',
    ],
    /* ------------------------------------ 坐标拾取 ------------------------------------------------- */
    'map.js'              => [
        'https://map.qq.com/api/js?v=2.exp&key=__MAP_KEY__&libraries=place',
        '/builder-assets/assets/qq-map/qq-map.min.js',
    ],
    /* ------------------------------------ 富文本编辑器 ------------------------------------------------- */
    'editor.js'           => [
        '/builder-assets/assets/ckeditor/ckeditor.js'
    ],
    
    /* ---------------------------------------------------------------------------------------------------- */
    'table.js'            => [
        '/builder-assets/assets/bootstrap-table/bootstrap-table.min.js',
        //  bootstrap 表格导出
        '/builder-assets/assets/table-export/tableExport.min.js',
        '/builder-assets/assets/table-export/libs/jsPDF/jspdf.min.js',
        '/builder-assets/assets/table-export/libs/jsPDF-AutoTable/jspdf.plugin.autotable.js',
        '/builder-assets/assets/bootstrap-table/extensions/export/bootstrap-table-export.min.js',
        //  bootstrap 表格编辑
        
        '/builder-assets/assets/bootstrap-table/extensions/editable/bootstrap-table-editable.min.js',
        '/builder-assets/assets/bootstrap-editable/js/bootstrap-editable.js',
        // 搜索框
        '/builder-assets/assets/bootstrap-table/extensions/toolbar/bootstrap-table-toolbar.min.js',
        '/builder-assets/assets/bootstrap-table/locale/bootstrap-table-zh-CN.min.js',
    ],
    'table.css'           => [
        '/builder-assets/assets/bootstrap-table/bootstrap-table.min.css',
        '/builder-assets/assets/bootstrap-editable/css/bootstrap-editable.min.css',
    ],
];
