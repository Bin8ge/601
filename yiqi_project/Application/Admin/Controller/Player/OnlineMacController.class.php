<?php
/**
 * 在线用户管理控制器
 * User: 1010
 * Date: 2018/5/25
 * Time: 17:09
 */

namespace Admin\Controller\Player;

use Common\Controller\BaseController;

class OnlineMacController extends BaseController
{

    //获取用户相关的视图模型
    private $newModel;

    private $userLogModel;


    /**
     * 初始化
     */
    public function __construct()
    {
        parent::__construct();

        $this->newModel= D('users');

        $this->userLogModel = D('user_log');

    }
    /**
     * 在线用户列表列表
     * @param
     */
    public function index() :void
    {
        if (IS_AJAX) {

            //获取查询条件
            [$where, $sort, $order, $offset, $limit] = $this->getSerachParam();

            if (!$where) {
                exit;
            }

            $where['level'] = 'level=0';
            $where['is_closure'] = 'is_closure=1';
            //判断筛选条件是否存在
            if ($where) {
                $where = implode(' and ', array_values($where));
            }

            //获取查询数量
            $count = $this->newModel->where($where)->count('uid');

            //分页数据
            $data = $this->newModel->where($where)
                ->field('room,uid,is_online,nickname')
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

                    # 查出这个uid 用过的设备号
                    $condition['uid'] = $val['uid'];
                    //$where['phyAdress'] =['not in', $phy];
                    $phys = $this->userLogModel->where($condition)->field('distinct phyAdress')->select();

                    # 通过的设备号 查出uid
                    $conditionPhy['phyAdress'] = ['in', array_column($phys, 'phyAdress')];
                    $users = $this->userLogModel->where($conditionPhy)->field('distinct uid')->select();

                    # 通过uid  计算出总数 及总输赢
                    $conditionUid['uid'] = array('in',array_column($users, 'uid'));
                    $conditionUid['is_closure'] =1;
                    $userData = $this->newModel
                        ->where($conditionUid)
                        ->field('count(uid) as total_num,sum(user_lose_win_all) as total_lose_win')
                        ->find();

                    $data[$key]['total_num']      = number_format($userData['total_num'] ?: 0);
                    $data[$key]['total_lose_win'] = $userData['total_lose_win'] ?: 0;
                }
            }


            $lose_win = array_column($data,'total_lose_win');
            array_multisort($lose_win,SORT_ASC,$data);

            //返回json数据给ajax
            $result = array('total' => $count, 'rows' => $data ?: []);
            $this->ajaxReturn($result, 'JSON');
        }

        //渲染模板
        $this->display();
    }








}