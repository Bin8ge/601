<?php
/**
 * 新手卡控制器
 * User: lbb
 * Date: 2019/3/12
 * Time: 15:08
 */

namespace Admin\Controller\Game;


use Admin\Library\Random;
use Common\Controller\BaseController;


class AwardController extends BaseController
{
    //数据对象
    private $model;
    private $awardCode;

    //表单验证
    private $fromValidate = [
        'row[name]' => 'required;',
        'row[addtime]' => 'required;',
        'row[endtime]' => 'required;',
        'row[gold]' => 'required;integer(+0);',
        'row[awardNum]' => 'required;integer(+0);',
        'row[content]' => 'required;',
    ];

    //默认统计查询字段
   protected const STATISTICS_QUERY = [
        'total_count',
        'get_total_number',
        'release_total_gold_number',
    ];


    public function __construct()
    {
        parent::__construct();

        $this->model = D('Award');

        $this->awardCode = D('AwardCode');
    }

    public function index() :void
    {
        if (IS_AJAX) {


            //获取查询条件
            [$where, $sort, $order, $offset, $limit] = $this->getSerachParam();

            //判断筛选条件是否存在
            if ($where) {
                $where = implode(' and ', array_values($where));
            }

            //获取查询数量
            $count = $this->model->where($where)->count();


            //获取数据
            $data = $this->model->where($where)
                ->limit($offset, $limit)
                ->order([$sort => $order])
                ->select();

            foreach ($data as $key => &$value) {
                $value['platform'] = $this->FieldConfig['platform'][$value['platform']];
                $value['channel'] = $this->FieldConfig['channel'][$value['channel']];
                $adminData = D('Admin')->where(['id' => $value['admin_id']])->find();
                $value['admin_id'] = $adminData['username'];
                $data[$key]['awardNum'] = number_format($data[$key]['awardNum']);
                $data[$key]['getNum'] = $this->awardCode->where(['awardId' => $value['id'],'isget'=>1])->count();
            }
            //返回json数据给ajax
            $result = array('total' => $count, 'rows' => $data ?: []);
            $this->ajaxReturn($result, 'JSON');
        }

        //渲染模板
        $this->display();
    }


    /**
     * 添加新手卡
     * Author:lbb
     */
    public function add() : void
    {
        if (IS_AJAX) {
            if ($post = I('post.row', [], 'strip_tags')) {
                //验证数据
                $postCheck = $this->update_check($post);

                if (!$this->model->create($postCheck) || !$this->model->add($postCheck)) {
                    $this->error('礼包创建失败');
                }

                //写入后台日志
                $this->adminLogModel->record($_POST);

                $this->success('礼包创建成功');
            }else{
                $this->error('礼包创建失败');
            }
        }

        //表单验证配置
        $this->assign('fromValidate', json_encode($this->fromValidate));

        $this->display();
    }

    /**
     * 查询
     * Author:lbb
     */
    public function search() : void
    {
        if (IS_AJAX) {
            if ($post = I('post.row', [], 'strip_tags')) {
                $condition['code'] = trim($post['code']);
                $info = $this->awardCode->field('isget,uid')->where($condition)->find();
                if ($info['isget'] === '1'){
                    $this->error("已被-{$info['uid']}-领取");
                }else{
                    $this->error('未领取');
                }
            }
        }
        $this->assign('fromValidate', json_encode( ['row[name]' => 'required;']));

        $this->display();
    }

    /**
     * 编辑新手卡
     * Author:lbb
     * @param int $id
     */
    public function edit($id = 0) :void
    {
        if (!$id || !$data = $this->model->where(['id' => $id])->find()) {
            $this->error('id不存在');
        }

        if (IS_AJAX) {
            if ($post = I('post.row', [], 'strip_tags')) {

                //验证数据
                $post = $this->update_check($post);

                $post['id'] = $id;

                if (!$this->model->create($post)) {
                    $this->error('礼包更新失败');
                }

                $this->model->save($post);

                //写入后台日志
                $this->adminLogModel->record($_POST);

                $this->success('礼包更新成功');
            }else{
                $this->error('礼包更新失败');
            }
        }

        //表单验证配置
        $this->assign('fromValidate', json_encode($this->fromValidate));

        //解析时间
        $data['addtime'] = date('Y-m-d H:i:s', $data['addtime']);
        $data['endtime'] = date('Y-m-d H:i:s', $data['endtime']);

        //分别获取钻石数和金币数
         $data['gold'] =  $data['awardGold'];
        $this->assign('data', $data);
        $this->display('add');
    }

    /**
     * 数据验证
     * @param array $post 表单提交数据
     * @return array
     */
    private function update_check($post = []) :array
    {
        //判断金币是否小于0
        if (isset($post['gold']) and $post['gold'] < 0) {
            $post['awardGold']=$post['gold'];
            $this->error('礼包金币不允许小于0');
        }

        //生成数量是否小于0
        if ($post['awardNum'] < 0) {
            $this->error('生成数量不允许小于0');
        }

        if (!isset($post['is_repeat'])) {
            $post['is_repeat'] = 0;
        }
        //判断礼包内容是否存在
        if (!isset($post['content'])) {
            $this->error('礼包内容必须存在');
        }
        $post['awardGold'] = $post['gold'];
        $post['awardValue'] =  $post['gold'];
        unset( $post['gold']);
        $post['addtime'] = strtotime($post['addtime']);
        $post['endtime'] = strtotime($post['endtime']);
        $post['admin_id'] = $this->auth->id;
        $post['is_release'] = 0;
        return $post;
    }


