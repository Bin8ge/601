<?php
/**
 * 游戏玩法数据
 * User: Lbb
 * Date: 2018/7/25 0025
 * Time: 15:55
 */

namespace Admin\Controller\Auto;

use Common\Controller\BaseController;


class GameController extends BaseController
{
    //游戏 game
    private $gameModel;

    //库存变化表
    private $gameStockModel;

    //库存变化表 及时值
    private $gameStockTimelyModel;

    //库存变化表
    private $gameTypeModel;

    //输赢 吃分吐分变化表
    private $roomRecordModel;

    private $gameLogModel;


    /**
     * 初始化
     * GameController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->gameModel = D('game');

        $this->gameTypeModel = D('game_type');

        $this->gameStockModel = D('game_stock');

        $this->gameStockTimelyModel = D('game_stock_timely');

        $this->roomRecordModel = D('room_record');

        $this->gameLogModel = D('game_log');

    }


    /**
     * 游戏玩法数据 查看
     * Author:lbb
     */
    public function index()
    {
        if (IS_AJAX)
        {
            #所有游戏房间的累计数据
            $stock  = $this->gameModel
                ->field('MachineID,ProductID,GameID,JackpotStock,PublicStock,taxStock,win_lose_total,spitting_gold,eating_gold,BenchmarkStock')
                ->order('MachineID asc')
                ->select();

            # 获取游戏名称 类型
            $gameName = $this->getGameRoom();

            # 单日税收 吞吐率
            $where['createtime']  =  array('between',array(strtotime(date('Y-m-d')),strtotime(date('Y-m-d').' 23:59:59')));
            $dayData = $this->roomDayTotal($where);

            # 当日税收
            $dayTax = $this->getDayTax($where);

            $total = [];
            foreach ($stock as $k=>&$val){
                $val['ProductID'] = $gameName[$val['ProductID']];
                $val['GameID'] = $gameName[$val['GameID']];

                #判断没有值
                if ( $val['spitting_gold'] ==='0' ||  $val['eating_gold']==='0') {
                    $val['than'] = 0.000;
                }else{
                    $val['than'] = $val['spitting_gold']/ $val['eating_gold'] ;
                }

                $val['day_lose_win'] = $dayData[$val['MachineID']]['sum_gold'] ?: 0;
                $val['day_than']     = $dayData[$val['MachineID']]['sum_than'] ?: 0;
                $val['day_tax']      = $dayTax[$val['MachineID']]['tax_stock'] ?: 0;

                //计算总计
                $total['lose_win_total'] += $val['win_lose_total'];    //累计输赢
                $total['total_tax']      += $val['taxStock'];          //累计税收
                $total['total_spi']      += $val['spitting_gold'];     //累计吐分
                $total['total_eat']      += $val['eating_gold'];       //累计吃分

                $total['day_lose_win_total'] += $dayData[$val['MachineID']]['sum_gold']; //累计当日输赢
                $total['total_day_tax']      += $val['day_tax'];                         //累计当日税收
                $total['total_day_spi']      += $dayData[$val['MachineID']]['sum_spi']; //累计当日吐分
                $total['total_day_eat']      += $dayData[$val['MachineID']]['sum_eat']; //累计当日吃分

            }




            $total['total_than'] = $total['total_spi']/ $total['total_eat'] ;                            //累计吞吐率
            if ( !$total['total_eat'] ||  !$total['total_spi']) {
                $total['total_than'] = 0.000;                                                            //判断没有值
            }

            $total['day_than'] = $total['total_day_spi']/ $total['total_day_eat'] ;                     //当日吞吐率
            if ( !$total['total_day_spi'] ||  !$total['total_day_eat']){
                $total['day_than'] = 0.000;                                                              //判断没有值
            }


            //模板变量赋值
            $this->assign('list',$stock);
            $this->assign('total',$total);
            $res['content'] = $this->fetch('Auto/game/replace');
            return returnAjax(200,'SUCCESS',$res);
        }
        $this->display();
    }


