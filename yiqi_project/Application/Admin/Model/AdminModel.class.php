<?php
/**
 * 后台管理员模型
 * User: 1010
 * Date: 2018/5/15 0015
 * Time: 下午 01:50
 */

namespace Admin\Model;


use Think\Model;

class AdminModel extends Model
{
    protected $tableName = 'admin';

    //验证数据
    protected $_validate = [
        ['username', '', '管理员名称必须唯一', 0, 'unique', 1],
        ['uid', '', '管理员用户id必须唯一', 0, 'unique', 1],
        ['email', '', '邮箱必须唯一', 0, 'unique', 1],
    ];

    //自动完成
    protected $_auto = [
        ['createtime', 'time', 1, 'function'],
        ['updatetime', 'time', 2, 'function'],
    ];
}