    /**
     * 发布礼包数据
     * @param int $id
     */
    public function release($id = 0) :void
    {
        if (!$id || !$data = $this->model->where(['id' => $id])->find()) {
            $this->error('id不存在');
        }

        if ($data['is_release'] === '1') {
            $this->error('礼包已发布');
        }

        //封装礼包数据
        $code_data = [];
        for ($i = 0; $i < $data['awardNum']; $i++) {
            $code_data[] =[
                'awardId'    => $id,
                'name'       => $data['name'],
                'code'       => $data['is_repeat'].'-'.Random::uuid(),
                'isget'      => 0,
                'status'     => 1,
                'createtime' => time(),
                'endtime'    => $data['endtime'],
            ];
        }

        //批量生成礼包
        $this->awardCode->addAll($code_data);

        //更新发布状态
        $this->model->where(['id' => $id])->save(['is_release' => 1]);

        //写入后台日志
        $this->adminLogModel->record($_POST);

        $this->success('发布礼包成功');
    }

    /**
     * 禁用/启用礼包
     * @param int $ids
     */
    public function disable($ids = 0) :void
    {
        if (!$ids || !$data = $this->model->where(['id' => ['in', $ids]])->find()) {
            $this->error('id不存在');
        }

        if ($data['status'] === '1') {
            $status = 0;
        } else {
            $status = 1;
        }

        //禁用礼包
        $this->model->where(['id' => ['in', $ids]])->save(['status' => $status]);

        //判断礼包是否发布 如果发布则需要 禁用礼包下面所有未领取的礼包卡号
        if ($data['is_release'] === '1') {
            $this->awardCode->where(['awardId' => ['in', $ids], 'isget' => 0])->save(['status' => $status]);
        }

        //写入后台日志
        $this->adminLogModel->record($_POST);
        $this->success('礼包禁用成功');

    }

    /**
     * 礼包详情
     * @param int $id
     */
    public function detail($id = 0) :void
    {

        if (I('get.file') === 'csv')
        {
            $header   = array('新手卡');
            $dataCsv = $this->awardCode->where(['awardId' => $id])->field('code')->select();
            $filename = '新手卡'.date('Y-m-d');
            outCsv($header,$dataCsv, $filename); exit;
        }


        $data = [];
        if (!$id || !$data = $this->model->where(['id' => $id])->find()) {
            $this->error('id不存在');
        }

        if ($data['is_release'] === '0') {
            $this->error('礼包没有发布 不能查看');
        }

        if (IS_AJAX) {

            $this->searchFilter();

            //获取查询条件
            [$where, $sort, $order, $offset, $limit] = $this->getSerachParam();

            //判断筛选条件是否存在
            if ($where) {
                $where = implode(' and ', array_values($where));
            }

            //获取查询数量
            $count = $this->awardCode->where($where)->where(['awardId' => $id])->count();

            //获取数据
            $data = $this->awardCode->field('yq_award_code.*,yq_user.nickname')
                ->where($where)->where(['awardId' => $id])
                ->join('left join yq_user on yq_user.uid=yq_award_code.uid')
                ->limit($offset, $limit)
                ->order([$sort => $order])
                ->select();

            foreach ($data as $key => &$value) {
                $awradData = $this->model->where(['id' => $value['awardId']])->find();
                $value['gettime'] = $value['gettime'] ?: 0;
                $value['content'] = $awradData['content'];
            }

            //如果数据存在 则将统计信息放入
            if ($data) {
                $where_total['id'] = $id;
                $data[0]['statistics'] = $this->UserStatistics($where_total);
            }

            //返回json数据给ajax
            $result = array('total' => $count, 'rows' => $data ?: []);
            $this->ajaxReturn($result, 'JSON');
        }

        $this->assign('id',$id);
        //渲染模板
        $this->display();
    }


    /**
     * 搜索参数过滤
     */
    private function searchFilter() :void
    {
        if (I('get.filter')) {
            $result = (array)I('get.filter', '', 'json_decode');
            $option = (array)I('get.option', '', 'json_decode');
            if (isset($result['select_or_text_select'])) {

                if ($result['select_or_text_text']) {
                    $result[$result['select_or_text_select']] = $result['select_or_text_text'];
                    $option[$result['select_or_text_select']] = $option['select_or_text_text'];
                }
                unset($result['select_or_text_select'], $result['select_or_text_text'],$option['select_or_text_select'], $option['select_or_text_text']);
            }
            $_GET['filter'] = json_encode($result);
            $_GET['option'] = json_encode($option);
        }
    }


    /**
     * 获取用户相关统计信息
     * Author:lbb
     * @param array $where
     * @param array $query
     * @return array|bool
     */
    private function UserStatistics($where = [], $query = [])
    {

        //总计
        $total_count = function ($where) {
            return $this->awardCode->where(['awardId' => $where['id']])->count() ?: 0;
        };

        //已领取
        $get_total_number = function ($where) {
            return $this->awardCode->where(['isget' => 1, 'awardId' => $where['id']])->count() ?: 0;
        };

        //已发放金币数
        $release_total_gold_number = function ($where) {
            $data = $this->model->where($where)->find();
            $count = $this->awardCode->where(['isget' => 1, 'awardId' => $where['id']])->count() ?: 0;
            return $data['awardGold'] * $count;
        };


        //判断默认的查询参数是否存在
        if (empty($query)) {
            $query = self::STATISTICS_QUERY;
        }

        //判断请求的类型
        if (IS_AJAX) {
            $data = [];
            foreach ($query as $key => $func) {
                $data[$func] = $$func($where);
            }
            return $data;
        } else {
            foreach ($query as $key => $func) {
                $this->assign($func, $$func($where));
            }
            return true;
        }
    }


}