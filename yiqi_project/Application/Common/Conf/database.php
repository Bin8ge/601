<?php
/**
 * 数据库配置信息
 * User: 1010
 * Date: 2018/5/23
 * Time: 16:46
 */
/*
return [
    'DB_TYPE' => 'mysqli', // 数据库类型
    'DB_HOST' => '192.168.0.199', // 服务器地址
    'DB_NAME' => 'yqback_manage', // 数据库名
    'DB_USER' => 'root', // 用户名
    'DB_PWD' => 'root123', // 密码
    'DB_PORT' => '3306', // 端口
    'DB_PREFIX' => 'yq_', // 数据库表前缀
];
*/
$config= array(
    //默认数据库链接
    'DB_TYPE' => 'mysqli', // 数据库类型
    //'DB_HOST' => '192.168.0.199', // 服务器地址

   /* 'DB_HOST' => '127.0.0.1', // 服务器地址
    'DB_NAME' => 'new_yqback_manage', // 数据库名*/

   /* 'DB_HOST' => '47.99.106.136', // 服务器地址
    'DB_NAME' => 'yq_mange', // 数据库名*/

     'DB_HOST' => 'rm-bp1c4majvsi8j1090.mysql.rds.aliyuncs.com', // 服务器地址
     'DB_NAME' => 'yq_mange', // 数据库名

    'DB_USER' => 'yiqiyouxi', // 用户名
    'DB_PWD' => 'bMXO2q70rzNBFisl', // 密码
    'DB_PORT' => '3306', // 端口
    'DB_PREFIX' => 'yq_', // 数据库表前缀
    //第二个数据库连接
    'DB_GAME_USER'=>array(
        'DB_TYPE' => 'mysqli',

       /* 'DB_HOST' => '127.0.0.1', // 服务器地址
        'DB_NAME' => 'new_yqback_manage',*/

        /*'DB_HOST' => '47.99.106.136', // 服务器地址
        'DB_NAME' => 'yq_mange', // 数据库名*/

        'DB_HOST' => 'rm-bp1y7lak3mf207opg.mysql.rds.aliyuncs.com', // 服务器地址
        'DB_NAME' => 'yqback_manage',

        'DB_USER' => 'yiqiyouxi',
        'DB_PWD' => 'bMXO2q70rzNBFisl',
        'DB_PORT' => '3306',
        'DB_PREFIX' => 'yq_', // 数据库表前缀
    ),
);
return $config;