<?php
/**
 * 角色组模型
 * User: Administrator
 * Date: 2018/5/11 0011
 * Time: 下午 03:01
 * @author 1010
 */

namespace Admin\Controller\Auth;


use Admin\Library\Tree;
use Common\Controller\BaseController;
use Think\Controller;
use Think\Model;
use Think\Page;
use Admin\Library\Auth;

class GroupController extends BaseController
{
    //todo:设定当前管理员为超级管理员 先编写管理操作代码 之后再补充权限控制

    //数据库对象
    private $model = null;

    //当前登录管理员所有子组别
    protected $childrenGroupIds = [];

    //表单验证配置
    private $fromValidate = [
        'group[name]' => 'required;',
        'group[rules]' => 'required;',
        'group[pid]' => 'required;'
    ];

    /**
     * 初始化
     */
    public function __construct()
    {
        parent::__construct();

        // 导入树形类
        import('Admin.Class.Tree', APP_PATH);

        //获取实例化对象
        $this->model = D("AuthGroup");

        //获取权限扩展类
        $this->auth = new Auth();

        //取出当前管理员所拥有权限的分组id
        $this->childrenGroupIds = $this->auth->getChildrenGroupIds(true);

        //表单验证配置
        $this->assign('fromValidate', json_encode($this->fromValidate));

    }

