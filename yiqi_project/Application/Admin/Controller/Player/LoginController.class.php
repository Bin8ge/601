<?php
/**
 * Created by PhpStorm.
 * User: Lbb
 * Date: 2019/3/28 0028
 * Time: 17:18
 */

namespace Admin\Controller\Player;

use Common\Controller\BaseController;

class LoginController extends BaseController
{
    //管理员操作表
    private $userLogView;

    public function __construct()
    {

        parent::__construct();

        $this->userLogView  = D('UserLogAll');

    }

    public function index() :void
    {

        $uid = I('get.uid');
        if (IS_AJAX) {
            //获取查询条件
            [$where, $sort, $order, $offset, $limit] = $this->getSerachParam();

            if ($uid > 0) {
                $where['uid']      = "uid='{$uid}'";
            }

            //判断筛选条件是否存在
            if ($where) {
                $where = implode(' and ', array_values($where));
            }

            if (!$where) {
                exit;
            }


            //获取查询数量
            $count = $this->userLogView->where($where)->count();

            //获取数据
            $data = $this->userLogView
                ->where($where)
                ->limit($offset, $limit)
                ->order([$sort => $order])
                ->select();

            //获取游戏房间
            $room =  $room = A('Player/User')->query_room();

            //处理特殊字段
            foreach ($data as $key=>$v){
                $data[$key]['roomname'] = $room[$v['room']];
                $data[$key]['gold'] = number_format($v['gold']);
            }

            //返回json数据给ajax
            $result = array('total' => $count, 'rows' => $data ?:  []);
            $this->ajaxReturn($result, 'JSON');
        }
        $this->display();
    }





}