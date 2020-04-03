<?php
namespace Admin\Controller\Team;
use Common\Controller\BaseController;
class MemberController extends BaseController
{

    private $teamMemberModel;

   /* private $paiHangModel;*/

    private $teamAgentModel;

    private $userModel;

    private $sysModel;

    /*private $handleLogModel;*/

    public function __construct()
    {
        parent::__construct();

        $this->teamAgentModel = D('team_agent');

        $this->teamMemberModel = D('team_member');

        $this->userModel      = D('user');

        $this->sysModel      = D('sys_conf');

       /* $this->handleLogModel = D('HandleLog');

        $this->paiHangModel   = D('paihang');*/
    }

    public function index() :void
    {
        if (IS_AJAX) {
            //搜索参数过滤处理
            $this->searchFilter();
            //获取查询条件
            [$where, $sort, $order, $offset, $limit] = $this->getSerachParam();


            $where['b.isdel'] = 'b.isdel=1';

            //判断筛选条件是否存在
            if ($where) {
                $where = implode(' and ', array_values($where));
            }


            $sql_count = $this->teamMemberModel
                ->alias('b')
                ->field('b.uid as uid')
                ->join('left join yq_vip_day_trading d on d.uid = b.uid')
                ->where($where)
                ->group('b.uid')
                ->select(false); // 查询满足要求的总记录数
            $count =  $count = M()->table($sql_count . ' ll')->count();

            //分页展示数据
            $list = $this->teamMemberModel
                ->alias('b')
                ->field('e.rebate as rebate,c.uid as id,a.teamId,a.teamName,c.uid,c.nickname,c.level,c.sign,b.createtime,c.gold+c.bank as golds,sum(d.send_gold) as send,sum(d.take_gold) as take,sum(d.send_gold)-sum(d.take_gold) as diff,sum(d.send_num) as send_times ,sum(d.send_people) as send_people')
                ->join('left join (select uid,gold,bank,level,nickname,sign from yq_user_account) c on c.uid = b.uid')
                ->join('left join  yq_vip_day_trading  d on d.uid = b.uid')
                ->join('left join (select teamId,teamName from yq_team_agent) a on a.teamId=b.teamId')
                ->join('left join (select uid,sum(rebate) as rebate from yq_rebate_log group by uid) e on e.uid = b.uid')
                ->where($where)
                ->group('b.uid')
                ->limit($offset, $limit)
                ->order([$sort => $order])
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



            //统计总计的数据
            $lists = $this->teamMemberModel
                ->alias('b')
                ->field('c.gold+c.bank as golds,sum(d.send_gold) as send,sum(d.take_gold) as take,sum(d.send_gold)-sum(d.take_gold) as diff,sum(d.send_num) as send_times,sum(d.send_people) as send_people,sum(d.take_gold)/sum(d.send_gold) as than,e.rebate')
                ->join('left join (select uid,gold,bank,level,nickname,sign from yq_user_account) c on c.uid = b.uid')
                ->join('left join  yq_vip_day_trading  d on d.uid = b.uid')
                ->join('left join (select teamId,teamName from yq_team_agent) a on a.teamId=b.teamId')
                ->join('left join (select uid,sum(rebate) as rebate from yq_rebate_log group by uid) e on e.uid = b.uid')
                ->where($where)
                ->group('b.uid')
                ->select();
            $total = [];
            foreach ($lists as $k=>$val){
                $total['gold_total']       += $val['golds'];       //总资产
                $total['send_count_total'] += $val['send_times'];  //赠送笔数
                $total['send_num_total']   += $val['send'];        //赠送金额
                $total['take_num_total']   += $val['take'];        //接收金额
                $total['diff_num_total']   += $val['diff'];        //顺差金额
                $total['rebate_total']     += $val['rebate'];      //总返利
            }


            $total_format = array_map('number_format',$total);
            $total_format['send_take_than'] =  number_format($total['take_num_total']/$total['send_num_total'],3);


           /* $uidList = $this->paiHangModel->field('uid')->select();

            $result = [];
            array_map(static function ($value) use (&$result) {
                $result = array_merge($result, array_values($value));
            }, $uidList);
            //$results = implode(',',$result);

           foreach ($list as $k=>$v){
               $list[$k]['results'] = $result;
           }*/

            $list[0]['statistics'] = $total_format;

            //返回json数据给ajax
            $result = array('total' => $count, 'rows' => $list ?: []);
            $this->ajaxReturn($result, 'JSON');
        }

        //渲染模板
        $this->display();

    }


