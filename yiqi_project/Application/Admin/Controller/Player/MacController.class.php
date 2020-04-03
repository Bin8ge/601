<?php
/**
 * 活动参数配置
 * Created by PhpStorm.
 * User: Lbb
 * Date: 2019/5/13 0013
 * Time: 15:06
 */

namespace Admin\Controller\player;


use Common\Controller\BaseController;

class MacController extends BaseController
{

    //数据对象
    private $userLogModel;

    private $newModel;

    private $userModel;

    private $handleLogModel;

    public function __construct()
    {
        parent::__construct();

        $this->userLogModel = D('user_log');

        $this->newModel     = D('users');

        $this->userModel    = D('user');

        $this->handleLogModel    = D('HandleLog');

    }

    public function index() :void
    {
        if (IS_AJAX) {

            $this->searchFilter();
            //获取查询条件
            [$where, $sort, $order, $offset, $limit] = $this->getSerachParam();

            if (!$where) {
                exit;
            }

            $where['is_closure'] = 'is_closure=1';

            //判断筛选条件是否存在
            if ($where) {
                $where = implode(' and ', array_values($where));
            }

            //获取数量
            $count = $this->newModel->where($where)->count();

            #获取数据
            $data = $this->newModel
                ->where($where)
                ->field('uid as id,is_online,nickname,level,createtime,logintime as login_createtime,user_lose_win_all as lose_win_total_all,daily_gold as lose_win_total_today,total_receive as accept_present_give_num,gold,total_send,point_control_status,point_control_type')
                ->limit($offset, $limit)
                ->order([$sort => $order])
                ->select();


            # 统计数据
            $total = $this->newModel
                ->where($where)
                ->field('sum(gold) as user_gold_number,sum(user_lose_win_all) as user_total_lose_win_all_number,sum(daily_gold) as user_total_lose_win_today_number,sum(accept_present_diff_num) as user_total_presend_diff_num')
                ->find();

            if ($data) {
                $data[0]['statistics'] = $total;
            }

            //返回json数据给ajax
            $result = array('total' => $count, 'rows' => $data ?: []);
            $this->ajaxReturn($result, 'JSON');
        }

        //渲染模板
        $this->display();
    }

    /**
     * 删除规则
     * @param string $ids 规则ids串 1,2,3.....
     */
    public function delete($ids = "")
    {


        if (!$ids = I('get.ids', '')) {
            $this->error('删除规则ids不存在');
        }


        $where['uid']    =  ['in', $ids];
        $where['status'] =  1;

        //判断用户修改状态是否成功
        if ($this->userModel->where($where)->save(['status' => 0,'is_send_presend'=>0])) {

            foreach (explode(',',$ids) as $val){
                //封装写入数组
                $user_data = [
                    'uid' => $val,
                    'title' =>'用户关联锁定',
                    'admin_id' => $this->auth->id,
                    'remark'   => '锁定'
                ];
                //写入用户日志
                $this->handleLogModel->record($user_data, 'lock', [
                    'field'    => 'clientMark',
                    'value'    => $ids,
                    'remark'   => '锁定'
                ]);
            }
            $post['uid']    = $ids;
            //写入日志
            $this->adminLogModel->record($post);
        }
        $this->success('关联锁定成功');
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
            $phy = [
                'EA7A5DD8-12B0-490D-8E16-88F189431E18',
                 '16df99a755921e887dde764dee88a3f90574b35',
                 '4CAB000F-781B-432F-82C2-66156A269CE3',
                 '591423b0b5d4ec00718bbd2af9d4262a',
                 'AB1E9B18-318B-43F8-8D75-5D1EDD7B3302',
                 '64514916-EF49-4B4D-A271-87DE8C64B80D',
                 '6B112D6A-0765-4FC3-884A-D8756F82AC44',
                 'BD24BB78-7A48-46DA-9CE9-A18D6E958FD1',
                 '8E6EF7AA-D1AE-4750-8848-1C0FDD129895'
                ];
            //普通用户 还是vip
            if (isset($result['uid']) ) {
                $where['uid'] = $result['uid'];
                $where['phyAdress'] =['not in', $phy];
                $phys = $this->userLogModel->where($where)->field('distinct phyAdress')->select();
                $condition['phyAdress'] = ['in', array_column($phys, 'phyAdress')];
                $users = $this->userLogModel->where($condition)->field('distinct uid')->select();
                $result['uid'] =array_column($users, 'uid');
                $option['uid'] = 'in';
            }

            if (isset($result['phyAdress']) ) {
                $condition['phyAdress'] = $result['phyAdress'];
                if (in_array($result['phyAdress'],$phy,true)){
                    $condition['phyAdress'] = '';
                }
                $users = $this->userLogModel->where($condition)->field('distinct uid')->select();
                $result['uid'] =array_column($users, 'uid');
                $option['uid'] = 'in';
                unset($result['phyAdress'],$option['phyAdress']);
            }


            if (isset($result['addIp']) ) {
                $users = $this->userLogModel->where("addIp not in ('','Invalid') and addIp='{$result['addIp']}'")->field('distinct uid')->select();
                $result['uid'] =array_values(array_unique(array_column($users, 'uid')));
                $option['uid'] = 'in';
                unset($result['addIp'],$option['addIp']);
            }

            $_GET['filter'] = json_encode($result);
            $_GET['option'] = json_encode($option);
        }
    }

}