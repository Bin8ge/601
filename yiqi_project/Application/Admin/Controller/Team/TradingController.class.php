<?php
/**
 * 当日交易
 * User: Lbb
 * Date: 2018/7/30 0030
 * Time: 16:36
 */

namespace Admin\Controller\Team;

use Common\Controller\BaseController;

class TradingController extends BaseController
{

    private $sendTakeModel;
    /**
     * @var \Model|\Think\Model
     */
    private $teamMemberModel;
    /**
     * @var \Model|\Think\Model
     */
    private $userModel;
    /**
     * @var \Model|\Think\Model
     */
    private $handleLogModel;
    /**
     * @var \Model|\Think\Model
     */
    private $paiHangModel;
    private $sysModel;

    public function __construct()
    {
        parent::__construct();

        $this->sendTakeModel = D('send_take');

        $this->teamMemberModel = D('team_member');
        
        $this->userModel      = D('user');

        $this->handleLogModel = D('HandleLog');

        $this->paiHangModel   = D('paihang');

        $this->sysModel      = D('sys_conf');
    }

    /**
     * 查看
     * Author:lbb
     */
    public function index(): void
    {
        if (IS_AJAX) {
            //获取查询条件
            [$where, $sort, $order, $offset, $limit] = $this->getSerachParam();
            $where[0] = 'h.createtime >= "' . strtotime(date('Y-m-d') . ' 00:00:00') . '"';
            $where[1] = 'h.createtime <= "' . strtotime(date('Y-m-d') . ' 23:59:59') . '"';
            $where['h.is_back'] = 'h.is_back = 0';
            $where['h.is_vip'] = 'h.is_vip=0';
            $where['level'] = 'level>0';
            if ($where) {
                $where = implode(' and ', array_values($where));
            }
            $sql = $this->sendTakeModel
                ->alias('h')
                ->field('h.uid as uid')
                ->join('left join yq_user_account c on h.uid = c.uid')
                ->where($where)
                ->group('h.uid')
                ->select(false);// 查询满足要求的总记录数
            $count = M()->table($sql . ' b')->count();

            $list = $this->sendTakeModel
                ->alias('h')
                ->field('c.nickname,c.gold+c.bank as golds,c.sign,c.level,a.teamName,b.createtime,h.uid,sum(h.send_gold) as send,sum(h.take_gold) as take,count(h.type="send" or NULL) as send_times,count(distinct h.take_uid)-count(distinct h.take_uid=0 or NULL) as send_people,sum(h.send_gold)-sum(h.take_gold) as diff')
                ->join('left join (select uid,gold,bank,level,nickname,sign from yq_user_account) c on h.uid = c.uid')
                ->join('left join (select uid,teamId,createtime from yq_team_member where isdel = 1) b on h.uid=b.uid')
                ->join('left join (select teamId,teamName from yq_team_agent) a on a.teamId=b.teamId')
                /*->join('left join (select uid,sum(rebate) as rebate from yq_rebate_log  group by uid) e on e.uid = h.uid')*/
                ->where($where)
                ->group('h.uid')
                ->order([$sort => $order])
                ->limit($offset, $limit)
                ->select();

            foreach ($list as $key => $value) {
                $list[$key]['send'] = number_format($value['send']);
                $list[$key]['take'] = number_format($value['take']);
                $list[$key]['golds'] = number_format($value['golds']);
                $list[$key]['send_times'] = number_format($value['send_times']);
                $list[$key]['send_people'] = number_format($value['send_people']);
                $list[$key]['diff'] = number_format($value['diff']);
                if($value['take']==='0' || $value['send']==='0' || !$value['send'] ||!$value['take']){
                    $list[$key]['than'] = number_format(0,3);
                } else{
                    $list[$key]['than'] = number_format($value['take']/$value['send'],3);
                }

            }

            $lists = $this->sendTakeModel
                ->alias('h')
                ->field('h.uid,c.nickname,c.level,c.sign,a.teamName,c.gold+c.bank as golds,sum(h.send_gold) as send,sum(h.take_gold) as take,sum(h.send_gold)-sum(h.take_gold) as diff,count(h.type="send" or NULL) as send_times,count(distinct h.take_uid)-count(distinct h.take_uid=0 or NULL) as send_people,sum(h.take_gold)/sum(h.send_gold) as than')
                ->join('left join (select uid,gold,bank,level,nickname,sign from yq_user_account) c on h.uid = c.uid')
                ->join('left join (select uid,teamId from yq_team_member where isdel = 1) b on h.uid=b.uid')
                ->join('left join (select teamId,teamName from yq_team_agent) a on a.teamId=b.teamId')
               /* ->join('left join (select uid,sum(rebate) as rebate from yq_rebate_log  group by uid) e on e.uid = h.uid')*/
                ->where($where)
                ->group('h.uid')
                ->select();

            $total = [];
            foreach ($lists as $k => $val) {
                $total['gold_total'] += $val['golds'];        //总资产
                $total['send_num_total'] += $val['send'];         //赠送金额
                $total['send_count_total'] += $val['send_times'];   //赠送笔数
                $total['take_num_total'] += $val['take'];         //接收金额
                $total['diff_num_total'] += $val['diff'];         //顺差
                /*$total['rebate_total'] += $val['rebate'];       //返利*/
            }
            $total_format = array_map('number_format', $total);
            $total_format['send_take_than'] = number_format($total['take_num_total'] / $total['send_num_total'], 3);

            $uidList = $this->paiHangModel->field('uid')->select();

            $result = [];
            array_map(static function ($value) use (&$result) {
                $result = array_merge($result, array_values($value));
            }, $uidList);
            //$results = implode(',',$result);

            foreach ($list as $k=>$v){
                $list[$k]['results'] = $result;
            }


            $list[0]['statistics'] = $total_format;


            //返回json数据给ajax
            $result = array('total' => $count, 'rows' => $list ?: []);
            $this->ajaxReturn($result, 'JSON');

        }
        $this->display();

    }


