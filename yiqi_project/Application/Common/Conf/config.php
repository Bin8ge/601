<?php
/**
 * 公共配置信息
 * User: 1010
 * Date: 2018/5/23
 * Time: 16:24
 */
return [
    //模板布局配置
    'LAYOUT_ON' => FALSE,
    'LAYOUT_NAME' => 'Layout/base',
    'URL_MODEL' => 2,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式：

    'URL_CASE_INSENSITIVE' => false, //路由区分大小写


    // 默认模块
    'DEFAULT_MODULE' => 'Home',
    'BIND_MODULE' => 'Home',



    //url配置
    'URL_HTML_SUFFIX' => '',

    //session 驱动
    'SESSION_PREFIX' => 'Admin_',
    'SESSION_TABLE' => '',// session 表名称
    'SESSION_TYPE' => '', // session hander类型 默认无需设置 除非扩展了session hander驱动

    'SESSION_OPTIONS' => [
        'name'              =>  'Admin_',                      //设置session名
        'expire'              =>  24*3600*2,                      //SESSION保存2天
        'use_trans_sid'       => 1,                               //跨页传递
        'use_only_cookies'    =>  0,                               //是否只开启基于cookies的session的会话方式
    ],


     //额外加载配置信息
    'LOAD_EXT_CONFIG' => 'database',
];