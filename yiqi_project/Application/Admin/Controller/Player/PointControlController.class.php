<?php
/**
 * 点控控制器
 * User: lbb
 * Date: 2019/2/22
 * Time: 16:48
 */

namespace Admin\Controller\Player;
use Common\Controller\BaseController;
use Think\Model;

class PointControlController extends BaseController
{
    //数据对象
    private $pointModel;

    //用户表
    private $userView ;

    //管理员操作表
    private $handleLogModel;


    //分页条数
    private $pageSize = 100;

    /**
     * 初始化
     */
    public function __construct()
    {
        parent::__construct();

        $this->pointModel     = D('PointControl');

        $this->handleLogModel = D('HandleLog');

        $this->userView       = D('users');
    }

    /**
     * 设置点控
     * Author:lbb
     * @param int $uid
     */
    public function add($uid = 0) :void
    {

        //判断用户是否存在
        $field = 'uid,nickname,gold,user_lose_win_all,daily_gold';
        if (!$uid || !($data = $this->userView->field($field)->where(['uid' => $uid])->find())) {
            $this->error('用户ID没有找到1');
        }

        //判断当前用户是否有点控
        $point_control = $this->pointModel
            ->where(['uid' => $uid, 'status' => 1])
            ->order('id desc')
            ->find();

        if (!empty($point_control)) {
            $is_control = 1;
        } else {
            $is_control = 0;
        }


        if (IS_POST) {

            if ($post = I('post.row', [], 'strip_tags')) {

                if($point_control){
                    $this->error('用户已有点控');
                }

                //封装操作用户
                $post['uid'] = $uid;
                $post['staffId'] = $this->auth->id;
                $post['status'] = 1;
                $post['createtime'] = time();

                //验证数据格式
                if (!$this->pointModel->create($post, Model::MODEL_INSERT)) {
                    $this->error('验证数据失败 请重新提交');
                }


                //新增数据
                $control_id=$this->pointModel->add($post);

                if (!$control_id) {
                    $this->error('新增点控数据失败');
                }
                //封装写入数组
                $user_data = [
                    'uid' => $uid,
                    'title' => '设置点控',
                    'admin_id' => $this->auth->id
                ];

                //写入用户日志
                $this->handleLogModel->record($user_data, 'point',$post);

                //写入后台日志
                $this->adminLogModel->record($post);

                //发送服务器
                $send_data=[
                    'id'            => (int)$uid,
                    'controlid'     => (int)$control_id,
                    'onoff'         => 1,                       //0:关，1开
                    'state'         => (int)$post['type'],      //输赢
                    'value'         => $post['controlSum'],     //目标
                    'schemeid'      => (int)$post['plan'],      //方案
                ];
                send_server($send_data,'/PointControl.php');

                $this->success('新增点控数据成功');
            }
        }

        //表单验证配置
        $this->assign('fromValidate', json_encode([
            'row[type]' => 'required;',
            'row[plan]' => 'required;',
            'row[controlSum]' => 'required;integer;',
            'row[disc]' => 'required;'
        ]));

        $this->assign('data', $data);
        $this->assign('is_control', $is_control);
        $this->assign('type_config', $this->FieldConfig['point_control_type']);
        $this->assign('plan_config', $this->FieldConfig['point_control_plan']);

        //渲染模板
        $this->display();
    }

