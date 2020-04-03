<?php
/**
 * 用户控制器
 * User: lbb
 * Date: 2019/2/18
 * Time: 16:10
 */

namespace Admin\Controller\Player;

use Admin\Library\Validate;
use Common\Controller\BaseController;


class UserController extends BaseController
{
    //数据对象
    private $model;

    //获取用户相关的视图模型
    private $newModel;

    //账户表
    private $accountModel;

    //管理员操作表
    private $handleLogModel;

    //团队成员表
    private $teamMemberModel;

    //团队表
    private $teamAgentModel;

    //点控表
    private $pointControlModel;

    //用户账户视图
    private $userAccountView;

    private $sendTakeModel;

    private $userLogModel;


    /**
     * 初始化
     */
    public function __construct()
    {
        parent::__construct();

        $this->model             = D('User');

        $this->newModel          = D('users');

        $this->accountModel      = D('account');

        $this->handleLogModel    = D('HandleLog');

        $this->teamMemberModel   = D('TeamMember');

        $this->teamAgentModel    = D('team_agent');

        $this->pointControlModel = D('point_control');

        $this->userAccountView   = D('user_account');

        $this->sendTakeModel     = D('send_take');

        $this->userLogModel     = D('user_log');

    }


    /**
     * 全部玩家
     * Author:lbb
     */
    public function index() :void
    {
        if (IS_AJAX) {

            //搜索参数过滤处理
            $this->searchFilter();

            //获取查询条件
            [$where, $sort, $order, $offset, $limit] = $this->getSerachParam();

            //判断筛选条件是否存在
            if ($where) {
                $where = implode(' and ', array_values($where));
            }

            //获取查询数量
            $count = $this->newModel->where($where)->count('uid');

            //分页数据
            $data = $this->newModel->where($where)
                ->field('room,uid,is_online,nickname,level,gold,user_lose_win_all,daily_gold,daily_stake,total_send,total_receive,createtime,logintime,point_control_controlSum,point_control_progress,point_control_start_time,point_control_status,point_control_type')
                ->limit($offset, $limit)
                ->order([$sort => $order])
                ->select();


            //总计数据
            /*  $total = $this->newModel
                  ->where($where)
                  ->field('count(uid) as total_user_number,count(is_online="1" or NULL) as online_total_user_number,sum(gold) as user_gold_number,sum(user_lose_win_all) as user_total_lose_win_all_number,sum(daily_gold) as user_total_lose_win_today_number,sum(total_send) as user_total_send,sum(total_receive) as user_total_give')
                  ->select();*/

            //如果数据存在 则将统计信息放入
            if ($data) {
                $room = $this->query_room();
                foreach ($data as $key => $val) {
                    if ($val['is_online']==='0'){
                        $data[$key]['is_online'] = '离线';
                    }else{
                        $data[$key]['is_online'] = $room[$val['room']];
                    }
                    $data[$key]['level']                    = $this->FieldConfig['level'][$val['level']];
                    $data[$key]['gold']                     = number_format($val['gold']);
                    $data[$key]['user_lose_win_all']        = number_format($val['user_lose_win_all']);
                    $data[$key]['daily_gold']               = number_format($val['daily_gold']);
                    $data[$key]['daily_stake']               = number_format($val['daily_stake']);
                    $data[$key]['total_send']               = number_format($val['total_send']);
                    $data[$key]['total_receive']            = number_format($val['total_receive']);
                    $data[$key]['point_control_controlSum'] = number_format($val['point_control_controlSum']);
                    $data[$key]['point_control_progress']   = number_format($val['point_control_progress']);
                }
                //$data [0]['statistics'] = array_map('number_format',$total[0]);
            }




            //返回json数据给ajax
            $result = array('total' => $count, 'rows' => $data ?: []);
            $this->ajaxReturn($result, 'JSON');
        }

        //渲染模板
        $this->display();
    }


