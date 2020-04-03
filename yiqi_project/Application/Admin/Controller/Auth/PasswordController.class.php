<?php
/**
 * 修改密码
 * User: Lbb
 * Date: 2018/9/6 0006
 * Time: 15:44
 */

namespace Admin\Controller\Auth;

use Common\Controller\BaseController;
use Admin\Library\Random;

class PasswordController extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        //获取数据对象
        $this->adminModel = D('Admin');

    }

    public function index()
    {

        $id = $_SESSION['Admin_']['admin']['id'];

        if (IS_AJAX) {

            $password = I('post.password');           //密码
            $confirm_password = I('post.confirm_password');   //确认密码

            //验证密码长度
            if (strlen($password) < 3 || strlen($password) > 30 || strlen($confirm_password) < 3 || strlen($confirm_password) > 30) {
                return returnAjax('400', '密码长度应在3~30之间,请核实~~');
            }

            //验证两次密码是否相等
            if ($password != $confirm_password) return returnAjax('400', '两次密码不一致,请核实~~');

            //获得密码盐
            $saveData['salt'] = Random::alnum();

            //对密码进行加盐操作
            $saveData['password'] = md5(md5($password) . $saveData['salt']);

            $saveData['updatetime'] = time();

            $condition['id'] = $id;
            $status = $this->adminModel->where($condition)->save($saveData);

            if ($status === FALSE) {
                return returnAjax('400', '修改失败~~');
            } else {
                return returnAjax('200', 'SUCCESS');
            }
        }
        $name = $_SESSION['Admin_']['admin']['username'];
        $this->assign('name', $name);
        $this->display();


    }

}