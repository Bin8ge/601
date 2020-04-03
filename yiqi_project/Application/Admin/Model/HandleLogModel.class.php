<?php
/**
 * 用户日志模型
 * User: Administrator
 * Date: 2018/5/25
 * Time: 17:15
 */

namespace Admin\Model;


use Common\Model\BaseModel;

class HandleLogModel extends BaseModel
{

    /**
     * 记录用户操作相关日志
     * @param array $userData
     * @param $type
     * @param array $disc
     */
    public function record($userData = [], $type, $disc = [])
    {

        //封装用户日志

        $data = [
            'uid'        => $userData['uid'],
            'title'      => $userData['title'],
            'type'       => $type,
            'disc'       => json_encode($disc),
            'admin_id'   => $userData['admin_id'],
            'remark'     => $userData['remark'] ?: '',
            'addIp'      => get_client_ip(),
            'createtime' => time(),
        ];
        $this->add($data);
    }
}