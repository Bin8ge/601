<?php
/**
 * 红包开奖记录
 * User: Lbb
 * Date: 2019/3/22 0022
 * Time: 16:22
 */

namespace Admin\Controller\Auto;


use Common\Controller\BaseController;
use Admin\Controller\Player\user;

class RedController extends BaseController
{
    private $goodLuckRecordModel;


    /**
     * 初始化
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->goodLuckRecordModel = D('goodluckrecord');

    }

    /**
     * 红包开奖记录 查看
     * Author:lbb
     */
    public function index() :void
    {
        if (IS_AJAX) {

            //获取今天00:00
            $todayStart = strtotime(date('Y-m-d' . ' 00:00:00'));
            //获取今天24:00
            $todayEnd = strtotime(date('Y-m-d' . ' 00:00:00').' +1 day');

            [$where, $sort, $order, $offset, $limit] = $this->getSerachParam();

            //默认当日时间
            if ( !isset($where[0]) && !isset($where[1]) ) {
                $where[0] = 'time>='.$todayStart;
                $where[1] = 'time<='.$todayEnd;
            }

            //判断筛选条件是否存在
            if ($where) {
                $where = implode(' and ', array_values($where));
            }

            // 查询满足要求的总记录数
            $count = $this->goodLuckRecordModel->where($where)->count();

            #获取数据
            $data = $this->goodLuckRecordModel->where($where)
                ->limit($offset, $limit)
                ->order([$sort => $order])
                ->select();

            #获取游戏房间
            $rooms = A('Player/User')->query_room();

            foreach ($data as &$val) {
                $val['room'] = $rooms[$val['ProductID']];
            }

            //总计
             $totals =  $this->goodLuckRecordModel
                 ->field('IsAi,count(id) as win_times,count(distinct uid) as win_people_times,sum(UserJackPot) as player_jack,sum(AiJackPot) as ai_jack')
                 ->where($where)
                 ->group('IsAi')
                 ->select();

            $totalInfo = [];
            foreach ($totals as $vv){
                if ($vv['IsAi']==='1'){
                    $totalInfo[1] = $vv;
                }else{
                    $totalInfo[0] = $vv;
                }
            }

            # 玩家中奖次数
            $total['player_win_times']  = $totalInfo[0]['win_times'];
            # 玩家中奖人数
            $total['player_win_number'] = $totalInfo[0]['win_people_times'] ?: 0;
            # 玩家中奖时AI贡献奖池总数
            $total['player_aj_jack']    = $totalInfo[0]['ai_jack'];
            # AI中奖次数
            $total['ai_win_times']      = $totalInfo[1]['win_times'];
            #AI中奖人数
            $total['ai_win_number']     = $totalInfo[1]['win_people_times'] ?: 0;
            #AI中奖时玩家贡献奖池总数
            $total['ai_player_jack']    = $totalInfo[1]['player_jack'];
            #系统输赢
            $total['lose_win']          = $total['ai_player_jack']-$total['player_aj_jack'];

            $data [0]['statistics'] = array_map('number_format',$total);

            //返回json数据给ajax
            $result = array('total' => $count, 'rows' => $data ?: []);
            $this->ajaxReturn($result, 'JSON');
        }
        $this->display();
    }

}