<?php
/**
 * 用户游戏信息管理模型
 * User: 1010
 * Date: 2018/5/25
 * Time: 17:27
 */

namespace Admin\Model;
use Common\Model\MysqlServerModel;

class GameUserModel extends MysqlServerModel
{
    // 表名
    protected  $tableName = '';

    public function __construct()
    {
        parent::__construct();
    }

    public static function  setTable($uid) {
        self::$tableName = $uid;
    }
    public function  gameDailyInfo($uid) {
        $start_time=strtotime(date('Y-m-d',time()));
        $end_time=$start_time+24 * 60 * 60-1;
        //M("$uid",'yq_','DB_GAME_USER')
        //$gameInfo=M('yqback_manage2.'.$uid,'yq_')->field('sum(gold)')->where(array('createtime'=>array(array('egt',$start_time),array('elt',$end_time))))->find();
        $gameInfo=M("$uid", "yq_", "DB_GAME_USER")->field('sum(gold)')->where(array('createtime'=>array(array('egt',$start_time),array('elt',$end_time))))->find();

        ///////////////////////
        //$this->db(1,"DB_GAME_USER")->query('select * from yq_109896');
        //////////////////////
        //var_dump($gameInfo);
        //var_dump(M()->getLastSql());exit;
        if($gameInfo){
            return $gameInfo;
        }else{
            return false;
        }
    }
}