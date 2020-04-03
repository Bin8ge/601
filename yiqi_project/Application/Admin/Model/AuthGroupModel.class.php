<?php
/**
 * 权限组模型
 * User: 1010
 * Date: 2018/5/11 0011
 * Time: 下午 02:51
 * @author 1010
 */

namespace Admin\Model;


use Think\Model;

class AuthGroupModel extends Model
{
    //验证数据
    protected $_validate = [
        ['name', '', '规则名称必须唯一', 0, 'unique', 1],
        ['rules', 'require', '规则id必须存在'],
        ['pid', 'require', '父级必须存在'],
    ];

    //自动完成
    protected $_auto = [
        ['createtime', 'time', 1, 'function'],
        ['updatetime', 'time', 2, 'function'],
    ];

    //命名范围
    protected $_scope = [
        'id_order' => [
            'order' => 'weigh desc'
        ],
    ];
}