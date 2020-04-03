<?php

namespace Admin\Controller;

use Admin\Library\Auth;
use Admin\Model\AdminLogModel;
use Admin\Library\Validate;
use Think\Verify;

class IndexController extends \Common\Controller\BaseController
{

    private $auth = null;

    protected function _initialize()
    {
        $this->auth = new Auth();
    }

    /**
     * 后台登录
     */
    public function login()
    {
        //检查用户是否登录并且检查用户是同一台设备同一地点登录
        if ($this->auth->isLogin()) {
            $this->success('用户已登录 可直接跳转到首页', '/admin/player/user');
        }

        if (IS_POST) {
            $username = I('post.username');
            $password = I('post.password');

            $rule = [
                'username' => 'require|length:3,30',
                'password' => 'require|length:3,30',
            ];

            $data = [
                'username' => $username,
                'password' => $password,
            ];

            //检查登录信息正确性
            $validate = new Validate($rule, [], ['用户名不正确', '密码不正确']);
            $result = $validate->check($data);

            if (!$result) {
                $this->ajaxReturn(['status' => 0, 'content' => $validate->getError()]);
            }


            $verify = new Verify();
            if (!$verify->check(I('post.verify'))) {
                $this->ajaxReturn(['status' => 0, 'content' => "验证码不正确"]);
            }

            $result = $this->auth->login($username, $password, 86400);

            if ($result === true) {

                //写入日志
                AdminLogModel::setTitle('登录成功');
                D('AdminLog')->record($_POST);

                $this->ajaxReturn(['status' => 1, 'content' => '登录成功', 'url' => '/admin/player/user']);
            } else {

                //写入日志
                AdminLogModel::setTitle('登录失败');
                D('AdminLog')->record($data);
                $this->ajaxReturn(['status' => 0, 'content' => $this->auth->getError()]);
            }
        }

        $this->display();
    }

    /**
     * 注销登录
     */
    public function logout()
    {
        //写入日志
        AdminLogModel::setTitle('退出登录成功');
        D('AdminLog')->record([]);

        $this->auth->logout();
        $this->success('退出登录成功', '/Admin/index/login');
    }

    /**
     * 后台字段验证
     * @param string $controller 控制器名称
     * @param string $action 方法名称
     * @param int $type 1 新增 2 更新 3 全部
     */
    public function fieldCheck($controller = '', $action = '', $type = 1)
    {
        //判断当前状态是否是登录状态
        if (IS_AJAX && $this->auth->isLogin()) {

            //检查模块名称存不存在
            if (!$action || !$controller) {
                $this->error('模块名称不存在 不能够正常验证');
            }

            //检查表单数据存不存在
            if (!$post = I('post.' . $action, [], 'strip_tags')) {
                $this->error('表单数据不存在');
            }

            //根据模块名获取
            $nameSpace = "Admin\Validate\\" . ucfirst($controller) . "\\" . ucfirst($action) . 'Validate';
            $model = new $nameSpace();

            //检查并创建数据结构
            if (!$model->create($post, $type)) {
                $this->error($model->getError());
            }

            $this->success('验证成功');
        }
    }

    /**
     * 显示验证码图片
     */
    public function verify()
    {
        $config = [
            'fontSize' => 30,    // 验证码字体大小
            'length' => 4,          // 验证码位数
            'fontttf' => '5.ttf',
            'useCurve' => true,
            'useNoise' => false, // 关闭验证码杂点
        ];
        $verify = new Verify($config);
        $verify->entry();
    }

    public function index()
    {
        $this->display();
    }

}