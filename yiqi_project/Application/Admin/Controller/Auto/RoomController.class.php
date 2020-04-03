<?php
/**
 * 房间参数设置
 * User: lbb
 * Date: 2018/5/25
 * Time: 17:09
 */

namespace Admin\Controller\Auto;

use Common\Controller\BaseController;

class RoomController extends BaseController
{

    private $gameTypeModel;


    //游戏配置表
    private $gameModel;

    /**
     * 初始化
     */
    public function __construct()
    {
        parent::__construct();

        $this->gameTypeModel = D('game_type');

        $this->gameModel     = D('game');

    }

    /**
     * 房间参数设置 查看
     * Author:lbb
     */

    public function index()
    {
        if (IS_AJAX) {

            $list=$this->gameModel
                ->field('yq_game.ProductID,StockRatio,b.NeedGold,b.type_name,b.name,b.is_open')
                ->join('left join yq_game_type as b on yq_game.ProductID = b.productid')
                ->group('yq_game.productID')
                ->order('yq_game.productID asc')
                ->select();

            foreach ($list as $k=>&$val){
                $stock = json_decode($val['StockRatio'],true);
                $val['public_stock']  = $stock['public_stock'];
                $val['jackpot_stock'] = $stock['jackpot_stock'];
                $val['tax_stock']     = $stock['tax_stock'];
                unset($val['StockRatio']);
            }
            $this->assign('list', $list);            //结果列表

            $data['content'] = $this->fetch('Auto/room/replace');
            return returnAjax(200, 'SUCCESS', $data);
        }

        //渲染模板
        $this->display();
    }


    /**
     * 游戏参数设置 保存
     * Author:lbb
     */
    public function edit()
    {
        if(IS_AJAX){
            $data = $_POST;

            foreach ($data as $k=>$val){
                if (!is_numeric($val)) {
                    return  returnAjax('400','非法参数~~');
                }
            }

            # 剔除type id
            $id = $data['id'];
            unset($data['id']);

            $saveGameTypeData['NeedGold'] = $data['NeedGold'];
            unset($data['NeedGold']);

            if (array_sum($data) !== 100) {
                return returnAjax(400,'分配比例不等于100%,请核对~~');
            }
            foreach ($data as $k=>$val){
                $data[$k] = (int)$val;
            }

            $saveGameData['StockRatio'] = json_encode($data);

            $condition['productid'] = $id;
            $where['ProductID']     = $id;
            $msg    = $this->gameModel->where($where)->save($saveGameData);
            $status = $this->gameTypeModel ->where($condition)->save($saveGameTypeData);

            //写入后台日志
            $this->adminLogModel->record($_POST);

            if ($status === FALSE || $msg === FALSE) {
                return returnAjax('400','保存失败~~');
            }else{
                $info = $this->gameModel->where($where)->find();
                # 发送服务器
                $server_data['game_id']       = (int)$info['GameID'];
                $server_data['product_id']    = (int)$info['ProductID'];
                $server_data['machine_id']    = (int)$info['MachineID'];
                $server_data['public_ratio']  = (int)$data['public_stock'];
                $server_data['jackpot_ratio'] = (int)$data['jackpot_stock'];
                $server_data['tax_ratio']     = (int)$data['tax_stock'];
                send_server($server_data,'/UpdateStockRatio.php');
                return returnAjax('200','SUCCESS');
            }
        }
    }

    /**
     *游戏开关
     * Author:lbb
     */
    public function switch()
    {
        $productId = $_POST['productId'];
        $isOpen    = $_POST['is_open'];
        $condition['productid'] = $productId;
        $saveData['is_open'] = $isOpen;
        $status = $this->gameTypeModel ->where($condition)->save($saveData);
        if ($status === FALSE) {
            return returnAjax('400','保存失败~~');
        }else{
            # 发送服务器
            $server_data['productid']   = (int)$productId;
            $server_data['open']        = (int)$isOpen;
            send_server($server_data,'/RoomOpen.php');
            return returnAjax('200','SUCCESS');
        }
    }



}