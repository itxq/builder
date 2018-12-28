<?php

// +----------------------------------------------------------------------
// | 静态资源设置
// +----------------------------------------------------------------------

return [
    /* ------------------------------- jquery ---------------------------------------------------------- */
    'jquery.js'           => [
        '/builder-assets/jquery-3.3.1/jquery-3.3.1.min.js'
    ],
    /* ------------------------------- bootstrap ---------------------------------------------------------- */
    'bootstrap.css'       => [
        '/builder-assets/bootstrap-4.1.3/css/bootstrap.min.css'
    ],
    'bootstrap.js'        => [
        '/builder-assets/bootstrap-4.1.3/js/bootstrap.bundle.min.js'
    ],
    /* ------------------------------- 表单 ---------------------------------------------------------- */
    'form.css'            => [
        '/builder-assets/bootstrapvalidator-0.5.3/css/bootstrapValidator.min.css',
        '/builder-assets/bootstrapvalidator-0.5.3/css/style.min.css'
    ],
    'form.js'             => [
        '/builder-assets/bootstrap-notify/bootstrap-notify.min.js',
        '/builder-assets/bootstrapvalidator-0.5.3/js/bootstrapValidator.min.js',
        '/builder-assets/bootstrapvalidator-0.5.3/js/language/zh_CN.js',
        '/builder-assets/bootstrapvalidator-0.5.3/js/script.min.js'
    ],
    /* ------------------------------- 拖动排序 ---------------------------------------------------------- */
    'sortable.js'         => [
        '/builder-assets/sortable-1.8.0-rc1/sortable.min.js',
        '/builder-assets/sortable-1.8.0-rc1/script.min.js',
    ],
    /* ------------------------------- select --------------------------------------------------- */
    'select.js'           => [
        '/builder-assets/bootstrap-select-1.13.5/js/bootstrap-select.min.js',
        '/builder-assets/bootstrap-select-1.13.5/js/i18n/defaults-zh_CN.min.js',
        '/builder-assets/bootstrap-select-1.13.5/js/script.min.js',
    ],
    'select.css'          => [
        '/builder-assets/bootstrap-select-1.13.5/css/bootstrap-select.css',
    ],
    /* ------------------------------- 时间、日期范围选择 ------------------------------------------ */
    'daterangepicker.css' => [
        '/builder-assets/daterangepicker-3.0.3/daterangepicker.css'
    ],
    'daterangepicker.js'  => [
        '/builder-assets/daterangepicker-3.0.3/moment.min.js',
        '/builder-assets/daterangepicker-3.0.3/daterangepicker.js',
        '/builder-assets/daterangepicker-3.0.3/script.min.js',
    ],
    /* ------------------------------- bootstrap-switch ------------------------------------------ */
    'switch.css'          => [
        '/builder-assets/bootstrap-switch-4.0.0-alpha.1/css/bootstrap-switch.min.css'
    ],
    'switch.js'           => [
        '/builder-assets/bootstrap-switch-4.0.0-alpha.1/js/bootstrap-switch.min.js',
        '/builder-assets/bootstrap-switch-4.0.0-alpha.1/js/script.min.js',
    ],
    /* ------------------------------- 颜色选择器 ------------------------------------------ */
    'color.css'           => [
        '/builder-assets/bootstrap-colorpicker-3.0.0-beta.1/css/bootstrap-colorpicker.min.css'
    ],
    'color.js'            => [
        '/builder-assets/bootstrap-colorpicker-3.0.0-beta.1/js/bootstrap-colorpicker.min.js'
    ],
    /* ------------------------------- 标签输入框插件 ------------------------------------------------- */
    'tags.js'             => [
        '/builder-assets/bootstrap-tagsinput-0.8.0/bootstrap-tagsinput.min.js',
        '/builder-assets/bootstrap-tagsinput-0.8.0/script.min.js',
    ],
    'tags.css'            => [
        '/builder-assets/bootstrap-tagsinput-0.8.0/bootstrap-tagsinput.css',
    ],
    /* ------------------------------------ 文件上传 ------------------------------------------------- */
    'file.css'            => [],
    'file.js'             => [
        '/builder-assets/ckfinder-3.4.5/ckfinder.js',
    ],
    /* ------------------------------------ 坐标拾取 ------------------------------------------------- */
    'map.js'              => [
        'https://map.qq.com/api/js?v=2.exp&key=__MAP_KEY__&libraries=place',
        '/builder-assets/qq-map-2.0/qq-map.min.js',
    ],
    /* ------------------------------------ 富文本编辑器 ------------------------------------------------- */
    'editor.js'           => [
        '/builder-assets/ckeditor-4.11.1/ckeditor.js'
    ],
];