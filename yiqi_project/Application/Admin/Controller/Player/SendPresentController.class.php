<?php
/**
 * 赠送管理控制器
 * User: 1010
 * Date: 2018/5/25
 * Time: 17:12
 */

namespace Admin\Controller\Player;


use Common\Controller\BaseController;

class SendPresentController extends BaseController
{

    private $sendTakeModel;

    private $userModel;

    private $accountModel;

    private $handleLogModel;


    /**
     * 初始化
     */
    public function __construct()
    {
        parent::__construct();

        $this->sendTakeModel = D('send_take');

        $this->userModel = D('user');

        $this->accountModel = D('account');

        $this->handleLogModel=D('handle_log');

    }


    public function index() :void
    {
        if (IS_AJAX) {

            $this->searchFilter();
            //获取今天00:00
            $todayStart = strtotime(date('Y-m-d' . ' 00:00:00'));
            //获取今天24:00
            $todayEnd = strtotime(date('Y-m-d' . ' 00:00:00').' +1 day');

            [$where, $sort, $order, $offset, $limit] = $this->getSerachParam();


            //默认当日时间
            if ( !isset($where[0]) && !isset($where[1]) ) {
                $where[] = 'createtime>='.$todayStart;
                $where[] = 'createtime<='.$todayEnd;
            }


            $uid_all = '';
            if ( isset($where['uids']) ) {
                $uidInfo  = substr($where['uids'],6,-1);
                $uid_all = ' and ( uid='.$uidInfo.' or '.'take_uid='.$uidInfo.')';
                unset($where['uids']);
            }

            $where['type']      = "type='send'";
            //$where['is_vip']    = "is_vip='0'";

            //判断筛选条件是否存在
            if ($where) {
                $where = implode(' and ', array_values($where));
                $where .= $uid_all;
            }


            //获取查询数量
            $count = $this->sendTakeModel
                ->where($where)
                ->count();


            //获取数据
            $data = $this->sendTakeModel
                ->where($where)
                ->limit($offset, $limit)
                ->order('id desc')
                ->select();


            //对特定字段进行处理
            foreach ($data as $key => $value) {
                $userSend = $this->userModel->where(['uid' => $value['uid']])->find();
                $userTake = $this->userModel->where(['uid' => $value['take_uid']])->find();

                $data[$key]['send_nickname'] = $userSend['nickname'];
                $data[$key]['take_nickname'] = $userTake['nickname'];

                $data[$key]['send_level'] = $this->FieldConfig['level'][$value['send_level']];
                $data[$key]['take_level'] = $this->FieldConfig['level'][$value['take_level']];

                $data[$key]['send_gold'] = number_format($value['send_gold']);
                $data[$key]['take_gold'] = number_format($value['send_gold']-$value['tax_gold']);
                $data[$key]['tax_gold'] = number_format($value['tax_gold']);

            }


            //如果数据存在 则将统计信息放入

    /*        //统计退回的交易税
            $where_back = $where.' and is_back=1';
            $total_back_tax = $this->sendTakeModel->where($where_back)->sum('tax_gold') ?: 0;

            $where .= ' and is_back=0';
            $total = $this->sendTakeModel
                ->field('sum(tax_gold) as total_tax,count(id) as total_num,sum(send_gold) as total_send,sum(send_gold)-sum(tax_gold) as total_take')
                ->where($where)
                ->select();
            $total = $total[0];
            $total['total_tax'] += $total_back_tax;

            //玩家Yu玩家之间的交易
            $where .= ' and send_level=0 and take_level=0';
            $total_people =  $this->sendTakeModel
                ->field('sum(send_gold) as total_send_people,sum(send_gold)-sum(tax_gold) as total_take_people')
                ->where($where)
                ->select();

            if (!$total_people) {
                $total_people = [
                    'total_send_people' => 0,
                    'total_take_people' => 0,
                ];
            }else{
                $total_people = $total_people[0];
            }

            if ($total){
                $total = array_merge($total_people,$total);
                //VIP yu VIP之间的交易
                $total['total_send_vip'] = $total['total_send']-$total_people['total_send_people'];
                $total['total_take_vip'] = $total['total_take']-$total_people['total_take_people'];
            }
            $data [0]['statistics']  = $total;*/

            //返回json数据给ajax
            $result = array('total' => $count, 'rows' => $data ?: []);
            $this->ajaxReturn($result, 'JSON');
        }

        //渲染模板
        $this->display();
    }

