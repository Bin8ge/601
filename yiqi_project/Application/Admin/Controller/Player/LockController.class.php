<?php
/**
 * Created by PhpStorm.
 * User: Lbb
 * Date: 2019/3/28 0028
 * Time: 17:18
 */

namespace Admin\Controller\Player;

use Common\Controller\BaseController;

class LockController extends BaseController
{
    //管理员操作表
    private $handleLogModel;

    private $userModel;

    public function __construct()
    {

        parent::__construct();

        $this->handleLogModel = D('HandleLog');

        $this->userModel      = D('user');
    }

    public function index() :void
    {
        if (IS_AJAX) {

            //获取查询条件
            [$where, $sort, $order, $offset, $limit] = $this->getSerachParam();


            //判断筛选条件是否存在
            if (!isset($where['type'])){
                $where['type'] = 'type in ("lock","unlock","present","no_present")';
            }

            if ($where) {
                $where = implode(' and ', array_values($where));
            }

            //获取查询数量
            $count = $this->handleLogModel
                ->where($where)
                ->count();

            //获取数据
            $data = $this->handleLogModel
                ->where($where)
                ->limit($offset, $limit)
                ->order([$sort => $order])
                ->select();



            //对特定字段进行处理
            foreach ($data as $key => &$value) {

                $adminData = D('Admin')->where(['id' => $value['admin_id']])->find();
                $value['admin_id'] = $adminData['username'];

                $userData = $this->userModel
                    /*->field('nickname,reg_time ,login_time ,(gold+bank) as gold,user_lose_win_all')*/
                    ->field('nickname,createtime ,logintime')
                    ->where(['uid' => $value['uid']])
                    ->find();

                $value['reg_time']    = $userData['createtime'];
                $value['login_time']  = $userData['logintime'];
                $value['nickname']    = $userData['nickname'];
            }

            //返回json数据给ajax
            $result = array('total' => $count, 'rows' => $data ?: []);
            $this->ajaxReturn($result, 'JSON');
        }
        $this->display();
    }

}