<?php
/**
 * 管理员控制器
 * User: 1010
 * Date: 2018/5/15 0015
 * Time: 下午 01:43
 */

namespace Admin\Controller\Auth;


use Admin\Library\Auth;
use Admin\Library\Random;
use Admin\Library\Tree;
use Common\Controller\BaseController;
use Think\Model;
use Think\Page;


class AdminController extends BaseController
{
    //数据库对象
    private $model = null;

    //取出当前管理员所拥有权限的管理员
    private $childrenAdminIds = null;

    //取出当前管理员所拥有权限的分组
    private $childrenGroupIds = null;

    //表单验证配置
    private $fromValidate = [
        'admin[username]' => 'required;remote(post:/admin/index/fieldcheck/controller/auth/action/admin/type/1)',
        'admin[email]' => 'email;required;remote(post:/admin/index/fieldcheck/controller/auth/action/admin/type/1)',
        'admin[password]' => 'required;',
        'group[]' => 'required;',
    ];

    const QUERY_FIELD=['id,username,nickname,email,status,logintime'];
    /**
     * 初始化类
     */
    public function __construct()
    {
        parent::__construct();

        //获取数据对象
        $this->model = D('Admin');

        //获取权限对象
        $this->auth = new Auth();

        //取出当前管理员所拥有权限的管理员id
        $this->childrenAdminIds = $this->auth->getChildrenAdminIds(true);

        //取出当前管理员所拥有权限的分组id
        $this->childrenGroupIds = $this->auth->getChildrenGroupIds(true);

        //表单验证配置
        $this->assign('fromValidate', json_encode($this->fromValidate));

    }

