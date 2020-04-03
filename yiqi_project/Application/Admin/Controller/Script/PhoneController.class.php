<?php
/**
 * 手机验证码添加记录
 * User: Lbb
 * Date: 2018/8/10 0010
 * Time: 16:06
 */

namespace Admin\Controller\Script;


class PhoneController
{
    //手机验证码表
    private $phoneVerif;

    //公告表
    private $noticeModel;

    //配置表
    private $sysConfModel;

    public function __construct()
    {
        $this->phoneVerif = D('phone_verif');

        $this->noticeModel = D('Notice');

        $this->sysConfModel = D('sys_conf');
    }

    /**
     * 添加验证码记录
     * Author:lbb
     */
    public function add() :void
    {
        $data['phone']        = (int)I('get.phone');
        $data['verification'] = (string)I('get.verif');
        $data['type']         = I('get.type');
        $data['createtime']   = time();
        if ($data['phone']>0 && $data['verification']>0) {
            $this->phoneVerif->add($data);
        }
    }

    /**
     * 客户端推送公告
     * Author:lbb
     */
    public function getNotice() :void
    {
        $condition['startTime'] = array('elt', time());
        $condition['endTime'] = array('egt', time());
        $condition['status'] = array('neq', '2');
        $lang = I('get.lang', '1');  //语言 1中文 2英文
        $list = [];
        if ($lang === '1') {
            $list = $this->noticeModel->field('title,content')->where($condition)->select();
        } elseif ($lang === '2') {
            $list = $this->noticeModel->field('title_en as title,content_en as content')->where($condition)->select();
        } elseif ($lang === '10') {
            $list = $this->noticeModel->field('title,content')->where($condition)->select();
        }
        print_r(json_encode($list));
        exit;
    }


    public function send_url() :void
    {
        #停服  白名单 开关状态
        $switch = $this->getSwitch();

		if($_GET['type']==='2'){
			$list['url']='http://www.by8k.cn/download/';
			$list['address']='47.110.230.55';
			$list['port']='2001';
            $list['Area']='1';
			$list['updateUrl']='http://winner.zsxh.com.cn/down.html';
            $list['copyUrl']='http://47.97.155.158:80/download/';
			$list['switch']=$switch;   #0 开  1关
		}elseif ($_GET['type']==='10'){

            # 下载地址域名数组
            $urlArray = [
                /*'www.gdbdqn.cn',*/
                'www.xyxx.com.cn',
            ];

            #下载页面的域名轮询
            $urlDownArray = [
                'down.bjfjxh.cn',
              /*  'down.51hi.cn',*/
            ];

            # 轮询域名  如果有域名被封 自动切换下个域名
           /* foreach ($urlArray as $val){
                if ($this->getHttpCode($val)===200){
                    $list['url']='http://'.$val.'/download/';
                    break;
                }
                continue;
            }*/
            $list['url']='http://cdn.m56pay.cn/download/';
            $list['updateUrl']='http://down.bjfjxh.cn/down.html';

         /*   foreach ($urlDownArray as $val){
                if ($this->getHttpCode($val)===200){
                    $list['updateUrl']='http://'.$val.'/down.html';
                    break;
                }
                continue;
            }*/

            //$list['url']='http://47.97.155.158/download/';
          /*  $list['updateUrl']='http://down.aibeiok.cn/down.html';
            $list['url']='http://www.by8k.cn/download/';*/
            $list['address']='tcp.mintimes.cn';
            $list['port']='2001';
            $list['Area']='1';
            $list['copyUrl']='http://www.47.97.155.158:80/download/';

            $list['switch']=$switch;   #0 开  1关
        }elseif ($_GET['type']==='1000'){
            # 正式环境的参数

            # 做域名数组
            $urlArray = [
                'www.by8k.cn',
                'www.waodiy.com.cn',
                //'www.mintimes.cn',
            ];


            #下载页面的域名轮询
            $urlDownArray = [
                'down.aibeiok.cn',
                'down.ysshxd.cn',
                'down.zhoufangbing.cn',
            ];

            # 轮询域名  如果有域名被封 自动切换下个域名
            foreach ($urlArray as $val){
                if ($this->getHttpCode($val)===200){
                    $list['url']='http://'.$val.'/download/';
                    break;
                }
                continue;
            }

            foreach ($urlDownArray as $val){
                if ($this->getHttpCode($val)===200){
                    $list['updateUrl']='http://'.$val.'/down.html';
                    break;
                }
                continue;
            }

            $list['address']='tcp.mintimes.cn';
            $list['port']='2001';
            $list['Area']='1';
            $list['copyUrl']='http://www.47.97.155.158:80/download/';

            $list['switch']=$switch;   #0 开  1关
        }else{
			$list['url']='http://192.168.50.10/download/';
			$list['address']='192.168.50.10';
			$list['port']='2001';
            $list['Area']='1';
			$list['updateUrl']='http://down.aibeiok.cn/down.html';
            $list['copyUrl']='http://192.168.50.10/download/';
            $list['switch']=$switch;   #0 开  1关
		}
        print_r(json_encode($list)) ;
    }

    /**
     * 白名单开关  停服开关
     * Author:lbb
     * @return mixed
     */
    private function getSwitch()
    {
        $where['groupId'] = array('in', '1020,1051');
        $switch_list = $this->sysConfModel->where($where)->select();
        $list = [];
        foreach ($switch_list as $val) {
            $list[$val['groupId']] = $val;
        }
        if ($list['1020']['value'] === '1') {
            return $list['1051']['value'];
        } else {
            return $list['1020']['value'];
        }
    }

    /**
     * 检测域名是否被封
     * Author:lbb
     * @param $url
     * @return mixed
     */
    private function getHttpCode($url)
    {
        $ch = curl_init();
        $timeout = 3;
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $httpCode;
    }

}