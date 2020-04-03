<?php

namespace Admin\Controller\Auth;

use Admin\Model\AuthModel;
use Common\Controller\BaseController;
use Think\Controller;

use Admin\Library\Tree;
use Think\Model;
use Think\Page;

/**
 * 权限规则控制器
 * @package Admin\Controller
 * @author 1010
 */
class RuleController extends BaseController
{
    //todo:设定当前管理员为超级管理员 先编写管理操作代码 之后再补充权限控制

    //数据对象
    private $model = null;

    //表单验证配置
    private $fromValidate = [
        'rule[node]' => 'required;remote(post:/admin/index/fieldcheck/controller/auth/action/rule/type/1)',
        'rule[name]' => 'required;'
    ];

    /**
     * 初始化
     */
    public function __construct()
    {
        parent::__construct();

        // 导入树形类
        import('Admin.Class.Tree', APP_PATH);

        //数据对象
        $this->model = D("AuthRule");

        //表单验证配置
        $this->assign('fromValidate', json_encode($this->fromValidate));

    }

    /**
     * 初始化树形类和数据
     */
    private function initTree()
    {
        //获取规则表数据
        $ruleList = $this->model->scope('weigh_order')->where(['status'=>'normal'])->select();

        //初始化树形类
        Tree::instance()->init($ruleList);
    }

    /**
     * 规则列表
     * @param int $page 当前页数
     */
    public function index($page = 0)
    {
        if (IS_AJAX) {
            $this->initTree();

            //获取树形类数组
            $ruleTreeArray = Tree::instance()->getTreeArray(0);

            //顶级节点总数以及当前分页
            $total = count($ruleTreeArray);

            //获取规则数组
            $ruleData = Tree::instance()->getTreeList($ruleTreeArray, 'name');
            $result = array("total" => $total, "rows" => $ruleData);
            $this->ajaxReturn($result,'JSON');
        }
        $this->display();
    }

    /**
     * 获取树形select
     * @return array
     */
    private function getRuleSelect($selectId = 0)
    {
        $this->initTree();

        //获取树形类数组
        $ruleTreeArray = Tree::instance()->getTree(0, "<option value=@id @selected @disabled>@spacer@name</option>", $selectId);

        return $ruleTreeArray;
    }

    /**
     * 新增规则
     */
    public function add()
    {
        if (IS_POST) {

            if ($post = I('post.rule', [], 'strip_tags')) {

                //检查并创建数据结构
                if (!$this->model->create($post, Model::MODEL_INSERT)) {
                    //TODO：现在还没有错误页面 之后存在错误页面则显示
                    $this->error($this->model->getError());
                    exit;
                }

                //新增数据
                if (!$id = $this->model->add()) {
                    $this->error($this->model->getError());
                    exit;
                }

                //写入日志
                D("AdminLog")->record($post);

                $this->success("新建规则成功");
            }
        } else {
            $this->assign('ruleSelect', $this->getRuleSelect());
            $this->display();
        }
    }

    /**
     * 编辑规则
     * @param int $id 规则id
     */
    public function edit($id = 0)
    {
        if (!$ruleData = $this->model->where(['id' => I('get.id', 0, 'intval')])->find()) {
            $this->error("用户id不存在");
        }
        $this->fromValidate['rule[node]'] = 'required;remote(post:/admin/index/fieldcheck/controller/auth/action/rule/type/2)';
        if (IS_POST) {

            if ($post = I('post.rule', [], 'strip_tags')) {

                $post['id'] = I('get.id', 0, 'intval');

                //检查并创建数据结构
                if (!$this->model->create($post, Model::MODEL_UPDATE)) {
                    //TODO：现在还没有错误页面 之后存在错误页面则显示
                    $this->error($this->model->getError());
                }

                //更新数据
                if (!$this->model->save()) {
                    $this->error($this->model->getError());
                }

                //写入日志
                D("AdminLog")->record($post);

                $this->success("规则修改成功");
            }
        } else {
            //表单验证配置
            $this->assign('fromValidate', json_encode($this->fromValidate));
            $this->assign('ruleData', $ruleData);
            $this->assign('ruleSelect', $this->getRuleSelect($ruleData['pid']));
            $this->display('add');
        }
    }

    /**
     * 删除规则
     * @param string $ids 规则ids串 1,2,3.....
     */
    public function delete($ids = "")
    {
        $this->initTree();

        if (!$ids = I('get.ids', '')) {
            $this->error("删除规则ids不存在");
        }

        $idList = [];

        //循环ids中的id并获取子节点id
        foreach (explode(',', $ids) as $key => $val) {
            $idList = array_merge($idList, Tree::instance()->getChildrenIds($val, true));
        }

        //数组去重
        $idList = array_unique($idList);

        if (empty($idList)) {
            $this->error("删除规则ids不存在");
        }

        //删除id 列表
        if (!$this->model->data(['status' => 'hidden'])->where(['id' => ['in', $idList]])->save()) {
            $this->error("规则删除失败");
        }

        //写入日志
        D("AdminLog")->record(['id' => ['in', $idList]]);

        $this->success("规则删除成功");
    }
}