<?php
/**
 * Created by PhpStorm.
 * User: 1010
 * Date: 2018/5/15 0015
 * Time: 下午 05:12
 */

namespace Common\Controller;

use Admin\Library\Auth;
use Admin\Model\AdminLogModel;
use Think\Controller;

class BaseController extends Controller
{
    //adminLog
    public $adminLogModel;

    //设定代理分组id
    public const AGENT_GROUP_ID = 18;

    //查询需要获取的参数
    public const SERACH_GET_PARAM = ['filter', 'option', 'order', 'sort', 'offset', 'limit'];

    //查询条件映射tp语句
    public const SQL_CONDITIONS_MAPPING = [
        '=' => 'eq',
        '!=' => 'neq',
        '>' => 'gt',
        '>=' => 'egt',
        '<' => 'lt',
        '<=' => 'elt',
        'NOT LIKE' => 'notlike',
        'LIKE' => 'like',
        'IN' => 'in',
        'NOT IN' => 'not in',
        'BETWEEN' => 'between',
        'NOT BETWEEN' => 'notbetween',
    ];

    //公共变量
    protected $commonParam = [];

    //字段配置信息
    //protected $FieldConfig = ['普通类型','vip1','vip2','vip3'];
    protected $FieldConfig = [];

    //不需要登录鉴权的方法
    private const NO_NEED_LOGIN = [
        '/admin/index/login',
        '/admin/index/logout',
        '/admin/index/fieldcheck',
        '/admin/index/verify',
        '/admin/index/login',
        '/admin/index/index',
        '/admin/script/script/statistics',
        '/admin/script/script/stock',
        '/admin/script/script/sendMobile',
        '/admin/script/script/rebate',
        '/admin/script/script/fund',
        '/admin/script/script/agent',
        '/admin/script/script/cordon',
    ];

    public function __construct()
    {
        parent::__construct();

        $this->adminLogModel = D('AdminLog');

        //过滤路径
        $path = str_replace('.', '/', CONTROLLER_NAME) . '/' . ACTION_NAME;

        //调用权限控制类
        $this->auth = new Auth();

        //设置当前请求的URI
        $this->auth->setRequestUri($path);

        // 检测是否需要验证登录
        if (!$this->auth->match(self::NO_NEED_LOGIN)) {

            //检测是否登录
            if (!$this->auth->isLogin()) {
                $this->success('用户还没有登录 请登录后进行后续操作', '/admin/index/login');
            }

            // 判断是否需要验证权限
            if (!$this->auth->match($this->noNeedRight)) {
                // 判断控制器和方法判断是否有对应权限
                if (!$this->auth->check($path)) {
                    //写入日志
                    AdminLogModel::setTitle('你没有权限访问');
                    D('AdminLog')->record([]);
                    //$controller = strtolower(CONTROLLER_NAME);
                    $this->error('你没有权限访问', '/admin/index/index',1);
                }
            }
        }

        //设置公共变量
        $this->commonParam = [
            'module' => strtolower(MODULE_NAME),
            'action' => strtolower(ACTION_NAME)
        ];

        //判断当前操作是否在分组中 如果在分组中 则需要拆分控制器名称
        if (strpos(CONTROLLER_NAME, '/') !== FALSE) {
           [$this->commonParam['group'], $this->commonParam['controller']] = explode('/', strtolower(CONTROLLER_NAME));
        } else {
            $this->commonParam['controller'] = strtolower(CONTROLLER_NAME);
        }

        $this->commonParam['all'] = strtolower(MODULE_NAME . "/" . CONTROLLER_NAME . "/" . ACTION_NAME);

        //获取字段配置信息
        $this->FieldConfig = get_config($this->commonParam['all']);

        //var_dump($this->FieldConfig);exit;
        $this->assign('FieldConfig', $this->FieldConfig);

        //javascript配置 所需要的配置信息
        $config = [
            //当前网址
            'self' => strtolower(__SELF__),
            //模块名称
            'module' => strtolower(MODULE_NAME),
            //控制器名称(包含分组名)
            'controller' => strtolower(CONTROLLER_NAME),
            //方法名称
            'action' => strtolower(ACTION_NAME),
            //模板操作配置
            'operation' => array_filter(build_toolbar(['release', 'detail', 'edit', 'disable', 'delete', 'back'], 2)),
            //当前管理员数据
            'admin' => [
                'id' => session('admin.id'),
                'username' => session('admin.username'),
                'uid' => session('admin.uid'),
                'avatar' => session('admin.avatar'),
            ]
        ];

        //面包屑数据
        $breadcrumb = $this->auth->getBreadCrumb($path);
        array_pop($breadcrumb);

        //配置信息
        $this->assign('config', json_encode($config));
        //当前网址
        $this->assign('selfUrl', __SELF__);
        //权限类
        $this->assign('auth', $this->auth);
        //渲染左侧菜单数据
        $this->assign('sidebar', $this->auth->getSidebar());
        //渲染面包屑对象
        $this->assign('breadcrumb', $breadcrumb);
        //渲染管理员对象
        $this->assign('admin', session('admin'));
    }


