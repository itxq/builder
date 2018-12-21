<?php

// +----------------------------------------------------------------------
// | 静态资源设置
// +----------------------------------------------------------------------

return [
    /* ------------------------------- jquery ---------------------------------------------------------- */
    'jquery.js'           => [
        '__builder_assets__/jquery-3.3.1/jquery-3.3.1.min.js'
    ],
    /* ------------------------------- bootstrap ---------------------------------------------------------- */
    'bootstrap.css'       => [
        '__builder_assets__/bootstrap-4.1.3/css/bootstrap.min.css'
    ],
    'bootstrap.js'        => [
        '__builder_assets__/bootstrap-4.1.3/js/bootstrap.bundle.min.js'
    ],
    /* ------------------------------- 表单 ---------------------------------------------------------- */
    'form.css'            => [
        '__builder_assets__/bootstrapvalidator-0.5.3/css/bootstrapValidator.min.css',
        '__builder_assets__/bootstrapvalidator-0.5.3/css/style.min.css'
    ],
    'form.js'             => [
        '__builder_assets__/bootstrap-notify/bootstrap-notify.min.js',
        '__builder_assets__/bootstrapvalidator-0.5.3/js/bootstrapValidator.min.js',
        '__builder_assets__/bootstrapvalidator-0.5.3/js/language/zh_CN.js',
        '__builder_assets__/bootstrapvalidator-0.5.3/js/script.min.js'
    ],
    /* ------------------------------- 拖动排序 ---------------------------------------------------------- */
    'sortable.js'         => [
        '__builder_assets__/Sortable-1.8.0-rc1/Sortable.min.js',
        '__builder_assets__/Sortable-1.8.0-rc1/script.min.js',
    ],
    /* ------------------------------- select --------------------------------------------------- */
    'select.js'           => [
        '__builder_assets__/bootstrap-select-1.13.5/js/bootstrap-select.min.js',
        '__builder_assets__/bootstrap-select-1.13.5/js/i18n/defaults-zh_CN.min.js',
        '__builder_assets__/bootstrap-select-1.13.5/js/script.min.js',
    ],
    'select.css'          => [
        '__builder_assets__/bootstrap-select-1.13.5/css/bootstrap-select.css',
    ],
    /* ------------------------------- 时间、日期范围选择 ------------------------------------------ */
    'daterangepicker.css' => [
        '__builder_assets__/daterangepicker-3.0.3/daterangepicker.css'
    ],
    'daterangepicker.js'  => [
        '__builder_assets__/daterangepicker-3.0.3/moment.min.js',
        '__builder_assets__/daterangepicker-3.0.3/daterangepicker.js',
        '__builder_assets__/daterangepicker-3.0.3/script.min.js',
    ],
    /* ------------------------------- bootstrap-switch ------------------------------------------ */
    'switch.css'          => [
        '__builder_assets__/bootstrap-switch-4.0.0-alpha.1/css/bootstrap-switch.min.css'
    ],
    'switch.js'           => [
        '__builder_assets__/bootstrap-switch-4.0.0-alpha.1/js/bootstrap-switch.min.js',
        '__builder_assets__/bootstrap-switch-4.0.0-alpha.1/js/script.min.js',
    ],
    /* ------------------------------- 颜色选择器 ------------------------------------------ */
    'color.css'           => [
        '__builder_assets__/bootstrap-colorpicker-3.0.0-beta.1/css/bootstrap-colorpicker.min.css'
    ],
    'color.js'            => [
        '__builder_assets__/bootstrap-colorpicker-3.0.0-beta.1/js/bootstrap-colorpicker.min.js'
    ],
    /* ------------------------------- 标签输入框插件 ------------------------------------------------- */
    'tags.js'             => [
        '__builder_assets__/bootstrap-tagsinput-0.8.0/bootstrap-tagsinput.min.js',
        '__builder_assets__/bootstrap-tagsinput-0.8.0/script.min.js',
    ],
    'tags.css'            => [
        '__builder_assets__/bootstrap-tagsinput-0.8.0/bootstrap-tagsinput.css',
    ],
    /* ------------------------------------ 文件上传 ------------------------------------------------- */
    'file.css'            => [
        '__builder_assets__/bootstrap-fileinput-4.5.1/css/fileinput.min.css',
        '__builder_assets__/bootstrap-fileinput-4.5.1/themes/explorer-fa/theme.min.css',
    ],
    'file.js'             => [
        '__builder_assets__/bootstrap-fileinput-4.5.1/js/fileinput.min.js',
        '__builder_assets__/bootstrap-fileinput-4.5.1/js/locales/zh.js',
        '__builder_assets__/bootstrap-fileinput-4.5.1/themes/explorer-fa/theme.min.js',
    ],
    /* ------------------------------------ 坐标拾取 ------------------------------------------------- */
    'map.js'              => [
        'https://map.qq.com/api/js?v=2.exp&key=__MAP_KEY__&libraries=place',
        '__builder_assets__/qq-map-2.0/qq-map.min.js',
    ],
];