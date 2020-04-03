<?php
/**
 * 代理团队
 * User: Lbb
 * Date: 2018/7/30 0030
 * Time: 16:36
 */
namespace Admin\Controller\Team;
use Common\Controller\BaseController;
use Admin\Library\Validate;

class AgentController extends BaseController
{
    //分页
    private $pageSize = 50;

    //team_agent表
    private $teamAgentModel;

    //team_member
    private $teamMemberModel;

    private $userModel;

    //验证类
    private $validate;


    public function __construct()
    {
        parent::__construct();

        $this->teamAgentModel = D('team_agent');

        $this->teamMemberModel = D('team_member');

        $this->userModel      = D('user');

        $this->validate      = new Validate();
    }

    /**
     * 查看
     * Author:lbb
     */
    public function index()
    {
        if (IS_AJAX) {
            //判断切换标签  玩法标签 难度
            $page = (int)$_REQUEST['p'];
            $this->team($page);
            $data['content'] = $this->fetch('Team/agent/replace');
            return returnAjax(200, 'SUCCESS', $data);
        }
        $this->display();
    }


    /**
     * csv
     * Author:lbb
     */
    public function indexCsv(): void
    {
        $page = (int)$_REQUEST['p'];
        $this->team($page);
    }

    /**
     * 代理团队列表
     * Author:lbb
     * @param $page
     */
    public function team($page): void
    {
        # 条件
        $teamName  = I('get.teamName');
        $starttime = I('get.starttime');
        $stoptime  = I('get.stoptime');
        if ( $teamName ) {
            $condition['a.teamName'] = array('like',"%$teamName%");
            $where['teamName'] =  $condition['a.teamName'];
        }
        if ( $starttime ) {
            $condition['d.day_time'] = array('egt',strtotime($starttime));
        }

        if ( $stoptime ){
            $condition['d.day_time'] = array('elt',strtotime($stoptime));
        }
        if ( $starttime && $stoptime ){
            $condition['d.day_time'] = array('between',array(strtotime($starttime),strtotime($stoptime)));
        }

        $condition['a.isdel'] = 1;

        # 排序 字段
        $field = I('get.field','a.teamId');
        # 排序 顺序
        $order = I('get.order','asc');

        $where['isdel'] = 1;
        $count = $this->teamAgentModel->where($where)->count();// 查询满足要求的总记录数

        $Page = new \Think\Ajaxpage($count, $this->pageSize, 'indexAjaxComm');// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出

        $list = $this->teamAgentModel
            ->alias('a')
            ->field('a.uid,a.teamId,a.teamName,count(distinct b.uid) as team_people, sum(distinct c.gold)+sum(distinct c.bank) as golds,sum(d.send_gold) as send,sum(d.take_gold) as take,sum(d.send_gold)-sum(d.take_gold) as diff,sum(send_num) as send_times ,sum(send_people) as send_people')
            ->join('left join (select uid,teamId from yq_team_member where isdel = 1) b on a.teamId=b.teamId')
            ->join('left join  (select uid,gold,bank from yq_account ) c on c.uid = b.uid')
            ->join('left join yq_vip_day_trading d on d.uid = b.uid')
            ->where($condition)
            ->order("{$field} {$order}")
            ->page($page, $this->pageSize)
            ->group('a.teamId')
            ->select();

        foreach ($list as $k=>$v){
            $list[$k]['teamLeader'] = $this->userModel->where('uid='.$v['uid'])->getField('nickname');
        }



        $lists = $this->teamAgentModel
            ->alias('a')
            ->field('a.uid,a.teamName,count(distinct b.uid) as team_people,sum(distinct c.gold)+sum(distinct c.bank) as gold,sum(d.send_gold) as send,sum(d.take_gold) as take,sum(d.send_gold)-sum(d.take_gold) as diff,sum(send_num) as send_times ,sum(send_people) as send_people,sum(d.take_gold)/sum(d.send_gold) as than')
            ->join('left join (select uid,teamId from yq_team_member where isdel = 1) b on a.teamId=b.teamId')
            ->join('left join  (select uid,gold,bank from yq_account ) c on c.uid = b.uid')
            ->join('left join yq_vip_day_trading d  on d.uid = b.uid')
            ->where($condition)
            ->group('a.teamId')
            ->select();

        foreach ($lists as $k=>$vv){
            $lists[$k]['teamLeader'] = $this->userModel->where('uid='.$vv['uid'])->getField('nickname');
            unset($lists[$k]['uid']);
        }


        $total = [];
        foreach ($lists as $k=>$val){
            $total['gold_total']       += $val['gold'];         //总资产
            $total['send_num_total']   += $val['send'];         //赠送金额
            $total['take_num_total']   += $val['take'];         //接收金额
            $total['diff_num_total']   += $val['diff'];         //顺差
            $total['send_count_total'] += $val['send_times'];   //赠送笔数
        }

        # 总赠送比
      /*  if ($total['send_num_total'] === 0 ||  $total['take_num_total']=== 0 ){
            $total['than_total'] = 0.000;
        }else{
            $total['than_total'] = $total['take_num_total']/$total['send_num_total'];
        }*/


        if (I('get.file') === 'csv')
        {
            $header   = array('团队昵称','团队负责人','团队人数','团队资产','礼物赠送(A)','礼物接收(B)','顺差','赠送笔数','赠送人数','赠送比');
            $filename = '代理团队列表'.date('Y-m-d');
            $data['csv'] =  outCsv($header,$lists, $filename);

        }


        $this->assign('total',$total);  // 总计
        $this->assign('page', $show);   // 赋值分页输出
        $this->assign('list', $list);   // 查询结果


    }



