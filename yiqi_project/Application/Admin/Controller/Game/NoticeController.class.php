<?php
/**
 * 游戏公告
 * User: Lbb
 * Date: 2018/7/4 0004
 * Time: 14:51
 */
namespace Admin\Controller\Game;
use Common\Controller\BaseController;

class NoticeController extends BaseController
{
    //数据对象
    private $model;

    private $adminModel;

    public function __construct()
    {
        parent::__construct();

        $this->model = D('notice');

        $this->adminModel = D('admin');

    }

    public function index()
    {
        if (IS_AJAX)
        {
            $list=$this->model->order('id desc')->select();
            //整理数据
            foreach ($list as $k=>$val){
                $where_admin['id'] = $val['adminId'];
                $user = $this->adminModel->where($where_admin)->field('username')->find();
                $list[$k]['admin_name'] = $user['username'];

                if ( $val['startTime'] <= time() && $val['endTime'] >=time() )
                {
                    $list[$k]['run_status'] = '<b style="color: #18bc9c">运行中</b>';
                }elseif ( $val['endTime'] < time() )
                {
                    $list[$k]['run_status'] = '<b style="color: red">已过期</b>';
                }elseif ( $val['startTime'] > time() )
                {
                    $list[$k]['run_status'] = '<b style="color: red">未开启</b>';
                }

                if ($val['status'] === '2'){
                    $list[$k]['run_status'] = '<b style="color: red">已停用</b>';
                }

                if ($val['type'] === '0') {
                    $list[$k]['type']= '游戏公告';
                }
            }
            $this->assign('list',$list); //查询结果
            $res['content'] = $this->fetch('Game/notice/replace');
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
            $this->checkInput($data['title'],'公告标题不可为空~~');
            $this->checkInput($data['content'],'公告内容不可为空~~');
            $this->checkInput($data['startTime'],'开始时间不可为空~~');
            $this->checkInput($data['endTime'],'结束时间不可为空~~');

            $data['startTime'] = strtotime($data['startTime']);
            $data['endTime']   = strtotime($data['endTime']);
            $data['adminId']   = $_SESSION['Admin_']['admin']['id'];
            $data['createtime']   =time();
            $data['status']    = 1;
            $status = $this->model->add($data);
            //写入后台日志
            $this->adminLogModel->record($_POST);
            if ($status)
            {
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

            $this->checkInput($data['content'],'公告内容不可为空~~');
            $this->checkInput($data['title'],'公告标题不可为空~~');
            $this->checkInput($data['startTime'],'开始时间不可为空~~');
            $this->checkInput($data['endTime'],'结束时间不可为空~~');

            $data['startTime']   = strtotime($data['startTime']);
            $data['endTime']     = strtotime($data['endTime']);
            $data['updatetime']  = time();

            $where['id']     = $data['id'];
            unset($data['id']);

            # 执行修改操作
            $status = $this->model->where($where)->data($data)->save();

            # 写入后台日志
            $this->adminLogModel->record($_POST);

            if ($status)
            {
                return returnAjax(200,'SUCCESS');
            }else {
                return returnAjax(400,'FAlSE');
            }
        }else{
            $id = I('get.id');
            $condition['id']     = $id;
            $list  =  $this->model->where($condition)->find(); // 查询结果
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
            # 执行修改操作
            $status = $this->model->where($condition)->save($data);
            # 写入后台日志
            $this->adminLogModel->record($_POST);
            if ($status)
            {
                return returnAjax(200,'SUCCESS');
            }else {
                return returnAjax(400,'FLASE');
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