    /**
     * 初始化树形类和数据
     */
    private function initTree()
    {
        //获取当前管理员所用有的权限数据
        $groupList = $this->model->where(['id' => ['in', $this->childrenGroupIds]])->select();

        //初始化树形类
        Tree::instance()->init($groupList);

        $GroupData = [];

        //判断管理员是否为超级管理员
        if ($this->auth->isSuperAdmin()) {
            //获取全部角色组数据
            $GroupData = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0), 'title');
        } else {
            //获取当前角色组数据
            $groups = $this->auth->getGroups();

            foreach ($groups as $m => $n) {
                $result = array_merge($GroupData, Tree::instance()->getTreeList(Tree::instance()->getTreeArray($n['pid']),'title'));
            }

            foreach ($result as $k => $v)
            {
                $GroupData[$v['id']] = $v;
            }

        }

        return $GroupData;

    }

    /**
     * 获取树形select
     * @return array
     */
    private function getGroupSelect($selectId = 0)
    {
        $treeData = $this->initTree();
        $first_data = reset($treeData);

        //初始化树形类
        Tree::instance()->init($this->initTree());

        //获取树形类数组
        $ruleTreeArray = Tree::instance()->getTree($first_data['pid'], "<option value=@id @selected @disabled>@spacer@name</option>", $selectId);

        return $ruleTreeArray;
    }

    /**
     * 角色组列表
     * @param int $page 当前页数
     */
    public function index($page = 0)
    {
        if(IS_AJAX){
            $GroupData =$this->initTree();
            //获取角色组树形数据
            array_walk($GroupData,function(&$value,&$key){
                $value['name'] = $value['title'].$value['name'];
            });
            $total = count($GroupData);
            $result = array("total" => $total, "rows" => $GroupData);
            $this->ajaxReturn($result,'JSON');
        }
        $this->display();
    }

    /**
     * 加载权限树
     */
    public function loadRoleTree($id = null, $pid = 1)
    {
        //获取当前组id以及父组id
        $id = I('post.id', null) ? I('post.id', null) : $id;
        $pid = I('post.pid', null) ? I('post.pid', null) : $pid;


        //获取父级数据
        $parentData = $this->model->where(['id' => $pid])->find();

        //当前分组数据
        $currentData = null;

        //编辑角色组时需要获取当前组数据
        if ($id) {
            $currentData = $this->model->where(['id' => $id])->find();
        }

        // 判断当前父级数据或者当前数据是否存在 不存在则报错
        if (($pid || $parentData) and (!$id || $currentData)) {

            $id = $id ? $id : NULL;
            $ruleList = D("AuthRule")->order('weigh desc')->select();

            //读取父类角色所有节点列表
            $parentRuleList = [];

            //判断父级分组是不是属于超级管理员组 如果是的话则讲规则表数据全部赋予
            if (in_array('*', explode(',', $parentData['rules']))) {
                $parentRuleList = $ruleList;
            } else {
                $parentRuleIds = explode(',', $parentData['rules']);
                foreach ($ruleList as $k => $v) {
                    if (in_array($v['id'], $parentRuleIds)) {
                        $parentRuleList[] = $v;
                    }
                }
            }

            //当前所有正常规则列表
            Tree::instance()->init($parentRuleList);

            //读取当前角色下规则ID集合
            $adminRuleIds = $this->auth->getRuleIds();

            //是否是超级管理员
            $superadmin = $this->auth->isSuperAdmin();

            //当前拥有的规则ID集合
            $currentRuleIds = $id ? explode(',', $currentData['rules']) : [];


            if (!$id || !in_array($pid, Tree::instance()->getChildrenIds($id, TRUE))) {

                $parentRuleList = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0), 'title');


                $hasChildrens = [];
                foreach ($parentRuleList as $k => $v) {
                    if ($v['haschild'])
                        $hasChildrens[] = $v['id'];
                }

                //获得父级规则id列表
                $parentRuleIds = array_map(function ($item) {
                    return $item['id'];
                }, $parentRuleList);

                $nodeList = [];
                $index = 0;

                foreach ($parentRuleList as $k => $v) {

                    if (!$superadmin && !in_array($v['id'], $adminRuleIds))
                        continue;

                    if ($v['pid'] && !in_array($v['pid'], $parentRuleIds))
                        continue;

                    $nodeList[$index] = [
                        'id' => $v['id'],
                        'pId' => $v['pid'] ? $v['pid'] : 0,
                        'name' => $v['name']
                    ];

                    if (in_array($v['id'], $currentRuleIds)) {
                        $nodeList[$index]['checked'] = true;
                    }

                    if (in_array($v['id'], $currentRuleIds)) {
                        $nodeList[$index]['open'] = true;
                    }

                    $index++;
                }

                if (IS_POST) {
                    echo json_encode($nodeList);
                } else {
                    return $nodeList;
                }

            } else {

                $this->error('不能够选择子节点');
            }

        } else {
            $this->error('没有发现分组数据');
        }
    }

    /**
     * 检查post数据
     * @param array $post
     * @return array
     */
    private function checkPost($post = [])
    {
        //解析选择的规则节点
        $post['rules'] = explode(",", $post['rules']);

        //判断父级id是否在当前管理员分组范围之内
        if (!in_array($post['pid'], $this->childrenGroupIds)) {
            $this->error('选择的父级必须在当前管理员分组范围内');
        }

        //判断父级数据是否存在
        $parentData = D("AuthGroup")->where(['id' => $post['pid']])->find();
        if (!$parentData) {
            $this->error('父级数据不存在');
        }

        //父级别的规则节点
        $parentRules = explode(',', $parentData['rules']);

        //当前组别的规则节点
        $currentRules = $this->auth->getRuleIds();

        $rules = $post['rules'];

        //如果父组不是超级管理员则需要过滤规则节点,不能超过父组别的权限
        $rules = in_array('*', $parentRules) ? $rules : array_intersect($parentRules, $rules);

        //如果当前组别不是超级管理员则需要过滤规则节点,不能超当前组别的权限
        $rules = in_array('*', $currentRules) ? $rules : array_intersect($currentRules, $rules);

        //合并规则id数组
        $post['rules'] = implode(',', $rules);

        return $post;
    }

    /**
     * 添加角色组
     */
    public function add()
    {
        if (IS_POST) {

            //获取当前的group数据
            $post = I("post.group");

            //检查post数据
            $post = $this->checkPost($post);

            //检查验证规则
            if (!$this->model->create($post, Model::MODEL_INSERT)) {
                $this->error($this->model->getError());
            }

            //新增数据
            if (!$id = $this->model->add()) {
                $this->error($this->model->getError());
            }

            //写入日志
            D("AdminLog")->record($post);

            $this->success("新建角色组成功");
        } else {
            //获取角色组树形数据
            $GroupSelect = $this->getGroupSelect();


            $treeData = $this->initTree();
            $first_data = reset($treeData);

            //角色组权限树
            $this->assign('RuleTree', json_encode($this->loadRoleTree()));

            //角色组select
            $this->assign('GroupSelect', $GroupSelect);

            //渲染页面
            $this->display();
        }

    }

    /**
     * 编辑角色组
     * @param null $id
     */
    public function edit($id = null)
    {
        $groupData = $this->model->where(['id' => I('get.id')])->find();

        if (!$groupData) {
            $this->error("角色组数据不存在");
        }

        if (IS_POST) {
            //获取当前的group数据
            $post = I("post.group");

            //检查post数据
            $post = $this->checkPost($post);

            $post['id'] = I('get.id', 0, 'intval');

            //检查验证规则
            if (!$this->model->create($post, Model::MODEL_UPDATE)) {
                $this->error($this->model->getError());
            }

            //新增数据
            if (!$id = $this->model->save()) {
                $this->error($this->model->getError());
            }

            //写入日志
            D("AdminLog")->record($post);

            $this->success("编辑角色组成功");
        } else {

            //角色组权限树
            $this->assign('RuleTree', json_encode($this->loadRoleTree($groupData['id'], $groupData['pid'])));

            //角色组select
            $this->assign('GroupSelect', $this->getGroupSelect($groupData['pid']));

            //渲染模板
            $this->assign('groupData', $groupData);

            $this->display('add');
        }

    }

    /**
     * 删除角色组
     * @param string $ids
     */
    public function delete($ids = "")
    {
        //获取角色组ids数组 如果不存在则报错
        if ($ids = explode(',', $ids)) {

            //获取当前角色组数据
            $groupData = $this->auth->getGroups();

            //获取当前角色组id
            $group_ids = array_column($groupData, 'id');

            //移除掉当前管理员所在组别
            $ids = array_diff($ids, $group_ids);

            //获取移除后的数据
            $groupData = $this->model->where('id', 'in', $ids)->select();
            $groupAccessModel = D('AuthGroupAccess');


            //循环判断每一个组别是否可删除
            foreach ($groupData as $k => $v) {

                //当前组别下有管理员
                $adminData = $groupAccessModel->where(['group_id' => $v['id']])->select();

                //判断角色组下的管理员是否存在
                if ($adminData) {
                    $ids = array_diff($ids, [$v['id']]);
                    continue;
                }

                //当前组别下有子组别
                $adminData = $this->model->where(['pid' => $v['id']])->select();

                //判断是否有子组
                if ($adminData) {
                    $ids = array_diff($ids, [$v['id']]);
                    continue;
                }
            }

            if (!$ids) {
                $this->error('您不能删除包含子组和管理员的组');
            }

            $count = $this->model->data(['status' => 0])->where(['id' => ['in', $ids]])->save();
            if ($count) {

                //写入日志
                D("AdminLog")->record(['id' => ['in', $ids]]);

                $this->success("删除成功");
            }
        }
    }

}