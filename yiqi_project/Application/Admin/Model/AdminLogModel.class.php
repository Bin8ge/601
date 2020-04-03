<?php
/**
 * 管理员日志模型
 * User: 1010
 * Date: 2018/5/21 0021
 * Time: 下午 05:46
 */

namespace Admin\Model;


use Admin\Library\Auth;
use Think\Model;

class AdminLogModel extends Model
{
    //自动完成
    protected $_auto = [
        ['createtime', 'time', 1, 'function'],
    ];

    //自定义日志标题
    protected static $title = '';

    //自定义日志内容
    protected static $content = '';

    /**
     * 设置日志标题
     * @param $title
     */
    public static function setTitle($title)
    {
        self::$title = $title;
    }

    /**
     * 设置日志内容
     * @param $content
     */
    public static function setContent($content)
    {
        self::$content = $content;
    }

    /**
     * 写入日志数据
     * @param string $title
     */
    public function record($post = '')
    {
        $auth = new Auth();

        //获取用户id和用户名称
        $admin_id = $auth->isLogin() ? $auth->id : 0;
        $username = $auth->isLogin() ? $auth->username : "";

        //过滤content
        $content = self::$content;
        if (!$content) {
            $content = $post;
            foreach ($content as $k => $v) {
                if (is_string($v) && strlen($v) > 200 || stripos($k, 'password') !== false) {
                    unset($content[$k]);
                }
            }
        }

        //设置title信息
        $title = self::$title;
        if (!$title) {
            $title = [];
            $path = str_replace('.', '/', CONTROLLER_NAME) . '/' . ACTION_NAME;
            $breadcrumb = $auth->getBreadcrumb($path);
            foreach ($breadcrumb as $k => $v) {
                $title[] = $v['title'];
            }
            $title = implode(' ', $title);
        }

        //封装写入数组
        $data = [
            'title' => $title,
            'content' => !is_scalar($content) ? json_encode($content) : $content,
            'url' => __SELF__,
            'admin_id' => $admin_id,
            'username' => $username,
            'useragent' => $_SERVER['HTTP_USER_AGENT'],
            'ip' => get_client_ip()
        ];


        //写入日志数据
        if ($this->create($data, Model::MODEL_INSERT)) {
            $this->add();
        }
        session('record', $this->getLastSql());
    }
}