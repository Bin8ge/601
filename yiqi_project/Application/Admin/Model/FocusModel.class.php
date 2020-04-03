<?php
/**
 * 关怀管理模型
 * User: 1010
 * Date: 2018/5/25
 * Time: 17:46
 */

namespace Admin\Model;


use Think\Model;

class FocusModel extends Model
{
    //自动完成
    protected $_auto = [
        ['createtime', 'time', 1, 'function'],
        ['updatetime', 'time', 3, 'function'],
    ];
}