    /**
     * 历史吞吐列表
     * Author:lbb
     */
    public function history()
    {
        $machineId = I('get.machine_id');
        $this->assign('machine_id',$machineId);

        if (IS_AJAX)
        {
            $startTime = I('get.startTime') ?: date('Y-m-d',strtotime('-15 day'));
            $endTime   = I('get.endTime')   ?: date('Y-m-d');

            $createStart = strtotime($startTime) ;
            $createEnd   = strtotime($endTime.' 23:59:59');

            //公共条件
            $condition['date_time']   = array('between',array($startTime,$endTime));
            $condition['machine_id']  = $machineId;

            #房间输赢 吃分 吐分
            $list  = $this->roomDayTotal($condition,'date_time');

            #当日税收
            $tax = $this->getDayTax($condition,'date_time');

           /* $lists = [];
            foreach ($list as $k=>$val){
                $lists[$val['date_time']] = $val;
                if (!$tax[$val['date_time']]['tax_stock']){
                    $tax[$val['date_time']]['tax_stock'] = 0;
                }
                $lists[$val['date_time']]['tax'] = $tax[$val['date_time']]['tax_stock'];
            }*/

            #总计条件
            if ( I('get.startTime') || I('get.endTime')) {
                $where_total = $condition;
            }else{
                $where_total['machine_id']  = $machineId;
            }

            #总计
            $total = $this->roomTotal($where_total);
            #税收总计
            $total['total_tax'] = $this->taxTotal($where_total);

            $array['gold'] = '0';
            $array['eatinggold'] = '0';
            $array['spittinggold'] = '0';
            $array['than'] = '0';
            $array['spittingnum'] = '0';

            $lists  = $this->dateMap($createStart,$createEnd,$list,$array);

            # 合并当日税收
            foreach ($lists as $k=>$val){
                $lists[$k]['tax'] = $tax[$k]['tax_stock'] ?: 0;
            }

            krsort($lists);
            $this->assign('list',$lists);
            $this->assign('total',$total);

            $res['content'] = $this->fetch('Auto/game/historyreplace');
            return returnAjax(200,'SUCCESS',$res);
        }
        $this->display();
    }


    /**
     * 统计房间总输赢   分组统计
     * Author:lbb
     * @param array $where
     * @param string $group
     * @return array
     */
    public function roomDayTotal($where=[],$group='machine_id') :array
    {
        $roomTotal = $this->roomRecordModel
            ->where($where)
            ->field('date_time,machine_id,sum(gold) as sum_gold,sum(eatinggold) as sum_eat,sum(spittinggold) as sum_spi,sum(spittingnum) as spittingnum,sum(eatingnum) as eatingnum,sum(spinnum) as spinnum')
            ->group($group)
            ->select();
        $roomTotals = [];
        if ($group === 'machine_id') {
            foreach ($roomTotal as $k => $v) {
                $roomTotals[$v['machine_id']] = $v;
                $roomTotals[$v['machine_id']]['sum_than'] = $v['sum_spi'] / $v['sum_eat'];
            }
        }else{

           /* foreach ($roomTotal as $k=>$v){
                $roomTotals[$k] = $v;
                $roomTotals[$k]['eat_than'] = $v['spittingnum']/$v['spinnum'];
                $roomTotals[$k]['than']     = $v['sum_spi']/$v['sum_eat'];
            }*/
            foreach ($roomTotal as $k=>$v){
                $roomTotals[$v['date_time']] = $v;
                $roomTotals[$v['date_time']]['eat_than'] = $v['spittingnum']/$v['spinnum'];
                $roomTotals[$v['date_time']]['than']     = $v['sum_spi']/$v['sum_eat'];
            }
        }
        return $roomTotals;

    }


    /**
     * 查询以天为单位的税收  分组统计
     * Author:lbb
     * @param array $where
     * @param string $group
     * @return array
     */
    public function getDayTax($where=[],$group='machine_id') :array
    {
        $data = $this->gameStockModel
            ->where($where)
            ->field('date_time,machine_id,sum(tax_stock) as tax_stock')
            ->group($group)
            ->select();


        $tax=[];
        if ($group === 'machine_id') {
            foreach ($data as $k => $v) {
                $tax[$v['machine_id']] = $v;
            }
        }else{
            foreach ($data as $k => $v) {
                $tax[$v['date_time']] = $v;
            }
        }
        return $tax;
    }