    /**
     * 点控列表
     * Author:lbb
     */
    public function index()
    {

        $uid = I('get.uid');
        $this->assign('uid',$uid);

        if (IS_AJAX) {
            //查询参数
            $where=$this->searchFilter();
            //获取数据
            $whereTwo=[];
            if(count($where)>1){
                unset($where[0]);
                foreach ($where as $k=>$v){
                    $whereTwo['p.'.$k]=$v;
                }
            }

            $count      = $this->pointModel->where($where)->count();                      //总条数
            $total= $this->pointModel
                ->where($where)
                ->field('type,sum(progress) as lose_win')
                ->group('type')
                ->select();                      //总输赢
            foreach ($total as $key=>$val) {
                $totals[$val['type']] = $val['lose_win'];
            }


            $page = new \Think\Ajaxpage($count, $this->pageSize, 'indexAjaxComm');// 实例化分页类 传入总记录数和每页显示的记录数(25)
            $show = $page->show();   // 分页显示输出
            $p = (int)$_REQUEST['p'];


            $data = M('PointControl as p')
                ->field('p.id,p.uid,u.nickname,u.level,p.staffId,p.createtime,p.type,p.plan,p.controlSum,p.progress,p.endtime,p.deleteStaff,p.status,p.totalWin,a.bunkogold')
                ->join('left join yq_user as u on u.uid=p.uid')
                ->join('left join yq_account as a on a.uid=p.uid')
                ->where($whereTwo)
                ->order('p.id desc')
                ->page($p, $this->pageSize)
                ->select();

            foreach ($data as $key => $val) {

                $data[$key]['level'] = $this->FieldConfig['level'][$val['level']];
                $data[$key]['plan'] = $this->FieldConfig['point_control_plan'][$val['type']][$val['plan']];

                $adminInfo = M('admin')->field('username')->where(array('id' => $val['staffId']))->find();
                $data[$key]['staffId'] = $adminInfo['username'];
                $data[$key]['createtime'] = date('Y-m-d H:i:s', $val['createtime']);
                if ($val['status'] === '0') {
                    $delAdminInfo = M('admin')->field('username')->where(array('id' => $val['deleteStaff']))->find();
                    $data[$key]['endtime'] = '点控取消';
                    $data[$key]['deleteStaff'] = $delAdminInfo['username'];
                } elseif ($val['status'] === '1') {
                    $data[$key]['endtime'] = '未完成';
                } else {
                    $data[$key]['endtime'] = date('Y-m-d H:i:s', $val['endtime']);
                }
            }

            $this->assign('page',$show); // 赋值分页输出
            //$this->assign(['data' => $data,'dataStatistics' => $dataStatistics]);
            $this->assign(['data' => $data]);
            $this->assign(array('count' => $count, 'count_win' => $totals[0] ?: 0, 'count_lose' => $totals[1] ?: 0));
            $res['content'] = $this->fetch('Player/point_control/replace'); // 赋值分页输出

            return returnAjax(200,'SUCCESS',$res);
        }
        //渲染模板
        $this->display();
    }

    /**
     * 点控取消原因
     * Author:lbb
     */
    public function reason()
    {
        $id = I('get.id');
        $res=$this->pointModel
            ->where(array('id'=>$id))
            ->find();

        $admin_info=M('admin')->field('username')->where(array('id'=>$res['deleteStaff']))->find();

        $res['nickname']=$admin_info['username'];
        $res['createtime']=date('Y-m-d H:i:s', $res['createtime']);
        $res['deletetime']=date('Y-m-d H:i:s', $res['deletetime']);
        $res['plan']=$this->FieldConfig['point_control_plan'][$res['type']][$res['plan']];
        $res['type']=$this->FieldConfig['point_control_type'][$res['type']];

        $this->assign('data',$res); // 赋值分页输出
        $this->display();
    }


    /**
     * 搜索参数过滤
     * Author:lbb
     * @return array
     */
    private function searchFilter()
    {
        //获取今天时间段
        $todayStart = strtotime(date('Y-m-d').' 00:00:00');
        $todayEnd = strtotime(date('Y-m-d').' 23:59:59');

        //默认条件
        $where[]='1=1';
        $where['createtime'] = array(array('egt',$todayStart),array('elt',$todayEnd));
       /* if(!empty(I('get.uid'))){
            $where[]='1=1';
        }else{
            $where[]='1=1';
            $where['createtime'] = array(array('egt',$todayStart),array('elt',$todayEnd));
        }
        var_dump($where);exit;*/

        //判断玩家uid
        if( I('get.uid')) {
            $where['uid'] = I('get.uid');
        }

        //判断管理员id
        if( I('get.staff_id')){
            $where['staffId'] = I('get.staff_id');
        }

        //点控类型
        if( !empty(I('get.point_control_type')) || I('get.point_control_type')==='0' ) {
            $where['type'] = I('get.point_control_type');
        }

        //点控状态
        if( !empty(I('get.point_control')) || I('get.point_control')==='0' ){
            $where['status'] = I('get.point_control');
        }

     if( I('get.totalWin')==='0' ){
         $where['totalWin'] = array('gt',0);
     }elseif ( I('get.totalWin')==='1') {
         $where['totalWin'] = array('lt',0);
     }

        //点控时间
        if( I('get.start_time')&&I('get.end_time')){
            $where['createtime'] = array(array('egt',strtotime(I('get.start_time'))),array('elt',strtotime(I('get.end_time'))));
        }elseif(I('get.start_time')){
            $where['createtime']=array('egt',strtotime(I('get.start_time')));
        }elseif(I('get.end_time')){
            $where['createtime']=array('elt',strtotime(I('get.end_time')));
        }

        return $where;
    }


}