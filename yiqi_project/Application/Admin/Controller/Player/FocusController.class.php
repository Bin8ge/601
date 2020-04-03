<?php
/**
 * 关怀管理控制器
 * User: 1010
 * Date: 2018/5/25
 * Time: 17:09
 */

namespace Admin\Controller\Player;

use Common\Controller\BaseController;

class FocusController extends BaseController
{

    /**
     * @var \Model
     */
    private $newModel;


    /**
     * 初始化
     */
    public function __construct()
    {
        parent::__construct();

        $this->newModel = D('users');
    }


    /**
     * 在线用户列表列表
     * @param
     */
    public function index($offset = 0): void
    {
        if (IS_AJAX) {

            //搜索参数过滤处理
            $this->searchFilter();

            //获取查询条件
            [$where, $sort, $order, $offset, $limit] = $this->getSerachParam();
            $where['is_focus'] = 'is_focus=1';

            //判断筛选条件是否存在
            if ($where) {
                $where = implode(' and ', array_values($where));
            }

            //获取查询数量
            $count = $this->newModel->where($where)->count('uid');

            //分页数据
            $data = $this->newModel->where($where)
                ->field('room,uid,is_online,nickname,level,gold as gold,user_lose_win_all,daily_gold,total_send,total_receive,createtime,logintime,point_control_controlSum,point_control_progress,point_control_start_time,point_control_status,point_control_type,focus_why,daily_stake')
                ->limit($offset, $limit)
                ->order([$sort => $order])
                ->select();


            //如果数据存在 则将统计信息放入
            if ($data) {
                $room = A('Player/User')->query_room();
                foreach ($data as $key => $val) {
                    if ($val['is_online']==='0'){
                        $data[$key]['is_online'] = '离线';
                    }else{
                        $data[$key]['is_online'] = $room[$val['room']];
                    }
                    $data[$key]['level'] = $this->FieldConfig['level'][$val['level']];
                    $data[$key]['gold']                     = number_format($val['gold']);
                    $data[$key]['user_lose_win_all']        = number_format($val['user_lose_win_all']);
                    $data[$key]['daily_gold']               = number_format($val['daily_gold']);
                    $data[$key]['total_send']               = number_format($val['total_send']);
                    $data[$key]['total_receive']            = number_format($val['total_receive']);
                    $data[$key]['point_control_controlSum'] = number_format($val['point_control_controlSum']);
                    $data[$key]['point_control_progress']   = number_format($val['point_control_progress']);
                }
            }

            //返回json数据给ajax
            $result = array('total' => $count, 'rows' => $data ?: []);
            $this->ajaxReturn($result, 'JSON');
        }

        //渲染模板
        $this->display();
    }

    /**
     * 搜索参数过滤
     */
    private function searchFilter(): void
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

            $_GET['filter'] = json_encode($result);
            $_GET['option'] = json_encode($option);
        }
    }


}