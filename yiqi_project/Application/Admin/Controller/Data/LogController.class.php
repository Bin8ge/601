<?php
/**
 * 日登陆量统计
 * User: Lbb
 * Date: 2018/7/17 0017
 * Time: 17:00
 */

namespace Admin\Controller\Data;

use Common\Controller\BaseController;

class LogController extends BaseController
{
    //数据对象
    private $model;

    private $userModel;

    private $commAction;

    public function __construct()
    {
        parent::__construct();

        $this->model = D('userLog');

        $this->userModel = D('user');

        //公共控制器 方法
        $this->commAction = new CommonController();

    }

    public function index()
    {
        if(IS_AJAX){
            //时间条件
            $whereTime = $this->commAction->commWhere();

            //时间查询的条件
            $condition['createtime']  =$whereTime['before_day'];
            $condition['type'] = 'login';

            //当日查询的条件
           /* $where_today['createtime'] =  $whereTime['to_day'];
            $where_today['type']       =  'login';*/

            //获取登录数据
            $user = $this->model
                ->where($condition)
                ->field('FROM_UNIXTIME(createtime,"%Y-%m-%d") as time ,count(distinct uid) as total')
                ->group('FROM_UNIXTIME(createtime,"%Y-%m-%d")')
                ->order('time desc')
                ->select();
            $create = [];
            //$users  = [];
            //计算当日新增登录
            foreach ($user as $k=>$val){
                //整理数据
                $create[$val['time']]  = (int)$val['total'];
                //$users [$val['time']]  = $val;
                //$users [$val['time']]['total'] = $val['total'];

                /*$where_new['createtime'] = array('between',array(strtotime(date($val['time'].' 00:00:00')),strtotime("{$val['time']} +1 days")));

                #当天注册人名单
                $new_people = $this->userModel
                    ->where($where_new)
                    ->field('uid')
                    ->select();

                #当天登录人名单
                $where_log = $where_new;
                $where_log['type'] = 'login';
                $log_people = $this->model
                    ->where($where_log)
                    ->field('uid')
                    ->group('uid')
                    ->select();

                if ($new_people && $log_people)
                {
                    $reg_num[$k] = array_column($new_people ,'uid');     //当天注册人uid 集合
                    $log_num[$k] = array_column($log_people ,'uid');     //当天登录人uid 集合
                    $add_num[$k] = array_intersect($reg_num[$k],$log_num[$k]);  //注册 登录 相同的人取出 把交集取出
                    $users [$val['time']]['rude'] = count($add_num[$k]);
                }else{
                    $users[$val['time']]['rude'] = 0;
                }*/
            }

            //折线图 生成断层时间
            $create = $this->commAction->dateMap($whereTime['startStamp'],$whereTime['stopStamp'],$create?:[]);

            //检测table 数组天数是否有间隔  如有则补全
           /* foreach ($create as $k => $v) {
                if (!array_key_exists($k,$users))
                {
                    $users [$k]['time']  = $k;
                    $users [$k]['total'] = 0;
                    $users [$k]['rude']  = 0;
                }
            }*/

            //krsort($users);                         //按时间排序

            $data['time']  = array_keys($create);            //折现图x轴
            $data['total'] = array_values($create);         //折现图y轴
            //$this->assign('list',$users);                 //结果列表


            //ajax返回信息，就是要替换的模板
            $data['content'] = $this->fetch('Data/log/replace');
            return returnAjax(200,'SUCCESS',$data);
        }
        $this->display();
    }



}