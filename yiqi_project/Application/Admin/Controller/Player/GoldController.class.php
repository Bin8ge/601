<?php
/**
 * 金币变动记录
 * User: Lbb
 * Date: 2018/8/17 0017
 * Time: 16:23
 */

namespace Admin\Controller\Player;


use Common\Controller\BaseController;

class GoldController extends BaseController
{

    //游戏玩家表
    private $userModel;

    //游戏名称
    private $gameTypeModel;

    //金币变化的类型
    private $changeGoldType;
    

    private $pageSize = 100;

    

    public function __construct()
    {
        parent::__construct();

        $this->userModel = D('user');

        $this->gameTypeModel = D('game_type');

        $this->changeGoldType = D('change_gold_type');

    }

    public function index()
    {
        if ( I('get.uid') ){
            $uid = (int)I('get.uid');
            $this->assign('uid',$uid);
        }
        if(IS_AJAX){
            //搜索条件
            if ( I('get.nickname') )
            {
                $where['nickname'] = I('get.nickname');
                $user = $this->userModel->where($where)->field('uid')->find();
                $uid = $user['uid'];
            }
            if ( I('get.uid') ){
                $uid = (int)I('get.uid');
            }

            //获取今天00:00
            $todayStart = strtotime(date('Y-m-d'));
            //获取今天24:00
            $todayEnd = strtotime(date('Y-m-d 23:59:59'));

            $condition  = [];
            $startTime  = I('get.starttime');
            $stopTime   = I('get.stoptime');
            if ($startTime || $stopTime) {
                $condition['createtime'] = array('between',array(strtotime($startTime),strtotime($stopTime)));
            }
            if(empty($startTime)&&empty($stopTime)){
                $condition['createtime'] = array('between',array($todayStart,$todayEnd));
            }


            if ( I('get.first_type')) {
                $condition['first_type'] = I('get.first_type');
            }

            $count      = M("{$uid}",'yq_','DB_GAME_USER')->where($condition)->count();// 查询满足要求的总记录数
            $Page       = new \Think\Ajaxpage($count,$this->pageSize,'indexAjaxComm');// 实例化分页类 传入总记录数和每页显示的记录数(25)
            $show       = $Page->show();// 分页显示输出
            $p = (int)$_REQUEST['p'];

            //分页数据
            $list =M("{$uid}",'yq_','DB_GAME_USER')
                ->field('first_type,second_type,productId,stake,winning,gold,surplus_gold,surplus_bank,control_id,control_type,control_progress,control_target,control_plan,createtime')
                ->where($condition)
                ->order('id desc')
                ->page($p,$this->pageSize)
                ->select();


            //获取游戏名称
            $games= $this->getGameType();



            //获取金币变化类型
            $goldType = $this->getGoldChangeType();


             # 处理显示游戏名称 及点控
            foreach ($list as $k=>$val){
                if ($list[$k]['first_type'] === '1'){
                    $list[$k]['game_name']   = $games[$val['productId']];
                }else{
                    $list[$k]['game_name']   = '--';
                }
                $list[$k]['first_type']  = $goldType[$val['first_type']];
                $list[$k]['second_type'] = $goldType[$val['second_type']];

                if($val['control_id']==='0'){
                    $list[$k]['control_type']     = '--';
                    $list[$k]['control_progress'] = '--';
                    $list[$k]['control_target']   = '--';
                    $list[$k]['control_plan']     = '--';
                }else{
                    $list[$k]['control_plan']     = $this->FieldConfig['point_control_plan'][$val['control_type']][$val['control_plan']];
                    $list[$k]['control_type']     = $this->FieldConfig['point_control_type'][$val['control_type']];
                }

            }


            //全部数据
            $gold = M("{$uid}",'yq_','DB_GAME_USER')
                ->field('createtime,productId,second_type,stake,winning,gold,surplus_gold,surplus_bank,first_type')
                ->where($condition)
                ->order('id desc')
                ->select();

            //统计汇总
            $total = [];
            $item=array();
            $typeList=array();
            foreach ($gold as $k=>$val){
                $gold[$k]['createtime'] = date('Y-m-d H:i:s',$val['createtime']);
                $gold[$k]['second_type'] = $goldType[$val['second_type']];
                if ($val['first_type'] === '1') {
                    $gold[$k]['productId']   = $games[$val['productId']];
                    if(!isset($item[$val['productId']]))
                    {
                        $item[$val['productId']]=$val;
                        $item[$val['productId']]['name'] =  $games[$val['productId']];
                    }else{
                        $item[$val['productId']]['gold'] += $val['gold'];
                    }
                    continue;
                } else {
                    $gold[$k]['productId']   = '--';
                    if(!isset($typeList[$val['second_type']])) {
                        $typeList[$val['second_type']]=$val;
                        $typeList[$val['second_type']]['name'] =  $goldType[$val['first_type']].$goldType[$val['second_type']];
                    }else{
                        $typeList[$val['second_type']]['gold'] += $val['gold'];
                    }
                }
            }



            $total['game_gold'] = array_sum(array_column($item,'gold'));  //总输赢


            $this->assign('total',$total);          // 总输赢
            $this->assign('gold',$item);            // 游戏
            $this->assign('typeList',$typeList);    // 汇总
            $this->assign('page',$show);            // 赋值分页输出
            $this->assign('list',$list);            //查询结果



            if (I('get.file') === 'csv') {
                $header   = array('时间','游戏ID','详情','押注值','中奖值','金币变化','剩余金币','剩余银行');
                $filename = $uid.'金币变化记录'.date('Y-m-d');
                $res['csv'] = outCsv($header,$gold, $filename);
            }


            //ajax返回信息，就是要替换的模板
            $res['content'] = $this->fetch('Player/gold/replace');

            return returnAjax(200,'SUCCESS',$res);
        }
        $this->assign('search', $this->getGoldChangeTypes()); // 检索
        $this->display();
    }

