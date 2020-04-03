<?php
/**
 * 游戏参数设置
 * User: lbb
 * Date: 2018/5/25
 * Time: 17:09
 */

namespace Admin\Controller\Auto;

use Common\Controller\BaseController;

class ParameterController extends BaseController
{

    private $gameTypeModel;


    /**
     * 初始化
     */
    public function __construct()
    {
        parent::__construct();

        $this->gameTypeModel = D('game_type');



    }

    /**
     * 游戏参数设置 查看
     * Author:lbb
     */

    public function index()
    {
        if (IS_AJAX) {

            $list=$this->gameTypeModel
                ->where('gameid>0')
                ->group('type')
                ->order('show_sort asc')
                ->select();

            $this->assign('list', $list);             //结果列表

            $data['content'] = $this->fetch('Auto/parameter/replace');

            return returnAjax(200, 'SUCCESS', $data);
        }

        //渲染模板
        $this->display();
    }


    /**
     * 游戏参数设置 保存
     * Author:lbb
     */
    public function edit()
    {
        if(IS_AJAX){
            $data = $_POST;

            foreach ($data as $k=>$val){
                if (!is_numeric($val)) {
                    return  returnAjax('400','非法参数~~');
                }
            }

            $id = $data['id'];
            $condition['type'] = $id;

            # 剔除type id
            unset($data['id']);
            $status = $this->gameTypeModel->where($condition)->save($data);

            //写入后台日志
            $this->adminLogModel->record($_POST);
            if ($status === FALSE) {
                return returnAjax('400','保存失败~~');
            }else{
                return returnAjax('200','SUCCESS');
            }
        }
    }

    /**
     * 添加房间
     * Author:lbb
     */
    public function add()
    {
        if (IS_AJAX) {
            $data = I('post.resource',[], 'strip_tags');
            //验证数据格式
            foreach ($data as $v) {
                if (empty($v)){
                    return  returnAjax('400','非法参数,请重新提交~~');
                    break;
                }
            }

            //新增数据
            $msg = $this->gameTypeModel->add($data);

            //写入后台日志
            $this->adminLogModel->record($data);

            if (!$msg){
                return  returnAjax('400','添加失败,请重新提交~~');
            }
            return  returnAjax('200','添加成功');

        }else{
            //表单验证配置
            $this->assign('fromValidate', json_encode([
                'resource[gameid]' => 'required;',
                'resource[game_name]' => 'required;',
                'resource[type]' => 'required;',
                'resource[type_name]' => 'required;',
                'resource[productid]' => 'required;',
                'resource[name]' => 'required;',
                'resource[NeedGold]' => 'required;',
            ]));
            $form_id = 'addform';
            $this->assign('form_id', $form_id);
        }
        $this->display();

    }






}