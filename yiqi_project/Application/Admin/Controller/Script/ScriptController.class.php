<?php
/**
 * 脚本文件   统计每日
 * User: Lbb
 * Date: 2018/8/6 0006
 * Time: 15:17
 */

namespace Admin\Controller\Script;

use Admin\Controller\Data\PlatformDayController;


class ScriptController
{
    //每日统计
    private $statistical;

    //gm 添加金币
    private $accountLog;

    //交易表
    private $sendTakeModel;

    //当日统计控制器
    private $platformDayAction;

    //输赢 吃分吐分变化表
    private $roomRecordModel;

    //增加库存表
    private $gameLogModel;

    #系统配置表
    private $sysModel;

    #会员明细表
    private $teamMemberModel;

    #账户表
    private $account;

    #账户视图
    private $userAccountView;

    #账户视图
    private $newModel;

    private $userLogModel;

    private $pointModel;

    private $handleLogModel;

    private $onlineModel;

    private $vipDayModel;

    public  $localIp = '47.98.121.138';
    public  $ip;


    public function __construct()
    {

        $this->platformDayAction = new PlatformDayController();

        $this->statistical     = D('statistical');

        $this->accountLog      = D('accountLog');

        $this->sendTakeModel   = D('send_take');

        $this->roomRecordModel = D('room_record');

        $this->gameLogModel    = D('game_log');

        $this->sysModel        = D('sys_conf');

        $this->teamMemberModel = D('team_member');

        $this->account         = D('account');

        $this->userAccountView = D('user_account');

        $this->newModel        = D('users');

        $this->userLogModel    = D('user_log');

        $this->pointModel      = D('PointControl');

        $this->handleLogModel  = D('HandleLog');

        $this->onlineModel  = D('online');

        $this->vipDayModel = D('vip_day_trading');

        $this->ip = get_client_ip();

        if ($this->ip !== $this->localIp) {
            exit('你没有权限访问');
        }

    }

