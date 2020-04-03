<?php
/**
 * 数据库连接
 * User: 1010
 * Date: 2018/5/25
 * Time: 17:46
 */

namespace Common\Model;


use Think\Model;

class MysqlServerModel extends Model {

    //　采用数组方式定义
    protected $connection;
    protected $tablePrefix;

    public function __construct()
    {
        //初始化数据库连接
        $this->connection = C('DB_GAME_USER');
        $this->tablePrefix = C('DB_GAME_USER')['DB_PREFIX'];
        parent::__construct();
    }

    public function test()
    {
        return 'VisitModel function...';
    }

}