<?php
/**
 * Created by PhpStorm.
 * User: lbb
 * Date: 2019/2/22 0009
 * Time: 10:33
 */

namespace Admin\Controller\Player;


use Common\Controller\BaseController;

class HandleLogController extends BaseController
{

    //数据对象
    private $handleModel;
    //admin表
    private $adminModel;
    //user表
    private $userModel;

    /**
     * 初始化
     */
    public function __construct()
    {
        parent::__construct();

        $this->handleModel = D('handle_log');

        $this->adminModel = D('admin');

        $this->userModel = D('user');
    }

    /**
     * 管理员操作日志
     * @param int $offset
     */
    public function index() :void
    {
        $uid = I('get.uid');

        if (IS_AJAX) {

            //获取查询条件
            [$where, $sort, $order, $offset, $limit]= $this->getSerachParam();

             if ($uid > 0) {
                 $where['uid']      = "uid='{$uid}'";
             }

            //判断筛选条件是否存在
            if ($where) {
                $where = implode(' and ' , array_values($where));
            }

            //获取查询数量
            $count = $this->handleModel->where($where)->count();


            //获取数据
            $data = $this->handleModel
                ->where($where)
                ->limit($offset, $limit)
                ->order([$sort => $order])
                ->select();

            //对特定字段进行处理
            foreach ($data as $key => &$value) {
                $adminData = $this->adminModel->field('username')->where(['id' => $value['admin_id']])->find();
                $value['admin_id'] = $adminData['username'];
                $userInfo = $this->userModel->field('nickname')->where(['uid' => $value['uid']])->find();
                $value['nickname'] = $userInfo['nickname'];
            }
            //返回json数据给ajax
            $result = array('total' => $count, 'rows' => $data ? $data : []);
            $this->ajaxReturn($result, 'JSON');
        }
        $this->display();
    }



}