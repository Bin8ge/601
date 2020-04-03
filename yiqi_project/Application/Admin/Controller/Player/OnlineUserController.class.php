<?php
/**
 * 在线用户管理控制器
 * User: 1010
 * Date: 2018/5/25
 * Time: 17:09
 */

namespace Admin\Controller\Player;

use Common\Controller\BaseController;

class OnlineUserController extends BaseController
{

    //获取用户相关的视图模型
    private $newModel;


    /**
     * 初始化
     */
    public function __construct()
    {
        parent::__construct();

        $this->newModel= D('users');

    }
    /**
     * 在线用户列表列表
     * @param
     */
    public function index($offset = 0)
    {
        if (IS_AJAX) {

            //搜索参数过滤处理
            $this->searchFilter();

            //获取查询条件
            [$where, $sort, $order, $offset, $limit] = $this->getSerachParam();
            $where['is_online'] = 'is_online=1';
            if (!$where['level']) {
                $where['level'] = 'level=0';
            }
            //判断筛选条件是否存在
            if ($where) {
                $where = implode(' and ', array_values($where));
            }

            //获取查询数量
            $count = $this->newModel->where($where)->count('uid');

            //分页数据
            $data = $this->newModel->where($where)
                ->field('room,uid,is_online,nickname,level,gold as gold,user_lose_win_all,daily_gold,total_send,total_receive,createtime,logintime,point_control_controlSum,point_control_progress,point_control_start_time,point_control_status,point_control_type')
                ->limit($offset, $limit)
                ->order([$sort => $order])
                ->select();

            //总计数据
          /*  $total = $this->newModel
                ->where($where)
                ->field('count(uid) as total_user_number,count(is_online="1" or NULL) as online_total_user_number,sum(gold) as user_gold_number,sum(user_lose_win_all) as user_total_lose_win_all_number,sum(daily_gold) as user_total_lose_win_today_number,sum(total_send) as user_total_send,sum(total_receive) as user_total_give')
                ->select();*/

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
                //$data [0]['statistics'] = array_map('number_format',$total[0]);
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
    private function searchFilter()
    {
        if (I('get.filter')) {
            $result = (array)I('get.filter', '', 'json_decode');
            $option = (array)I('get.option', '', 'json_decode');
            if (isset($result['select_or_text_select'])) {

                if ($result['select_or_text_text']) {
                    $result[$result['select_or_text_select']] = $result['select_or_text_text'];
                    $option[$result['select_or_text_select']] = $option['select_or_text_text'];
                }
                unset($result['select_or_text_select'], $result['select_or_text_text']);
                unset($option['select_or_text_select'], $option['select_or_text_text']);
            }

            $_GET['filter'] = json_encode($result);
            $_GET['option'] = json_encode($option);
        }
    }





}