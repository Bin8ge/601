<?php
/**
 * 充值分析
 * User: Lbb
 * Date: 2018/7/11 0011
 * Time: 16:38
 */

namespace Admin\Controller\Data;


use Common\Controller\BaseController;

class RechargeController extends BaseController
{

    //充值表
    private $sendTakeModel;



    public function __construct()
    {
        parent::__construct();

        $this->sendTakeModel = D('send_take');
    }



    public function index()
    {
        if (IS_AJAX) {
            //时间条件
            $startTime = I('get.startTime')?:date('Y-m-d');
            $stopTime  = strtotime("{$startTime} +1 day");
            $startTime = strtotime($startTime);
            $condition['createtime'] = array('between',array($startTime,$stopTime));
            $condition['type'] = 'send';
            $condition['send_level'] = array('gt',0);
            $condition['take_level'] = 0;



            $info =  $this->sendTakeModel
                ->field("from_unixtime( createtime ,'%H') as hours,sum(send_gold) as total_gold,count(id) as send_num")
                ->where($condition)
                ->group('hours')
                ->select();

            #笔数
            $number =  $this->sendTakeModel
                ->field('send_gold,count(*) as num')
                ->where($condition)
                ->group('send_gold')
                ->order('num desc')
                ->limit(10)
                ->select();

            $timeArray = [];
            for($i=0;$i<=23;$i++) {
                if ($i<10) {
                    $timeArray[] = str_pad($i,2,0,STR_PAD_LEFT);
                }else{
                    $timeArray[] = (string)$i;
                }
            }


            $trading = [];
            foreach ($info as $key=>$val) {
                   $trading[$val['hours']]    = $val;
            }
            foreach ($timeArray as $v) {
                if (!isset($trading[$v])){
                    $trading[$v]    = [
                        'hours'      => $v,
                        'total_gold' => 0,
                        'send_num'   => 0,
                    ];
                }else{
                    $trading[$v]['total_gold'] = (int)$trading[$v]['total_gold'];
                    $trading[$v]['send_num'] = (int)$trading[$v]['send_num'];
                }
            }
            ksort($trading);

            $data['gold'] = array_column($trading,'total_gold');
            $data['times'] = array_column($trading,'hours');
            $data['num']  = array_column($trading,'send_num');

            $data['send_gold']  = array_column($number,'send_gold');
            $data['send_num']  = array_map('intval',array_column($number,'num'));


            //ajax返回信息，就是要替换的模板
            $data['content'] = $this->fetch('Data/recharge/replace');
            return returnAjax(200, 'SUCCESS', $data);
        }
        $this->display();
    }



}