    /**
     * 添加团队
     * Author:lbb
     */
    public function add()
    {
        if (IS_AJAX) {
            $data  = $_POST;

            //验证所填数据不能为空
            $this->checkInput($data['teamName'],'代理团队昵称不可为空~~');
            $this->checkInput($data['uid'],'团队负责人ID不可为空~~');
           /* $this->checkInput($data['phone'],'负责人手机号不可为空~~');
            $this->checkInput($data['wechat'],'负责人微信号不可为空~~');
            $isTrue = $this->validate->regex($data['phone'],'mobile');*/

            //验证手机号
           /* if ($isTrue === false) {
                return returnAjax(400,'请输入正确手机号~~');
            }*/

            $data['createtime'] = time();
            $condition['uid'] = $data['uid'];

            //验证uid 是否存在
            $user = $this->userModel->field('uid,nickname,level')->where($condition)->find();
            if(!$user){
                return returnAjax(400,'此ID系统不存在,请核实~~');
            }
            if( $user['level'] < 1 ){
                return returnAjax(400,'此用户不是VIP不可创建团队,请到玩家列表设置VIP等级~~');
            }

            //验证此人是否在代理团队明细表中有记录
            $teamUid = $this->teamMemberModel->field('teamId,isdel')->where($condition)->find();

            //写入后台日志
            $this->adminLogModel->record($_POST);

            if ($teamUid)
            {    //验证是否有归属团队
                if($teamUid['teamId']>0 && $teamUid['isdel']==='1') {
                    return returnAjax(400,'此ID已有归属代理团队,请核实~~');
                }else{
                    //获取负责人昵称
                    $data['teamLeader'] = $user['nickname'];

                    $status = $this->teamAgentModel->add($data);
                    if ($status) {
                        $data_member_save['teamId']     = $status;
                        $data_member_save['level']      = $user['level'];
                        $data_member_save['is_leader']  = 1;
                        $data_member_save['createtime'] = time();
                        $data_member_save['isdel']      = 1;
                        $where_save['uid'] = $data['uid'];
                        $status = $this->teamMemberModel->where($where_save)->save($data_member_save);
                        if ($status === FALSE){
                            return returnAjax(400,'添加失败~~');
                        } else{
                            return returnAjax(200,'SUCCESS');
                        }
                    }else {
                        return returnAjax(400,'添加失败~~');
                    }
                }
            }else{
                //获取负责人昵称
                $data['teamLeader'] = $user['nickname'];

                $status = $this->teamAgentModel->add($data);
                if ($status) {
                    $data_member['teamId']    = $status;
                    $data_member['uid']       = $user['uid'];
                    $data_member['level']     = $user['level'];
                    $data_member['is_leader'] = 1;
                    $data_member['createtime'] = time();
                    $this->teamMemberModel->add($data_member);
                    return returnAjax(200,'SUCCESS');
                }else {
                    return returnAjax(400,'添加失败~~');
                }
            }
        }
        $this->display();
    }

