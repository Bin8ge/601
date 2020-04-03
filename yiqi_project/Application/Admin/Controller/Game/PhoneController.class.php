<?php
/**
 * 手机验证码
 * User: Lbb
 * Date: 2018/8/10 0010
 * Time: 10:44
 */

namespace Admin\Controller\Game;

use Common\Controller\BaseController;

class PhoneController extends BaseController
{
    //手机验证码表
    private $phoneVerification;


    public function __construct()
    {
        parent::__construct();
        $this->phoneVerification = D('phone_verif');
    }

    /**
     * 手机验证码 查询
     * Author:lbb
     */
    public function index()
    {
        if (IS_AJAX) {
            $phone = I('post.phone');
            $condition['phone'] = $phone;
            $condition['type']  =I('post.type');
            $status = $this->phoneVerification->field('verification')->where($condition)->order('id desc')->find();
            if ($status){
                return returnAjax('200','SUCCESS',$status['verification']);
            }else{
                return returnAjax('400','暂无记录~~');
            }
        }
        $type = [1=>'登录验证码',2=>'保险箱验证码',3=>'大转盘验证码'];
        $this->assign('type',$type);
        $this->display();
    }



}