    /**
     *  获取房间名称
     * Author:lbb
     * @param array $where
     * @return array
     */
    public function getGameRoom($where = []) :array
    {
        $game  = $this->gameTypeModel->where($where)->select();
        $games = [];
        foreach ($game as $k=>$val){
            $games[$val['productid']] = $val['name'];
            $games[$val['type']] = $val['type_name'];
        }
        return $games;
    }


    /**
     * 统计房间总计
     * Author:lbb
     * @param array $where
     * @return mixed
     */
    public function roomTotal($where=[])
    {
        $roomTotal = $this->roomRecordModel->where($where)
            ->field('sum(gold) as gold,sum(eatinggold) as eatinggold ,sum(spittinggold) as spittinggold,sum(eatingnum) as eatingnum,sum(spittingnum) as spittingnum,sum(spinnum) as spinnum ')
            ->select();

        foreach ($roomTotal as $k=>$v){
            $roomTotal[$k]['than']     = is_nan($v['spittinggold'] / $v['eatinggold']) ? 0 : $v['spittinggold'] / $v['eatinggold'];
            $roomTotal[$k]['eat_than'] = is_nan($v['spittingnum'] / $v['spinnum']) ? 0 : $v['spittingnum'] / $v['spinnum'];
        }
        return $roomTotal[0];
    }


    /**
     * 统计税收总计
     * Author:lbb
     * @param array $where
     * @return int
     */
    public function taxTotal($where=[]) :int
    {
        return $this->gameStockModel
            ->where($where)
            ->sum('tax_stock') ?: 0;
    }


    /**
     * 库存变化
     * Author:lbb
     */
    public function stock()
    {

        $machineId = I('get.machine_id');
        $this->assign('machine_id',$machineId);


        //判断切换标签  公共库存  奖池库存
        $field = '';
        $action = I('get.action','public');
        $bench = $this->gameModel->where('MachineID='.$machineId)->field('BenchmarkStock,ProductID')->find();
        $gameInfo = $this->gameTypeModel->where('productid='.$bench['ProductID'])->find();
        $this->assign('game_info',$gameInfo['type_name'].'--'.$gameInfo['name'].'--'.$machineId.'房');
        switch ($action){
            case 'public':
                $field     = 'public_stock';
                $this->assign('benchmark',(int)$bench['BenchmarkStock']);
                break;
            case 'jackpot':
                $field     = 'jackpot_stock';
                break;
        }

        if (IS_AJAX)
        {
            //查询条件

            $startTime = I('get.startTime') ?: date('Y-m-d').' 00';
            $endTime   = I('get.endTime')   ?: date('Y-m-d').' 23';


            $createStart = strtotime($startTime.':00:00');
            $createEnd   = strtotime($endTime.':59:59');


            $condition['createtime'] = array(array('egt',$createStart), array('elt',$createEnd),'and');
            $condition['machine_id'] =$machineId;


            # 输出数据
            $list= $this->gameStockTimelyModel
                ->field("{$field},createtime")
                ->where($condition)
                ->order('id asc')
                ->select();


            //整理数据
            $lists = [];
            foreach ($list as $k => $v) {
                $create[date('H:i:s',$v['createtime'])] = (int)$v[$field]; //整理折现图数据
                $lists[$k]['time']  =  date('Y-m-d H:i:s',$v['createtime']);
                $lists[$k]['total'] =  $v[$field];
            }

            //ksort($create);                          //排序

            $data['time']  = array_keys($create);          //折现图x轴
            $data['total'] = array_values($create);        //折现图y轴
            $data['min']   = min($create);                 //最小值
            $data['max']   = max($create);                 //最大值
            $this->assign('list',$lists);                  //结果列表

            $data['content'] = $this->fetch('Auto/game/stockreplace');
            return returnAjax(200,'SUCCESS',$data);
        }

        $this->display();
    }


    /**
     * 生成断层时间
     * Author:lbb
     * @param $start
     * @param $end
     * @param $array
     * @param array $val
     * @return array
     */

    public function dateMap($start,$end,$array,$val=[]) :array
    {
        $dates = range($start, $end, 86400);
        $dates = array_map(function ($v) {return date('Y-m-d', $v);}, $dates);
        $zero = array_fill_keys($dates,$val);
        if (!$array) {
            $array = [];
            $result = array_merge($array,$zero);
        }else{
            $result = array_merge($zero,$array);
        }
        return $result;
    }


