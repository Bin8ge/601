<?php
/**
 * 首次进入游戏
 * User: Lbb
 * Date: 2018/7/11 0011
 * Time: 16:38
 */

namespace Admin\Controller\Data;


use Common\Controller\BaseController;

class FirstController extends BaseController
{
     //公共控制器
    private $commAction ;

    //在线人数
    private $usersModel;

    private $userModel;


    public function __construct()
    {
        parent::__construct();

        $this->usersModel = D('users');

        $this->userModel = D('user');

    }

    public function index()
    {
        if (IS_AJAX) {

            //公共条件
            //时间条件
            $startTime = I('get.startTime');
            $stopTime  = I('get.stopTime');
            $startTime = strtotime($startTime) ?: strtotime(date('Y-m-d'));
            $stopTime  = strtotime($stopTime.' 23:59:59')  ?: time();
            $condition['createtime'] = array('between',array($startTime,$stopTime));

            $where['first_room'] = array('gt',0);
            $where['createtime'] =$condition['createtime'];
            $info = $this->userModel
                ->field('type_name,type,count(first_room) as num')
                ->join('left join yq_game_type b on b.productid = yq_user.first_room')
                ->group('type')
                ->where($where)
                ->select();

            $condition['gold']              = 0;
            $condition['user_lose_win_all'] = 0;
            $condition['level']             = 0;
            $conditions['level']            = 0;
            $conditions['createtime']       = $condition['createtime'];
            #注册总人数
            $data['total']      = $this->usersModel->where($conditions)->count('uid');

            #总资产;总输赢为0的玩家  普通玩家
            $user[0]['y']      = (int)$this->usersModel->where($condition)->count('uid');
            $user[0]['name']   = '总资产;总输赢为0的玩家<b>'.$user[0]['y'].'</b>人';
            #总资产;总输赢不为0的玩家 普通玩家
            $user[1]['y']      = (int)$data['total']-$user[0]['y'];
            $user[1]['name']   = '总资产;总输赢不为0的玩家<b>'.$user[1]['y'].'</b>人';

            $items = [];
            $sum  = 0;
            foreach ($info as $k=>$v){
                $items[$k]['name'] = $v['type_name'].'在线:<b>'.$v['num'].'</b>人';
                $items[$k]['y'] = (int)$v['num'];
                $sum += $v['num'];
            }

            $data['people']         = $items;
            $data['total_people']   = $sum;
            $data['user']           = $user;

            //ajax返回信息，就是要替换的模板
            $data['content'] = $this->fetch('Data/first/replace');
            return returnAjax(200, 'SUCCESS', $data);
        }
        $this->display();
    }


}