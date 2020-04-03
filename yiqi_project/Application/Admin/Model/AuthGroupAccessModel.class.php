<?php
/**
 * 分组与管理员关系模型
 * User: 1010
 * Date: 2018/5/15
 * Time: 23:57
 */

namespace Admin\Model;


use Think\Model;

class AuthGroupAccessModel extends Model
{
    //验证数据
    protected $_validate = [
        ['uid', 'require', '管理员id必须存在'],
        ['group_id', 'require', '分组id必须存在'],
    ];

}