    /**
     * Notes: 编辑VIP
     * User: Lbb
     * Date: 2019/9/24
     * Time: 19:58
     */
    public function edit()
    {

        if (IS_AJAX) {
            $data = $_POST;

            //判断是否修改为VIP
            if($data['level']=== '0'){
                $is_del = 0;
            }else{
                $is_del = 1;
            }
            $addData['level'] = I('post.level');
            $addData['teamId'] = I('post.teamId');
            $addData['createtime'] = time();
            $addData['uid'] = I('post.uid');
            $addData['isdel'] = $is_del;
            //添加修改用户所属代理团队
            D('TeamMember')->record($addData);

            $where_user['uid'] = I('post.uid');
            $data_user['level'] = I('post.level');
            $user = $this->userModel->where($where_user)->save($data_user);
            if ($user === FALSE){
                return returnAjax('400','保存失败');
            }else{
                //封装写入数组
                $user_data = [
                    'uid' =>  $data['uid'],
                    'title' => '修改用户等级及代理团队',
                    'admin_id' => $this->auth->id
                ];

                //写入用户日志
                $this->handleLogModel->record($user_data, 'level_change', [
                    'level'  => $data['level'],
                    'teamId' => $data['teamId'],
                ]);

                //写入后台日志
                $this->adminLogModel->record([ 'level'  => $data['level'], 'teamId' => $data['teamId']]);

                //通知服务器
                $param=array(
                    'userid'     =>(int)$data['uid'],
                    'propid'     =>9,//vip等级
                    'propvalue'  =>(string)$data['level'],
                );
                send_server($param,'/SetUserProp.php');
                $this->success('修改成功');

            }
        }else{

            $condition['uid'] = I('get.id');
            $teamId = $this->teamMemberModel->where($condition)->field('teamId')->find();
            $user = $this->userModel->field('uid,nickname,level')->where($condition)->find();  //用户信息
            $agent=M('team_agent')->field('teamId,teamName')->select();                        //代理团队
            $level = $this->getLevel();                                                        //用户等级
            $this->assign('agent',$agent);
            $this->assign('user',$user);
            $this->assign('teamId',$teamId['teamId']);
            $this->assign('level',$level);
            $this->assign('fromValidate', json_encode([
                'resource[remark]' => 'required;',
            ]));
            $this->display();
        }

    }

    /**
     * Notes: 添加榜
     * User: Lbb
     * Date: 2019/9/24
     * Time: 18:37
     * @param int $id
     */
    public function bang($id=0): void
    {
        if(IS_AJAX){

            $uid = $id;

            if (!$uid || $uid<=0 ) {
                $this->error('非法参数,请重新输入~~');
            }

            $condition['uid'] = $uid;
            # 判断代理是否存在
            $isHave = $this->userModel->where($condition)->find();
            if ( !$isHave ){
                $this->error('代理不存在,请核实~~');
            }
            # 判断是否存在排行表中
            $isMsg = $this->paiHangModel->where($condition)->find();
            if ( $isMsg ){
                $this->error('玩家已存在排行表中,无需再添加~~');
            }

            $data['uid']         = $uid;
            $data['createtime']  = time();

            $status = $this->paiHangModel->add($data);

            # 写入后台日志
            $this->adminLogModel->record($_GET);

            if ($status){
                $this->success('添加成功！！');
            }else{
                $this->error('添加失败~~');
            }
        }

    }

    /**
     * Notes: 获取用户等级
     * User: Lbb
     * Date: 2019/9/24
     * Time: 19:58
     * @return mixed
     */
    public function getLevel()
    {
        $sys = $this->sysModel->where('id=106')->field('value_scope')->find();
        return json_decode($sys['value_scope'], true);
    }


}