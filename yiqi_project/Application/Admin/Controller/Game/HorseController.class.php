<?php
/**
 * 添加跑马灯
 * User: Lbb
 * Date: 2018/7/5 0005
 * Time: 14:41
 */

namespace Admin\Controller\Game;

use Common\Controller\BaseController;

class HorseController extends BaseController
{
    //数据对象
    private $model;
    //admin表
    private $adminModel;

    private $pageSize = 50;

    public function __construct()
    {
        parent::__construct();

        $this->model = D('Marquee');

        $this->adminModel = D('Admin');
    }

    public function index()
    {
        if (IS_AJAX)
        {
            $condition = [];
            $page  = (int)$_REQUEST['p'];

            # 查询满足要求的总记录数
            $count = $this->model->where($condition)->count();

            # 实例化分页类 传入总记录数和每页显示的记录数(25)
            $Page  = new \Think\Ajaxpage($count,$this->pageSize,'indexAjaxComm');

            # 分页显示输出
            $show  = $Page->show();
            $list=$this->model
                ->where($condition)
                ->order('id desc')
                ->page($page,$this->pageSize)
                ->select();

            foreach ($list as $k=>$val){
                $list[$k]['runstatus']  = $val['endTime']<time()? '已过期': '运行中';
                if ($val['status'] === '2'){
                    $list[$k]['runstatus']  = '已停止';
                }
                if ($val['type'] === '100'){
                    $list[$k]['type']  = '所有人弹板';
                }elseif($val['type'] === '200'){
                    $list[$k]['type']  = 'VIP弹板';
                }elseif($val['type'] === '300'){
                    $list[$k]['type']  = '玩家弹板';
                }else{
                    $list[$k]['type']  = '正常';
                }
                $where['id'] = $val['adminId'];
                $admin = $this->adminModel->where($where)->field('username')->find();
                $list[$k]['adminId'] = $admin['username'];
            }

            $this->assign('page',$show); // 赋值分页输出
            $this->assign('list',$list); //查询结果

            //ajax返回信息，就是要替换的模板
            $res['content'] = $this->fetch('Game/horse/replace');
            return returnAjax(200,'SUCCESS',$res);
        }
        $this->display();
    }



    /**
     * Author:lbb
     * 添加
     */
    public function add()
    {
        if (IS_AJAX)
        {
            $data = $_POST;
            $this->checkInput($data['content'],'跑马灯内容不可为空~~');
            $this->checkInput($data['startTime'],'开始时间不可为空~~');
            $this->checkInput($data['endTime'],'结束时间不可为空~~');
            if (!$data['interval'] || !is_numeric($data['interval']) || $data['interval'] < 0){
                return returnAjax('400','播放间隔为空或非法参数~~');
            }
            if (!$data['repeatTime'] || !is_numeric($data['repeatTime']) || $data['repeatTime']< -1 ) {
                return returnAjax('400','重复次数为空或非法参数~~');
            }
            $data['startTime']  = strtotime($data['startTime']);
            $data['endTime']    = strtotime($data['endTime']);
            $data['adminId']    = $_SESSION['Admin_']['admin']['id'];
            $data['status']     = 1;
            $data['createtime'] = time();
            $status = $this->model->add($data);
            # 写入后台日志
            $this->adminLogModel->record($_POST);
            if ($status)
            {
                send_server([ 'disc'=>'跑马灯添加'],'/LineLight.php');
                return returnAjax(200,'SUCCESS');
            }else {
                return returnAjax(400,'FAlSE');
            }
        }
        $this->display();
    }

    /**
     * Author:lbb
     * 编辑
     */
    public function edit()
    {
        if(IS_AJAX)
        {
            $data = $_POST;
            $this->checkInput($data['content'],'跑马灯内容不可为空~~');
            $this->checkInput($data['startTime'],'开始时间不可为空~~');
            $this->checkInput($data['endTime'],'结束时间不可为空~~');
            if (!$data['interval'] || !is_numeric($data['interval']) || $data['interval']<0){
                return returnAjax('400','播放间隔为空或非法参数~~');
            }
            if (!$data['repeatTime'] || !is_numeric($data['repeatTime']) || $data['repeatTime']<-1){
                return returnAjax('400','重复次数为空或非法参数~~');
            }
            $data['startTime']    = strtotime($data['startTime']);
            $data['endTime']      = strtotime($data['endTime']);
            $where['id']          = $data['dataid'];
            unset($data['dataid']);
            $list = $this->model->where($where)->find();
            $is_change = FALSE;
            foreach ($data as $k=>$val){
                if ($list[$k] !== $val){
                    $arr[$k] = $val;
                    $is_change = TRUE;
                }
            }
            if (!$is_change) {
                return returnAjax(400,'请修改内容后，在提交~~');
            }
            $data['updatetime']   = time();
            $status = $this->model->where($where)->data($data)->save();
            # 写入后台日志
            $this->adminLogModel->record($_POST);
            if ($status)
            {
                send_server([ 'disc'=>'跑马灯修改'],'/LineLight.php');
                return returnAjax(200,'SUCCESS');
            }else {
                return returnAjax(400,'FAlSE');
            }
        }else{
            $id = I('get.id');   $condition['id']     = $id;
            $list=$this->model->where($condition)->find();
            $list['startTime'] = date('Y-m-d H:i:s',$list['startTime']);
            $list['endTime'] = date('Y-m-d H:i:s',$list['endTime']);
            $this->assign('list',$list);
            $this->display();
        }
    }



    /**
     * Author:lbb
     * 删除
     */
    public function del()
    {
        if (IS_AJAX){
            $condition['id'] = I('post.postId');
            $data['status'] = I('post.editData');
            $status = $this->model->where($condition)->save($data);
            # 写入后台日志
            $this->adminLogModel->record($_POST);
            if ($status)
            {
                send_server([ 'disc'=>'跑马灯修改'],'/LineLight.php');
                return returnAjax(200,'SUCCESS');
            }else {
                return returnAjax(400,'false');
            }
        }
    }

    /**
     * 验证字段是否为空
     * Author:lbb
     * @param $field    字段名
     * @param $msg      提示语
     */
    public function checkInput($field,$msg)
    {
        if (!$field){
            return returnAjax(400,$msg);
            exit;
        }
    }






}