    /**
     * Notes: csv 导出
     * User: Lbb
     * Date: 2019/9/25
     * Time: 12:23
     */
    public function csv(): void
    {
        //搜索参数过滤处理
        $this->searchFilter();
        //获取查询条件
        [$where, $sort, $order, $offset, $limit] = $this->getSerachParam();

        $where['b.isdel'] = 'b.isdel=1';

        //判断筛选条件是否存在
        if ($where) {
            $where = implode(' and ', array_values($where));
        }

        $lists = $this->teamMemberModel
            ->alias('b')
            ->field('c.uid,c.nickname,c.level,c.sign,b.createtime,a.teamName,c.gold+c.bank as golds,sum(d.send_gold) as send,sum(d.take_gold) as take,sum(d.send_gold)-sum(d.take_gold) as diff,sum(d.send_num) as send_times ,sum(d.send_people) as send_people,sum(d.take_gold)/sum(d.send_gold) as than')
            ->join('left join (select uid,gold,bank,level,nickname,sign from yq_user_account) c on c.uid = b.uid')
            ->join('left join  yq_vip_day_trading  d on d.uid = b.uid')
            ->join('left join (select teamId,teamName from yq_team_agent) a on a.teamId=b.teamId')
            ->where($where)
            ->group('b.uid')
            ->select();

        foreach ($lists as $k=>$val){
            $lists[$k]['level']      = 'VIP'.$val['level'];
            $lists[$k]['createtime'] = date('Y-m-d H:i:s',$val['createtime']);
            $lists[$k]['than']      = number_format($val['than'],3);
        }

        $header   = array('用户ID','用户昵称','用户等级','签名','添加时间','代理团队','资产','赠送金额','接收金额','顺差','赠送笔数','赠送人数','赠送比');
        $filename = '团队成员列表'.date('Y-m-d');
        $data['csv'] = outCsv($header,$lists, $filename);
    }

    /**
     * Notes: 编辑VIP
     * User: Lbb
     * Date: 2019/9/24
     * Time: 19:58
     */
    /*public function edit()
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

    }*/

    /**
     * Notes: 添加榜
     * User: Lbb
     * Date: 2019/9/24
     * Time: 18:37
     * @param int $id
     */
   /* public function bang($id=0): void
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

    }*/

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


    /**
     * Notes: 搜索参数过滤
     * User: Lbb
     * Date: 2019/9/24
     * Time: 19:58
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
                unset($result['select_or_text_select'], $result['select_or_text_text'], $option['select_or_text_select'], $option['select_or_text_text']);
            }


            //普通用户 还是vip
            if (isset($result['teamName']) ) {
                $where_team['teamName'] = $result['teamName'];
                $team = $this->teamAgentModel->where($where_team)->field('teamId')->find();
                $result['b.teamId'] = $team['teamId'] ?: 0;
                $option['b.teamId'] = '=';
                unset($result['teamName'], $option['teamName']);
            }

            # 用户昵称 转化 用户id
            if( I('get.nickname'))
            {
                $where_user['nickname'] = trim(I('get.nickname'));
                $user = $this->userModel->where($where_user)->field('uid')->find();
                $result['b.uid'] = $user['uid'] ?: 0;
                $option['b.uid'] = '=';
                unset($result['nickname'], $option['nickname']);
            }

            $_GET['filter'] = json_encode($result);
            $_GET['option'] = json_encode($option);
        }
    }


}