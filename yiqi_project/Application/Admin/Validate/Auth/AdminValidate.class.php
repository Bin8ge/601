<?php
/**
 * 管理员验证器
 * User: Administrator
 * Date: 2018/5/20
 * Time: 19:06
 */

namespace Admin\Validate\Auth;


use Think\Model;

class AdminValidate extends Model
{
    protected $trueTableName = 'yq_admin';

    //验证数据
    protected $_validate = [
        ['username', 'require', '管理员名称必须唯一', 0, 'unique', 1],
        ['email', 'email', '邮箱必须唯一', 0, 'unique', 1],
        ['pid', 'require', '父级必须存在', 0],
        ['password', 'require', '密码必须存在', 0,'',1],
    ];
}