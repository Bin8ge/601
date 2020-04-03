<?php
/**
 * 开关页面
 * User: Lbb
 * Date: 2018/8/9 0009
 * Time: 14:41
 */

namespace Admin\Controller\Game;


use Common\Controller\BaseController;

class ChatController extends BaseController
{
    //系统配置表
    private $systemConfModel = null;

    public function __construct()
    {

        $this->systemConfModel = D('sys_conf');

        parent::__construct();

    }

    /**
     * 游戏开关
     * Author:lbb
     */
    public function index()
    {

        $condition['groupId'] = array('in','1014,1015,1016,1017,1018,1019,1049,1050');
        //修改操作
        if (IS_AJAX){
            $where['groupId'] = $_POST['groupId'];
            $data['value']    = $_POST['value'];
            $status = $this->systemConfModel->where($where)->save($data);

            # 写入后台日志
            $this->adminLogModel->record($_POST);

            if ($status === FALSE)
            {
                return returnAjax('400','保存失败~~~');
            }else{
                return returnAjax('200','SUCCESS');
            }
        }
        //获取默认值
        $list = $this->systemConfModel->where($condition)->field('name,groupId,value')->select();
        $this->assign('list',$list);
        $this->display();
    }

}