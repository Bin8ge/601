<?php
/**
 * 输赢榜
 * User: Lbb
 * Date: 2019/3/12 0012
 * Time: 14:35
 */

namespace Admin\Controller\Game;


use Common\Controller\BaseController;

class WinningController extends BaseController
{
    //数据对象
    private $userAccountView ;

    public function __construct()
    {
        parent::__construct();

        $this->userAccountView = D('user_account');

    }

    public function index() :void
    {
        if (IS_AJAX) {

            //获取查询条件
            [$where, $sort, $order, $offset, $limit] = $this->getSerachParam();

            //判断筛选条件是否存在
            $where['level'] = 'level=0';
            $where['daily_gold'] = 'daily_gold<>0';
            $where = implode(' and ', array_values($where));

            //获取查询数量
            $count = $this->userAccountView->where($where)->count();

            //获取数据
            $data = $this->userAccountView
                ->field('uid,nickname,gold+bank as total_gold,daily_gold')
                ->where($where)
                ->limit($offset, $limit)
                ->order([$sort => $order])
                ->select();

            //返回json数据给ajax
            $result = array('total' => $count, 'rows' => $data ?: []);
            $this->ajaxReturn($result, 'JSON');
        }

        //渲染模板
        $this->display();
    }

}