    /**
     * 玩家详情
     * Author:lbb
     * @param int $uid
     */
    public function detail($uid = 0) :void
    {
        //判断用户是否存在
        if (!$uid || !($data = $this->model->where(['uid' => $uid])->find())) {
            $this->error('用户ID没有找到');
        }

        //公共条件 用户id
        $where['uid'] = $uid;

        //用户信息
        $data=$this->model
            ->field('qq,uid,nickname,sign,mobile,createtime,regip,equipment_type,clientMark,logintime,loginIP,last_login_mac,level,status,onLine,room,first_room,is_focus,is_send_presend')
            ->where($where)
            ->find();
        # 注册设备类型
        $data['platform'] = $this->FieldConfig['platform'][$data['equipment_type']];

        //处理等级  在线 账户状态
        $room = $this->query_room();
        if ($data['onLine'] === '0') {
            $data['is_online'] = '离线';
        } else {
            $data['is_online'] = $room[$data['room']];
        }
        $data['first_room'] = $room[$data['first_room']];
        $data['is_closure'] = $data['status'];
        if($data['status'] === '1'){
            $data['status']='正常用户';
        }elseif($data['status'] === '0'){
            $data['status']='封停用户';
        }
        $data['levels'] = $data['level'];
        $data['level'] = $this->FieldConfig['level'][$data['level']];
        $data['mobile_type'] = getPhoneType($data['mobile']);


        //账户信息
        $account = $this->accountModel
            ->field('gold,bank,bunkogold,daily_gold,daily_time,sendprient,receiveprient,FoundGold')
            ->where($where)
            ->find();

        //当日输赢
        if( $account['daily_time'] < strtotime(date('Y-m-d')) ){
            $account['daily_gold'] = 0;
        }

        //合并个人信息与账户信息
        $data = array_merge($data,$account);

        //总资产
        $data['total_gold'] = $data['gold']+$data['bank'];

        //代理团队
        $team = $this->teamMemberModel->where($where)->field('teamId')->find();
        if ($team['teamId']>0){
            $teamInfo = $this->teamAgentModel->where(['teamId'=>$team['teamId']])->field('teamName')->find();
            $data['team'] = $teamInfo['teamName'];
        }else{
            $data['team'] = '无代理团队';
        }


        //点控
        $user_point=$this->pointControlModel
            ->field('controlSum as point_control_controlSum,type as point_control_type,plan as point_control_plan,progress as point_control_progress,endtime as point_control_end_time,createtime as point_control_start_time,status as point_control_status,staffId as point_control_admin_id')
            ->where($where)
            ->order('id desc')
            ->find()?:[];

        //合并点控信息
        $data = array_merge($user_point,$data);
        $data['point_status'] = $data['point_control_status'];
        $data['point_control_type']=$this->FieldConfig['point_control_type'][$data['point_control_type']];
        $data['point_control_plan']=$this->FieldConfig['point_control_plan'][$data['point_control_type']][$data['point_control_plan']];
        $data['point_control_status']=$this->FieldConfig['point_control_status'][$data['point_control_status']];


        //交易记录

        //从vip接收
        $where_take_vip = $where;
        $where_take_vip['send_level'] = array('gt',0);
        $where_take_vip['type']       = 'take';
        $where_take_vip['is_back']    = 0;
        $data['take_vip'] = $this->sendTakeModel->where($where_take_vip)->sum('take_gold');

        //从普通接受
        $where_take_people = $where;
        $where_take_people['send_level'] = 0;
        $where_take_people['type']       = 'take';
        $where_take_people['is_back']    = 0;
        $data['take_people'] = $this->sendTakeModel->where($where_take_people)->sum('take_gold');

        //向vip赠送
        $where_send_vip = $where;
        $where_send_vip['take_level'] = array('gt',0);
        $where_send_vip['type']       = 'send';
        $where_send_vip['is_back']    = 0;
        $data['send_vip'] = $this->sendTakeModel->where($where_send_vip)->sum('send_gold');

        //向普通赠送
        $where_take_people = $where;
        $where_take_people['take_level'] = 0;
        $where_take_people['type']       = 'send';
        $where_take_people['is_back']    = 0;
        $data['send_people'] = $this->sendTakeModel->where($where_take_people)->sum('send_gold');

        //当日赠送 接收
        //获取今天00:00
        $todayStart = strtotime(date('Y-m-d'));
        //获取今天24:00
        $todayEnd = strtotime(date('Y-m-d').'23:59:59');

        $where_day            = $where;
        $where_day['is_back'] = 0;
        $where_day['createtime'] = array('between',array($todayStart,$todayEnd));
        $sendTakeDay  = $this->sendTakeModel->field('sum(send_gold) as send_gold,sum(take_gold) as take_gold')->where($where_day)->find();

        $data['send_day'] =  $sendTakeDay['send_gold'];
        $data['take_day'] =  $sendTakeDay['take_gold'];


        $this->assign('safe_mobile_alink', build_toolbar(['safe_mobile'], 2)['safe_mobile']);
        $this->assign('lock_alink', build_toolbar(['lock'], 2)['lock']);
        $this->assign('unbind_mobile_alink', build_toolbar(['unbind_mobile'], 2)['unbind_mobile']);
        $this->assign('edit_resource_alink', build_toolbar(['edit_resource'], 2)['edit_resource']);
        $this->assign('change_mobile_alink', build_toolbar(['change_mobile'], 2)['change_mobile']);
        $this->assign('bind_mobile_alink', build_toolbar(['bind_mobile'], 2)['bind_mobile']);
        $this->assign('cancel_point_control_alink', build_toolbar(['cancel_point'], 2)['cancel_point']);
        $this->assign('kick_alink', build_toolbar(['kick'], 2)['kick']);
        $this->assign('account_alink', build_toolbar(['account'], 2)['account']);
        $this->assign('bank_alink', build_toolbar(['bank'], 2)['bank']);
        $this->assign('controller_path', '/' . $this->commonParam['module'] . '/' . $this->commonParam['group'] . '/' . $this->commonParam['controller']);
        $this->assign('data', $data);
        $this->assign('FieldConfig', $this->FieldConfig);

        //渲染模板
        $this->display();
    }


    /**
     * 转账
     * Author:lbb
     * @param int $uid
     */
    public function bank( $uid = 0 ) :void
    {
        $data = [];
        M()->startTrans();
        //检查用户是否存在
        if (!$uid || !$data = $this->userAccountView->lock(true)->field('uid,level,gold,bank')->where(['uid' => $uid])->find()) {
            $this->error('用户id不存在~~~');
        }

        if (IS_AJAX) {
            if ($post = I('post.resource', [], 'strip_tags')) {

                if ($data['bank'] === '0') {
                    M()->rollback();
                    $this->error('该银行账户金币已为0~~~');
                }

                //判断传入数值 是否为正负数
                if ( (int)$post['bank'] <= 0 ) {
                    M()->rollback();
                    $this->error('转账金币不能小于等于0~~~');
                }

                //判断账户金币是否够
                if ($data['bank']-$post['bank']<0) {
                    M()->rollback();
                    $this->error("转账金币不能大于{$data['bank']}~~~");
                }

                $take_uid = $post['take_uid'];
                $where_take['uid'] = $take_uid;
                $where_take['level'] = array('gt',0);
                if ( !$take_use = $this->userAccountView->field('level,gold,bank')->where($where_take)->find()) {
                    M()->rollback();
                    $this->error('转账用户不是VIP~~~');
                }



                # 赠送者账户
                $sendMsg = $this->accountModel->where(['uid' => $uid])->setDec('bank', (int)$post['bank']);

                # 接收者账户
                $takeMsg = $this->accountModel->where(['uid' => $take_uid])->setInc('bank',(int)$post['bank']);


                //修改账户数据
                if ( $takeMsg !== false && $sendMsg !== false ) {

                    $send_take_comm['send_level'] = $data['level'];
                    $send_take_comm['take_level'] = $take_use['level'];
                    $send_take_comm['createtime'] = time();
                    $send_take_comm['admin_id']   = $this->auth->id;
                    if ($data['level']>0 && $take_use['level']>0){
                        $send_take_comm['is_vip'] = 1;
                    }else{
                        $send_take_comm['is_vip'] = 0;
                    }
                    #赠送者
                    $send = $send_take_comm;
                    $send['uid'] = $uid;
                    $send['send_gold']  = (int)$post['bank'];
                    $send['type']       = 'send';
                    $send['take_uid']   = $take_uid;

                    $send_id = $this->sendTakeModel->add($send);

                    #接收者
                    $take = $send_take_comm;
                    $take['uid'] = $take_uid;
                    $take['take_gold']  = (int)$post['bank'];
                    $take['type']       = 'take';
                    $take['send_uid']   = $uid;
                    $take['send_id']    = $send_id;
                    $this->sendTakeModel->add($take);

                    M()->commit();
                    #判断是否有金币变化表
                    $this->createTable('yq_'.$uid);
                    $this->createTable('yq_'.$take_uid);

                    //添加金币变化记录
                    $gold_data['UserID']       = $uid;
                    $gold_data['first_type']   = 2;
                    $gold_data['second_type']  = 22;  #赠送
                    $gold_data['gold']         = -$data['bank'];
                    $gold_data['surplus_gold'] = $data['gold'];
                    $gold_data['surplus_bank'] = $data['bank']-$post['bank'];
                    $gold_data['createtime']   = time();
                    M((string)$uid,'yq_','DB_GAME_USER')->add($gold_data);


                    $gold_data['UserID']       = $take_uid;
                    $gold_data['first_type']   = 2;
                    $gold_data['second_type']  = 23;  #接收
                    $gold_data['gold']         = $data['bank'];
                    $gold_data['surplus_gold'] = $take_use['gold'];
                    $gold_data['surplus_bank'] = $take_use['bank']+$post['bank'];
                    $gold_data['createtime']   = time();
                    M((string)$take_uid,'yq_','DB_GAME_USER')->add($gold_data);


                    //封装写入数组
                    $user_data = [
                        'uid' => $uid,
                        'title' => '转账',
                        'admin_id' => $this->auth->id
                    ];

                    //写入用户日志
                    $this->handleLogModel->record($user_data, 'bank', [
                        'uid'      => $uid,
                        'take_uid' => $take_uid,
                        'bank'     => $data['bank'],
                    ]);

                    //写入日志
                    $this->adminLogModel->record($post);

                    $this->success('转账成功！！！');
                }else{
                    M()->rollback();
                    $this->success('转账失败~~~');
                }
            }
        }else{
            //表单验证配置
            $this->assign('fromValidate', json_encode([
                'resource[take_uid]' => 'required;',
                'resource[bank]'     => 'required;',
            ]));
            $form_id = "/uid/$uid";
            $this->assign('form_id', $form_id);
            $this->assign('data', $data);
            M()->rollback();
            $this->display();
        }

    }