    public function csv() :void
    {
        //搜索条件
        if ( I('get.nickname') )
        {
            $where['nickname'] = I('get.nickname');
            $user = $this->userModel->where($where)->field('uid')->find();
            $uid = $user['uid'];
        }
        if ( I('get.uid') ){
            $uid = (int)I('get.uid');
        }

        //获取今天00:00
        $todayStart = strtotime(date('Y-m-d'));
        //获取今天24:00
        $todayEnd = strtotime(date('Y-m-d 23:59:59'));

        $condition  = [];
        $startTime  = I('get.starttime');
        $stopTime   = I('get.stoptime');
        if ($startTime || $stopTime) {
            $condition['createtime'] = array('between',array(strtotime($startTime),strtotime($stopTime)));
        }
        if(empty($startTime)&&empty($stopTime)){
            $condition['createtime'] = array('between',array($todayStart,$todayEnd));
        }


        if ( I('get.first_type')) {
            $condition['first_type'] = I('get.first_type');
        }

        //全部数据
        $gold = M("{$uid}",'yq_','DB_GAME_USER')
            ->field('createtime,first_type,productId,second_type,stake,winning,gold,surplus_gold,surplus_bank')
            ->where($condition)
            ->order('id desc')
            ->select();

        //获取游戏名称
        $games= $this->getGameType();



        //获取金币变化类型
        $goldType = $this->getGoldChangeType();


        foreach ($gold as $k=>$val){
            $gold[$k]['createtime'] = date('Y-m-d H:i:s',$val['createtime']);
            $gold[$k]['second_type'] = $goldType[$val['second_type']];
            $gold[$k]['first_type']  = $goldType[$val['first_type']];
            if ($val['first_type'] === '1') {
                $gold[$k]['productId']   = $games[$val['productId']];
            } else {
                $gold[$k]['productId']   = '--';
            }
        }


        $header = array('时间', '类型','游戏ID', '详情', '押注值', '中奖值', '金币变化', '剩余金币', '剩余银行');
        $filename = $uid . '金币变化记录' . date('Y-m-d');
        $res['csv'] = outCsv($header, $gold, $filename);


    }

    /**
     * 获取金币变化类型
     * Author:lbb
     * @return array
     */
    private function getGoldChangeType() :array
    {
        $returnData = [];
        $types = $this->changeGoldType->select();
        foreach ($types as $val) {
            $returnData[$val['id']] = $val['title'];
        }
        return $returnData;
    }

    /**
     * 获取一级分类 金币变化类型
     * Author:lbb
     * @return array
     */
    private function getGoldChangeTypes() :array
    {
        $returnData = [];
        $types = $this->changeGoldType->where('pid=0')->select();
        foreach ($types as $val) {
            $returnData[$val['id']] = $val['title'];
        }
        return $returnData;
    }

    /**
     * 获取游戏名称
     * Author:lbb
     * @return array
     */
    private function getGameType()  :array
    {
        $game = $this->gameTypeModel->field('productid,type_name,name')->select();
        $games = [];
        foreach ($game as $k=>$val){
            $games[$val['productid']] = $val['type_name'].'--'.$val['name'];
        }
        return $games;
    }

}