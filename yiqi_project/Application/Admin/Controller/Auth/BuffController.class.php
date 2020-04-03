<?php
/**
 * 超级管理员点控
 * User: LBB
 * Date: 2019/6/28 0015
 * Time: 下午 01:43
 */

namespace Admin\Controller\Auth;
use Common\Controller\BaseController;

class BuffController extends BaseController
{
    //数据库对象
    private $buffModel;

    /**
     * 初始化类
     */
    public function __construct()
    {
        parent::__construct();

        //获取数据对象
        $this->buffModel = D('buff');

    }



    /**
     * 管理员列表
     */
    public function index() :void
    {
        if(IS_AJAX){
            $condition = [];

            //获取管理员日志总数
            $count = $this->buffModel->where($condition)->count();

            $Data  = $this->buffModel
                ->field('yq_buff.id,yq_buff.uid,yq_buff.buffsate,yq_user.nickname')
                ->join('left join yq_user on yq_buff.uid = yq_user.uid')
                ->where($condition)
                ->select();

            $result = array('total' => $count, 'rows' => $Data ?:[]);

            $this->ajaxReturn($result,'JSON');
        }
        $this->display();
    }

    /**
     * 新增点控成员
     */
    public function add() :void
    {
        if (IS_POST) {
            if ($post = I('post.buff', [], 'strip_tags')) {
                $addData['uid'] = $post['uid'];
                $addData['createtime'] = time();
                $msg = $this->buffModel->add($addData);
                if ($msg) {
                    $this->success('新增点控成员成功');
                } else {
                    $this->error('新增点控成员失败');
                }
            }
        }
        $fromValidate = [
            'rule[uid]' => 'required;'
        ];
        //表单验证配置
        $this->assign('fromValidate', json_encode($fromValidate));
        $this->display();
    }

    /**
     * 编辑管理员
     */
    public function edit($id = 0) :void
    {
        if (!$buffData = $this->buffModel->where(['id' => $id])->find()) {
            $this->error('点控成员数据不存在~~~');
        }
        if (IS_POST) {
            if ($post = I('post.buff', [], 'strip_tags')) {
                $saveData['buffsate']=  $post['buffsate'];
                $msg = $this->buffModel->where(['id' => $id])->save($saveData);
                if ($msg !== FALSE ) {
                    $this->success('设置点控成功');
                } else {
                    $this->error('设置点控失败');
                }
            }
        } else {
            $fromValidate = [
                'rule[uid]' => 'required;'
            ];
            //表单验证配置
            $this->assign('fromValidate', json_encode($fromValidate));
            $this->assign('buffData', $buffData);
            $this->display();
        }
    }

    /**
     * 删除
     * Author:lbb
     */
    public function delete()
    {
        if (!$ids = I('get.ids', '')) {
            $this->error('删除规则id不存在');
        }

        //删除id 列表
        if (!$this->buffModel->where(['id' => $ids])->delete()) {
            $this->error('规则删除失败');
        }

        $this->success('删除成功');
    }




}