    /**
     * 转到银行
     * Author:lbb
     * @param int $uid
     */
    public function account( $uid = 0 ) :void
    {
        if (IS_AJAX) {

            $data = [];
            //检查用户是否存在
            if (!$uid || !$data = $this->accountModel->field('uid,gold,bank')->where(['uid' => $uid])->find()) {
                $this->error('用户id不存在~~~');
            }

            if ($data['gold'] === '0') {
                $this->error('该账户金币已为0~~~');
            }

            $edit['bank'] = $data['bank'] +  $data['gold'];
            $edit['gold'] = 0;

            $status = $this->accountModel->where(['uid' => $uid])->save($edit);

            if ($status === false) {
                $this->error('转入失败~~~');
            }

            //添加金币变化记录
            $gold_data['UserID']       = $uid;
            $gold_data['first_type']   = 2;
            $gold_data['second_type']  = 20;
            $gold_data['gold']         = $data['gold'];
            $gold_data['surplus_gold'] = 0;
            $gold_data['surplus_bank'] = $edit['bank'];
            $gold_data['createtime']   = time();
            M("{$uid}",'yq_','DB_GAME_USER')->add($gold_data);

            //封装写入数组
            $user_data = [
                'uid' => $uid,
                'title' => '转入银行',
                'admin_id' => $this->auth->id
            ];

            //写入用户日志
            $this->handleLogModel->record($user_data, 'account', [
                'gold' => (int)$data['gold'],
                'bank' => (int)$data['bank'],
            ]);

            //写入日志
            $this->adminLogModel->record($data);

            $this->success('转入成功！！！');

        }


    }


    /**
     * 获取关联查询
     * Author:lbb
     * @param string $field
     * @param string $value
     * @param int $offset
     */
    public function related_query($field = '', $value = '', $offset = 0) :void
    {

        if (IS_AJAX) {

            //获取查询条件
            [$where, $sort, $order, $offset, $limit] = $this->getSerachParam();

            //设置过滤参数
            $where = [$field => $value];
            if ($field==='regip' && ( !$value || $value==='Invalid')) {
                $this->ajaxReturn('', 'JSON');
            }

            $where['is_closure'] =1;
            //获取数量
            $count = $this->newModel->where($where)->count();

            #获取数据
            $data = $this->newModel
                ->where($where)
                ->field('uid,is_online,nickname,level,createtime,logintime as login_createtime,user_lose_win_all as lose_win_total_all,daily_gold as lose_win_total_today,total_receive as accept_present_give_num,gold,total_send')
                ->limit($offset, $limit)
                ->select();




            # 统计数据
            $total = $this->newModel
                ->where($where)
                ->field('sum(gold) as user_gold_number,sum(user_lose_win_all) as user_total_lose_win_all_number,sum(daily_gold) as user_total_lose_win_today_number,sum(total_send)-sum(total_receive) as user_total_presend_diff_num')
                ->find();

            if ($data) {
                $data[0]['statistics'] = $total;
            }

            //返回json数据给ajax
            $result = array('total' => $count, 'rows' => $data ?: []);
            $this->ajaxReturn($result, 'JSON');
        }

        //渲染模板
        $this->display();
    }




    /**
     * 获取关联查询 登录码
     * Author:lbb
     * @param string $field
     * @param string $value
     * @param int $offset
     */
    public function related_log($field = '', $value = '', $offset = 0) :void
    {

        if (IS_AJAX) {

            //获取查询条件
            [$where, $sort, $order, $offset, $limit] = $this->getSerachParam();

            //设置过滤参数
            $where = [$field => $value];
            if ($field==='addIp' && ( !$value || $value==='Invalid') ){
                $this->ajaxReturn('', 'JSON');
            }
            $phy = [
                'EA7A5DD8-12B0-490D-8E16-88F189431E18',
                '16df99a755921e887dde764dee88a3f90574b35',
                '4CAB000F-781B-432F-82C2-66156A269CE3',
                '591423b0b5d4ec00718bbd2af9d4262a',
                'AB1E9B18-318B-43F8-8D75-5D1EDD7B3302',
                '64514916-EF49-4B4D-A271-87DE8C64B80D',
                '6B112D6A-0765-4FC3-884A-D8756F82AC44',
                'BD24BB78-7A48-46DA-9CE9-A18D6E958FD1',
                '8E6EF7AA-D1AE-4750-8848-1C0FDD129895'
            ];
            if (in_array($value,$phy,true)){
                $where['phyAdress'] = '';
            }

            //$where['phyAdress'] = ['not in', ['8E6EF7AA-D1AE-4750-8848-1C0FDD129895','591423b0b5d4ec00718bbd2af9d4262a']];
            $users = $this->userLogModel->where($where)->field('distinct uid')->select();
            //echo M()->getLastSql();exit;
            $condition['uid'] = ['in', array_column($users, 'uid')];
            $condition['is_closure'] =1;
            //获取数量
            $count = $this->newModel->where($condition)->count();

            #获取数据
            $data = $this->newModel
                ->where($condition)
                ->field('uid,is_online,nickname,level,createtime,logintime as login_createtime,user_lose_win_all as lose_win_total_all,daily_gold as lose_win_total_today,total_receive as accept_present_give_num,gold,total_send')
                ->limit($offset, $limit)
                ->select();


            # 统计数据
            $total = $this->newModel
                ->where($condition)
                ->field('sum(gold) as user_gold_number,sum(user_lose_win_all) as user_total_lose_win_all_number,sum(daily_gold) as user_total_lose_win_today_number,sum(accept_present_diff_num) as user_total_presend_diff_num')
                ->find();

            if ($data) {
                $data[0]['statistics'] = $total;
            }

            //返回json数据给ajax
            $result = array('total' => $count, 'rows' => $data ?: []);
            $this->ajaxReturn($result, 'JSON');
        }

        //渲染模板
        $this->display();
    }