    /**
     * 查看
     * Author:lbb
     */
    public function show()
    {
        $condition['teamId'] = I('get.teamId');
        $team = $this->teamAgentModel->where($condition)->find();
        $this->assign('team',$team);
        $this->display();
    }

    /**
     * 编辑
     * Author:lbb
     */
    public function edit()
    {
        if (IS_AJAX) {

            $data  = $_POST;
            $teamId = $data['teamId'];
            unset($data['teamId']);
            $where_team['teamId'] = $teamId;    //修改的条件

            //验证所填数据不能为空
            $this->checkInput($data['teamName'],'代理团队昵称不可为空~~');
            $this->checkInput($data['uid'],'团队负责人ID不可为空~~');
            $this->checkInput($data['phone'],'负责人手机号不可为空~~');
            $this->checkInput($data['wechat'],'负责人微信号不可为空~~');
            $isTrue = $this->validate->regex($data['phone'],'mobile');

            //验证手机号
            if ($isTrue === false){
                return returnAjax(400,'请输入正确手机号~~');
            } 

            $data['updatetime'] = time();
            $condition['uid'] = $data['uid'];

            //验证uid 是否修改过
            $old_team_uid = $data['old_team_uid'];
            unset($data['old_team_uid']);
            if ($old_team_uid !==  $data['uid']) {
                //验证uid 是否存在
                $user = $this->userModel->field('uid,nickname,level')->where($condition)->find();
                if(!$user) {
                    return returnAjax(400,'此ID系统不存在,请核实~~');
                }

                //验证此人是否有代理团队
                $teamUid = $this->teamAgentModel->field('teamName')->where($condition)->find();
                if($teamUid) {
                    return returnAjax(400,"此ID已有归属代理团队,代理团队昵称为{$teamUid['teamName']},请核实~~");
                }

                //获取负责人昵称
                $data['teamLeader'] = $user['nickname'];
            }

            $status = $this->teamAgentModel->where($where_team)->save($data);



            if ( $status === FALSE ) {
                return returnAjax(400,'FALSE');
            }else {
                //验证uid 是否修改过
                if ($old_team_uid !==  $data['uid']) {
                    //修改是否是领导者
                    $data_leader['is_leader'] = 0;
                    $where_leader['uid'] = $old_team_uid;
                    $this->teamMemberModel->where($where_leader)->save($data_leader);

                    //$where_member['teamId']  = $teamId;
                    $where_member['uid']     = $data['uid'];
                    $isHave = $this->teamMemberModel->where($where_member)->find();

                    //验证修改过得uid 是否在团队里 如果不在则执行添加操作
                    if (!$isHave) {
                        $data_member['teamId']    = $teamId;
                        $data_member['uid']       = $user['uid'];
                        $data_member['level']     = $user['level'];
                        $data_member['is_leader'] = 1;
                        $data_member['createtime'] = time();
                        $this->teamMemberModel->add($data_member);
                    }else{
                        $data_member_del['isdel']     = 1;
                        $data_member_del['teamId']    = $teamId;
                        $data_member_del['is_leader'] = 1;
                        $this->teamMemberModel->where($where_member)->save($data_member_del);
                    }
                }
                //写入后台日志
                $this->adminLogModel->record($_POST);
                return returnAjax(200,'SUCCESS');
            }
        }else{
            $where_team['teamId'] = I('get.teamId');
            $team = $this->teamAgentModel->where($where_team)->find();
            $this->assign('team',$team);
            $this->display();
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