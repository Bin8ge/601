<?php
/**
 * 推广返利记录
 * User: Lbb
 * Date: 2019/3/22 0022
 * Time: 16:22
 */

namespace Admin\Controller\Auto;


use Common\Controller\BaseController;

class FundController extends BaseController
{
    private $fundModel;


    /**
     * 初始化
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->fundModel = D('fund');

    }

    /**
     * 推广返利记录 查看
     * Author:lbb
     */
    public function index() :void
    {
        if (IS_AJAX) {

            //获取今天00:00
            $todayStart = strtotime(date('Y-m-d' . ' 00:00:00'));
            //获取今天24:00
            $todayEnd = strtotime(date('Y-m-d' . ' 00:00:00').' +1 day');
            //搜索参数过滤处理
            $this->searchFilter();
            [$where, $sort, $order, $offset, $limit] = $this->getSerachParam();

            //默认当日时间
            if ( !isset($where[0]) && !isset($where[1]) ) {
                $where[0] = 'yq_fund.createtime>='.$todayStart;
                $where[1] = 'yq_fund.createtime<='.$todayEnd;
            }

            //判断筛选条件是否存在
            if ($where) {
                $where = implode(' and ', array_values($where));
            }

            // 查询满足要求的总记录数
            $count = $this->fundModel
                ->where($where)
                ->join('left join yq_user on yq_fund.uid = yq_user.uid')
                ->count();

            #获取数据
            $data = $this->fundModel
                ->field('yq_fund.uid,fund_change,fund_type,yq_fund.createtime,level')
                ->where($where)
                ->join('left join yq_user on yq_fund.uid = yq_user.uid')
                ->limit($offset, $limit)
                ->order([$sort => $order])
                ->select();

            $type = [
                1 => '交易税',
                2 => '押注返利',
                3 => '领取基金',
                4 => '输赢基金'
            ];

            foreach ($data as $k=>$val) {
                $data[$k]['fund_type'] = $type[$val['fund_type']];
                $data[$k]['level']     = $this->FieldConfig['level'][$val['level']];
            }


            //总计
            $total =  $this->fundModel
                ->field('fund_type,sum(fund_change) as total_gold')
                ->join('left join yq_user on yq_fund.uid = yq_user.uid')
                ->where($where)
                ->group('fund_type')
                ->select();
            $totals = [];
            foreach ($total as $val) {
                $totals['type'.$val['fund_type']]  = $val['total_gold'];
            }
            $totals['type1'] = $totals['type1']?:0;
            $totals['type2'] = $totals['type2']?:0;
            $totals['type3'] = $totals['type3']?:0;
            $totals['type4'] = $totals['type4']?:0;

            if ($total){
                $data[0]['statistics'] = array_map('number_format',$totals);
            }

            //返回json数据给ajax
            $result = array('total' => $count, 'rows' => $data ?: []);
            $this->ajaxReturn($result, 'JSON');
        }
        $this->display();
    }

    /**
     * 搜索参数过滤
     * Author:lbb
     */
    private function searchFilter() :void
    {
        if (I('get.filter')) {
            $result = (array)I('get.filter', '', 'json_decode');
            $option = (array)I('get.option', '', 'json_decode');



            //普通用户 还是vip
            if (isset($result['levels']) ) {
                if ($result['levels']=== '0') {
                    $result['level'] = 0;
                    $option['level'] = '=';
                }else{
                    $result['level'] = '1,2,3';
                    $option['level'] = 'in';
                }
                unset($result['levels'],$option['levels']);
            }

            $_GET['filter'] = json_encode($result);
            $_GET['option'] = json_encode($option);
        }
    }

}