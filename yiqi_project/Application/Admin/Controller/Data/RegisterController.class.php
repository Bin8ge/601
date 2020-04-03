<?php
/**
 * 日注册量统计
 * User: Lbb
 * Date: 2018/7/16 0016
 * Time: 14:58
 */

namespace Admin\Controller\Data;


use Common\Controller\BaseController;

class RegisterController extends BaseController
{
    //数据对象
    private $userModel;

    //公共控制器 方法
    private  $commAction = null;


    public function __construct()
    {
        parent::__construct();

        $this->userModel = D('user');


        $this->statistical =D('statistical');

        $this->commAction = new CommonController();

    }


    public function index()
    {
        if (IS_AJAX) {
            //获取时间条件
            $whereTime = $this->commAction->commWhere();

            //公共条件
            $condition['createtime'] = $whereTime['before_day'];

            #列表数据
            $list = $this->statistical
                ->where($condition)
                ->order('id asc')
                ->select();

            $data['time'] = array_column($list, 'day_time');
            $data['reg'] = array_map('intval',array_column($list, 'reg'));       //折现图y轴
            $data['login'] =  array_map('intval',array_column($list, 'login'));         //折现图y轴

            $this->assign('list', $list);                 //结果列表



            //ajax返回信息，就是要替换的模板
            $data['content'] = $this->fetch('Data/register/replace');
            return returnAjax(200, 'SUCCESS', $data);
        }
        $this->display();
    }


    /**
     * old
     * Author:lbb
     */
    public function indexs()
    {
        if(IS_AJAX){
            //获取时间数组
            $whereTime = $this->commAction->commWhere();

            //时间查询的条件
            $condition['createtime']  = $whereTime['before_day'];
            //当日查询的条件
            $where_today['createtime'] =  $whereTime['to_day'];


            $users = $this->userModel
                ->where($condition)
                ->field('equipment_type,FROM_UNIXTIME(createtime,"%Y-%m-%d") as time ,count(uid) as total')
                ->group('FROM_UNIXTIME(createtime,"%Y-%m-%d"),equipment_type')
                ->order('time desc')
                ->select();

            $user = $this->userModel
                ->where($condition)
                ->field('FROM_UNIXTIME(createtime,"%Y-%m-%d") as time ,count(uid) as total')
                ->group(' FROM_UNIXTIME(createtime,"%Y-%m-%d")')
                ->order('time desc')
                ->select();


            //生成折线图数组 $creat
            foreach ($user as $k=>$val){
                $creat[$val['time']]=  (int)$val['total'];
            }

            //整理数据 table
            foreach ($users as $k => $v) {
                $userss [$v['time']][$v['equipment_type'].'equipment_type'] = number_format($v['total']);
                $userss [$v['time']]['total'] = number_format($creat[$v['time']]);
            }

            //补充空缺的时间段  折线图
            $creat = $this->commAction->dateMap($whereTime['startStamp'],$whereTime['stopStamp'],$creat?:[]);

            //补充空缺的时间段  table
            $userss = $this->commAction->dateMap($whereTime['startStamp'],$whereTime['stopStamp'],$userss?:[],$value=['1equipment_type'=>'0','2equipment_type'=>'0','3equipment_type'=>'0']);
            krsort($userss);     //按键排序

            $data['time'] = array_keys($creat);            //折现图x轴
            $data['total'] = array_values($creat);         //折现图y轴
            $this->assign('list',$userss);                 //结果列表

            //ajax返回信息，就是要替换的模板
            $data['content'] = $this->fetch('Data/register/replace');
            return returnAjax(200,'SUCCESS',$data);
        }
        $this->display();
    }





}