<?php
/**
 * 交易参数设置
 * User: Lbb
 * Date: 2018/7/5 0005
 * Time: 14:32
 */

namespace Admin\Controller\Auto;


use Common\Controller\BaseController;

class TransactionController extends BaseController
{
    //数据对象
    private $model ;
    //基本配置
    public $groupInfo = [
        1001 => ['giver'=>'普通用户','accept'=>'普通用户'],
        1002 => ['giver'=>'普通用户','accept'=>'VIP用户'],
        1003 => ['giver'=>'VIP用户','accept'=>'普通用户'],
        1004 => ['giver'=>'VIP用户','accept'=>'VIP用户'],
    ];
    //折算率
    private  $rate = 100;
    //基本配置
    private  $groupIds = '1001,1002,1003,1004,1005,1035';

    public function __construct()
    {
        parent::__construct();
        $this->model = D('sys_conf');
    }

    public function index() :void
    {
        if (IS_AJAX){
            $this->edit($data = $_POST);
        }
        $where['groupId'] = ['in',$this->groupIds];
        $list = $this->model->where($where)->field('id,value,groupId')->select();
        $send = [];
        foreach ($list as $k=>$val){
            if ($val['groupId'] === '1005' || $val['groupId'] === '1035') {
                $send[$val['groupId']] = $val['value'];
                unset($list[$k]);
            }else{
                $list[$k] = array_merge($this->groupInfo["{$val['groupId']}"],$val);
                $list[$k]['value'] = $val['value'] / $this->rate;
            }
        }
        $this->assign('send',$send);
        $this->assign('list',$list);
        $this->display();
    }

    /**
     * Author:lbb
     * @param $data
     * 修改
     */
    public function edit($data)
    {
        $is_true = TRUE;
        M()->startTrans();
        foreach ($data as $k=>$val) {
            if ( $val<0 || !is_numeric($val) ) {
                return returnAjax(400,'非法参数~~');
            }
            $condition['groupId'] = $k;
            if ($k === 1005 || $k === 1035) {
                $saveData['value']    = $val;
            }else{
                $saveData['value']    = $val * $this->rate;   //存入数据库需要  *  折算率
            }
            $status = $this->model->where($condition)->data($saveData)->save();
            if ($status === false) {
                $is_true = FALSE;
            }
        }
        if ($is_true){
            M()->commit();
            //写入日志
            $this->adminLogModel->record($data);
            return returnAjax(200,'SUCCESS');
        }else{
            M()->rollback();
            return returnAjax(400,'false');
        }
    }



}