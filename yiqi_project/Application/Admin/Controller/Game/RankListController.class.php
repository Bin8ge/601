<?php
/**
 * 排行榜
 * User: lbb
 * Date: 2019/3/11
 * Time: 15:08
 */

namespace Admin\Controller\Game;
use Common\Controller\BaseController;

class RankListController extends BaseController
{
    //排行表
    private $paihangModel;

    private $teamAgentModel;

    private $teamMemberModel;

    private $userModel;

    private $pageSize = 100;

    public function __construct()
    {
        parent::__construct();

        $this->paihangModel    = D('paihang');

        $this->teamAgentModel  = D('team_agent');

        $this->teamMemberModel = D('team_member');

        $this->userModel       = D('user');

    }

    /**
     * Author:lbb
     */
    public function index()
    {
        if (IS_AJAX)
        {
            $condition = [];
            $uid = I('get.uid');

            if ($uid) {
                $condition['a.uid'] = $uid;
            }

            $page = (int)$_REQUEST['p'];
            # 查询满足要求的总记录数
            $count = $this->paihangModel ->alias('a')
                ->where($condition)
                ->count();

            # 实例化分页类 传入总记录数和每页显示的记录数(25)
            $Page  = new \Think\Ajaxpage($count,$this->pageSize,'indexAjaxComm');

            # 分页显示输出
            $show  = $Page->show();

            $list=$this->paihangModel
                ->where($condition)
                ->alias('a')
                ->join('left join yq_user_account b on a.uid = b.uid')
                ->field('a.uid,a.is_list,b.gold+b.bank as gold,b.nickname,b.level')
                ->order('gold desc')
                ->page($page,$this->pageSize)
                ->select();

            foreach ($list as &$value) {
                $whereUid['uid'] = $value['uid'];
                $teamId  = $this->teamMemberModel->where($whereUid)->field('teamId')->find();
                $whereTeam['teamId'] = $teamId['teamId'];
                $teamName = $this->teamAgentModel->where($whereTeam)->field('teamName')->find();
                $value['team'] = $teamName['teamName'];
                $value['level']                    = $this->FieldConfig['level'][$value['level']];
            }

            $this->assign('page',$show); // 赋值分页输出
            $this->assign('list',$list); //查询结果

            //ajax返回信息，就是要替换的模板
            $res['content'] = $this->fetch('Game/rank_list/replace');
            return returnAjax(200,'SUCCESS',$res);

        }

        $this->display();
    }


    /**
     * 添加代理排行榜 old
     * Author:lbb
     */
    /*public function adds()
    {
        if(IS_AJAX){
            $uid = (int)I('post.uid');

            if (!$uid || $uid<=0 ) {
                return returnAjax('400','非法参数,请重新输入~~');
            }

            $condition['uid'] = $uid;
            # 判断代理是否存在
            $isHave = $this->userModel->where($condition)->find();
            if ( !$isHave ){
                return returnAjax('400','代理不存在,请核实~~');
            }
            # 判断是否存在排行表中
            $isMsg = $this->paihangModel->where($condition)->find();
            if ( $isMsg ){
                return returnAjax('400','玩家已存在排行表中,无需再添加~~');
            }

            $data['uid']         = $uid;
            $data['createtime']  = time();

            $status = $this->paihangModel->add($data);

            # 写入后台日志
            $this->adminLogModel->record($_POST);

            if ($status){
                return returnAjax('200','true');
            }else{
                return returnAjax('400','false');
            }
        }
        $this->display();
    }*/

    /**
     * 添加代理排行榜
     * Author:lbb
     */
    public function add()
    {
        if(IS_AJAX){
            $uid = trim(I('post.uid'));
            $uid_array = explode(';',$uid);
            $data = [];
            foreach ($uid_array as $key=>$val) {
                $val = trim($val);
                $condition['uid'] = $val;
                # 判断代理是否存在
                $isHave = $this->userModel->where($condition)->find();
                if ( !$isHave ){
                    continue;
                }
                $isMsg = $this->paihangModel->where($condition)->find();
                if ( $isMsg ){
                    continue;
                }

                $data[] = [
                    'uid' => $val,
                    'createtime' => time()
                ];
            }
            $status = $this->paihangModel->addAll($data);
            $this->adminLogModel->record($_POST);
            if ($status){
                return returnAjax('200','添加成功');
            }else{
                return returnAjax('400','添加失败~~~');
            }
        }
        $this->display();
    }


    /**
     * Author:lbb
     * 上下榜
     */
    public function del()
    {
        if (IS_AJAX){
            $condition['uid'] = I('post.postId');
            $data['is_list'] = I('post.editData');
            # 执行修改操作
            $status = $this->paihangModel->where($condition)->save($data);
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




}