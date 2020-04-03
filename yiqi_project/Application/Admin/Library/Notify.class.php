<?php
/**
 * Created by PhpStorm.
 * User: 1010
 * Date: 2018/7/09
 * Time: 15:28
 */

namespace Admin\Library;


class Notify
{
    //设定代理分组id
    // const SERVER_HOST = "http://192.168.0.106:8080";
    //const SERVER_HOST = "http://192.168.50.10:8080";
    const SERVER_HOST = 'http://47.99.78.80:8080';

    /**
     * 服务器发送消息
     * Author:lbb
     * @param $action
     * @param $send_data
     */
    public function send_to_server($action, $send_data) :void
    {
        $url=self::SERVER_HOST.''.$action;

        header('Content-type:application/json;charset=utf-8');

        $this->http_post_data($url, $send_data);
    }

    /**
     * Author:lbb
     * @param $url           请求地址
     * @param $data_string   请求参数(json)
     * @return array
     * $data = json_encode($param);
     * list($return_code, $return_content) = http_post_data($url, $data);//return_code是http状态码
     */
    private function http_post_data($url, $data_string) :array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($data_string))
        );
        ob_start();
        curl_exec($ch);
        $return_content = ob_get_contents();
        ob_end_clean();
        $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        return array($return_code, $return_content);
    }
}