    /**
     * 房间设置
     * Author:lbb
     */
    public function edit()
    {

        if (IS_AJAX){
            $data   = $_POST;

            //条件
            $condition['MachineID'] = $data['machine_id'];
            unset($data['machine_id']);

            //修改配置
            $status = $this->gameModel->where($condition)->save($data);

            //写入后台日志
            $this->adminLogModel->record($_POST);

            if($status==='false')
            {
                return returnAjax(400,'保存失败~~');
            }else{

                # 发送服务器
                $server_data['Enable']     = (int)$data['Enable'];
                $server_data['MaxBot']     = (int)$data['MaxBot'];
                $server_data['MaxPlayer']  = (int)$data['MaxPlayer'];
                $server_data['MachineID']  = (int)$_POST['MachineID'];
                send_server($server_data,'/LoadStockPer.php');

                return returnAjax(200,'SUCCESS');
            }
        }else{
            $condition['MachineID']  = I('get.machine_id');

            //获取游戏名称
            $game      = $this->getGameRoom();

            //获取游戏
            $game_list = $this->gameModel
                ->field('Enable,MaxPlayer,MaxBot,MachineID,ProductID,GameID,BenchmarkStock')
                ->where($condition)
                ->find();
            $game_list['name'] = $game[$game_list['GameID']]. ' ' . $game[$game_list['ProductID']] .'  '. $game_list['MachineID'];

            $this->assign('list',$game_list);
        }
        $this->display();
    }


    /**
     * 增加库存
     * Author:lbb 旧版本
     */
    public function adds()
    {
        if (IS_AJAX) {
            $data = $_POST;
            if (!$data['runtime']){
                return returnAjax(400,'执行时间不可为空~~');
            }
            if (!$data['public_stock'] || !is_numeric($data['public_stock'])){
                return returnAjax(400,'库存值不可为空或非法参数~~');
            }

            $data['runtime']     = strtotime($data['runtime']);
            $data['createtime']  = time();
            $data['status']      = 0;
            $data['admin_id']    = $this->auth->id;
            $status = $this->gameLogModel->add($data);

            //写入后台日志
            $this->adminLogModel->record($_POST);
            if ($status) {
                return returnAjax(200,'SUCCESS');
            }else {
                return returnAjax(400,'FAlSE');
            }
        }else{
            $machine_id = I('get.machine_id');
            $where['MachineID'] = $machine_id;
            $game  = $this->gameModel->where($where)->field('ProductID')->find();
            $where_type['productid'] = $game['ProductID'];
            $info  = $this->gameTypeModel->where($where_type)->find();
            $name = $info['type_name'].' '.$info['name'].' '.$machine_id.'房';

            $this->assign('name',$name);
            $this->assign('info',$info);
            $this->assign('machine_id',$machine_id);
            $this->display();
        }

    }

    /**
     * 增加库存
     * Author:lbb
     */
    public function add()
    {
        if (IS_AJAX) {
            $data = $_POST;

            if (!$data['public_stock'] || !is_numeric($data['public_stock'])){
                return returnAjax(400,'库存值不可为空或非法参数~~');
            }

            $data['runtime']     = strtotime($data['runtime'])?:0;
            $data['createtime']  = time();
            $data['status']      = 0;
            $data['admin_id']    = $this->auth->id;
            #判断是否立即生效
            if (!$data['runtime']){
                $data['status']    = 1;
            }

            $status = $this->gameLogModel->add($data);
            if (!$data['runtime']){
                # 发送服务器
                $server_data['game_id'] = (int)$data['game_id'];
                $server_data['product_id'] = (int)$data['product_id'];
                $server_data['machine_id'] = (int)$data['machine_id'];
                $server_data['public_stock'] = (string)$data['public_stock'];
                send_server($server_data, '/LoadStockPer.php');
            }
            //写入后台日志
            $this->adminLogModel->record($_POST);
            if ($status) {
                return returnAjax(200,'SUCCESS');
            }else {
                return returnAjax(400,'FAlSE');
            }
        }else{
            $machine_id = I('get.machine_id');
            $where['MachineID'] = $machine_id;
            $game  = $this->gameModel->where($where)->field('ProductID')->find();
            $where_type['productid'] = $game['ProductID'];
            $info  = $this->gameTypeModel->where($where_type)->find();
            $name = $info['type_name'].' '.$info['name'].' '.$machine_id.'房';

            $this->assign('name',$name);
            $this->assign('info',$info);
            $this->assign('machine_id',$machine_id);
            $this->display();
        }

    }


