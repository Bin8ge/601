<?php
/**
 * 活动参数配置
 * Created by PhpStorm.
 * User: Lbb
 * Date: 2019/5/13 0013
 * Time: 15:06
 */

namespace Admin\Controller\Auto;


use Common\Controller\BaseController;

class ActiveController extends BaseController
{

    //基本配置
    private  $groupIds = '1022,1023,1024,1025,1027,1030,1031,1032,1036,1037,1038,1039,1040,1052,1053,1054,1055,1042,1056,1057,1058,1059,1060,1061,1062,1063,1064,1065,1066,1067,1068,1069,1070,1071,1072,1073,1074,1075,1076';

    //数据对象
    private $sysModel;

    /**
     * 初始化
     * GameController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->sysModel = D('sys_conf');

    }

    public function index()
    {
        $where['groupId'] = ['in',$this->groupIds];
        $list = $this->sysModel->where($where)->field('id,value,groupId,name')->select();
        $this->assign('list',$list);
        $this->display();
    }

    /**
     * Author:lbb
     * 修改
     */
    public function edit()
    {
        $data = $_POST;
        $is_true = TRUE;
        M()->startTrans();
        foreach ($data as $k=>$val) {
            #条件
            $condition['groupId'] = $k;
            #修改的值
            $saveData['value']    = $val;
            $status = $this->sysModel->where($condition)->data($saveData)->save();
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