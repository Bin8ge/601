<?php
/**
 * 点控管理模型
 * User: 1010
 * Date: 2018/5/25
 * Time: 17:47
 */

namespace Admin\Model;


use Common\Model\BaseModel;

class PointControlModel extends BaseModel
{
    //验证数据
    protected $_validate = [
        ['controlSum', 'number', '点控目标格式不正确', 3],
    ];

    //自动完成
    protected $_auto = [
        ['status', '', 1],
        ['createtime', 'time', 1, 'function'],
        ['updatetime', 'time', 3, 'function'],
    ];
}