    /**
     *每日数据统计 脚本
     * Author:lbb
     * @throws \Exception  statistics
     */
    public function statistics(): void
    {
        //公共条件
        $data['day_time'] = date('Y-m-d', strtotime('-1 day'));
        $where_today['day_time'] = $data['day_time'];
        if ($this->statistical->where($where_today)->find()) {
            $this->writeLog(date('Y-m-d H:i:s') . ' 数据库已有昨天的统计数据~~~');
            exit;
        }

        $data['createtime'] = strtotime($data['day_time']);

        $condition['createtime'] = array('between', array(strtotime($data['day_time'] . ' 00:00:00'), strtotime(date('Y-m-d'))));

        #注册
        $reg = $this->platformDayAction->register($condition);

        $data['reg_android'] = $reg['ad'];
        $data['reg_ios'] = $reg['ios'];
        $data['reg_pc'] = $reg['pc'];
        $data['reg'] = $reg['total'];

        #登录
        $data['login'] = $this->platformDayAction->log($condition);

        #最高在线
        $online = $this->platformDayAction->online($condition);
        $data['max_online_vip'] = ($online['max_total'] ?: 0) - ($online['max_people'] ?: 0);
        $data['max_online_player'] = $online['max_people'] ?: 0;

        #gm 添加金币
        $data['gm_gold'] = $this->accountLog($condition);

        #玩家 VIP 总金币
        $gold = $this->platformDayAction->gold();
        $data['player_gold'] = $gold['people_gold'] + $gold['people_bank'];
        $data['vip_gold'] = $gold['vip_gold'] + $gold['vip_bank'];

        #vip 赠送 接收 顺差 增收比
        $data = array_merge($data, $this->sendTake($condition));

        #系统输赢  吃分 吐分 吞吐率
        $data = array_merge($data, $this->roomTotal($condition));

        #点控输赢
        $data['point_lose_win'] = $this->platformDayAction->point($condition);

        #公共库存
        $data['public_stock'] = $this->platformDayAction->gamePublic();


        #游戏税收
        $data['game_tax'] = $this->platformDayAction->gameStock($condition);

        #交易税统计
        $data['trade_tax'] = $this->platformDayAction->sendTakeOut($condition);

        try {
            $status = $this->statistical->add($data);
            if (!$status) {
                $log = date('Y-m-d H:i:s') . ' ' . M()->getLastSql();
            } else {
                $log = date('Y-m-d H:i:s') . ' SUCCESS';
            }

            $url = 'script_log/statistics-' . date('Y-m') . '.log';
            $this->writeLog($log, $url);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * 增减库存脚本
     * Author:lbb
     */
    public function stock(): void
    {
        $time = time();
        $condition['status'] = 0;
        $condition['runtime'] = array('elt', $time);
        $data = $this->gameLogModel->where($condition)->field('id,game_id,product_id,machine_id,public_stock')->select();

        $url = 'script_log/stock-' . date('Y-m') . '.log';

        if ($data) {
            foreach ($data as $val) {
                $where['id'] = $val['id'];
                $saveData['status'] = 1;
                $saveData['update_time'] = time();
                $msg = $this->gameLogModel->where($where)->save($saveData);
                if ($msg) {
                    # 发送服务器
                    $server_data['game_id'] = (int)$val['game_id'];
                    $server_data['product_id'] = (int)$val['product_id'];
                    $server_data['machine_id'] = (int)$val['machine_id'];
                    $server_data['public_stock'] = (string)$val['public_stock'];
                    send_server($server_data, '/LoadStockPer.php');
                }
                $this->writeLog(date('Y-m-d H:i:s') . '--' . json_encode($val), $url);
            }

        } else {
            $this->writeLog(date('Y-m-d H:i:s') . '--Success Run', $url);
        }


    }

    /**
     * 遍历发送短信 通知负责人
     * Author:lbb
     */
    public function sendMobile(): void
    {
        #6  12  18  23     0 6,12,18,23 * * * /var/www/shell/send.sh
        # 手机号数组
        $mobileArray = [
            '15711499290',
            '18601980534',
            '13240969867',
            '18511904882',
        ];

        #增收比
        $where['createtime'] = array('egt', strtotime(date('Y-m-d')));
        $data = $this->sendTake($where);
        $than = $this->roomTotal($where);

        foreach ($mobileArray as $value) {
            $this->sending($value, number_format($data['trade_than'], 2), number_format($than['than'], 2));
        }

    }

    /**
     * 金币异常
     * Author:lbb
     */
    public function gold(): void
    {
        #日志存放目录
        $url = 'script_log/gold-' . date('Y-m') . '.log';

        $condition['gold'] = array('lt',-1);
        $users = $this->account->where($condition)->select();

        if ( !$users ) {
            $this->writeLog(  date('Y-m-d H:i:s') . '--' . 'not found', $url);
            exit;
        }

        foreach ( $users as $val) {
            if ( D('user')->save(['uid' => $val['uid'], 'is_send_presend' => 0]) ) {

                //封装写入数组
                $user_data = [
                    'uid' => $val['uid'],
                    'title' => '禁止赠送',
                    'admin_id' => 28
                ];

                //写入数据
                $this->handleLogModel->record($user_data, 'no_present', [
                    'is_send_presend' => 0
                ]);

                //发送服务器
                $param = array(
                    'userid' => (int)$val['uid'],
                    'propid' => 12,            //赠送权限
                    'propvalue' => '0',
                );
                send_server($param, '/SetUserProp.php');

            }
        }

        # 手机号数组
        $mobileArray = [
            '15711499290',
            '18601980534',
            '13240969867',
            '13141165543',
            '18610984774',
        ];
        foreach ($mobileArray as $value) {
            $this->sending($value,   '异常');
        }


        $this->writeLog(  date('Y-m-d H:i:s') . '--' . 'success', $url);

    }

    /**
     * Notes: 在线人数检测
     * User: Lbb
     * Date: 2019/9/13
     * Time: 19:19
     */
    public function online () :void
    {
        #日志存放目录
        $url = 'script_log/online-' . date('Y-m') . '.log';
        $switch = $this->sysModel->where('groupId=1076')->getField('value');
        if ($switch === '0') {
            $this->writeLog(  date('Y-m-d H:i:s') . '--' . 'close', $url);
            exit;
        }
        $vip = $this->sysModel->where('groupId=1074')->getField('value');
        $player_diff =  $this->sysModel->where('groupId=1075')->getField('value');
        $data = $this->onlineModel->order('id desc')->limit(4)->select();
        if ($data[0]['vipnum'] <$vip  || ($data[3]['commonnum'])-$data[0]['commonnum']>=$player_diff){
            # 手机号数组
            $mobileArray = [
                '15711499290',
                '18601980534',
                '13240969867',
                '13141165543',
                '18610984774',
                '18511904882',
            ];
            foreach ($mobileArray as $value) {
                $this->sending($value,   '在线');
            }
            $this->writeLog(  date('Y-m-d H:i:s') . '--' . 'success', $url);
        }


    }

    /**
     * 返利数据 每日设置默认值
     * Author:lbb
     */
    public function rebate(): void
    {
        $url = 'script_log/rebate-' . date('Y-m') . '.log';
        $groupIds = '1027,1028';
        $where['groupId'] = ['in', $groupIds];
        $list = $this->sysModel->where($where)->select();
        if ($list[0]['value'] === '1') {
            $saveData['rebate_times'] = $list[1]['value'];
            $saveData['rebate_day'] = 0;
            $condition['level'] = array('gt', 0);
            $status = $this->teamMemberModel->where($condition)->save($saveData);
            if ($status === 'false') {
                $this->writeLog(date('Y-m-d H:i:s') . '-- ERROR ' . M()->getLastSql(), $url);
            } else {
                $this->writeLog(date('Y-m-d H:i:s') . '-- SUCCESS', $url);
            }
        }
    }

    /**
     * 推广基金
     * Author:lbb
     */
    public function fund(): void
    {
        # http://47.98.121.138/Admin/script/script/fund
        $start = time();

        #日志存放目录
        $url = 'script_log/fund-' . date('Y-m') . '.log';


        #推广基金开关
        $where['groupId'] = 1036;
        $switch = $this->sysModel->where($where)->field('value')->find();
        if ($switch['value'] === '0') {
            $this->writeLog(time() - $start . '-- ' . date('Y-m-d H:i:s') . '--' . 'close', $url);
            /*   return returnAjax('400','推广基金已关闭~~~');*/
            exit;
        }

        # 查找配置  玩家输赢比例
        $where['groupId'] = 1040;
        $sys = $this->sysModel->where($where)->field('value')->find();
        $rate = $sys['value'] / 100;

        #查符合条件的代理
        $condition['daily_time'] = array('gt', strtotime(date('Y-m-d')));
        $condition['daily_stake'] = array('gt', 0);
        $condition['RecommendedUID'] = array('gt', 0);

        $users = $this->account
            ->field('sum(daily_stake) as total,RecommendedUID')
            ->where($condition)
            ->group('RecommendedUID')
            ->select();

        $addData = [];
        $logData = [];
        foreach ($users as $k => $val) {
            # 修改操作
            if ($val['RecommendedUID'] === '0') {
                continue;
            } else {
                $where_user['uid'] = $val['RecommendedUID'];
                /*$foundGold = -($val['total'] * $rate);
                $sql = "UPDATE `yq_account` SET `FoundGold`=FoundGold+{$foundGold} WHERE `uid` = {$val['RecommendedUID']}";
                M()->query($sql);*/
                $status = $this->account->where($where_user)->setInc('FoundGold', $val['total'] * $rate);
                $addData[] = [
                    'uid' => $val['RecommendedUID'],
                    'fund_change' => $val['total'] * $rate,
                    'fund_type' => 2,
                    'createtime' => time(),
                ];
                $logData[] = [
                    'uid' => $val['RecommendedUID'],
                    'daily_stake' => $val['total'],
                    'rate' => $rate,
                    /* 'sql' => M()->getLastSql(),*/
                    'status' => $status,
                ];
            }
        }

        #添加操作
        D('fund')->addAll($addData);
        $this->writeLog(time() - $start . '-- ' . date('Y-m-d H:i:s') . '--' . json_encode($logData), $url);
        /*if ($users) {
            return returnAjax('200','运行成功~~~');
        }else{
            return returnAjax('200','没有符合条件的玩家~~~');
        }*/


    }

    /**
     * 输赢返利
     * Author:lbb
     */
    public function agent(): void
    {
        # http://47.98.121.138/Admin/script/script/agent
        $start = time();
        #日志存放目录
        $url = 'script_log/agent-' . date('Y-m') . '.log';


        #推广基金开关
        $where['groupId'] = 1056;
        $switch = $this->sysModel->where($where)->field('value')->find();
        if ($switch['value'] === '0') {
            $this->writeLog(time() - $start . '-- ' . date('Y-m-d H:i:s') . '--' . 'close', $url);
            exit;
        }

        # 查找配置  玩家输赢比例
        $where['groupId'] = 1057;
        $sys = $this->sysModel->where($where)->field('value')->find();
        $rate = $sys['value'] / 100;

        #查符合条件的代理
        $condition['daily_time'] = array('gt', strtotime(date('Y-m-d')));
        $condition['RecommendedUID'] = array('gt', 0);
        $condition['daily_gold'] = array('lt', 0);
        $condition['level'] = array('gt', 0);

        $users = $this->account
            ->alias('a')
            ->field('sum(daily_gold) as total,RecommendedUID')
            ->join('left join yq_user b on b.uid = a. RecommendedUID ')
            ->where($condition)
            ->group('RecommendedUID')
            ->select();


        $addData = [];
        $logData = [];
        foreach ($users as $k => $val) {
            # 修改操作

            $where_user['c.uid'] = $val['RecommendedUID'];
            $where_user['level'] = array('gt', 0);
            $users = $this->account
                ->alias('c')
                ->field('RecommendedUID as r_uid')
                ->join('left join yq_user d on d.uid = c.RecommendedUID ')
                ->where($where_user)
                ->find();

            if ($users['r_uid'] > 0) {
                $where_update['uid'] = $users['r_uid'];
                $status = $this->account->where($where_update)->setInc('FoundGold', abs($val['total']) * $rate);

                $addData[] = [
                    'uid' => $users['r_uid'],
                    'fund_change' => abs($val['total']) * $rate,
                    'fund_type' => 4,
                    'createtime' => time(),
                ];

                $logData[] = [
                    'uid' => $users['r_uid'],
                    'lose_win' => $val['total'],
                    'rate' => $rate,
                    /* 'sql'      => M()->getLastSql(),*/
                    'status' => $status,
                ];
            }
        }

        #添加操作
        D('fund')->addAll($addData);
        $this->writeLog(time() - $start . '-- ' . date('Y-m-d H:i:s') . '--' . json_encode($logData), $url);
    }

    /**
     * 玩家金币总量 短信
     * Author:lbb
     */
    public function cordon(): void
    {
        $rate = 100000000;   //比例
        $where['groupId'] = 1064;

        $sys = $this->sysModel->where($where)->field('value')->find();
        $playerGold = $sys['value'] / $rate;    //20亿

        $condition['level'] = 0;
        $gold = $this->userAccountView
            ->where($condition)
            ->field('sum(gold) as gold,sum(bank) as bank')
            ->find();
        $data = number_format(array_sum($gold) / $rate, 3);
        # 手机号数组
        $mobileArray = [
            '15711499290',
            '18601980534',
            '13240969867',
            '13141165543',
            '18610984774',
        ];
        if ($data > $playerGold) {
            foreach ($mobileArray as $value) {
                $this->sending($value, $data . '亿');
            }
        }
    }

    /**
     * Notes: 自动点控脚本
     * User:  Lbb
     * Date:  2019/9/16
     * Time: 15:57
     */
    public function brush(): void
    {

        set_time_limit(0);
        $start = time();

        #日志存放目录
        $url = 'script_log/brush-' . date('Y-m') . '.log';

        # 获取配置参数
        $param = $this->getParam();

        #开关
        if ($param['1065'] === 0 && $param['1068'] === 0 ) {
            $this->writeLog(time() - $start . '-- ' . date('Y-m-d H:i:s') . '--' . 'close', $url);
            exit;
        }

        $macNum     = $param['1069'];   # 设备关联数
        $lose_win   = $param['1070'];   # 输赢条件
        $point_gold = $param['1071'];   # 上点控金额

        # 在线玩家
        $data = $this->onlinePlayer();


        //如果数据存在 则将统计信息放入
        if ($data) {
            $accord= [];
            foreach ($data as $key => $val) {

                #执行关联查询
                $accordData = $this->accord($val['uid']);
                $userData   = $accordData['userData'];
                $users      = $accordData['users'];

                #判断满足控输  控赢得判断
                if ($userData['total_num'] >= $macNum && $userData['total_lose_win'] >= $lose_win) {
                    if ($param['1065'] === 0) {
                        continue;
                    }
                    $accord['lose'][] = $val['uid'];
                    foreach ($users as $k => $v) {
                        #执行点控操作  控输
                        $this->point($v['uid'],$point_gold);
                    }
                } elseif ( $userData['total_lose_win']<0  ) {
                    if ($param['1068'] === 0) {
                        continue;
                    }
                    # 查询是否有次数
                    $isPointNum = $this->isPointNum($val['uid']);

                    # 判断是否有控赢次数
                    if ($isPointNum > 0) {
                        # 获取点控金额
                        $pointWinGold = $this->getPointWin((int)$userData['total_lose_win']);
                        if ($pointWinGold===0) {
                            continue;
                        }else{
                            # 获取点控方案
                            $plan = $this->getPointNum($val['uid']);
                            if ($plan === 0) {
                                continue;
                            }
                            $this->point($val['uid'],$pointWinGold,0,$plan);
                            $this->editPointNum($val['uid']);
                            $accord['win'][] = $val['uid'];
                        }
                    } else {
                        continue;
                    }
                } else {
                    continue;
                }
            }
            $this->writeLog(time() - $start . '-- ' . date('Y-m-d H:i:s') . '--' . json_encode($accord), $url);
        }

    }

    /**
     * Notes: account  自动清零 00:00
     * User: Lbb
     * Date: 2019/9/16
     * Time: 17:18
     */
    public function zero() :void
    {
        #日志存放目录
        $url = 'script_log/zero-' . date('Y-m') . '.log';
        $data['receiveprientNum'] = 0;
        $data['IsPoint'] = 0;
        $data['point_num'] = 0;
        $msg = $this->account->where('1=1')->save($data);
        $this->writeLog( date('Y-m-d H:i:s') . '--' . json_encode($msg), $url);
    }

    /**
     * Notes: 统计每日代理交易 00:13
     * User: Lbb
     * Date: 2019/9/25
     * Time: 16:35
     */
    public function vipTrading(): void
    {
        $condition['is_vip'] = 0;
        $condition['level'] = array('gt',0);
        $condition['is_back'] = 0;
        $start = strtotime(date('Y-m-d',strtotime('-1 day')). '00:00:00');
        $stop  = strtotime(date('Y-m-d',strtotime('-1 day')). '23:59:59');
        $condition['createtime'] = array('between',array($start,$stop));

        #日志存放目录
        $url = 'script_log/vip-' . date('Y-m') . '.log';

        $list = $this->sendTakeModel
            ->alias('a')
            ->field('a.uid,sum(send_gold) as send_gold,sum(take_gold) as take_gold,count(a.type="send" or NULL) as send_num ,count(a.type="take" or NULL) as take_num,count(distinct a.take_uid)-count(distinct a.take_uid=0 or NULL) as send_people')
            ->join('left join (select uid,level from yq_user where level>0) b on a.uid = b.uid ')
            ->where($condition)
            ->group('a.uid')
            ->select();
        $time['day_time'] = strtotime(date('Y-m-d',strtotime('-1 day')). '00:13:00');
        $addData = [];
        foreach ($list as $k => $v) {
            $addData[] = array_merge($v, $time);
        }

        $msg = $this->vipDayModel->addAll($addData);

        if ($msg){
            $this->writeLog( date('Y-m-d H:i:s') . '--' . $msg, $url);
        }else{
            $this->writeLog( date('Y-m-d H:i:s') . '--' . 'error', $url);
        }

    }

    /**
     * Notes: 统计每日代理交易 00:13
     * User: Lbb
     * Date: 2019/9/25
     * Time: 16:35
     */
    /*public function vipTradingss(): void
    {

        $begin = new \DateTime('2019-11-12');
        $end   = new \DateTime('2019-11-13');
        #日志存放目录
        $url = 'script_log/vip-' . date('Y-m') . '.log';

        for($i = $begin; $i <= $end; $i->modify('+1 day')){
            $date =  $i->format('Y-m-d');
            $condition['is_vip'] = 0;
            $condition['level'] = array('gt',0);
            $condition['is_back'] = 0;
            $start = strtotime(date($date,strtotime('-1 day')). '00:00:00');
            $stop  = strtotime(date($date,strtotime('-1 day')). '23:59:59');
            $condition['createtime'] = array('between',array($start,$stop));



            $list = $this->sendTakeModel
                ->alias('a')
                ->field('a.uid,sum(send_gold) as send_gold,sum(take_gold) as take_gold,count(a.type="send" or NULL) as send_num ,count(a.type="take" or NULL) as take_num,count(distinct a.take_uid)-count(distinct a.take_uid=0 or NULL) as send_people')
                ->join('left join (select uid,level from yq_user where level>0) b on a.uid = b.uid ')
                ->where($condition)
                ->group('a.uid')
                ->select();



            $time['day_time'] = strtotime(date($date,strtotime('-1 day')). '00:13:00');

            $addData = [];
            foreach ($list as $k => $v) {
                $addData[] = array_merge($v, $time);
            }
            if ($addData) {
                $msg = $this->vipDayModel->addAll($addData);

                if ($msg){
                    $this->writeLog( date('Y-m-d H:i:s') . '--' . $msg, $url);
                }else{
                    $this->writeLog( date('Y-m-d H:i:s') . '--' . 'error', $url);
                }
            }
        }

    }*/



    /**
     * Notes: 在线玩家列表
     * User: Lbb
     * Date: 2019/9/12
     * Time: 10:36
     * @return array
     */
    private function onlinePlayer() :array
    {
        $where['level'] = '0';
        $where['is_online'] = '1';
        return $this->newModel->where($where)->field('uid')->select() ?: [];
    }

    /**
     * Notes: 关联查询
     * User: Lbb
     * Date: 2019/9/12
     * Time: 14:12
     * @param $uid
     * @return array
     */
    private function accord($uid) :array
    {
        # 查出这个uid 用过的设备号
        $condition['uid'] = $uid;
        $phys = $this->userLogModel->where($condition)->field('distinct phyAdress')->select();

        # 通过的设备号 查出uid
        $conditionPhy['phyAdress'] = ['in', array_column($phys, 'phyAdress')];
        $accord['users'] = $this->userLogModel->where($conditionPhy)->field('distinct uid')->select();

        # 通过uid  计算出总数 及总输赢
        $conditionUid['uid'] = array('in', array_column($accord['users'], 'uid'));
        $conditionUid['is_closure'] = 1;
        $accord['userData'] = $this->newModel
            ->where($conditionUid)
            ->field('count(uid) as total_num,sum(user_lose_win_all) as total_lose_win')
            ->find() ;
        return $accord ?: [];
    }

    /**
     * Notes: 点控操作
     * User: Lbb
     * Date: 2019/9/12
     * Time: 14:40
     * @param $uid
     * @param int $pointGold
     * @param int $pointType
     * @param int $plan
     */
    private function point($uid, int $pointGold, int $pointType=1, int $plan=70 ) :void
    {
        if (!$this->pointModel->where(['uid' => $uid, 'status' => 1])->order('id desc')->find()) {

            $data_point['totalWin'] = $this->account->where(['uid' => $uid])->sum('bunkogold');
            $data_point['type'] = $pointType;
            $data_point['plan'] = $plan;
            $data_point['controlSum'] = $pointGold;
            $data_point['uid'] = $uid;
            $data_point['staffId'] = 34;
            $data_point['status'] = 1;
            $data_point['createtime'] = time();

            $control_id = $this->pointModel->add($data_point);

            //封装写入数组
            $user_data = [
                'uid' => $uid,
                'title' => '设置点控',
                'admin_id' => 34
            ];

            //写入用户日志
            $this->handleLogModel->record($user_data, 'point', $data_point);

            //发送服务器
            $send_data = [
                'id' => $uid,
                'controlid' => (int)$control_id,
                'onoff' => 1,                                      //0:关，1开
                'state' => $data_point['type'],                    //输赢
                'value' => (string)$data_point['controlSum'],      //目标
                'schemeid' => $data_point['plan'],                 //方案
            ];
            send_server($send_data, '/PointControl.php');
        }

    }

    /**
     * Notes: 查询是否有控赢次数
     * User: Lbb
     * Date: 2019/9/16
     * Time: 10:13
     * @param $uid
     * @return int
     */
    private function isPointNum ($uid): int
    {
        # 查询是否有次数
        $isPoint = $this->account->where(['uid' => $uid])->field('IsPoint')->find();
        return  $isPoint['IsPoint'] ?: 0;
    }

    /**
     * Notes: 获取配置参数
     * User: Lbb
     * Date: 2019/9/16
     * Time: 11:32
     * @return array
     */
    private function getParam() :array
    {
        $groupIds = '1065,1068,1069,1070,1071';
        $where['groupId'] = ['in', $groupIds];
        $list = $this->sysModel->field('groupId,value')->where($where)->select();
        $data = [];
        foreach ($list as $key => $val) {
            $data[$val['groupId']] = (int)$val['value'];
        }
        return $data ?: [];
    }

    /**
     * Notes: 获取满足条件的控赢值
     * User: Lbb
     * Date: 2019/9/16
     * Time: 15:23
     * @param $num
     * @return int
     */
    private function getPointWin(int $num) :int
    {
        $groupIds = '1072,1073';
        $where['groupId'] = ['in', $groupIds];
        $key = $this->sysModel->field('value')->where('groupId=1072')->find();
        $val = $this->sysModel->field('value')->where('groupId=1073')->find();
        $ratios = array_combine(explode(',',$key['value']),explode(',',$val['value']));

        $ratio = 0;
        foreach ($ratios as $k=> $v) {
            if($num >= $k) {
                $ratio = $v;
                break;
            }
        }
        return $ratio;
    }

    /**
     * Notes: 获取点控方案
     * User: Lbb
     * Date: 2019/9/16
     * Time: 15:48
     * @param $uid
     * @return int
     */
    private function getPointNum($uid) :int
    {
        $where['uid'] = $uid;
        $num = $this->account->where($where)->field('point_num')->find();
        $plan= [70,50,30];
        return $plan[$num['point_num']] ?: 0;
    }

    /**
     * Notes: 修改点控机会
     * User: Lbb
     * Date: 2019/9/16
     * Time: 15:56
     * @param $uid
     * @return bool
     */
    private function editPointNum($uid) :bool
    {
        $where['uid'] = $uid;
        $data['IsPoint'] = 0;
        $msg = $this->account->where($where)->save($data);
        if ($msg !==  false ) {
            return true;
        } else{
            return false;
        }
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

    /**
     * 赠送接收
     * Author:lbb
     * @param array $where
     * @return array
     */
    private function sendTake($where = []): array
    {
        # 条件
        $where_comm = $where;
        $where_comm['is_back'] = 0;

        #vip赠送玩家
        $where_send = $where_comm;
        $where_send['send_level'] = array('gt', '0');
        $where_send['take_level'] = 0;
        $where_send['type'] = 'send';
        $data = $this->sendTakeModel
            ->field('sum(send_gold) as vip_send,count(distinct take_uid)-count(distinct take_uid=0 or NULL) as trade_people,count(id) as trade_number')
            ->where($where_send)
            ->find();

        #vip接收玩家
        $where_take = $where_comm;
        $where_take['send_level'] = 0;
        $where_take['take_level'] = array('gt', '0');
        $where_take['type'] = 'take';
        $data['vip_take'] = $this->sendTakeModel
            ->where($where_take)
            ->sum('take_gold') ?: 0;

        $data['trade_diff'] = $data['vip_send'] - $data['vip_take'];
        $data['trade_than'] = $data['vip_take'] === 0 ? 0.000 : $data['vip_take'] / $data['vip_send'];
        $data['trade_people'] = $data['trade_people'] ?: 0;
        $data['trade_number'] = $data['trade_number'] ?: 0;
        $data['vip_send'] = $data['vip_send'] ?: 0;
        return $data;
    }

    /**
     * 吃分 吐分 系统输赢 吞吐率
     * Author:lbb
     * @param array $where
     * @return array
     */
    private function roomTotal($where = []): array
    {
        $roomTotal = $this->roomRecordModel
            ->where($where)
            ->field('sum(gold) as system_lose_win,sum(eatinggold) as eat_gold ,sum(spittinggold) as spit_gold ')
            ->find();

        $roomTotal['system_lose_win'] = $roomTotal['system_lose_win'] ?: 0;
        $roomTotal['eat_gold'] = $roomTotal['eat_gold'] ?: 0;
        $roomTotal['spit_gold'] = $roomTotal['spit_gold'] ?: 0;

        $roomTotal['than'] = $roomTotal['spit_gold'] / $roomTotal['eat_gold'];
        if (is_nan($roomTotal['than'])) {
            $roomTotal['than'] = 0;
        }
        return $roomTotal;
    }

    /**
     * 写日志
     * Author:lbb
     * @param string $data 日志内容
     * @param string $url 设置路径目录信息
     */
    private function writeLog($data = '', $url): void
    {
        $fp = fopen($url, 'a');//打开文件资源通道 不存在则自动创建
        fwrite($fp, var_export($data, true) . "\r\n");//写入文件
        fclose($fp);//关闭资源通道
    }

    /**
     * 发送短信
     * Author:lbb
     * @param $mobile
     * @param $number
     * @param $than
     */
    private function sending($mobile, $number, $than = 0): void
    {
        $apiKey = '1f1f3f34341320ad0aa718b2cc171785';
        if ($than > 0) {
            $text = "【雅乐】当前平台数据为: [ {$number};{$than} ]， 如发现异常请登录查询。";
        } else {
            $text = "【雅乐】当前平台数据为: [ {$number} ]， 如发现异常请登录查询。";
        }

        $url = 'script_log/send-' . date('Y-m') . '.log';

        $ch = curl_init();

        // 发送短信
        $data = array('text' => $text, 'apikey' => $apiKey, 'mobile' => $mobile);
        $json_data = $this->send($ch, $data);
        $array = json_decode($json_data, true);
        $this->writeLog(date('Y-m-d H:i:s') . '--' . $array, $url);
    }

    /**
     * CURL 配置
     * Author:lbb
     * @param $ch
     * @param $data
     * @return mixed
     */
    private function send($ch, $data)
    {
        curl_setopt($ch, CURLOPT_URL, 'https://sms.yunpian.com/v2/sms/single_send.json');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $result = curl_exec($ch);
        $error = curl_error($ch);
        $this->checkErr($result, $error);
        curl_close($ch);
        return $result;
    }

    /**
     * curl错误处理
     * Author:lbb
     * @param $result
     * @param $error
     */
    private function checkErr($result, $error): void
    {
        if ($result === false) {
            echo 'Curl error: ' . $error;
        }
    }









}