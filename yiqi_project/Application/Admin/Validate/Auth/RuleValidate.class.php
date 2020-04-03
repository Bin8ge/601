<?php
/**
 * 规则表验证器
 * User: 1010
 * Date: 2018/5/17 0017
 * Time: 下午 05:54
 */

namespace Admin\Validate\Auth;


use Think\Model;

class RuleValidate extends Model
{
    protected $trueTableName = 'yq_auth_rule';

    //验证数据
    protected $_validate = [
        ['name', 'require', '规则名称必须存在',0],
        ['node','','规则路径必须唯一',0, 'unique',1],
        ['pid', 'require', '父级必须存在',0],
        ['weigh', 'number', '权重值格式不正确',0],
    ];

}