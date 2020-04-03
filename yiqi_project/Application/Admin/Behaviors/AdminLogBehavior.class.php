<?php
/**
 * 日志行为类
 * User: Administrator
 * Date: 2018/5/21 0021
 * Time: 下午 05:57
 */

namespace Admin\Behaviors;


use Admin\Model\AdminLog;
use Think\Behavior;

class AdminLogBehavior extends Behavior
{
    /**
     * 日志记录
     * @param mixed $param
     */
    public function run(&$param)
    {
        if ($post = session('postData')) {
            D("AdminLog")->record($post);
        }

    }
}