    /**
     * 获取当前管理员能够控制的分组
     * @return array
     */
    private function getAdminGroup()
    {
        //获取分组数据
        $groupData = D("AuthGroup")->where(['id' => ['in', $this->childrenGroupIds]])->select();

        //初始化树形结构数据
        Tree::instance()->init($groupData);

        $groupData = [];

        //判断管理员是否是超级管理员
        if ($this->auth->isSuperAdmin()) {

            //获取分组的树形结构
            $groupData = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0), 'title');

        } else {
            //获取当前管理的角色组数据
            $nowGroup = $this->auth->getGroups();
            $groupData = [];
            foreach ($nowGroup as $m => $n) {
                $childlist = Tree::instance()->getTreeList(Tree::instance()->getTreeArray($n['id']), 'title');
                $temp = [];
                foreach ($childlist as $k => $v) {
                    $temp[$v['id']] = $v;
                }
                $groupData[$n['id']] = $temp;
            }
        }

        return $groupData;
    }

    /**
     * 获取树形select
     * @return array
     */
    private function getAdminGroupSelect($selectId = 0)
    {
        //获取admin权限下的分组数据
        $treeData = $this->getAdminGroup();

        //判断管理员是否是超级管理员
        if ($this->auth->isSuperAdmin()) {
            //实例化树形数据
            Tree::instance()->init($treeData);
            //获取树形类数组
            $treeSelect = Tree::instance()->getTree(0, "<option value=@id @selected @disabled>@spacer@name</option>", $selectId);
            return $treeSelect;
        } else {
            $treeSelect = [];
            //迭代父级分组下的子级分组
            foreach ($treeData as $key => $value) {
                //实例化树形数据
                Tree::instance()->init($value);
                //获取树形类数组
                $treeSelect[] = Tree::instance()->getTree($key, "<option value=@id @selected @disabled>@spacer@name</option>", 0);
            }

            //合并分组option
            return implode("", $treeSelect);
        }

    }

    /**
     * 管理员列表
     */
    public function index($offset = 0)
    {
        if(IS_AJAX){
            //获取当前权限下子分组名称
            $groupData = D("AuthGroup")->field('id,name')->where(["id" => ['in', $this->childrenGroupIds]])->select();
            $groupName = array_column($groupData, 'name', 'id');

            //获取分组与管理员对应关系
            $authGroupData = D("AuthGroupAccess")->field('uid,group_id')
                ->where(["group_id" => ['in', $this->childrenGroupIds]])->select();

            //设置管理员已权限分组数据对应关系
            $adminGroupName = [];
            foreach ($authGroupData as $k => $v) {
                if (isset($groupName[$v['group_id']]))
                    $adminGroupName[$v['uid']][$v['group_id']] = $groupName[$v['group_id']];
            }

            //获取管理员对应分组名称
            $groups = $this->auth->getGroups();
            foreach ($groups as $m => $n) {
                $adminGroupName[$this->auth->id][$n['id']] = $n['name'];
            }

            //获取查询条件
            list($where, $sort, $order, $offset, $limit) = $this->getSerachParam();

            //判断筛选条件是否存在
            if ($where) {
                $where = implode(" and ", array_values($where));
            }


            //获取管理员总数
            $adminCount = $this->model->where($where)
                ->where(['id' => ['in', $this->childrenAdminIds]])
                ->count();


            //获取管理员数据
            $adminData = $this->model->field(self::QUERY_FIELD)
                ->where($where)
                ->where(['id' => ['in', $this->childrenAdminIds]])
                ->limit($offset, $limit)
                ->order([$sort => $order])
                ->select();
            //循环 增加分组信息
            foreach ($adminData as $key => &$value) {
                $groups = isset($adminGroupName[$value['id']]) ? $adminGroupName[$value['id']] : [];
                $value['groups'] = implode(',', array_keys($groups));
                $value['groups_text'] = implode(',', array_values($groups));
            }
            unset($value);

            $result = array("total" => $adminCount, "rows" => $adminData ?$adminData:[]);

            $this->ajaxReturn($result,'JSON');
        }
        $this->display();
    }

    /**
     * 新增管理员
     */
    public function add()
    {
        if (IS_POST) {
            if ($post = I('post.admin', [], 'strip_tags')) {

                //获得密码盐
                $post['salt'] = Random::alnum();

                //管理员用户id
                $post['uid'] = mt_rand(100, 10000);

                //对密码进行加盐操作
                $post['password'] = md5(md5($post['password']) . $post['salt']);

                //设置新管理员默认头像。
                $post['avatar'] = '/Public/Admin/Cube/img/img/user.jpg';

                //检查并创建数据结构
                if (!$this->model->create($post, Model::MODEL_INSERT)) {
                    $this->error($this->model->getError());
                }

                //新增管理员数据
                if (!$id = $this->model->add()) {
                    $this->error($this->model->getError());
                }

                //获取分组id
                if (!$group = I('post.group', [], 'strip_tags')) {
                    $this->error('所属组别必须存在');
                }

                //过滤不允许的组别,避免越权
                $group = array_intersect($this->childrenGroupIds, $group);

                //封装多条数据
                $dataset = [];
                foreach ($group as $value) {
                    $dataset[] = ['uid' => $id, 'group_id' => $value];
                }

                //写入多个组别对应关系
                D('AuthGroupAccess')->addAll($dataset);

                //写入日志
                D('AdminLog')->record($post);

                $this->success('新增管理员成功');
            }
        } else {
            //获取默认分组信息
            $adminGroupData = $this->getAdminGroupSelect();
            $this->assign('adminGroupData', $adminGroupData);
            $this->assign('groupids', json_encode([]));
            $this->display();
        }
    }

    /**
     * 编辑管理员
     */
    public function edit($id = NULL)
    {
        if (!$adminData = $this->model->where(['id' => $id])->find()) {
            $this->error('管理员数据不存在');
        }

        if (IS_POST) {
            if ($post = I('post.admin', [], 'strip_tags')) {

                if ($post['password']) {
                    $post['salt'] = Random::alnum();
                    $post['password'] = md5(md5($post['password']) . $post['salt']);
                } else {
                    unset($post['password'], $post['salt']);
                }

                $post['id'] = I('get.id', 0, 'intval');

                //检查并创建数据结构
                if (!$this->model->create($post, Model::MODEL_UPDATE)) {
                    $this->error($this->model->getError());
                }

                //编辑管理员数据
                if (!$id = $this->model->save()) {
                    $this->error($this->model->getError());
                }

                //先移除所有权限
                D('AuthGroupAccess')->where(['uid' => $post['id']])->delete();

                //获取分组id
                $group = I('post.group', [], 'strip_tags');

                //过滤不允许的组别,避免越权
                $group = array_intersect($this->childrenGroupIds, $group);

                //封装多条数据
                $dataset = [];
                foreach ($group as $value) {
                    $dataset[] = ['uid' => $post['id'], 'group_id' => $value];
                }

                //写入多个组别对应关系
                D('AuthGroupAccess')->addAll($dataset);

                //写入日志
                D('AdminLog')->record($post);

                $this->success('编辑管理员成功');
            }
        } else {

            $this->fromValidate['admin[username]'] = 'required;remote(post:/admin/index/fieldcheck/controller/auth/action/admin/type/2)';
            $this->fromValidate['admin[email]'] = 'email;required;remote(post:/admin/index/fieldcheck/controller/auth/action/admin/type/2)';
            unset($this->fromValidate['admin[password]']);

            //获取当前分组
            $groupNowData = $this->auth->getGroups($adminData['id']);
            $groupids = [];
            foreach ($groupNowData as $k => $v) {
                $groupids[] = $v['id'];
            }

            $adminGroupData = $this->getAdminGroupSelect();
            $this->assign('adminGroupData', $adminGroupData);
            $this->assign('fromValidate', json_encode($this->fromValidate));
            $this->assign("adminData", $adminData);
            $this->assign("groupids", json_encode($groupids));
            $this->display('add');
        }
    }

    /**
     * 删除管理员
     * @param string $ids
     */
    public function delete($ids = '')
    {
        if ($ids) {
            //避免越权删除管理员
            $group_ids = implode(",", $this->childrenGroupIds);
            $sql = "SELECT * FROM `yq_admin` WHERE ( `id` IN ($ids) AND `id` IN ( SELECT `uid` FROM `yq_auth_group_access` WHERE `group_id` IN ($group_ids) ) )";
            $adminData = $this->model->query($sql);

            if ($adminData) {
                $deleteIds = [];
                foreach ($adminData as $k => $v) {
                    $deleteIds[] = $v['id'];
                }

                $deleteIds = array_diff($deleteIds, [$this->auth->id]);

                if ($deleteIds) {
                    $this->model->data(['status' => 'hidden'])->where(['id' => ['in', $deleteIds]])->save();

                    //由于使用admin使用了字段代替真正的删除 所以分组关系必须保存下来 以便之后进行恢复
                    //D('AuthGroupAccess')->where(['uid'=> ['in', $deleteIds]])->delete();

                    //写入日志
                    D("AdminLog")->record(['ids' => $ids]);

                    $this->success("删除成功");
                }
            }
        }
    }

}