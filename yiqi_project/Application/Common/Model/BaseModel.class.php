<?php
/**
 * 公共数据模型
 * User: lizhqiiang
 * Date: 2018/5/27
 * Time: 23:02
 */

namespace Common\Model;


use Think\Model;

class BaseModel extends Model
{
    protected $sqlConditions = null;

    /**
     * 设置用户信息转化为对象
     * @param string $field
     * @param mixed $value
     * @return mixed|void4
     */
    public function __set($field = "", $value)
    {
        $this->sqlConditions[$field] = $value;
    }

    /**
     * 将用户信息转化为对象属性
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if (property_exists($this, 'sqlConditions')) {
            return $this->getInfo($name, $this->sqlConditions);
        } else {
            return "";
        }
    }

    /**
     * 获取单条信息
     * @param bool $field
     * @param $where
     * @return array|false|mixed|\PDOStatement|string|Model
     */
    protected function getInfo($field = true, $where = null)
    {
        $pk = $this->pk;
        if (!$where) {
            $where = $this->sqlConditions;
        }
        return $this->field($field)->where($where)->order("$pk desc")->find();
    }

    /**
     * 获取多条信息
     * @param bool $field
     * @param $where
     * @return array|false|mixed|\PDOStatement|string|Model
     */
    protected function getData($field = true, $where = null)
    {
        if (!$where) {
            $where = $this->sqlConditions;
        }
        return $this->field($field)->where($where)->select();
    }

    /**
     * 获取数据数量
     * @param bool $field
     * @param $where
     * @return array|false|mixed|\PDOStatement|string|Model
     */
    protected function getCount($field = true, $where = null)
    {
        if (!$where) {
            $where = $this->sqlConditions;
        }
        return $this->field($field)->where($where)->count();
    }

    /**
     * 查询天数相关where部分
     * @param $day
     * @param $field
     * @return string
     */
    protected function getDayWhere($field = "", $day = 0)
    {
        return "DATE_SUB(CURDATE(), INTERVAL $day DAY) <= from_unixtime($field, '%Y-%m-%d')";
    }

    /**
     * 查询时间范围相关where部分
     * @param string $startTime
     * @param string $endTime
     * @return string
     */
    protected function getTimeBetweenWhere($startTime = "", $endTime = "")
    {
        return "from_unixtime(createtime, '%Y-%m-%d') BETWEEN '$startTime' AND '$endTime'";
    }

    /**
     * 查询月份之差相关数据
     * @param $field
     * @param int $month
     * @return string
     */
    protected function getMonthWhere($field, $month = 0)
    {
        return "PERIOD_DIFF(from_unixtime($field, '%Y%m' ) ,  DATE_FORMAT( CURDATE() , '%Y%m' ) ) =$month";
    }

    /**
     * 查询季度之差相关数据
     * @param $field
     * @param int $quarter
     * @return string
     */
    protected function getQuarterWhere($field, $quarter = 0)
    {
        return "QUARTER(from_unixtime($field, '%Y-%m-%d' ))=QUARTER(DATE_SUB(now(),interval $quarter QUARTER))";
    }

}