<?php
/**
 * 用户日志模型
 * User: Administrator
 * Date: 2018/5/25
 * Time: 17:15
 */

namespace Admin\Model;


use Admin\Library\Random;
use Common\Model\BaseModel;

class UserLogModel extends BaseModel
{

    /**
     * 记录用户相关日志
     * @param array $userData
     * @param $type
     * @param array $disc
     */
    public function record($userData = [], $type, $disc = [])
    {
        $data = [];
        //封装用户日志
        foreach ($userData as $key => $value) {
            $data[] = [
                'uid' => $value['uid'],
                'type' => $type,
                'disc' => json_encode($disc),
                'phyAdress' => Random::uuid(),
                'addIp' => get_client_ip(),
                'createtime' => time()
            ];
        }

        $this->addAll($data);
    }
}