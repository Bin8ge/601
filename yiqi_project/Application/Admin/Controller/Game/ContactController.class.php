<?php
/**
 *联系我们
 * User: Lbb
 * Date: 2018/7/5 0005
 * Time: 13:43
 */

namespace Admin\Controller\Game;

use Common\Controller\BaseController;

class ContactController extends BaseController
{

    //回答表
    private $answerModel;

    private $userModel;

    private $adminModel;

    private $pageSize = 50;


    public function __construct()
    {
        parent::__construct();

        $this->answerModel = D('QuestionAnswer');

        $this->userModel = D('User');

        $this->adminModel = D('admin');

    }

    /**
     * Author:lbb
     */
    public function index() :void
    {
        if (IS_AJAX)
        {
            //搜索参数过滤处理
            $this->searchFilter();

            //获取查询条件
            [$where, $sort, $order, $offset, $limit] = $this->getSerachParam();

            //判断筛选条件是否存在
            if ($where) {
                $where = implode(' and ', array_values($where));
            }

            #查询满足要求的总记录数
            $count  = $this->answerModel
                ->alias('a')
                ->where($where)
                ->count('distinct askuid');

            $list   = $this->answerModel
                ->alias('a')
                ->field('askuid as id,askuid,nickname,max(a.createtime) as time')
                ->join('left join yq_user b on a.askuid = b.uid ')
                ->where($where)
                ->group('askuid')
                ->limit($offset, $limit)
                ->order('time desc')
                ->select();

            foreach ($list as $k=>&$val){
                //回复人员
                $condition['answerUid'] = $val['askuid'];
                $condition['askuid']    = $val['askuid'];
                $condition['_logic']    = 'or';
                $answer = $this->answerModel->where($condition)->field('answerUid,askuid,content,createtime,type')->order('id desc')->find();

                if ($answer['type'] === '1'){
                    $where_user['id'] = $answer['answerUid'];
                    $user = $this->adminModel->where($where_user)->field('username')->find();
                    $val['sysname'] = $user['username'] ?: '';
                }else{
                    $val['sysname'] = '';
                }
                $val['content']    = $answer['content'];
                $val['createtime'] = $answer['createtime'];
            }

            //返回json数据给ajax
            $result = array('total' => $count, 'rows' => $list ?: []);
            $this->ajaxReturn($result, 'JSON');


        }

        $this->display();
    }

    /**
     * 搜索参数过滤
     * Author:lbb
     */
    private function searchFilter() :void
    {
        if (I('get.filter')) {
            $result = (array)I('get.filter', '', 'json_decode');
            $option = (array)I('get.option', '', 'json_decode');
            if (isset($result['select_or_text_select'])) {

                if ($result['select_or_text_text']) {
                    $result[$result['select_or_text_select']] = $result['select_or_text_text'];
                    $option[$result['select_or_text_select']] = $option['select_or_text_text'];
                }
                unset($result['select_or_text_select'], $result['select_or_text_text']);
                unset($option['select_or_text_select'], $option['select_or_text_text']);
            }
        }
        $_GET['filter'] = json_encode($result);
        $_GET['option'] = json_encode($option);
    }

    /**
     * 查看
     * Author:lbb
     * @param $id
     */
    public function see( $id ) :void
    {
        if (IS_AJAX){
            $data['content']    = I('post.content');
            $data['answerUid']  = (int)$_SESSION['Admin_']['admin']['id'];
            $data['type']       = 1;
            $data['createtime'] = time();
            $data['askuid']     = (int)I('post.askuid');
            if (!$data['content']){
                $this->error('400', '请输入内容后在提交~~~');
            }

            $addStatus  = $this->answerModel->add($data);

            # 写入后台日志
            $this->adminLogModel->record($_POST);

            if (!$addStatus)
            {
                $this->error('400','FALSE');
            }else{
                #发送服务器
                $serverData['SendUserID'] = $data['answerUid'];
                $serverData['ReUserID']   = $data['askuid'];
                $serverData['createtime'] = $data['createtime'];
                $serverData['Content']    = (string)$data['content'];
                $action = '/PushCustomer.php';
                #send_server($serverData, $action);
                $this->success('成功');
            }
        }else{
            $askuid = $id;
            $where['answerUid'] = $askuid;
            $where['askuid'] = $askuid;
            $where['_logic'] = 'or';
            $list = $this->answerModel->where($where)->order('id asc')->select();

            foreach ($list as $k => $val) {
                if ($val['type'] === '1') {
                    $where_admin['id'] = $val['answerUid'];
                    $admin = $this->adminModel->where($where_admin)->field('username')->find();
                    $list[$k]['nickname'] = $admin['username'];
                } else {
                    $where_user['uid'] = $val['askuid'];
                    $user = $this->userModel->where($where_user)->field('nickname')->find();
                    $list[$k]['nickname'] = $user['nickname'];
                    $SendUserID = $val['answerUid'];
                }
            }
            $this->assign('SendUserID', $SendUserID);
            $this->assign('askuid', $askuid);
            $this->assign('list', $list); //查询结果
            //表单验证配置
            $this->assign('fromValidate', json_encode([
                'askuid' => 'required;',
            ]));
        }

        $this->display();

    }


    /**
     * 删除
     * Author:lbb
     */
    public function delete()
    {
        if (IS_AJAX){
            if (!$ids = I('get.ids', '')) {
                $this->error('删除规则ids不存在');
            }
            $condition['askuid'] =['in', $ids];
            # 执行修改操作
            $status = $this->answerModel->where($condition)->delete();
            # 写入后台日志
            $this->adminLogModel->record($_POST);
            if ($status)
            {
                $this->success('删除成功！！！');
            }else {
                $this->error('删除失败~~~');
            }
        }

    }
}