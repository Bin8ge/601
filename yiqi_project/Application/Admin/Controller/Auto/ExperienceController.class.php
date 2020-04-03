<?php
/**
 * 体验记录
 * User: Lbb
 * Date: 2019/3/22 0022
 * Time: 16:22
 */

namespace Admin\Controller\Auto;


use Common\Controller\BaseController;


class ExperienceController extends BaseController
{
    private $newUserLogModel;


    /**
     * 初始化
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->newUserLogModel = D('newuserlog');

    }

    /**
     * 体验记录 查看
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
                $where[0] = 'yq_newuserlog.createtime>='.$todayStart;
                $where[1] = 'yq_newuserlog.createtime<='.$todayEnd;
            }

            //判断筛选条件是否存在
            if ($where) {
                $where = implode(' and ', array_values($where));
            }


            // 查询满足要求的总记录数
            $count = $this->newUserLogModel->where($where)->count();

            #获取数据
            $data = $this->newUserLogModel
                ->field('yq_newuserlog.uid,betgold,wingold,changgold,pid,yq_newuserlog.createtime,gametype,nickname')
                ->join('left join yq_user on yq_user.uid = yq_newuserlog.uid')
                ->where($where)
                ->limit($offset, $limit)
                ->order([$sort => $order])
                ->select();


            $rooms = A('Player/User')->query_room();

            $type = [
                '1' => '新手体验',
                '2' => '充值体验',
                '3' => '波动修正',
            ];

            foreach ($data as $k=>$val) {
                $data[$k]['gametype'] = $type[$val['gametype']];
                $data[$k]['game_name'] = $rooms[$val['pid']];
            }

            //总计
            $totals =  $this->newUserLogModel
                ->field('sum(changgold) as gold,gametype,count(distinct uid) as people')
                ->where($where)
                ->group('gametype')
                ->select();

            $total = [];
            foreach ($totals as $vv){
                if ($vv['gametype']==='1'){
                    $total['total_user'] = $vv['gold'];
                    $total['total_user_num'] = $vv['people'];
                }elseif($vv['gametype']==='2'){
                    $total['total_recharge'] = $vv['gold'];
                    $total['total_recharge_num'] = $vv['people'];
                }
                elseif($vv['gametype']==='3'){
                    $total['total_wave'] = $vv['gold'];
                    $total['total_wave_num'] = $vv['people'];
                }
            }

            $total['total_user'] = $total['total_user'] ?: 0;
            $total['total_user_num'] = $total['total_user_num'] ?: 0;
            $total['total_recharge'] = $total['total_recharge'] ?: 0;
            $total['total_recharge_num'] = $total['total_recharge_num'] ?: 0;
            $total['total_wave'] = $total['total_wave'] ?: 0;
            $total['total_wave_num'] = $total['total_wave_num'] ?: 0;
            $total['total_sum'] = $total['total_user'] + $total['total_recharge']+$total['total_wave'];

            $data [0]['statistics'] = array_map('number_format', $total);



            //返回json数据给ajax
            $result = array('total' => $count, 'rows' => $data ?: []);
            $this->ajaxReturn($result, 'JSON');
        }
        $this->display();
    }

}