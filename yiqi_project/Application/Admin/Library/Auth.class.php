<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/13
 * Time: 14:10
 */

namespace Admin\Library;


use Think\Model;

class Auth extends \Think\Auth
{
    protected $_error = '';

    private $model = null;

    private $requestUri = null;

    private $breadcrumb = null;

    /**
     * 初始化
     */
    public function __construct()
    {
        parent::__construct();

        $this->model = D('Admin');
    }

    /**
     * 将用户信息转化为对象属性
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return session('admin.' . $name);
    }

    /**
     * 管理员登录
     *
     * @param   string $username 用户名
     * @param   string $password 密码
     * @param   int $keeptime 有效时长
     * @return  boolean
     */
    public function login($username, $password, $keeptime = 0)
    {
        $model = $this->model;

        $admin = $model->where(['username' => $username])->find();
        if (!$admin) {
            $this->setError('用户名错误');
            return false;
        }
        if ($admin['status'] == 'hidden') {
            $this->setError('用户名错误');
            return false;
        }
        if ($admin['password'] !== md5(md5($password) . $admin['salt'])) {
            $this->setError('密码错误');
            return false;
        }
        $token = uuid();
        $admin['token'] = $token;
        $admin['logintime'] = time();
        $admin['loginip'] = get_client_ip();

        if (!$model->create($admin, Model::MODEL_UPDATE)) {
            $this->setError($model->getError());
        }
        $model->save();

        session(['name' => 'admin', 'expire' => $keeptime]);
        session('admin', $admin);
        return true;
    }

    /**
     * 注销登录
     */
    public function logout()
    {
        $model = $this->model;
        $admin = $model->where(["id" => $this->id])->find();
        if (!$admin) {
            return true;
        }
        $admin['token'] = '';
        $model->data($admin)->save();
        session('admin', null);
        return true;
    }

    /**
     * 检测是否登录
     *
     * @return boolean
     */
    public function isLogin()
    {
        $admin = session('admin');

        if (!$admin) {
            return false;
        }

        //判断是否同一时间同一账号只能在一个地方登录
        $userData = $this->model->where(['id' => $admin['id']])->find();
        if (!$userData || $userData['token'] !== $admin['token']) {
            return false;
        }

        return true;
    }

    /**
     * 检查权限
     * @param array|string $name
     * @param string $uid
     * @param int $type
     * @param string $mode
     * @param string $relation
     * @return bool
     */
    public function check($name, $uid = '', $type = 'file', $mode = 'url', $relation = 'or')
    {
        return parent::check($name, $this->id, $type = 'file', $mode = 'url', $relation = 'or');
    }

    /**
     * 根据用户id获取用户组,返回值为数组
     * @param null $uid
     * @return array
     */
    public function getGroups($uid = null)
    {
        $uid = is_null($uid) ? $this->id : $uid;
        return parent::getGroups($uid);
    }

    /**
     * 获得权限列表
     * @param integer $uid 用户id
     * @param integer $type
     */
    public function getAuthList($uid = null, $type = 1)
    {
        $uid = is_null($uid) ? $this->id : $uid;
        return parent::getAuthList($uid, $type);
    }

    /**
     * 获取用户信息
     * @param null $uid
     * @return mixed
     */
    public function getUserInfo($uid = null)
    {
        $uid = is_null($uid) ? $this->id : $uid;
        return $uid != $this->id ? $this->model->where(['id' => intval($uid)])->find() : session('admin');
    }

    /**
     * 获取用户所属用户组设置的所有权限规则id
     * @param null $uid
     * @return array
     */
    public function getRuleIds($uid = null)
    {
        $uid = is_null($uid) ? $this->id : $uid;
        return parent::getRuleIds($uid);
    }

    /**
     * 判断用户是否超级管理员
     * @return bool
     */
    public function isSuperAdmin()
    {
        return in_array('*', $this->getRuleIds()) ? TRUE : FALSE;
    }


    /**
     * 检测当前控制器和方法是否匹配传递的数组
     *
     * @param array $arr 需要验证权限的数组
     */
    public function match($arr = [])
    {
        $arr = is_array($arr) ? $arr : explode(',', $arr);
        if (!$arr) {
            return FALSE;
        }

        $arr = array_map('strtolower', $arr);

        // 是否存在
        if (in_array(strtolower(__ACTION__), $arr) || in_array('*', $arr)) {
            return TRUE;
        }

        // 没找到匹配
        return FALSE;
    }

    /**
     * 获取管理员所属于的分组ID
     * @param int $uid
     * @return array
     */
    public function getGroupIds($uid = null)
    {
        $groups = $this->getGroups($uid);
        $groupIds = [];
        foreach ($groups as $K => $v) {
            $groupIds[] = (int)$v['group_id'];
        }
        return $groupIds;
    }