    /**
     * 关联锁定用户
     * Author:lbb
     * @param string $value
     */
    public function lock($value = '',$type=1) :void
    {
        if (IS_POST) {
            if($post = I('post.lock', [], 'strip_tags')){

                //根据条件获取uid
                if ($type === 1) {
                    $userData = $this->model->field('uid')->where(['clientMark' => $value])->select();
                }else{
                    $userData = $this->userLogModel->where(['phyAdress' => $value])->field('distinct uid')->select();
                }

                //修改条件
                $where['uid']    =  ['in', array_column($userData, 'uid')];
                $where['status'] =  1;

                //判断用户修改状态是否成功
                if ($this->model->where($where)->save(['status' => 0,'is_send_presend'=>0])) {

                    foreach ($userData as $val){
                        //封装写入数组
                        $user_data = [
                            'uid' => $val['uid'],
                            'title' =>'用户关联锁定',
                            'admin_id' => $this->auth->id,
                            'remark'   => $post['remark']
                        ];
                        //写入用户日志
                        $this->handleLogModel->record($user_data, 'lock', [
                            'field'    => 'clientMark',
                            'value'    => $value,
                            'remark'   => $post['remark']
                        ]);
                    }
                    $post['uid']    =array_column($userData, 'uid');
                    //写入日志
                    $this->adminLogModel->record($post);
                }
                $this->success('关联锁定成功');
            }
        }

        //表单验证配置
        $this->assign('fromValidate', json_encode([
            'lock[remark]' => 'required;',
        ]));
        $form_id = "/field/clientMark/value/$value";
        $this->assign('form_id', $form_id);
        $this->assign('title', '注册码');
        $this->assign('value', $value);

        //渲染模板
        $this->display();
    }


    /**
     * 绑定手机号
     * Author:lbb
     * @param int $uid
     */
    public function bind_mobile($uid = 0) :void
    {
        //检查用户是否存在
        if (!$uid || !$data = $this->model->where(['uid' => $uid])->find()) {
            $this->error('用户id不存在');
        }

        if (IS_AJAX) {

            if ($post = I('post.mobile', [], 'strip_tags')) {

                if (!$post['mobile']) {
                    $this->error('手机号不能为空！');
                    exit;
                }

                //验证手机号码格式
                $regex = new Validate();
                $isTrue = $regex->regex($post['mobile'], 'mobile');
                if ($isTrue === false) {
                    $this->error('请输入正确手机号！');
                    exit;
                }

                //验证该用户是否绑定手机号
                $mobile = $this->model->field('mobile')->where(array('uid' => $uid))->find();
                if (!empty($mobile['mobile'])) {
                    $this->error('该用户已绑定手机号！');
                    exit;
                }

                //验证手机号唯一
                $mobile = $this->model->field('mobile')->where(array('mobile' => $post['mobil']))->find();
                if ($mobile) {
                    $this->error('手机号已绑定！');
                    exit;
                }

                //封装修改数组
                $mobile_data = [
                    'mobile' => $post['mobile'],
                    'updatetime' => time(),
                ];

                //修改账户数据
                if ($this->model->where(['uid' => $uid])->save($mobile_data)) {

                    //封装写入数组
                    $user_data =  [
                        'uid' => $uid,
                        'title' => '用户绑定手机号',
                        'admin_id' => $this->auth->id
                    ];

                    //写入用户日志
                    $this->handleLogModel->record($user_data, 'bind_mobile', [
                        'mobile' => $post['mobile'],
                    ]);

                    //写入后台日志
                    $this->adminLogModel->record($post);

                    $this->success('修改成功');

                } else {
                    $this->error('修改失败');
                }
            }
        }
        //表单验证配置
        $this->assign('fromValidate', json_encode([
            'mobile[remark]' => 'required;',
        ]));
        $form_id = 'bindemobilefrom';
        $this->assign('form_id', $form_id);
        $this->assign('data', $data);
        $this->display();
    }


    /**
     * 改绑手机号
     * Author:lbb
     * @param int $uid
     */
    public function change_mobile($uid = 0) :void
    {
        //检查用户是否存在
        if (!$uid || !$data = $this->model->where(['uid' => $uid])->find()) {
            $this->error('用户id不存在');
        }

        if (IS_AJAX) {

            if ($post = I('post.mobile', [], 'strip_tags')) {

                //验证新录入的手机号不能为空
                if (!$post['newMobile']) {
                    $this->error('手机号不能都为空！');
                    exit;
                }

                //验证新录入的手机号格式是否正确
                $regex = new Validate();
                $isTrue = $regex->regex($post['newMobile'], 'mobile');
                if ($isTrue === false) {
                    $this->error('请输入正确手机号！');
                    exit;
                }

                //验证新录入的手机号的唯一性
                $mobile = $this->model->field('mobile')->where(array('mobile' => $post['newMobile']))->find();
                if ($mobile) {
                    $this->error('手机号已绑定！');
                    exit;
                }

                //封装修改数组
                $mobile_data = [
                    'mobile' => $post['newMobile'],
                    'updatetime' => time(),
                ];

                //修改账户数据
                if ($this->model->where(['uid' => $uid])->save($mobile_data)) {

                    //封装写入数组
                    $user_data = [
                        'uid' => $uid,
                        'title' => '修改用户手机号',
                        'admin_id' => $this->auth->id
                    ];

                    //写入用户日志
                    $this->handleLogModel->record($user_data, 'change_mobile', [
                        'old_mobile' => $post['oldMobile'],
                        'new_mobile' => $post['newMobile'],
                    ]);

                    //写入后台日志
                    $this->adminLogModel->record($post);

                    $this->success('修改成功');
                } else {
                    $this->error('修改失败');
                }
            }
        }
        //表单验证配置
        $this->assign('fromValidate', json_encode([
            'mobile[remark]' => 'required;',
        ]));
        $form_id = "changemobilefrom";
        $this->assign('form_id', $form_id);
        $this->assign('data', $data);
        $this->display();
    }


