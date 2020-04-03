<?php
return [

    //模板参数配置
    'TMPL_PARSE_STRING' => array(
        '__CUBE__' => '/Public/Admin/Cube',
        '__JS__' => '/Public/Admin/Cube/js',
        '__CSS__' => '/Public/Admin/Cube/css',
        '__IMG__' => '/Public/Admin/Cube/img',
        '__JSLIBS__' => '/Public/Admin/Cube/libs',
    ),

    //控制器分层设置
    'CONTROLLER_LEVEL'      =>  2,
    'URL_CASE_INSENSITIVE' => True,

    //token设置
    'token' => [
        // 缓存前缀
        'key' => 'truck20180513',
        // 加密方式
        'hashalgo' => 'ripemd160',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
    ],

    //额外加载配置信息
    'LOAD_EXT_CONFIG' => [
        'VIEW' => 'view',
        'SYSTEM' => 'system'
    ],
];
