<?php
/**
 * gm 添加金币
 * User: Lbb
 * Date: 2018/9/6 0006
 * Time: 15:44
 */

namespace Admin\Controller\Auth;

use Common\Controller\BaseController;



class RecLogController extends BaseController
{

    private $accountModel;

    private $userModel;

    private $adminModel;

    public function __construct()
    {
        parent::__construct();

        $this->accountModel = D('AccountLog');

        $this->userModel = D('user');

        $this->adminModel = D('admin');

    }


    public function index() :void
    {

        if (IS_AJAX) {
            //搜索条件
            if(I('get.filter')){
                $result = (array)I('get.filter','', 'json_decode');
                if(isset($result['admin_name'])){
                    $userInfo = D('Admin')->where(['username' => $result['admin_name']])->find();
                    unset($result['admin_name']);
                    $result['admin_id'] = $userInfo['id'];
                    $_GET['filter'] = json_encode($result);
                }
            }

            //获取查询条件
            [$where, $sort, $order, $offset, $limit]= $this->getSerachParam();

            //判断筛选条件是否存在
            if ($where) {
                $where = implode(' and ', array_values($where));
            }

            //获取总数
            $count = $this->accountModel
                ->where(['type' => 'gmChange'])
                ->where($where)
                ->count();

            //获取管理员日志数据
            $data =$this->accountModel
                ->where(['type' => 'gmChange'])
                ->where($where)
                ->limit($offset, $limit)
                ->order([$sort => $order])
                ->select();

            foreach ($data as $key => $val) {
                $user_info = $this->userModel->field('nickname')->where(array('uid' => $val['uid']))->find();
                $admin_info = $this->adminModel->field('username')->where(array('id' => $val['admin_id']))->find();
                $data[$key]['admin_name'] = $admin_info['username'];
                $data[$key]['user_name'] = $user_info['nickname'];
                $data[$key]['handleNum'] = number_format($val['handleNum']);
            }
            $result = array('total' => $count, 'rows' => $data ?:[]);

            $this->ajaxReturn($result,'JSON');

        }
        $this->display();
    }

}