    /**
     * 添加房间
     * Author:lbb
     */
    public function addRoom()
    {
        if (IS_AJAX) {
            $data = I('post.resource',[], 'strip_tags');
            //验证数据格式
            foreach ($data as $v) {
                if ((int)$v < 0) {
                    return  returnAjax('400','非法参数,请重新提交~~');
                    break;
                }
            }

            #查gameID
            $where['productid'] = $data['ProductID'];
            $gameInfo  = $this->gameTypeModel->where($where)->field('type')->find();
            $data['GameID'] = $gameInfo['type'];

            #判断库存比例是否为100
            if ($data['public_stock']+$data['jackpot_stock']+$data['tax_stock'] !== 100){
                return  returnAjax('400','库存分配比例不等于100~~');
            }

            #将库存比例转为json
            $stock['public_stock']  = $data['public_stock'];
            $stock['jackpot_stock'] = $data['jackpot_stock'];
            $stock['tax_stock']     = $data['tax_stock'];
            unset($data['public_stock'],$data['jackpot_stock'],$data['tax_stock']);
            $data['StockRatio'] = json_encode($stock);

            //新增数据
            $msg = $this->gameModel->add($data);

            //写入后台日志
            $this->adminLogModel->record($data);

            if ($msg){
                # 发送服务器
                $server_data['game_id']        = (int)$data['GameID'];
                $server_data['product_id']     = (int)$data['ProductID'];
                $server_data['machine_id']     = (int)$data['MachineID'];

                $server_data['public_stock']   = (int)$data['PublicStock'];
                $server_data['jackpot_stock']  = (int)$data['JackpotStock'];
                $server_data['tax_stock']      = 0;

                $server_data['public_ratio']   = (int)$stock['public_stock'];
                $server_data['jackpot_ratio']  = (int)$stock['jackpot_stock'];
                $server_data['tax_ratio']      = (int)$stock['tax_stock'];

                $server_data['max_player']       = (int)$data['MaxPlayer'];
                $server_data['max_bot']          = (int)$data['MaxBot'];
                $server_data['stock_breakdown']  = (int)$data['StockBreakdown'];

                send_server($server_data,'/AddNewMatchine.php');

                return  returnAjax('200','添加成功');
            }else{
                return  returnAjax('400','添加失败,请重新提交~~');
            }


        }else{
            $game = $this->getGameRoomSelect();
            $this->assign('game',$game);
            //表单验证配置
            $this->assign('fromValidate', json_encode([
                'resource[ProductID]' => 'required;',
                'resource[MachineID]' => 'required;',
                'resource[PublicStock]' => 'required;',
                'resource[JackpotStock]' => 'required;',
                'resource[public_stock]' => 'required;',
                'resource[jackpot_stock]' => 'required;',
                'resource[tax_stock]' => 'required;',
                /*'resource[BonusGameMaxMultiples]' => 'required;',*/
                'resource[MaxPlayer]' => 'required;',
                'resource[MaxBot]' => 'required;',
                'resource[BenchmarkStock]' => 'required;',
                'resource[StockBreakdown]' => 'required;',
            ]));
            $this->assign('form_id', 'addform');
        }
        $this->display();

    }

    /**
     * 获取游戏  select 使用
     * Author:lbb
     * @param array $where
     * @return array
     */
    private function getGameRoomSelect($where = []) :array
    {
        $where['productid'] = array('gt',0);
        $game  = $this->gameTypeModel->where($where)->select();
        $games = [];
        foreach ($game as $k=>$val){
            $games[$k]['productid'] = $val['productid'];
            $games[$k]['game_name'] = $val['type_name'].'--'.$val['name'].'('.$val['type'].'--'.$val['productid'].')';
        }
        return $games;
    }


}