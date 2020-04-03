<?php
/**
 * 平台当日明细
 * User: Lbb
 * Date: 2018/7/21 0021
 * Time: 14:48
 */

namespace Admin\Controller\Data;

use Common\Controller\BaseController;

class PlatformDayController extends BaseController
{
    //user 表
    private $userModel;

    //点控
    private $pointModel;

    //账户表
    private $userAccountView;

    //在线
    private $onlineModel;

    //公共控制器 方法
    private  $commAction;

    //房间表  统计系统输赢
    private $roomRecordModel;

    //游戏表
    private $game;

    //游戏库存记录表
    private $gameStock;

    //交易表
    private $sendTakeModel;

    //登录
    private $logModel;

    //gm
    private $accountLog;



    public function __construct()
    {
        parent::__construct();

        $this->userModel       = D('user');

        $this->userAccountView = D('user_account');

        $this->onlineModel     = D('online');

        $this->pointModel      = D('point_control');

        $this->roomRecordModel = D('room_record');

        $this->sendTakeModel   = D('send_take');

        $this->game            = D('game');

        $this->gameStock       = D('game_stock');

        $this->logModel        = D('userLog');

        $this->accountLog      = D('account_log');

        $this->commAction      = new CommonController();

    }


    /**
     * Author:lbb
     */
    public function index() :void
    {
        //获取条件
        $whereTime  =  $this->commAction->commWhere();
        //公共条件
        $condition['createtime'] =  $whereTime['same_day'];

        # 系统输赢
        $data['lose_win']  = $this->loseWin($condition);

        # 点控输赢
        $data['point']     = $this->point($condition);

        # 游戏税收
        $data['tax']    = $this->gameStock($condition);
        $data['public'] = $this->gamePublic();

        # 交易税
        $data['trading_tax'] = $this->sendTakeOut($condition);

        #单日吞吐
        $data['day_than']    = $this->roomTotal($condition);

        # 金币统计
        $data = array_merge($data,$this->gold());

        # GM添加
        $data['gm_gold'] = $this->accountLog($condition);

        # 赠送接收
        $data = array_merge($data,$this->sendTake($condition));

        //在线情况
        $data = array_merge($data,$this->online($condition));
        $data = array_merge($data,$this->onlineGroup());

        #当日登录
        $data['day_log'] = $this->log($condition);

        #日注册
        $data = array_merge($data,$this->register($condition));
        

        #总注册
        $register =$this->register();


        $data['total_and'] = $register['ad'];
        $data['total_ios'] = $register['ios'];
        $data['total_pc']  = $register['pc'];
        $data['total_reg'] = $register['total'];

        $this->assign('data',$data);
        $this->display();
    }


    /**
     * 系统输赢
     * Author:lbb
     * @param array $where
     * @return int
     */
    public function loseWin($where = []) :int
    {
        return $this->roomRecordModel->where($where)->sum('gold') ?: 0;
    }


    /**
     * 点控输赢
     * Author:lbb
     * @param array $where
     * @return int
     */
    public function point($where = []) :int
    {
        #系统赢  控玩家输
        $where_point_win['type'] = 1;
        $where_point_win = array_merge($where_point_win, $where);
        $point_win = $this->pointModel->where($where_point_win)->sum('progress') ?: 0;

        #系统输  控玩家赢
        $where_point_lose['type'] = 0;
        $where_point_lose = array_merge($where_point_lose, $where);
        $point_lose = $this->pointModel->where($where_point_lose)->sum('progress') ?: 0;
        $point = $point_win - $point_lose;
        return $point ?: 0;
    }


    /**
     * 交易税统计
     * Author:lbb
     * @param array $where
     * @return int
     */
    public function sendTakeOut($where = []) :int
    {
        $where_take = $where;
        $where_take['type'] = 'send';
        return D('send_take')->where($where_take)->sum('tax_gold') ?: 0;
    }


    /**
     * 游戏税收
     * Author:lbb
     * @param array $where
     * @return int
     */
    public function gameStock($where = []): int
    {
        return $this->gameStock->where($where)->sum('tax_stock') ?: 0;
    }

    /**
     * 游戏库存  公共库存
     * Author:lbb
     * @return int
     */
    public function gamePublic() :int
    {
        return $this->game->sum('PublicStock') ?: 0;
    }

    /**
     * 吞吐率
     * Author:lbb
     * @param array $where
     * @return float|int
     */
    public function roomTotal($where = [])
    {
        $total = $this->roomRecordModel
            ->where($where)
            ->field('sum(eatinggold) as sum_eat,sum(spittinggold) as sum_spi')
            ->find();
        if ($total['sum_spi']>0 && $total['sum_eat']>0){
            return number_format($total['sum_spi'] / $total['sum_eat'],3);
        }else{
            return 0.0;
        }
    }


    /**
     * 金币
     * Author:lbb
     * @param array $where
     * @return array
     */
    public function gold($where = []) :array
    {
        $gold = $this->userAccountView
            ->where($where)
            ->field('level,sum(gold) as gold,sum(bank) as bank')
            ->group('level')
            ->select();
        $golds = [];
        foreach ($gold as $k => $val) {
            if ($val['level'] === '0') {
                $golds['people_gold'] += $val['gold'];
                $golds['people_bank'] += $val['bank'];
            } else {
                $golds['vip_gold'] += $val['gold'];
                $golds['vip_bank'] += $val['bank'];
            }
        }
        return $golds;
    }