    /**
     * 取出当前管理员所拥有权限的分组
     * @param boolean $withself 是否包含当前所在的分组
     * @return array
     */
    public function getChildrenGroupIds($withself = false)
    {
        //取出当前管理员所有的分组
        $groups = $this->getGroups();
        $groupIds = [];
        foreach ($groups as $k => $v) {
            $groupIds[] = $v['id'];
        }

        $model = D("AuthGroup");

        // 取出所有分组
        $groupList = $model->where(['status' => 1])->select();

        $objList = [];
        foreach ($groups as $K => $v) {
            if ($v['rules'] === '*') {
                $objList = $groupList;
                break;
            }
            // 取出包含自己的所有子节点
            $childrenList = Tree::instance()->init($groupList)->getChildren($v['id'], true);
            $obj = Tree::instance()->init($childrenList)->getTreeArray($v['pid']);
            $objList = array_merge($objList, Tree::instance()->getTreeList($obj));
        }
        $childrenGroupIds = [];
        foreach ($objList as $k => $v) {
            $childrenGroupIds[] = $v['id'];
        }
        if (!$withself) {
            $childrenGroupIds = array_diff($childrenGroupIds, $groupIds);
        }
        return $childrenGroupIds;
    }

    /**
     * 取出当前管理员所拥有权限的管理员
     * @param boolean $withself 是否包含自身
     * @return array
     */
    public function getChildrenAdminIds($withself = false)
    {
        $childrenAdminIds = [];
        if (!$this->isSuperAdmin()) {
            $groupIds = $this->getChildrenGroupIds(false);

            $model = D('AuthGroupAccess');
            $authGroupList = $model->field('uid,group_id')
                ->where(['group_id' => ['in', $groupIds]])
                ->select();

            foreach ($authGroupList as $k => $v) {
                $childrenAdminIds[] = $v['uid'];
            }
        } else {
            //超级管理员拥有所有人的权限
            $model = D('Admin');
            $userInfo = $model->field('id')->select();
            $childrenAdminIds = array_column($userInfo, 'id');
        }

        if ($withself) {
            if (!in_array($this->id, $childrenAdminIds)) {
                $childrenAdminIds[] = $this->id;
            }
        } else {

            $childrenAdminIds = array_diff($childrenAdminIds, [$this->id]);
        }

        return $childrenAdminIds;
    }

    /**
     * 获得面包屑导航
     * @param string $path
     * @return array
     */
    public function getBreadCrumb($path = '')
    {
        $path_rule_id = 0;

        // 筛选条件
        $map = [
            'status' => 'normal',
        ];

        if (!in_array('*', $this->getRuleIds())) {
            $map['id'] = ['in', $this->getRuleIds() ?: '0'];
        }

        $this->rules = D("AuthRule")->where($map)->select();

        foreach ($this->rules as $rule) {
            $path_rule_id = $rule['node'] == strtolower($path) ? $rule['id'] : $path_rule_id;
        }

        if ($path_rule_id) {
            $this->breadcrumb = Tree::instance()->init($this->rules)->getParents($path_rule_id, true);

            foreach ($this->breadcrumb as $k => &$v) {
                $v['url'] = "/".strtolower(MODULE_NAME)."/".$v['node'];
                $v['title'] = $v['name'];
            }
            unset($v);
        }
        return $this->breadcrumb;
    }


    /**
     * 获取左侧菜单栏
     * @return string
     */
    public function getSidebar($fixedPage = '')
    {

        // 读取管理员当前拥有的权限节点
        $userRule = $this->getAuthList();
        $select_id = 0;


        //设定条件规则状态正常且属于菜单
        $where = [
            'status' => 'normal',
            'type' => 'menu',
        ];

        //获取全部角色数据
        $ruleData = D("AuthRule")->where($where)->order('weigh desc')->select();
        $pinyin = new \Org\Util\Pinyin;

        //循环 过滤不在权限范围内的菜单
        foreach ($ruleData as $k => &$v) {
            if (!in_array($v['node'], $userRule)) {
                unset($ruleData[$k]);
                continue;
            }

            $select_id = $v['node'] == $fixedPage ? $v['id'] : $select_id;
            $v['url'] = '/' . MODULE_NAME . '/' . $v['node'];
            $v['py'] = $pinyin->qupinyin($v['name']);


            $groupName = explode('/',strtolower(CONTROLLER_NAME));
            if ($groupName[0] == $v['node']) {
                $v['select'] = 'glyphicon-minus';
                $v['open'] = 'in';
            }else{
                $v['select'] = 'glyphicon-plus';
                $v['open'] = '';
            }
        }
        // 构造菜单数据
        Tree::instance()->init($ruleData);
        $menu = Tree::instance()->getTreeArray(0);
        return $menu;
    }

    /**
     * 获取当前请求的URI
     * @return string
     */
    public function getRequestUri()
    {
        return $this->requestUri;
    }

    /**
     * 设置当前请求的URI
     * @param string $uri
     */
    public function setRequestUri($uri)
    {
        $this->requestUri = $uri;
    }

    /**
     * 设置错误信息
     * @param $error  错误信息
     * @return $this
     */

    public function setError($error)
    {
        $this->_error = $error;
        return $this;
    }


    /**
     * 获取错误信息
     * @return string
     */
    public function getError()
    {
        return $this->_error ? $this->_error : '';
    }
}