<?php
/**
 * 邮件系统
 * User: lbb
 * Date: 2019/3/8
 * Time: 11:08
 */

namespace Admin\Controller\Game;
use Common\Controller\BaseController;

class MessageController extends BaseController
{
    //数据对象  赠送表
    private $sendModel ;

    //user 表
    private $userModel ;

    private $pageSize = 50;

    public function __construct()
    {
        parent::__construct();

        $this->sendModel     = D('send_present');

        $this->userModel     = D('user');

    }




    public function index()
    {
        if(IS_AJAX){
            $condition = [];
            //搜索条件
            $startTime = I('get.startTime');
            $endTime   = I('get.endTime');
            $updateStartTime = I('get.updateStart');
            $updateEndTime   = I('get.updateEnd');
            if ($startTime && $endTime) {
                $condition['createtime'] = array('between',array(strtotime($startTime),strtotime($endTime)));
            }
            if ($updateStartTime && $updateEndTime) {
                $condition['updatetime'] = array('between',array(strtotime($updateStartTime),strtotime($updateEndTime)));
            }
            if(is_numeric(I('get.uid'))) {
                $condition['uid'] = I('get.uid');
            }
            if(is_numeric(I('get.isget'))) {
                $condition['isget'] = I('get.isget');
            }
            if(!empty(trim(I('get.title')))) {
                $condition['title'] = array('like',trim(I('get.title')).'%');
            }

            # 总条数 分页
            $count = $this->sendModel->where($condition)->count();// 查询满足要求的总记录数
            $Page  = new \Think\Ajaxpage($count,$this->pageSize,'indexAjaxComm');// 实例化分页类 传入总记录数和每页显示的记录数(25)
            $show  = $Page->show();// 分页显示输出
            $page  = (int)$_REQUEST['p'];

            //查询数据
            $list=$this->sendModel
                ->where($condition)
                ->order('id desc')
                ->page($page,$this->pageSize)
                ->select();

            foreach ($list as $k=>$v){
                if($v['isget']==='0'){
                    $list[$k]['status']='未领取';
                }else{
                    $list[$k]['status']='已领取';
                }
                $whereUser['uid'] = $v['uid'];
                $user = $this->userModel->where($whereUser)->field('nickname')->find();
                $list[$k]['nickname'] = $user['nickname'];
            }

            $this->assign('page',$show); // 赋值分页输出
            $this->assign('list',$list); //查询结果

            //ajax返回信息，就是要替换的模板
            $res['content'] = $this->fetch('Game/message/replace');
            return returnAjax(200,'SUCCESS',$res);
        }
        $this->display();
    }

    /**
     * 发送邮件
     * Author:lbb
     */
    public function add()
    {
        if(IS_AJAX){
            $data = $_POST;

            if ((int)$data['is_online'] > 0 || (int)$data['send_type'] > 0) {
                $condition = [];
                if ($data['is_online']==='1') {
                    $condition['onLine'] = 1;
                }
                if ($data['send_type'] === '1') {
                    $condition['level'] = array('gt',0);
                }elseif ($data['send_type'] === '2') {
                    $condition['equipment_type'] = 2;
                    $condition['level'] = 0;
                }elseif ($data['send_type'] === '3') {
                    $condition['level'] = 0;
                }

                $uidTwo = $this->userModel->where($condition)->field('uid')->select();
                $uid = array_column($uidTwo,'uid');
            } else {
                //验证是否有值
                if (!$data['sendId']) {
                    return returnAjax('400', '请输入收件人ID~~');
                }
                $uid = explode(';', trim($data['sendId']));
            }

            foreach ($uid as $k => $val) {
                $where_user['uid'] = $val;
                $uid[$k] = $this->userModel->field('uid,nickname,avatar')->where($where_user)->find();
                if (!$uid[$k]) {
                    return returnAjax('400', "{$val} 的用户在系统中不存在请核实~~");
                    exit;
                }
            }

            //对接收到的值进行验证
            if ( empty($data['title']) ) {
                return returnAjax('400','标题不可为空~~');
            }
            if ( empty($data['disc']) )  {
                return returnAjax('400','描述不可为空~~');
            }
            if ( !is_numeric($data['totalGive'])  ||  $data['totalGive'] < 0 ) {
                return returnAjax('400','数量 非法参数,请输入大于0的整数~~');
            }

            $time = time();
            $code = 200;
            $loseUid = [];
            foreach ($uid as $k=>$val){
                unset($data['is_online'],$data['send_type']);
                $data['uid']        = $val['uid'];        //接受ID
                $data['sendname']   = '游戏官方';          //赠送者名字
                $data['sendicon']   = '';                 //赠送者头像
                $data['revicename'] = $val['nickname'];   //接受者名字
                $data['reviceicon'] = $val['avatar'];     //接受者头像
                $data['giveNum']    = $data['totalGive']; //用户获得数
                $data['sendId']     = 0;
                $data['createtime'] = $time;
                $data['type']       = 0;
                $data['adminId']    =  $this->auth->id;    //操作人员ID
                $msg = $this->sendModel->add($data);
                if ($msg) {
                    #发送服务器
                    $serverData['id']         = (int)$msg;
                    $serverData['uid']        = (int)$val['uid'];
                    $serverData['totalGive']  = (int)$data['totalGive'];
                    $serverData['createtime'] = $time;
                    $serverData['isGet']      = 0;
                    $serverData['title']      = (string)$data['title'];
                    $serverData['text']       = (string)$data['disc'];
                    $action = '/PushPresent.php';
                    send_server($serverData, $action);
                }else{
                    $code = 400;
                    $loseUid[] = $val['uid'];
                }
            }
            $this->adminLogModel->record($_POST);
            if ($code === 200){
                return returnAjax('200','SUCCESS');
            }else{
                return returnAjax('400','发送失败'.implode(';',$loseUid));
            }

        }
        $this->display();
    }







}