    /**
     * 赠送接收
     * Author:lbb
     * @param array $where
     * @return array
     */
    public function sendTake($where = []): array
    {
        # 总赠送 接收
        $where_comm = $where;
        $where_comm['is_back'] = 0;

        #代理赠送代理   代理接收代理
        $where_vip_vip = $where_comm;
        $where_vip_vip['send_level'] = array('gt', '0');
        $where_vip_vip['take_level'] = array('gt', '0');
        $vip = $this->sendTakeModel
            ->where($where_vip_vip)
            ->field('sum(send_gold) as total_send,sum(take_gold) as total_take')
            ->find();
        $data['vip_send_vip'] = $vip['total_send'];
        $data['vip_take_vip'] = $vip['total_take'];

        #代理赠送玩家
        $where_vip_send_player = $where_comm;
        $where_vip_send_player['send_level'] = array('gt', '0');
        $where_vip_send_player['take_level'] = 0;
        $where_vip_send_player['type']       = 'send';
        $data['vip_send_player'] = $this->sendTakeModel
            ->where($where_vip_send_player)
            ->sum('send_gold') ?: 0;

        #代理接收玩家
        $where_vip_take_player = $where_comm;
        $where_vip_take_player['send_level'] = 0;
        $where_vip_take_player['take_level'] = array('gt', '0');
        $where_vip_take_player['type'] = 'take';
        $data['vip_take_player'] = $this->sendTakeModel
            ->where($where_vip_take_player)
            ->sum('take_gold') ?: 0;


        #代理总赠送   代理总接收
        $data['total_vip_send'] = $data['vip_send_vip'] + $data['vip_send_player'];
        $data['total_vip_take'] = $data['vip_take_vip'] + $data['vip_take_player'];


        #赠收顺差   赠收比
        $data['diff_total'] = $data['vip_send_player'] - $data['vip_take_player'];
        $data['than']       = is_nan($data['vip_take_player'] / $data['vip_send_player']) ? 0.000 : $data['vip_take_player'] / $data['vip_send_player'];


        #玩家赠送代理
        $where_player_send_vip = $where_comm;
        $where_player_send_vip['send_level'] = 0;
        $where_player_send_vip['take_level'] = array('gt', '0');
        $where_player_send_vip['type']       = 'send';
        $data['player_send_vip']  = $this->sendTakeModel
            ->where($where_player_send_vip)
            ->sum('send_gold') ?: 0;

        #玩家赠送玩家
        $where_player_send_player = $where_comm;
        $where_player_send_player['send_level'] = 0;
        $where_player_send_player['take_level'] = 0;
        $where_player_send_player['type']       = 'send';
        $data['player_send_player']  = $this->sendTakeModel
            ->where($where_player_send_player)
            ->sum('send_gold') ?: 0;

        #玩家接收代理
        $where_player_take_vip = $where_comm;
        $where_player_take_vip['send_level'] =  array('gt', '0');
        $where_player_take_vip['take_level'] = 0;
        $where_player_take_vip['type']       = 'take';
        $data['player_take_vip']  = $this->sendTakeModel
            ->where($where_player_take_vip)
            ->sum('take_gold') ?: 0;

        #玩家接收玩家
        $where_player_take_player = $where_comm;
        $where_player_take_player['send_level'] = 0;
        $where_player_take_player['take_level'] = 0;
        $where_player_take_player['type']       = 'take';
        $data['player_take_player']  = $this->sendTakeModel
            ->where($where_player_take_player)
            ->sum('take_gold') ?: 0;

        return $data ?: [];
    }


    /**
     * 在线人数
     * Author:lbb
     * @param array $where
     * @return array
     */
    public function online($where=[]) :array
    {
        $online = $this->onlineModel
            ->where($where)
            ->field('max(OnlineCount) as max_total,max(commonnum) as max_people')
            ->find();
        return $online ?: [];
    }


    /**
     * 在线人数 分组统计  普通用户 VIP用户
     * Author:lbb
     */
    public function onlineGroup() :array
    {
        $where['onLine'] = 1;
        $data = $this->userModel
            ->where($where)
            ->field('count(uid) as total_online,count(level=0 or NULL) as people_online')
            ->find();
        return $data ?: [];
    }


    /**
     * 登录统计
     * Author:lbb
     * @param array $where
     * @return int
     */
    public function log($where=[])  :int
    {
        $where['type'] = 'login';
        return $this->logModel->where($where)->count('distinct uid') ?: 0;
    }


    /**
     * 按设备统计注册人数 及总注册人数
     * Author:lbb
     * @param array $where
     * @return array
     */
    public function register($where=[]) :array
    {
        $register = $this->userModel
            ->where($where)
            ->field('count(equipment_type=1 OR NULL) as ad,count(equipment_type=2 OR NULL) as ios,count(equipment_type=3 OR NULL) as pc,count(uid) as total')
            ->find();
        return $register ?: [];
    }

    /**
     * gm 添加金币
     * Author:lbb
     * @param array $where
     * @return int
     */
    private function accountLog($where = []): int
    {
        return $this->accountLog->where($where)->sum('handleNum') ?: 0;
    }









}