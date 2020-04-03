<?php
/**
 * 靓号管理
 * User: Lbb
 * Date: 2018/9/6 0006
 * Time: 15:44
 */

namespace Admin\Controller\Auth;

use Common\Controller\BaseController;



class LiangController extends BaseController
{

    private $accountModel;

    private $userModel;

    public function __construct()
    {
        parent::__construct();

        $this->accountModel = D('Account');

        $this->userModel = D('user');
    }


    public function index()
    {
        if (IS_AJAX) {

            $uid    = I('post.uid');     //用户ID
            $lian  = I('post.liang');   //靓号ID

            $where['uid'] = $uid;
            $status = $this->userModel->where($where)->find();
            if ($status) {
                M()->startTrans();
                $data['uid'] = $lian;
                $msgOne = $this->userModel->where($where)->save($data);
                $msgTwo = $this->accountModel->where($where)->save($data);
                if ( $msgOne && $msgTwo ) {
                    M()->commit();
                    //写入后台日志
                    $this->adminLogModel->record($_POST);
                    return returnAjax('200', 'SUCCESS');
                } else {
                    M()->rollback();
                    return returnAjax('400', '修改失败~~');
                }
            } else {
                return returnAjax('400', $uid . ' 该用户不存在~~');
            }
        }
        $this->display();
    }

}