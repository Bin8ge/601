<?php
/**
 * 在线统计
 * User: Lbb
 * Date: 2018/7/11 0011
 * Time: 16:38
 */

namespace Admin\Controller\Data;


use Common\Controller\BaseController;

class OnlineController extends BaseController
{
     //公共控制器
    private $commAction ;

    //在线人数
    private $onlineModel;

    //在线人数
    private $userModel;


    public function __construct()
    {
        parent::__construct();

        $this->onlineModel = D('online');

        $this->userModel = D('user');

        //公共控制器 方法
        $this->commAction = new CommonController();

    }

    public function index()
    {
        if (IS_AJAX) {
            //时间条件
            $startTime = I('get.startTime');
            $stopTime  = I('get.stopTime');

            //统计类型
            $action = I('get.action', 'hour');
            $create = [];
            $list = [];
            switch ($action) {
                case 'hour':
                    //查询条件
                    $startTime = strtotime($startTime) ?: strtotime(' -1 day');
                    $stopTime  = strtotime($stopTime)  ?: time();
                    $condition['createtime'] = array('between',array($startTime,$stopTime));

                    $list = $this->onlineModel
                        ->where($condition)
                        ->field('createtime as time,commonnum as online')
                        ->select();

                    //整理数据
                    foreach ($list as $k => $val) {
                        $create[date('H:i:s',$val['time'])] = (int)$val['online'];
                    }
                    break;
                case 'day':
                    //查询条件
                    $startTime = strtotime($startTime) ?: strtotime(' -1 month');
                    $stopTime  = strtotime($stopTime)  ?: time();
                    $condition['createtime'] = array('between',array($startTime,$stopTime));
                    //生成sql
                    $sql = $this->onlineModel
                        ->field("Id, commonnum, DATE_FORMAT(FROM_UNIXTIME(createtime,'%Y-%m-%d'),'%Y-%m-%d')  as createtime")
                        ->where($condition)
                        ->select(false);
                    //查询
                    $list = M()->table($sql.' a')->field('ceil(avg(commonnum)) as online,createtime as time')
                        ->group("DATE_FORMAT( createtime ,'%Y-%m-%d')")
                        ->select();
                    //整理数据
                    $list_data = [];
                    foreach ($list as $k => $val) {
                        $list_data[$val['time']] = (int)$val['online'];
                    }
                    //补充断层时间
                    $create = $this->commAction->dateMap($startTime,$stopTime,$list_data);
                    break;
                case 'month':
                    //查询条件
                    $startTime = strtotime($startTime) ?: strtotime(' -1 years');
                    $stopTime = strtotime($stopTime)   ?: time();

                    $condition['createtime'] = array('between',array($startTime,$stopTime));
                    //生成sql
                    $sql = $this->onlineModel
                        ->field("Id, commonnum, DATE_FORMAT(FROM_UNIXTIME(createtime,'%Y-%m-%d'),'%x年-%m月')  as createtime")
                        ->where($condition)
                        ->select(false);
                    //查询
                    $list = M()->table($sql.' a')
                        ->field('ceil(avg(commonnum)) as online,createtime as time')
                        ->group('createtime')
                        ->select();
                    //整理数据
                    $list_data = [];
                    foreach ($list as $k => $val) {
                        $list_data[$val['time']] = (int)$val['online'];
                    }
                    //补充断层时间
                    $month = $this->commAction->getLdTimes(date('Y-m-d',$startTime),date('Y-m-d',$stopTime),'月');
                    $zero  = array_fill_keys($month['months'],0);
                    $create = array_merge($zero,$list_data);
                    break;
            }

             $data['time']  = array_keys($create);          //折现图x轴
             $data['total'] = array_values($create);        //折现图y轴
             $this->assign('list',$list);                    //结果列表

            $gameRoom = $this->gameRoom();
            $this->assign('gameRoom',$gameRoom);                 //结果列表
            $data['people'] =$gameRoom['item'];
            $data['sum']   =$gameRoom['sum'];

            //ajax返回信息，就是要替换的模板
            $data['content'] = $this->fetch('Data/online/replace');
            return returnAjax(200, 'SUCCESS', $data);
        }
        $this->display();
    }


    /**
     * 获取房间信息 及人数
     * Author:lbb
     * @return mixed
     */
    public function gameRoom()
    {
        $condition['onLine'] = 1;
        $condition['level'] = 0;
        $list = $this->userModel
            ->alias('a')
            ->field('count(a.uid) as total,a.room,b.type,b.type_name')
            ->join('left join yq_game_type b on b.productid = a.room')
            ->where($condition)
            ->group('type')
            ->order('total desc')
            ->select();

        $item=[];
        foreach ($list as $k=>$val){
            if(!isset($item[$val['type']])){
                $item[$val['type']]['y']=(int)$val['total'];
                $item[$val['type']]['name']=$val['type_name'];
            }else{
                $item[$val['type']]['y']+=(int)$val['total'];
            }
            $lists[$val['room']] = (int)$val['total'];
        }

        unset($item['0']);
        $sum = 0;
        $items=[];
        foreach ($item as $k=>$v ){
            $items[] = $v;
            $sum += $v['y'];
        }

        foreach ($items as $k=>$v){
            $items[$k]['name'] = $v['name'].'在线:<b>'.$v['y'].'</b>人';
        }

        //总人数
        $sum_total = $this->userModel
            ->where($condition)
            ->count();

        $data['list'] = $list;
        $data['sum_total'] = $sum_total;
        $data['item'] = $items;
        $data['sum'] = $sum;
        return $data;
    }


    /**
     * 详细
     * Author:lbb
     */
    public function detail()
    {
        if (IS_AJAX){
            $type = I('post.type');
            $condition['onLine'] = 1;
            $condition['type'] = $type;
            $list = $this->userModel
                ->alias('a')
                ->field('count(a.uid) as total,a.room,b.type,b.type_name,b.name')
                ->join('left join yq_game_type b on b.productid = a.room')
                ->where($condition)
                ->group('room')
                ->order('room asc')
                ->select();
            $str = '';
            foreach ($list as $val) {
                $str .= '<tr class="'.$val['type'].'">';
                $str .= '<td>'.$val['type_name'] . $val['name'].'</td>';
                $str .= '<td>'.number_format($val['total']).'</td>';
                $str .= '</tr>';
            }

            return returnAjax(200, 'SUCCESS', $str);
        }
    }


}