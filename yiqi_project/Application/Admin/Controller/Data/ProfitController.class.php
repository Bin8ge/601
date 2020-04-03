<?php
/**
 * 平台收益明细
 * User: Lbb
 * Date: 2018/7/23 0023
 * Time: 20:30
 */

namespace Admin\Controller\Data;


use Common\Controller\BaseController;

class ProfitController extends BaseController
{

    //交易税视图
    private $statistical;

    private $commAction;

    public function __construct()
    {
        parent::__construct();

        //公共控制器 方法
        $this->commAction = new CommonController();

        $this->statistical =D('statistical');

    }


    public function index()
    {
        if (IS_AJAX)
        {
            //获取时间条件
            $whereTime  =  $this->commAction->commWhere();

            //公共条件
            $condition['createtime']  = $whereTime['before_day'];

            #列表数据
            $list = $this->statistical
                ->where($condition)
                ->order('id desc')
                ->select();

            #总计数据
            $total = [];
            foreach ($list as $val){
                $total['total_reg']    += $val['reg'];
                $total['total_gm']     += $val['gm_gold'];
                $total['total_send']   += $val['vip_send'];
                $total['total_take']   += $val['vip_take'];
                $total['total_system'] += $val['system_lose_win'];
                $total['total_point']  += $val['point_lose_win'];
                $total['total_game']   += $val['game_tax'];
                $total['total_trade']  += $val['trade_tax'];
            }

            $this->assign('total',$total);
            $this->assign('list',$list);
            $res['content'] = $this->fetch('Data/profit/replace');
            return returnAjax(200,'SUCCESS',$res);
        }
        $this->display();
    }







}