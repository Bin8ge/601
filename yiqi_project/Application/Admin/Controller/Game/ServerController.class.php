<?php
/**
 * 停服开关
 * User: Lbb
 * Date: 2018/9/4 0004
 * Time: 10:30
 */

namespace Admin\Controller\Game;

use Common\Controller\BaseController;

class ServerController extends BaseController
{
    //系统配置表
    private $systemConfModel = null;

    public function __construct()
    {

        $this->systemConfModel = D('sys_conf');
        parent::__construct();

    }

    /**
     * 停服开关
     * Author:lbb
     */
    public function index()
    {

        //修改操作
        if (IS_AJAX){
            $where['groupId']   = (int)$_POST['groupId'];
            $data['value']      = $_POST['value'];
            $status = $this->systemConfModel->where($where)->save($data);

            # 写入后台日志
            $this->adminLogModel->record($_POST);

            if ($status === FALSE)
            {
                return returnAjax('400','保存失败~~~');
            }else{
                if ($where['groupId'] === 1020) {
                    $msg = $this->systemConfModel->where($where)->field('value_scope')->find();
                    if ($data['value'] === '1')
                    {
                        $server_data['Msg']   = $msg['value_scope'];
                    }else{
                        $server_data['Msg']   = 'SUCCESS';
                    }
                    $server_data['State'] = (int) $data['value'];
                    send_server($server_data,'/SystemMainten.php');
                }
                return returnAjax('200','SUCCESS');
            }
        }
        $condition['groupId'] = array('in','1020,1051');
        //获取默认值
        $list = $this->systemConfModel->where($condition)->field('name,groupId,value')->select();
        $this->assign('list',$list);
        $this->display();
    }




}