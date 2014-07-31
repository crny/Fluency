<?php

return [
    'debug' => true,

    // 模板目录
    'templates.path' => APP_PATH . '/views',

    /**
     * 类别名
     */
    'aliases' => [
        'App' => '\Clear\Facades\App',
    ],

    'providers' => [
        '\Clear\Lang\LangServiceProvider',
    ],
];