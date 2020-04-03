<?php
/**
 * 数据管理 公共控制器
 * User: Lbb
 * Date: 2018/7/20 0020
 * Time: 11:11
 */

namespace Admin\Controller\Data;

use Common\Controller\BaseController;

class CommonController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 生成断层时间
     * Author:lbb
     * @param $start        开始时间戳
     * @param $end          结束数据戳
     * @param array $array  查询结果
     * @param int $val      默认数组键值
     * @return array
     */
    public function dateMap($start,$end,$array=[],$val=0) :array
    {
        $dates = range($start, $end, 86400);
        $dates = array_map(function($v){return date('Y-m-d', $v);}, $dates);
        $zero = array_fill_keys($dates,$val);
        $result = array_merge($zero,$array);
        return $result;
    }

    /**
     * 指定天的周一和周天
     * Author:lbb
     * @param $day
     * @return array
     */
    public function getDays($day) :array
    {
        $lastDay=date('Y-m-d',strtotime("$day Sunday"));
        $firstDay=date('Y-m-d',strtotime("$lastDay -6 days"));
        return array($firstDay,$lastDay);
    }

    /**
     * 指定月的第一天和最后一天
     * Author:lbb
     * @param $day
     * @return array
     */
    public function getMonths($day) :array
    {
        $firstDay = date('Y-m-01',strtotime($day));
        $lastDay  = date('Y-m-d',strtotime("$firstDay +1 month -1 day"));
        return array($firstDay,$lastDay);
    }

    /**
     * 输入开始时间，结束时间，粒度（周，月，季度）
     * @param 参数一：开始时间
     * @param 参数二：结束时间
     * @param 参数三：粒度（周，月，季度）
     * @return 时间段字符串数组
     */
    public function getLdTimes($st,$et,$ld) :array
    {
        if($ld==='周'){
            $timeArr=array();
            $t1=$st;
            $t2=$this->getDays($t1)['1'];
            while($t2<$et || $t1<=$et){//周为粒度的时间数组
                $timeArr['dates'][]=$t1.','.$t2;
                $timeArr['weeks'][]=date('Y',strtotime($t2)).'年-第'.date('W',strtotime($t2)).'周';
                $t1=date('Y-m-d',strtotime("$t2 +1 day"));
                $t2=$this->getDays($t1)['1'];
                $t2=$t2>$et?$et:$t2;
            }
            return $timeArr;
        }else if($ld==='月'){
            $timeArr=array();
            $t1=$st;
            $t2=$this->getMonths($t1)['1'];
            while($t2<$et || $t1<=$et){//月为粒度的时间数组
                $timeArr['dates'][]=$t1.','.$t2;
                $timeArr['months'][]=date('Y',strtotime($t2)).'年-'.date('m',strtotime($t2)).'月';
                $t1=date('Y-m-d',strtotime("$t2 +1 day"));
                $t2=$this->getMonths($t1)['1'];
                $t2=$t2>$et?$et:$t2;
            }
            return $timeArr;
        }else if($ld==='季度'){
            $tStr=explode('-',$st);
            $month=$tStr['1'];
            if($month<=3){
                $t2=date("$tStr[0]-03-31");
            }else if($month<=6){
                $t2=date("$tStr[0]-06-30");
            }else if($month<=9){
                $t2=date("$tStr[0]-09-30");
            }else{
                $t2=date("$tStr[0]-12-31");
            }
            $t1=$st;
            $t2=$t2>$et?$et:$t2;
            $timeArr=array();
            while($t2<$et || $t1<=$et){//月为粒度的时间数组
                $timeArr[]=$t1.','.$t2;
                $t1=date('Y-m-d',strtotime("$t2 +1 day"));
                $t2=date('Y-m-d',strtotime("$t1 +3 months -1 day"));
                $t2=$t2>$et?$et:$t2;
            }
            return $timeArr;
        }else{
            return array('参数错误!');
        }
    }

    /**
     * 时间筛选 及默认值
     * Author:lbb
     * @return mixed
     */
    public function commWhere()
    {
        //当日时间戳
        $arr['same_day']  =  array('between',array(strtotime(date('Y-m-d').' 00:00:00'),strtotime(date('Y-m-d').' 23:59:59')));

        //提前十五天的时间 及时间戳
        $arr['startTime'] = I('get.startTime')  ?: date('Y-m-d',strtotime('-1 month'));
        $arr['stopTime']  = I('get.stopTime')   ?: date('Y-m-d');

        $arr['startStamp'] = strtotime($arr['startTime']) ;
        $arr['stopStamp']  = strtotime($arr['stopTime'].' 23:59:59');

        $arr['before_day'] =  array('between',array( $arr['startStamp'],$arr['stopStamp']));

        return $arr;
    }




















}