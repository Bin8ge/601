<?php
/**
 * 用户日志模型
 * User: Administrator
 * Date: 2018/5/25
 * Time: 17:15
 */

namespace Admin\Model;


use Common\Model\BaseModel;

class TeamMemberModel extends BaseModel
{

    /**
     * 添加修改VIP
     * @param array
     * @param $type
     * @param array $disc
     */
    public function record($userData = [])
    {
        $data = [];
        $res=M('team_member')->where(array('uid'=>$userData['uid']))->find();
        if($res){
            $data['teamId'] = $userData['teamId'];
            $data['level']  = $userData['level'];
            $data['isdel']  = $userData['isdel'];
            $data['createtime']  = time();
            $this->where(array('uid'=>$userData['uid']))->save($data);

        }else{
            $this->add($userData);
        }
    }

}