    /**
     * 更换保险箱手机号
     * Author:lbb
     * @param int $uid
     */
    public function safe_mobile($uid = 0) :void
    {
        //检查用户是否存在
        if (!$uid || !$data = $this->model->where(['uid' => $uid])->find()) {
            $this->error('用户id不存在');
        }

        if (IS_AJAX) {

            if ($post = I('post.mobile', [], 'strip_tags')) {

                //验证新录入的手机号不能为空
                if (!$post['newMobile']) {
                    $this->error('手机号不能都为空！');
                    exit;
                }

                //验证新录入的手机号格式是否正确
                $regex = new Validate();
                $isTrue = $regex->regex($post['newMobile'], 'mobile');
                if ($isTrue === false) {
                    $this->error('请输入正确手机号！');
                    exit;
                }

                //验证新录入的手机号的唯一性
              /*  $mobile = $this->model->field('qq')->where(array('mobile' => $post['newMobile']))->find();
                if ($mobile) {
                    $this->error('手机号已绑定！');
                    exit;
                }*/

                //封装修改数组
                $mobile_data = [
                    'qq' => $post['newMobile'],
                    'updatetime' => time(),
                ];

                //修改账户数据
                if ($this->model->where(['uid' => $uid])->save($mobile_data)) {

                    //封装写入数组
                    $user_data = [
                        'uid' => $uid,
                        'title' => '修改保险箱手机号',
                        'admin_id' => $this->auth->id
                    ];

                    //写入用户日志
                    $this->handleLogModel->record($user_data, 'safe_mobile', [
                        'old_mobile' => $post['oldMobile'],
                        'new_mobile' => $post['newMobile'],
                    ]);

                    //写入后台日志
                    $this->adminLogModel->record($post);

                    $this->success('修改成功');
                } else {
                    $this->error('修改失败');
                }
            }
        }
        //表单验证配置
        $this->assign('fromValidate', json_encode([
            'mobile[newMobile]' => 'required;',
        ]));
        $form_id = "safemobilefrom";
        $this->assign('form_id', $form_id);
        $this->assign('data', $data);
        $this->display();
    }


    /**
     * 修改保险箱密码
     * Author:lbb
     * @param int $uid
     */
    public function edit_safe($uid = 0) :void
    {
        //检查用户是否存在
        if (!$uid || !$data = $this->model->where(['uid' => $uid])->find()) {
            $this->error('用户id不存在');
        }

        if (IS_AJAX) {

            if ($post = I('post.safe', [], 'strip_tags')) {

                //验证新录入的手机号不能为空
                if (strlen($post['newPwd'])<6) {
                    $this->error('密码不能少于6位！');
                    exit;
                }

                //封装修改数组
                $safe_data = [
                    'bank_pass' => $post['newPwd'],
                ];

                //修改账户数据
                if ($this->accountModel->where(['uid' => $uid])->save($safe_data)) {

                    //封装写入数组
                    $user_data = [
                        'uid' => $uid,
                        'title' => '编辑保险箱密码',
                        'admin_id' => $this->auth->id
                    ];

                    //写入用户日志
                    $this->handleLogModel->record($user_data, 'edit_safe', [
                        'new_mobile' => $post['newMobile'],
                    ]);

                    //写入后台日志
                    $this->adminLogModel->record($post);

                    $this->success('修改成功');
                } else {
                    $this->error('修改失败');
                }
            }
        }
        //表单验证配置
        $this->assign('fromValidate', json_encode([
            'safe[newPwd]' => 'required;',
        ]));
        $form_id = "editsafefrom";
        $this->assign('form_id', $form_id);
        $this->assign('data', $data);
        $this->display();
    }


    /**
     * 修改密码
     * Author:lbb
     * @param int $uid
     */
    public function edit_pwd($uid = 0) :void
    {
        //检查用户是否存在
        if (!$uid || !$data = $this->model->where(['uid' => $uid])->find()) {
            $this->error('用户id不存在');
        }

        if (IS_AJAX) {

            if ($post = I('post.pwd', [], 'strip_tags')) {

                //密码不能为空
                if (!$post['newPwd']) {
                    $this->error('密码不能都为空！');
                    exit;
                }

                //封装修改数组
                $pwd_data = [
                    'password' => strtoupper(md5($post['newPwd'])),
                    'updatetime' => time(),
                ];

                //修改账户数据
                if ($this->model->where(['uid' => $uid])->save($pwd_data)) {

                    //封装写入数组
                    $user_data = [
                        'uid' => $uid,
                        'title' => '修改登录密码',
                        'admin_id' => $this->auth->id,
                        'remark' => ''
                    ];

                    //写入用户日志
                    $this->handleLogModel->record($user_data, 'edit_pwd', [
                        'old_pwd' => $data['password'],
                        'new_pwd' => $post['newPwd'],
                    ]);

                    //写入后台日志
                    $this->adminLogModel->record($post);

                    $this->success('修改成功');
                } else {
                    $this->error('修改失败');
                }
            }
        }
        //表单验证配置
        $this->assign('fromValidate', json_encode([
            'mobile[newPwd]' => 'required;',
        ]));
        $form_id = "editpwdfrom";
        $this->assign('form_id', $form_id);
        $this->assign('data', $data);
        $this->display();
    }


