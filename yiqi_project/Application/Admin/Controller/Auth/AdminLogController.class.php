<?php
/**
 * 管理员日志控制器
 * User: 1010
 * Date: 2018/5/21
 * Time: 22:45
 */

namespace Admin\Controller\Auth;


use Common\Controller\BaseController;
use Think\Page;

class AdminLogController extends BaseController
{
    //数据库对象
    protected $model = null;

    //当前管理员权限控制下子分组ids
    protected $childrenGroupIds = [];

    //当前管理员权限控制下管理员ids
    protected $childrenAdminIds = [];

    /**
     * 初始化应用
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = D('AdminLog');

        $this->childrenAdminIds = $this->auth->getChildrenAdminIds(true);
        $this->childrenGroupIds = $this->auth->getChildrenGroupIds($this->auth->isSuperAdmin() ? true : false);
    }

    public function index($offset = 0)
    {
        if(IS_AJAX){

            if(I("get.filter")){
                $result = (array)I("get.filter","", "json_decode");
                if(isset($result['username'])){
                    $userInfo = D('Admin')->where(['username' => $result['username']])->find();
                    unset($result['username']);
                    $result['admin_id'] = $userInfo['id'];
                    $_GET['filter'] = json_encode($result);
                }
            }

            //获取查询条件
            list($where, $sort, $order, $offset, $limit) = $this->getSerachParam();


            //判断筛选条件是否存在
            if ($where) {
                $where = implode(" and ", array_values($where));
            }

            //获取管理员日志总数
            $adminLogCount = $this->model->where($where)
                ->where(['admin_id' => ['in', $this->childrenAdminIds]])
                ->count();

            //获取管理员日志数据
            $adminLogData = $this->model->where($where)
                ->where(['admin_id' => ['in', $this->childrenAdminIds]])
                ->limit($offset, $limit)
                ->order([$sort => $order])
                ->select();

            //循环 增加用户信息
            foreach ($adminLogData as $key => &$value) {
                $UserInfo = $this->auth->getUserInfo($value['admin_id']);
                $value['username'] = $UserInfo['username'];
            }
            $result = array("total" => $adminLogCount, "rows" => $adminLogData ?:[]);

            $this->ajaxReturn($result,'JSON');

        }
        $this->display();
    }

    /**
     * 获取详情
     * @param string $id
     * @return mixed
     */
    public function detail($id = "")
    {
        $adminLogData = $this->model->where(['id' => $id])->find();
        $this->assign("adminLogData", $adminLogData);
        return $this->display();
    }

    /**
     * 删除
     */
    public function delete($ids = "")
    {
        if ($ids) {
            //避免越权删除管理员
            $group_ids = implode(",", $this->childrenGroupIds);
            $sql = "SELECT * FROM `yq_admin_log` WHERE ( `id` IN ($ids) AND `admin_id` IN ( SELECT `uid` FROM `yq_auth_group_access` WHERE `group_id` IN ($group_ids) ) )";
            $adminData = $this->model->query($sql);

            if ($adminData) {
                $deleteIds = [];
                foreach ($adminData as $k => $v) {
                    $deleteIds[] = $v['id'];
                }
                if ($deleteIds) {
                    $this->model->where(['id' => ['in', $deleteIds]])->delete();
                    $this->success("删除成功");
                }
            }
        }
    }
}