<?php
/**
 * Created by PhpStorm.
 * User: 1010
 * Date: 2018/8/9 0009
 * Time: 15:33
 */

namespace Admin\Controller\Player;


use Common\Controller\BaseController;

class GameLogController extends BaseController
{

    public function __construct()
    {
        parent::__construct();

    }

    public function index($uid=0)
    {
        if(IS_AJAX){
            $host='http://172.16.49.229/';
            $file=$host;

            if ( I('get.createTime') ){
                $file.=I('get.createTime').'/';
            }
            if ( I('get.uid') ){
                $uid=I('get.uid');
                $file.= ($uid%100).'/';
            }
            $file .= $uid . '.log';
            $str = file_get_contents($file);//将整个文件内容读入到一个字符串中
            $str_encoding = mb_convert_encoding($str, 'UTF-8', 'GBK');//转换字符集（编码）
            $arr = explode("\r\n", $str_encoding);//转换成数组

            if (count($arr)===0) {
                return returnAjax(400,'未找到该时间段的日志~~');
            }else{
                //得到后的数组
                $this->assign('content',$arr);
                $res['content'] = $this->fetch('Player/game_log/replace');
                return returnAjax(200,'SUCCESS',$res);
            }

        }

        $this->assign('uid',$uid ?: '');
        $this->display();
    }

    /**
     * 读取日志
     */
    private function readLogs($filePath,$num=20){
        $fp = fopen($filePath,"r");
        $pos = -2;
        $eof = '';
        $head = false;   //当总行数小于Num时，判断是否到第一行了
        $lines = array();
        while($num>0){
            while($eof !== "\n"){
                if(fseek($fp, $pos, SEEK_END)===0){    //fseek成功返回0，失败返回-1
                    $eof = fgetc($fp);
                    $pos--;
                }else{                               //当到达第一行，行首时，设置$pos失败
                    fseek($fp,0,SEEK_SET);
                    $head = true;                   //到达文件头部，开关打开
                    break;
                }

            }
            array_unshift($lines,fgets($fp));
            if($head){ break; }                 //这一句，只能放上一句后，因为到文件头后，把第一行读取出来再跳出整个循环
            $eof = '';
            $num--;
        }
        fclose($fp);
        return array_reverse($lines);
    }

}