    /**
     * 解除手机绑定
     * Author:lbb
     * @param int $uid
     */
    public function unbind_mobile($uid = 0) :void
    {
        if (IS_AJAX) {

            //检查用户是否存在
            if (!$uid || !$data = $this->model->where(['uid' => $uid])->find()) {
                $this->error('用户id不存在');
            }

            //将手机号置空
            if ($this->model->where(['uid' => $uid])->save(['mobile' => ''])) {

                //封装写入数组
                $user_data = [
                    'uid' => $uid,
                    'title' => '解除手机绑定',
                    'admin_id' => $this->auth->id
                ];
                //写入用户日志
                $this->handleLogModel->record($user_data, 'unbind_mobile', [
                    'mobile' => $data['mobile'],
                ]);

                //写入系统日志
                $this->adminLogModel->record(['uid' => $uid]);

                $this->success('解除手机绑定成功');
            } else {
                $this->error('解除手机绑定失败');
            }

        }

    }


    /**
     * 强踢玩家
     * Author:lbb
     * @param int $uid
     */
    public function kick($uid = 0) :void
    {
        if (IS_AJAX) {

            //检查用户是否存在
            if (!$uid || !$data = $this->model->where(['uid' => $uid])->find()) {
                $this->error('用户id不存在');
            }
            $server_data['UID'] = (int)$uid;
            $action = '/KickStuckPlayer.php';
            send_server($server_data, $action);

            //封装写入数组
            $user_data = [
                'uid' => $uid,
                'title' => '强踢玩家',
                'admin_id' => $this->auth->id
            ];
            //写入用户日志
            $this->handleLogModel->record($user_data, 'kick', []);

            //写入系统日志
            $this->adminLogModel->record(['uid' => $uid]);

            $this->success('强踢玩家成功');
        }
    }


    /**
     * 编辑资源
     * Author:lbb
     * @param int $uid
     */
    public function edit_resource($uid = 0) :void
    {
        //检查用户是否存在
        $data = [];

        if (!$uid || !$data = $this->userAccountView->field('uid,bank,nickname')->where(['uid' => $uid])->find()) {
            $this->error('用户id不存在');
        }

        if (IS_AJAX) {
            if ($post = I('post.resource', [], 'strip_tags')) {

                if (!$post['gold_set']) {
                    $post['gold_set'] = '+0';
                }

                //判断传入数值 是否为正负数
                $pattern = '/^(-|\+)?\d+$/i';
                if ( !preg_match($pattern, $post['gold_set']) ) {
                    $this->error('金币格式不正确');
                }

                //获取金币与数据库中的数值之差
                $gold_diff = $data['bank'] + (int)$post['gold_set'];

                //判断金币是否小于0
                if ($gold_diff < 0) {
                    $this->error('银行金币不能扣除到0以下');
                }

                //封装用户修改数组
                $userData = [
                    'bank' => $gold_diff,
                ];

                //修改账户数据
                if ($this->accountModel->where(['uid' => $uid])->save($userData)) {

                    $this->createTable('yq_'.$uid);
                    //添加金币变化记录
                    $userAccount = D('Account')->where(['uid' => $uid])->find();
                    $gold_data['UserID']       = $uid;
                    $gold_data['first_type']   = 2;
                    $gold_data['second_type']  = 24;
                    $gold_data['gold']         = $post['gold_set'];
                    $gold_data['surplus_gold'] = $userAccount['gold'];
                    $gold_data['surplus_bank'] = $userAccount['bank'];
                    $gold_data['createtime']   = time();
                    M("{$uid}",'yq_','DB_GAME_USER')->add($gold_data);

                    //账户变化记录
                    $logData=[
                        'uid' => $uid,
                        'type' => 'gmChange',
                        'admin_id' => $this->auth->id,
                        'handleNum' => (int)$post['gold_set'],
                        'bank' => $gold_diff,
                        'disc' => $post['remark'],
                        'createtime' => time(),
                    ];

                    D('AccountLog')->add($logData);

                    //封装写入数组
                    $user_data = [
                        'uid' => $uid,
                        'title' => '编辑资源',
                        'admin_id' => $this->auth->id
                    ];
                    //写入用户日志
                    $this->handleLogModel->record($user_data, 'edit_resource', [
                        'remark' => $post['remark'],
                        'handleNum' => (int)$post['gold_set'],
                        'bank' => $data['bank'],
                        'change_bank' => $gold_diff,
                    ]);

                    //发送服务器
                    /*   $serverData['UserID']= (int)$uid;
                       $serverData['GoldCoin']= $post['gold_set'];
                       $action='/UpdateUserGoldCoin.php';
                       send_server($serverData,$action);*/

                    //写入日志
                    $this->adminLogModel->record($post);
                    $this->success('修改账户资源成功');
                }
            }
        }else{
            //表单验证配置
            $this->assign('fromValidate', json_encode([
                'resource[remark]' => 'required;',
            ]));
            $form_id = "/uid/$uid";
            $this->assign('form_id', $form_id);
            $this->assign('data', $data);
            $this->display();
        }


    }


