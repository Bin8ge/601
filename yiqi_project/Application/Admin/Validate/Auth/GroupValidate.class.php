<?php
/**
 * 角色组验证器
 * User: Administrator
 * Date: 2018/5/19
 * Time: 1:52
 */

namespace Admin\Validate\Auth;


use Think\Model;

class GroupValidate extends Model
{
    protected $trueTableName = 'yq_auth_group';

    //验证数据
    protected $_validate = [
        ['name', 'require', '规则名称必须存在', 0],
        ['rules', 'require', '规则路径必须存在', 0],
        ['pid', 'require', '父级必须存在', 0],
    ];
}