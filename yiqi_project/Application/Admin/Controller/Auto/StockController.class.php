<?php
/**
 * 库存修改记录
 * User: Lbb
 * Date: 2019/3/22 0022
 * Time: 16:22
 */

namespace Admin\Controller\Auto;


use Common\Controller\BaseController;

class StockController extends BaseController
{
    private $gameLogModel;

    /**
     * 初始化
     * GameController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->gameLogModel = D('game_log');

    }

    public function index() :void
    {
        if (IS_AJAX) {

            [$where, $sort, $order, $offset, $limit] = $this->getSerachParam();

            //判断筛选条件是否存在
            if ($where) {
                $where = implode(' and ', array_values($where));
            }

            // 查询满足要求的总记录数
            $count = $this->gameLogModel->where($where)->count();

            #获取数据
            $data = $this->gameLogModel->where($where)
                ->limit($offset, $limit)
                ->order([$sort => $order])
                ->select();

            #获取游戏房间
            $rooms = A('Player/User')->query_room();

            foreach ($data as &$val) {
                $val['name'] = $rooms[$val['product_id']];
                if ($val['status']==='0'){
                    $val['status'] = '未运行';
                }else{
                    $val['status'] = '已运行';
                }
            }
            $total['total_stock'] = $this->gameLogModel->where($where)->sum('public_stock') ?: 0;
            $data [0]['statistics'] = array_map('number_format',$total);
            //返回json数据给ajax
            $result = array('total' => $count, 'rows' => $data ?: []);
            $this->ajaxReturn($result, 'JSON');
        }
        $this->display();
    }

}