    /**
     * 金币变动表  检测是否有 没有执行创建操作
     * Author:lbb
     * @param $uid
     */
    private function createTable($uid) :void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$uid} (
                `Id` int(11) NOT NULL AUTO_INCREMENT,
                `UserID` int(11) DEFAULT NULL,
                `first_type` int(5) DEFAULT NULL,
                `second_type` int(5) DEFAULT NULL,
                `gameId` int(11) DEFAULT NULL,
                `productId` int(11) DEFAULT NULL,
                `stake` bigint(20) DEFAULT NULL,
                `winning` bigint(20) DEFAULT NULL,
                `gold` bigint(20) DEFAULT NULL,
                `surplus_gold` bigint(20) DEFAULT NULL,
                `surplus_bank` bigint(20) DEFAULT NULL,
                `control_id` int(11) DEFAULT NULL,
                `control_type` int(4) DEFAULT NULL,
                `control_progress` bigint(20) DEFAULT NULL,
                `control_target` bigint(20) DEFAULT NULL,
                `control_plan` int(4) DEFAULT NULL,
                `createtime` int(20) DEFAULT NULL,
                 PRIMARY KEY (`Id`),
                 KEY `uid` (`UserID`,`createtime`) USING BTREE,
                 KEY `type` (`first_type`,`second_type`) USING BTREE
                )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;";
        M()->db(2,'DB_GAME_USER')->query($sql);
    }


    /**
     * 取消玩家点控
     * Author:lbb
     * @param int $uid
     */
    public function cancel_point($uid = 0) :void
    {

        //检查用户是否存在
        $data = [];
        if (!$uid || !$data = $this->pointControlModel->where(['uid' => $uid,'status'=>1])->order('id desc')->find()) {
            $this->error('用户id不存在');
        }
        //获取昵称
        $user_info = $this->model->where(['uid' => $uid])->field('nickname')->find();
        $data['username'] = $user_info['nickname'];

        if (IS_AJAX) {
            if ($post = I('post.cancel', [], 'strip_tags')) {
                if (!$post['reason']) {
                    $this->error('原因不能为空');
                }
                //封装点控修改数组
                $control_data = [
                    'reason' => $post['reason'],
                    'deleteStaff' => $this->auth->id,
                    'deletetime' => time(),
                    'status' => 0,
                ];

                //修改账户数据
                if ($this->pointControlModel->where(['id' => $data['id']])->save($control_data)) {

                    //封装写入数组
                    $user_data = [
                        'uid' => $uid,
                        'title' => '取消点控',
                        'admin_id' => $this->auth->id
                    ];
                    //写入用户日志
                    $this->handleLogModel->record($user_data, 'no_point', $control_data);

                    //写入后台日志
                    $this->adminLogModel->record($control_data);

                    //发送服务器
                    $server_data = [
                        'controlid' => (int)$data['id'],
                        'id'       => (int)$uid,
                        'onoff'     => 0,//0:关，1开
                        'schemeid'  => 0,//0:关，1开
                        'state'     => 0,//0:关，1开
                        'vaule'     => '0',//0:关，1开
                    ];
                    send_server($server_data,'/PointControl.php');

                    $this->success('取消点控成功');
                }else{
                    $this->error('取消点控失败');
                }
            }
        }



        //表单验证配置
        $this->assign('fromValidate', json_encode([
            'resource[remark]' => 'required;',
        ]));
        $form_id = "cancelfrom";
        $this->assign('form_id', $form_id);
        $this->assign('data', $data);
        $this->display();

    }


    /**
     * 锁定用户
     * Author:lbb
     * @param int $uid
     */
    public function user_lock($uid = 0) :void
    {
        //检查用户是否存在
        $data = [];

        if (!$uid || !$data = $this->model->where(['uid' => $uid])->find()) {
            $this->error('用户id不存在');
        }

        if (IS_AJAX) {
            if ($post = I('post.lock', [], 'strip_tags')) {
                if (!$post['reason']) {
                    $this->error('原因不能为空！');
                }

                if ($post['title'] === '0') {
                    $status = 1;
                    $type = '解除用户锁定';
                    $lock = 'unlock';
                } else {
                    $status = 0;
                    $type = '用户锁定';
                    $lock = 'lock';
                }

                //封装修改数组
                $sign_data['status'] = $status;

                if ($status === '0') {
                    $sign_data['is_send_presend'] = 0;
                }

                //修改账户数据
                if ($this->model->where(['uid' => $uid])->save($sign_data)) {

                    //封装写入数组
                    $user_data = [
                        'uid'      => $uid,
                        'title'    => $type,
                        'admin_id' => $this->auth->id,
                        'remark'   => $post['reason']
                    ];
                    //写入用户日志
                    $this->handleLogModel->record($user_data, $lock, [
                        'remark' => $post['reason'],
                        'status' => $status,
                    ]);

                    //发送服务器
                    if ($lock === 'lock') {
                        $server_data['UID'] = (int)$uid;
                        $server_data['Msg'] = '您的账号已封停，请联系管理员。';
                        $action = '/KickPlayer.php';
                        send_server($server_data, $action);
                    }

                    //写入管理员日志
                    $this->adminLogModel->record($post);
                    $this->success('修改成功');

                } else {
                    $this->error('修改失败');
                }
            }
        }
        //表单验证配置
        $this->assign('fromValidate', json_encode([
            'sign[remark]' => 'required;',
        ]));

        $this->assign('form_id', 'lockform');
        $this->assign('data', $data);
        $this->display();

    }


    /**
     * 修改用户昵称、签名
     * Author:lbb
     * @param int $uid
     */
    public function user_sign($uid = 0) :void
    {
        //检查用户是否存在
        $data = [];
        if (!$uid || !$data = $this->model->where(['uid' => $uid])->find()) {
            $this->error('用户id不存在');
        }

        if (IS_AJAX) {

            if ($post = I('post.sign', [], 'strip_tags')) {
                if ( !$post['sign'] && !$post['nickname']) {
                    $this->error('昵称或签名不能都为空！');
                }
                //封装修改数组
                $sign_data = [
                    'sign'     => $post['sign'],
                    'nickname' => $post['nickname'],
                ];

                //修改账户数据
                if ($this->model->where(['uid' => $uid])->save($sign_data)) {
                    //封装写入数组
                    $user_data = [
                        'uid' => $uid,
                        'title' => '修改用户昵称、签名',
                        'admin_id' => $this->auth->id
                    ];
                    //写入用户日志
                    $this->handleLogModel->record($user_data, 'edit_user', [
                        'sign'     => $post['sign'],
                        'nickname' => $post['nickname'],
                    ]);

                    //写入管理员日志
                    $this->adminLogModel->record($post);

                    $this->success('修改成功');

                }else{
                    $this->error('修改失败');
                }
            }
        }
        //表单验证配置
        $this->assign('fromValidate', json_encode([
            'sign[remark]' => 'required;',
        ]));

        $this->assign('form_id', 'signfrom');
        $this->assign('data', $data);
        $this->display();

    }


    /**
     * 是否允许赠送 是否成为关怀用户
     * Author:lbb
     * @param int $uid
     * @param string $field
     * @param int $value
     */
    public function status_set($uid = 0, $field = 'is_focus', $value = 0) :void
    {

        //判断用户是否存在
        if (!$uid || !($data = $this->model->where(['uid' => $uid])->find())) {
            $this->error('用户ID没有找到');
        }

        $title='';
        $type='';
        switch ($field) {
            case 'is_focus':
                if($value ==='1'){
                    $value = 0;
                    $title = '取消关怀用户';
                    $type  = 'focus';
                }else{
                    $value = 1;
                    $title = '设成关怀用户';
                    $type  = 'no_focus';
                }
                break;
            case 'is_send_presend':
                if($value ==='1'){
                    $value = 0;
                    $title = '禁止赠送';
                    $type  = 'present';
                }else{
                    $value = 1;
                    $title = '允许赠送';
                    $type  = 'no_present';
                }
                break;
        }
        //修改参数
        if ($this->model->save(['uid' => $uid, $field => $value])) {
            //封装写入数组
            $user_data = [
                'uid' => $uid,
                'title' => $title,
                'admin_id' => $this->auth->id
            ];
            //写入数据
            $this->handleLogModel->record($user_data, $type, [
                $field => $value
            ]);

            //发送服务器
            if ($field === 'is_send_presend') {
                $param = array(
                    'userid' => (int)$uid,
                    'propid' => 12,            //赠送权限
                    'propvalue' => (string)$value,
                );
                send_server($param,'/SetUserProp.php');
            }

            //写入日志
            $this->adminLogModel->record([ $field => $value ]);
            $this->success('设置成功');
        }else{

            $this->error('设置失败');
        }


    }


    /**
     * 设置关怀用户
     * @param int $uid
     */

    public function focus($uid = 0): void
    {
        $data = [];
        if (!$uid || !$data = $this->model->where(['uid' => $uid])->find()) {
            $this->error('用户id不存在');
        }

        if (IS_AJAX && $post = I('post.lock', [], 'strip_tags')) {
            if (!$post['focus_why']) {
                $this->error('原因不能为空！');
            }
            if($post['is_focus'] ==='1'){
                $value = 0;
                $title = '取消关怀用户';
                $type  = 'focus';
            }else{
                $value = 1;
                $title = '设成关怀用户';
                $type  = 'no_focus';
            }
            //修改参数
            if ($this->model->save(['uid' => $uid, 'is_focus' => $value,'focus_why'=>$post['focus_why']])) {

                //封装写入数组
                $user_data = [
                    'uid' => $uid,
                    'title' => $title,
                    'admin_id' => $this->auth->id
                ];
                //写入数据
                $this->handleLogModel->record($user_data, $type, [
                    'is_focus' => $value
                ]);

                //写入日志
                $this->adminLogModel->record([ 'is_focus' => $value ]);
                $this->success('设置成功');
            }else{

                $this->error('设置失败');
            }
        }
        //表单验证配置
        $this->assign('fromValidate', json_encode([
            'lock[focus_why]' => 'required;',
        ]));

        $this->assign('form_id', 'focusform');
        $this->assign('data', $data);
        $this->display();

    }


    /**
     * 修改用户等级
     * Author:lbb
     * @param int $uid
     */
    public function level_change($uid = 0) :void
    {
        $data = [];
        //检查用户是否存在
        if (!$uid || !$data = $this->model->where(['uid' => $uid])->find()) {
            $this->error('用户id不存在');
        }

        if (IS_AJAX) {
            if ($post = I('post.rank', [], 'strip_tags')) {
                //判断是否修改为VIP
                if($post['vip']==='0'){
                    $is_del = 0;
                }else{
                    $is_del = 1;
                }
                //封装修改数组
                $control_data = [
                    'level'      => $post['vip'],
                    'teamId'     => $post['team'],
                    'createtime' => time(),
                    'isdel'      =>$is_del,
                    'uid'        =>$uid,
                ];

                if ($this->model->where(['uid' => $uid])->save(['level' => $post['vip']])) {

                    //添加修改用户所属代理团队
                    $this->teamMemberModel->record($control_data);

                    //封装写入数组
                    $user_data = [
                        'uid' => $uid,
                        'title' => '修改用户等级及代理团队',
                        'admin_id' => $this->auth->id
                    ];

                    //写入用户日志
                    $this->handleLogModel->record($user_data, 'level_change', [
                        'level'  => $post['vip'],
                        'teamId' => $post['team'],
                    ]);

                    //写入后台日志
                    $this->adminLogModel->record([ 'level'  => $post['vip'], 'teamId' => $post['team']]);

                    //通知服务器
                    $param=array(
                        'userid'     =>(int)$uid,
                        'propid'     =>9,//vip等级
                        'propvalue'  =>(string)$post['vip'],
                    );
                    send_server($param,'/SetUserProp.php');

                    $this->success('修改成功');
                }
            }

            $this->error('修改失败');
        }
        //表单验证配置
        $this->assign('fromValidate', json_encode([
            'resource[remark]' => 'required;',
        ]));

        //获取团队名称
        $agent=$this->getTeamName();

        $this->assign('form_id',  'levelchange');
        $this->assign('agent', $agent);
        $this->assign('data', $data);
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


            //普通用户 还是vip
            if (isset($result['levels']) ) {
                if ($result['levels']=== '0') {
                    $result['level'] = 0;
                    $option['level'] = '=';
                }else{
                    $result['level'] = 0;
                    $option['level'] = '>';
                }
                unset($result['levels']);
                unset($option['levels']);
            }

            //是否是手机号绑定
            if (isset($result['mobiles']) ) {

                if ($result['mobiles']=== '0') {
                    $result['mobile'] = '';
                    $option['mobile'] = '!=';
                }else{
                    $result['mobile'] = '';
                    $option['mobile'] = '=';
                }
                unset($result['mobiles']);
                unset($option['mobiles']);
            }

            //是否是微信绑定
            if (isset($result['openId']) ) {

                if ($result['openId']=== '0') {
                    $result['openId'] = '';
                    $option['openId'] = '!=';
                }else{
                    $result['openId'] = '';
                    $option['openId'] = '=';
                }
            }

            if (isset($result['fund_type']) ) {
                $where_user['uid'] =  $result['uid']?:0;
                unset($result['uid'],$option['uid']);
                $fund = $this->accountModel->where($where_user)->field('RecommendedUID')->find();
                if ($result['fund_type']=== '0') {
                    $result['uid'] = $fund['RecommendedUID'];
                    $option['uid'] = '=';
                }else{
                    $result['RecommendedUID'] = $where_user['uid'];
                    $option['openId'] = '=';
                }
                unset($result['fund_type'],$option['fund_type']);

            }

            $_GET['filter'] = json_encode($result);
            $_GET['option'] = json_encode($option);
        }
    }


    /**
     * 获取团队名称
     * Author:lbb
     */
    private function getTeamName()
    {
        $agent = M('team_agent')->field('teamId,teamName')->select();
        return $agent;
    }


    /**
     * 获取游戏房间
     * Author:lbb
     * @return mixed
     */
    public function query_room()
    {
        $rooms = [];
        $room=M('game_type')->field('productid,name,type_name')->select();
        foreach ($room as $k=>$val) {
            if ($val['productid'] === '0') {
                $rooms[$val['productid']]= $val['name'];
            }else{
                $rooms[$val['productid']]= $val['type_name'].'-'.$val['name'];
            }
        }
        return $rooms;
    }



}