    /**
     * 退回
     * Author:lbb
     * @param int $id
     */
    public function send_back($id = 0)
    {



        $data = $this->sendTakeModel->field('take_uid,uid')->where(['id' => $id, 'is_back' => 0])->find();
        $takeInfo = $this->sendTakeModel->field('take_gold,send_id')->where(['send_id' => $id, 'is_back' => 0])->find();

        //判断交易记录是否存在
        if (!$data && !$takeInfo) {
            return returnAjax(400, '赠送ID没有找到或已退回~~');
        }

        M()->startTrans();
        //判断接收者的银行账户是否 够退回金额
        $userAccount = $this->accountModel->lock(true)->field('gold,bank')->where(['uid' => $data['uid']])->find();       //赠送者账户
        $takeAccount = $this->accountModel->lock(true)->field('gold,bank')->where(['uid' => $data['take_uid']])->find();  //接收者账户

        if ($takeAccount['bank'] - $takeInfo['take_gold'] < 0) {
            M()->rollback();
            return returnAjax(400, '接收者银行账户资金不足~~');
        }


        //修改交易表 标记为退回
        $saveData['is_back'] = 1;
        $saveData['updatetime'] = time();
        $saveData['admin_id'] = $this->auth->id;

        $sendMsg = $this->sendTakeModel->where(['id' => $id])->save($saveData);
        $takeMsg = $this->sendTakeModel->where(['send_id' => $id])->save($saveData);

        //修改两者的银行账户
        $back_data['bank'] = $userAccount['bank'] + $takeInfo['take_gold'];
        $back_data_take['bank'] = $takeAccount['bank'] - $takeInfo['take_gold'];


        $where_take['uid'] =  $data['take_uid'];
        $where_take['bank'] =  array('egt',$takeInfo['take_gold']);
       /* $where_take['bank'] =  array('ngt', $takeInfo['take_gold']);*/
        $accountMsg = $this->accountModel->where(['uid' => $data['uid']])->setInc('bank',$takeInfo['take_gold']);
        $accountTakeMsg = $this->accountModel->where($where_take)->setInc('bank',-$takeInfo['take_gold']);


        if (!$sendMsg || !$accountMsg || !$takeMsg || !$accountTakeMsg) {
            M()->rollback();
            return returnAjax(400, '退回失败~~');
        }

        //添加赠送者金币变化记录
        $gold_data['UserID'] = $data['uid'];
        $gold_data['first_type'] = 2;
        $gold_data['second_type'] = 25;
        $gold_data['gold'] = $takeInfo['take_gold'];
        $gold_data['surplus_gold'] = $userAccount['gold'];
        $gold_data['surplus_bank'] = $userAccount['bank'] + $takeInfo['take_gold'];
        $gold_data['createtime'] = time();

        //添加接收者金币变化记录
        M((string)($data['uid']), 'yq_', 'DB_GAME_USER')->add($gold_data);
        $take_data['UserID'] = $data['take_uid'];
        $take_data['gold'] = -$takeInfo['take_gold'];
        $take_data['surplus_gold'] = $takeAccount['gold'];
        $take_data['surplus_bank'] = $takeAccount['bank'] - $takeInfo['take_gold'];
        $take_data['first_type'] = 2;
        $take_data['second_type'] = 25;
        $take_data['createtime'] = time();
        M((string)($data['take_uid']), 'yq_', 'DB_GAME_USER')->add($take_data);


        //写入赠送者用户日志
        $user_data = [
            'uid' => $data['uid'],
            'title' => '银行退回',
            'admin_id' => $this->auth->id
        ];

        $this->handleLogModel->record($user_data, 'is_back', [
            'handleNum' => $takeInfo['take_gold'],
            'bank' => $gold_data['surplus_bank'],
        ]);


        //写入接收者用户日志
        $user_take_data = [
            'uid' => $data['take_uid'],
            'title' => '银行退回',
            'admin_id' => $this->auth->id
        ];

        $this->handleLogModel->record($user_take_data, 'is_back', [
            'handleNum' => -$takeInfo['take_gold'],
            'bank' => $take_data['surplus_bank'],
        ]);


        //写入管理员日志
        $post = [
            'uid' => $data['uid'],
            'take_uid' => $data['take_uid'],
            'handleNum' => $takeInfo['take_gold'],
            'send_bank' => $gold_data['surplus_bank'],
            'take_bank' => $take_data['surplus_bank'],
        ];
        $this->adminLogModel->record($post);

        M()->commit();
        return returnAjax(200, '退回成功~~');

    }


    /**
     * 搜索参数过滤
     * Author:lbb
     */

    private function searchFilter(): void
    {
        if (I('get.filter') || I('get.uuid')) {
            $result = (array)I('get.filter', '', 'json_decode');
            $option = (array)I('get.option', '', 'json_decode');
            if (isset($result['select_or_text_select'])) {

                if ($result['select_or_text_text']) {
                    $result[$result['select_or_text_select']] = $result['select_or_text_text'];
                    $option[$result['select_or_text_select']] = $option['select_or_text_text'];
                }
                unset($result['select_or_text_select'], $result['select_or_text_text'], $option['select_or_text_select'], $option['select_or_text_text']);
            }


            //普通用户 还是vip
              if (isset($result['levels']) ) {
                    if ($result['levels']=== '0') {
                        $result['send_level'] = 0;
                        $option['send_level'] = '=';
                    }else{
                        $result['send_level'] = '1,2,3';
                        $option['send_level'] = 'in';
                    }
                    unset($result['levels'],$option['levels']);
                }

            if (isset($result['levelss']) ) {
                if ($result['levelss']=== '0') {
                    $result['take_level'] = 0;
                    $option['take_level'] = '=';
                }else{
                    $result['take_level'] = '1,2,3';
                    $option['take_level'] = 'in';
                }
                unset($result['levelss'],$option['levelss']);
            }

            if (I('get.uuid') > 0) {
                $result['uids'] = I('get.uuid');
                $option['uids'] = '=';
            }


            $_GET['filter'] = json_encode($result);
            $_GET['option'] = json_encode($option);
        }
    }






}