    /**
     * Author:lbb
     * 根据查询参数 返回相应查询条件
     * @param string /json filter 过滤参数
     * @param string /json option 过滤参数配置
     * @param string      order  排序方向
     * @param string      sort   排序字段
     * @param int         offset 数据开始位置
     * @param int         limit  数据展示数量
     * @return array
     */
    protected function getSerachParam() :array
    {
        //获取查询相关参数
        [$filter, $option, $order, $sort, $offset, $limit] = array_map(function ($item) {

            $result = I('get.' . $item, '');

            if ($item === 'order' && !I('get.' . $item, '')) {
                $result = 'asc';
            }

            if ($item === 'sort' && !I('get.' . $item, '')) {
                $result = 'id';
            }

            if ($item === 'offset' && !I('get.' . $item, '')) {
                $result = 0;
            }

            if ($item === 'limit' && !I('get.' . $item, '')) {
                $result = 50;
            }

            //解析json
            if ($item === 'filter' || $item === 'option') {
                if (I('get.' . $item, '')) {
                    $result = (array)I('get.' . $item, '', 'json_decode');
                } else {
                    $result = [];
                }
            }

            return $result;
        }, self::SERACH_GET_PARAM);

        //查询参数映射
        $mapping = self::SQL_CONDITIONS_MAPPING;

        $where = [];

        $i = 100;
        // 循环迭代过滤条件
        foreach ($filter as $key => $value) {

            //过滤参数配置值
            $optionVal = isset($option[$key]) ? strtoupper($option[$key]) : "=";

            //判断如果值不是数组则过滤空格
            $value = !is_array($value) ? trim($value) : $value;


            //根据过滤操作符 获取不同的where条件
            switch ($optionVal) {
                case '=':
                    $where[$key] = "$key='$value'";
                    break;
                case '!=':
                    $where[$key] = "$key!='$value'";
                    break;
                case 'LIKE':
                case 'NOT LIKE':
                case 'LIKE %...%':
                case 'NOT LIKE %...%':
                    $conditions = trim(str_replace('%...%', '', $optionVal));
                    $where[$key] = "$key $conditions '%" . $value . "%'";
                    break;

                case '>':
                case '>=':
                case '<':
                case '<=':


                    //替换开始与结束的标识
                    $key = str_replace(['-start', '-end'], ['', ''], $key);


                    //如果含有time字段则将值转化为时间戳
                    if (stripos(strtolower($key), 'time') !== FALSE) {
                        $value = strtotime($value);
                    }

                    if ($key === 'send_gold') {
                        $where[$i] = "$key $optionVal '{$value}'";
                        $i++;
                    }else{
                        $where[] = "$key $optionVal '{$value}'";
                    }

                    break;

                case 'FINDIN':
                case 'FIND_IN_SET':
                    $where[$key] = "FIND_IN_SET('{$value}', `{$key}`)";
                    break;

                case 'IN':
                case 'IN(...)':
                case 'NOT IN':
                case 'NOT IN(...)':

                    $conditions = trim(str_replace('%...%', '', $optionVal));

                    if (!is_array($value)) {
                        $value = explode(',', $value);
                    }

                    $where[$key] = "$key $conditions (" . implode(',', $value) . ')';
                    break;

                case 'BETWEEN':
                case 'NOT BETWEEN':

                    $array = array_slice(explode(',', $value), 0, 2);

                    if (strpos($value, ',') === false || !array_filter($array)){
                        break;
                    }

                    $where[] = [$key => [$mapping[$optionVal], $array]];
                    break;
                default:
                    break;
            }

        }
        return [$where, $sort, $order, $offset, $limit];
    }
}