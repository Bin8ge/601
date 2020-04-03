<?php
/**
 * 用户留存
 * User: Lbb
 * Date: 2018/7/19 0019
 * Time: 10:12
 */

namespace Admin\Controller\Data;

use Common\Controller\BaseController;

class UserRetentionController extends BaseController
{
    //数据对象
    private $userModel ;

    private $logModel ;

    private  $array_time;
    //公共控制器 方法
    private  $commAction;

    public function __construct()
    {
        parent::__construct();

        $this->userModel = D('user');

        $this->logModel = D('userLog');

        $this->accountModel = D('account');

        $this->commAction = new CommonController();

        $this->array_time = [
            'one'       => 1,
            'two'       => 2,
            'three'     => 3,
            'seven'     => 7,
            'fifteen'   => 15,
            'thirty'    => 30,
        ];
    }

    public function index()
    {
        if(IS_AJAX){

            //时间条件
            $whereTime = $this->commAction->commWhere();
            //时间戳
            $condition['createtime']   = $whereTime['before_day'];
          /*  $conditions['gold']        = 0;
            $conditions['bank']        = 0;
            $conditions['bunkogold']   = 0;*/
           /* $conditions['_logic']    = 'or';
            $condition['_complex'] = $conditions;*/
            /*$nutIn = $this->accountModel->where($conditions)->field('uid')->select();
            //echo M()->getLastSql();exit;
            $condition['uid'] = array('not in',implode(',',array_column($nutIn, 'uid')));*/


            $users = $this->accountModel
                ->alias('a')
                ->where($condition)
                ->where('b.uid is null')
                ->join('left join (select uid  from yq_account where gold = 0 and bank=0 and bunkogold=0 ) as b on a.uid = b.uid ')
                ->field('FROM_UNIXTIME(createtime,"%Y-%m-%d") as time ,count(a.uid) as total')
                ->group('time')
                ->order('time desc')
                ->select();

            $list = [];
            //整理数据
            foreach ($users as $k => $v) {
                $list[$v['time']] = $v;
            }

            foreach ($list as $k=>$val){

                #当天注册人数总数
                $where_com['createtime']  = array('between',array(strtotime(date($k.' 00:00:00')),strtotime(date($k.' 23:59:59'))));
              /*  $where_com_s['gold']        = 0;
                $where_com_s['bank']        = 0;
                $where_com_s['bunkogold']   = 0;
                $where_com_s['createtime']  = array('between',array(strtotime(date($k.' 00:00:00')),strtotime(date($k.' 23:59:59'))));
                $nutIns = $this->accountModel->where($where_com_s)->field('uid')->select();
                $where_com['uid'] = array('not in',implode(',',array_column($nutIns, 'uid')));*/
              /*  $where_com_s['_logic']    = 'or';
                $where_com['_complex'] = $where_com_s;*/
                $reg_num = $this->accountModel
                    ->alias('a')
                    ->where($where_com)
                    ->where('b.uid is null')
                    ->join('left join (select uid  from yq_account where gold = 0 and bank=0 and bunkogold=0 ) as b on a.uid = b.uid ')
                    ->field('a.uid')
                    ->select();
                $reg_num = array_column($reg_num ,'uid');

                #处理 前 1 2 3 7 15 30 的登录人数总数
                foreach ($this->array_time as $kk => $vv) {
                    $where_log['createtime'] = array('between', array(strtotime(date('Y-m-d', strtotime("{$k} +{$vv} day")) . ' 00:00:00'), strtotime(date('Y-m-d', strtotime("{$k} +{$vv} day")) . ' 23:59:59')));
                    #当天登录人数
                    $log_num[$kk] = $this->logModel
                        ->where($where_log)
                        ->field('distinct uid')
                        ->select();
                    $log_num[$kk] = array_column($log_num[$kk], 'uid');        //当天登录人数数组
                    //判断是否有登录 注册人数
                    if ($log_num[$kk]) {
                        $return_num[$kk] = array_intersect($reg_num, $log_num[$kk]);  //注册 登录 相同的人取出 把交集取出
                        //判断是否有匹配值  交集
                        if (count($return_num[$kk]) === 0) {
                            $list[$k][$kk] = '0.00';
                        } else {
                            $list[$k][$kk] = (number_format(count($return_num[$kk]) / count($reg_num), 4) * 100);
                        }
                    } else {
                        $list[$k][$kk] = '0.00';
                    }
                    $log_num = array();
                }
            }

            $creat = $this->commAction->dateMap($whereTime['startStamp'],$whereTime['stopStamp'],$list);


            //检测天数是否有间隔  如有则补全
            foreach ($creat as $k => $v) {
                if (!array_key_exists($k,$list)) {
                    $list [$k]['time']  = $k;
                    $list [$k]['total'] = '0';
                    foreach ($this->array_time as $kk => $vv) {
                        $list[$k][$kk]  = '0.00';
                    }
                }
            }

            krsort($list);              //按时间排序
            $this->assign('list',$list);       //结果列表

            //ajax返回信息，就是要替换的模板
            $data['content'] = $this->fetch('Data/user_retention/replace');
            return returnAjax(200,'SUCCESS',$data);
        }
        $this->display();
    }




}