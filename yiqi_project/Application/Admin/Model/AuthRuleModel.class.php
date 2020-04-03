<?php
/**
 * 规则表模型
 * User: 1010
 * Date: 2018/5/11
 * Time: 0:37
 * @author 1010
 */

namespace Admin\Model;

use Think\Model;

class AuthRuleModel extends Model
{
    //验证数据
    protected $_validate = [
        ['name', 'require', '规则名称必须存在',1],
        ['node','','规则路径必须唯一',1, 'unique',3],
        ['pid', 'require', '父级必须存在',1],
        ['weigh', 'number', '权重值格式不正确',1],
    ];

    //自动完成
    protected $_auto = [
        ['status', 'normal'],
        ['createtime', 'time', 1, 'function'],
        ['updatetime', 'time', 2, 'function'],
    ];

    //命名范围
    protected $_scope = [
        'weigh_order' => [
            'order' => 'weigh desc'
        ],
        'status' => [
            'status' => 'normal'
        ],
        'default_limit' => [
            'limit' => 10
        ]
    ];
}