<?php
/**
 * 交易控制器
 * User: 1010
 * Date: 2018/5/25
 * Time: 17:06
 */

namespace Admin\Controller\Player;

use Common\Controller\BaseController;

class TradingController extends BaseController
{

    //数据对象
    private $sendTakeModel;
    private $pointModel;
    private $pageSize = 50;


    /**
     * 初始化
     */
    public function __construct()
    {
        parent::__construct();

        $this->sendTakeModel = D('send_take');

        $this->pointModel = D('point_control');
    }

    /**
     * Author:lbb
     * 当日交易列表查看
     */
    public function index()
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
                $where[0] = 'createtime>='.$todayStart;
                $where[1] = 'createtime<='.$todayEnd;
            }

            $where['is_vip']     = "is_vip='0'";
            $where['is_back']    = "is_back='0'";

            //判断筛选条件是否存在
            if ($where) {
                $where = implode(' and ', array_values($where));
            }


            //生成sql
            $sqlCount = $this->sendTakeModel
                ->alias('a')
                ->field('b.nickname,b.level,b.daily_gold,(b.gold+b.bank) as user_gold,b.user_lose_win_all,a.uid,sum(send_gold) as total_send,sum(take_gold) as total_take,sum(send_gold)-sum(take_gold) as total_diff,count(type="send" or null) as total_send_num,count(type="take" or null) as total_take_num,count(distinct send_uid)-count(distinct send_uid=0 or NULL) as total_send_people ,count( DISTINCT take_uid)-count(distinct take_uid=0 or NULL) as total_take_people')
                ->join('left join yq_user_account b on a.uid=b.uid')
                ->where($where)
                ->group('uid')
                ->select(false);

            // 查询满足要求的总记录数
            $count =  M()->table($sqlCount.' j')->count('j.uid');


            #获取数据
            $data =  M()->table($sqlCount.' a')
                ->field('a.uid,nickname,level,user_gold,user_lose_win_all,daily_gold,a.total_send,a.total_take,a.total_send_num,a.total_take_num,total_diff,a.total_take_people,a.total_send_people')
                ->limit($offset, $limit)
                ->order([$sort => $order])
                ->select();


            foreach ($data as $key => $val) {
                $data[$key]['level'] = $this->FieldConfig['level'][$val['level']];
                $where_point['uid'] = $val['uid'];
                $point = $this->pointModel->where($where_point)->field('type,status')->order('id desc')->find();
                $data[$key]['point_status'] = $point['status'];
                $data[$key]['point_type'] = $point['type'];
            }


            //总计
           /* $total =  M()->table($sqlCount.' a')
                ->field('(sum(b.gold)+sum(b.bank)) as gold_total,sum(b.user_lose_win_all) as lose_win_total,sum(b.daily_gold) as lose_win_day_total, sum(a.total_send) as send_total,sum(a.total_take) as take_total')
                ->join('left join yq_user_account b on a.uid=b.uid')
                ->select();*/

            //$data [0]['statistics'] = array_map('number_format',$total[0]);

            //返回json数据给ajax
            $result = array('total' => $count, 'rows' => $data ?: []);
            $this->ajaxReturn($result, 'JSON');

        }
        //渲染模板
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
            if (isset($result['select_or_text_select'])) {

                if ($result['select_or_text_text']) {
                    $result[$result['select_or_text_select']] = $result['select_or_text_text'];
                    $option[$result['select_or_text_select']] = $option['select_or_text_text'];
                }
                unset($result['select_or_text_select'], $result['select_or_text_text'],$option['select_or_text_select'], $option['select_or_text_text']);
            }


            if (!isset($result['createtime-start']) &&  !isset($result['createtime-end']))
            {
                $result['createtime-start'] = date('Y-m-d' . ' 00:00:00');
                $option['createtime-start'] = '>=';
                $result['createtime-end']   = date('Y-m-d' . ' 23:59:59');
                $option['createtime-end']   ='<=';
            }


            //普通用户 还是vip
            if (isset($result['levels']) ) {
                if ($result['levels']=== '0') {
                    $result['level'] = 0;
                    $option['level'] = '=';
                }else{
                    $result['level'] = 0;
                    $option['level'] = '>';
                }
                unset($result['levels'],$option['levels']);
            }

            $_GET['filter'] = json_encode($result);
            $_GET['option'] = json_encode($option);
        }
    }

    public function index_s()
    {
        if (IS_AJAX) {

            # 用户推广号 检索条件
            if (I('get.uid')) {
                $where['uid'] = (int)I('get.uid');
            }


            # 排序 字段
            $field = I('get.field','a.uid');
            # 排序 顺序
            $order = I('get.order','asc');

            # 判断是否有时间条件
            if ( I('get.starttime') || I('get.stoptime')) {
                $todayStart = strtotime(I('get.starttime'));
                $todayEnd   = strtotime(I('get.stoptime'));

                $where['createtime'] = array('between', array($todayStart, $todayEnd));
            } else {
                //获取今天00:00
                $todayStart = strtotime(date('Y-m-d' . ' 00:00:00'));
                //获取今天24:00
                $todayEnd = strtotime(date('Y-m-d' . ' 00:00:00').' +1 day');
                //统计今天的用户
                $where['createtime'] = array('between', array($todayStart, $todayEnd));
            }

            $p = (int)$_REQUEST['p'];

            $where['is_vip']  = array('eq', '0');  # 剔除VIP条件
            $where['is_back'] = array('eq', '0');  # 剔除退回的

            $sqlCount = $this->sendTakeModel
                ->field('uid,sum(send_gold) as total_send,sum(take_gold) as total_take,count(type="send" or null) as total_send_num,count(type="take" or null) as total_take_num,count(distinct send_uid)-count(distinct send_uid=0 or NULL) as total_send_people ,count( DISTINCT take_uid)-count(distinct take_uid=0 or NULL) as total_take_people')
                ->where($where)
                ->group('uid')
                ->select(false);// 查询满足要求的总记录数



            $count =  M()->table($sqlCount.' j')->count('j.uid');
            $Page = new \Think\Ajaxpage($count, $this->pageSize, 'indexAjaxComm');// 实例化分页类 传入总记录数和每页显示的记录数(25)
            $show = $Page->show();// 分页显示输出

            //生成sql
            $data =  M()->table($sqlCount.' a')
                ->field('a.uid,b.nickname,b.level,(b.gold+b.bank) as user_gold,b.user_lose_win_all,b.daily_gold, a.total_send,a.total_take,a.total_send_num,a.total_take_num,a.total_take_people,a.total_send_people')
                ->join('left join yq_user_account b on a.uid=b.uid')
                ->page($p, $this->pageSize)
                ->order("{$field} {$order}")
                ->group('a.uid')
                ->select();


            //总计
            /* $total =  M()->table($sqlCount.' a')
                 ->field('(sum(b.gold)+sum(b.bank)) as gold_total,sum(b.user_lose_win_all) as lose_win_total,sum(b.daily_gold) as lose_win_day_total, sum(a.total_send) as send_total,sum(a.total_take) as take_total')
                 ->join('left join yq_user_account b on a.uid=b.uid')
                 ->select();*/


            $total[0]['people_total'] = $count;
            $this->assign('page', $show); // 赋值分页输出
            $this->assign('list', $data);
            //$this->assign('total', $total[0]);
            $res['content'] = $this->fetch('Player/trading/replace');
            return returnAjax(200, 'SUCCESS', $res);
        }
        //渲染模板
        $this->display();
    }
}