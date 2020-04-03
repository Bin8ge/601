<?php

use Admin\Library\Notify;


/**
 * 后台公共文件
 * 主要定义后台公共函数库
 */

/**
 * 数组分页函数  核心函数  array_slice
 * 用此函数之前要先将数据库里面的所有数据按一定的顺序查询出来存入数组中
 * $count   每页多少条数据
 * $page   当前第几页
 * $array   查询出来的所有数组
 * order 0 - 不变     1- 反序
 */

function page_array($count, $page, $array, $order = 0)
{
    global $countpage; #定全局变量
    $page = (empty($page)) ? '1' : $page; #判断当前页面是否为空 如果为空就表示为第一页面
    $start = ($page - 1) * $count; #计算每次分页的开始位置
    if ($order === 1) {
        $array = array_reverse($array);
    }
    $totals = count($array);
    $countpage = ceil($totals / $count); #计算总页面数
    $pagedata = array();
    $pagedata = array_slice($array, $start, $count);
    return $pagedata;  #返回查询数据
}

/**
 * 获取全球唯一标识
 * @return string
 */
function uuid()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

/**
 * 移动端判断
 * Author:lbb
 * @return bool
 */
function isMobile()
{
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }
    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA'])) {
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array('nokia',
            'sony',
            'ericsson',
            'mot',
            'samsung',
            'htc',
            'sgh',
            'lg',
            'sharp',
            'sie-',
            'philips',
            'panasonic',
            'alcatel',
            'lenovo',
            'iphone',
            'ipod',
            'blackberry',
            'meizu',
            'android',
            'netfront',
            'symbian',
            'ucweb',
            'windowsce',
            'palm',
            'operamini',
            'operamobi',
            'openwave',
            'nexusone',
            'cldc',
            'midp',
            'wap',
            'mobile'
        );
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    // 协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}


/**
 * 根据当前操作 获取需要的配置信息
 * @param string $path
 * @return array
 */
function get_config($path = '')
{
    //系统配置数据对象
    $sys_conf_model = D('SysConf');

    //系统配置分组数据对象
    $sys_conf_group_model = D('SysGroup');

    //配置数据
    $config = [];

    //节点列表
    $node_list = explode('/', $path);

    //遍历节点
    foreach ($node_list as $key => $value) {
        //获取当前节点列表
        $now_node_list = array_slice($node_list, 0, $key + 1, true);

        //获取当前节点字符串
        $now_node_str = '/' . implode('/', $now_node_list);

        //检查分组是否存在
        if ($group = $sys_conf_group_model->where(['node' => $now_node_str])->find()) {
            //检查配置数据是否存在
            if ($data = $sys_conf_model->where(['groupId' => $group['id']])->select()) {
                foreach ($data as $k => $val) {
                    $config[$val['name']] = json_decode($val['value_scope'], true);
                }
            }
        }

    }

    return $config;
}

/**
 * 生成表格操作按钮栏
 * Author:lbb
 * @param array $buttons
 * @param int $type
 * @param array $attr
 * @return array|string
 */

function build_toolbar($buttons = [], $type = 1, $attr = [])
{
    //权限类
    $auth = new \Admin\Library\Auth();

    //控制器名称
    $controller = strtolower(CONTROLLER_NAME);

    //配置参数
    $buttons = $buttons ?: ['add', 'delete'];
    //$buttons = is_array($buttons) ? $buttons : explode(',', $buttons);

    //查找如果出现delete 则置换
    $index = array_search('del', $buttons,TRUE );
    if ($index !== FALSE) {
        $buttons[$index] = 'delete';
    }

    //获取按钮配置信息
    $btnAttr = array_merge( $attr,C('VIEW.list')['button']);

    $html = [];

    foreach ($buttons as $k => $v) {

        //权限验证地址
        $authUrl = "{$controller}/{$v}";

        //如果没有定义或者鉴权不正确则跳过
        if (!isset($btnAttr[$v]) || !$auth->check($authUrl)) {
            continue;
        }
        //获得面包屑数据
        $breadCrumb = $auth->getBreadCrumb($authUrl);
        //当前方法名称
        $text = end($breadCrumb)['name'];

        //完整网址
        $url = end($breadCrumb)['url'];

        //判断如果是表格内按钮栏则添加样式
        if ($type === 2) {
            $class = $btnAttr[$v]['class2'];
        } else {
            $class = $btnAttr[$v]['class1'];
        }
        $extend = [];
        //封装额外数据
        if ($type === 1) {
            $extend[] = "data-title='$text'";
            foreach ($btnAttr[$v]['data'] as $key => $value) {

                //判断如果表单id为空则自动补充
                if ($key === 'form-id' && !$value) {
                    $value = str_replace('/', '', $url);
                }

                //判断如果url为空则自动补充
                if ($key === 'url' && !$value) {
                    $value = $url;
                }

                //将数据插入数组中
                $extend[] = "data-$key" . '=' . "'$value'";
                //array_push($extend, "data-$key" . '=' . "'$value'");
            }

            //合并数据
            $extend = implode(' ', $extend);
        }

        //判断图标是否存在
        $icon = '';
        if (isset($btnAttr[$v]['icon'])) {
            if ($type === 1) {
                $icon = "<i class='{$btnAttr[$v]['icon']}'></i>&nbsp;";
            } else {
                $icon = $btnAttr[$v]['icon'];
            }
        }

        //将标签数据放入数组中
        if ($type === 1) {
            $html[] = "<a href='javascript:;' class='$class' $extend >$icon$text</a>";
        } else {
            $html[$v] = [
                'text' => $text,
                'class' => $class,
                'icon' => $icon,
                'url' => $url,
                'data' => $btnAttr[$v]['data'],
            ];
        }

    }

    if ($type === 1) {
        //var_dump($html);exit;
        return implode(' ', $html);
    } else {
        return $html;
    }




}

/**
 * 生成搜索栏数据
 * Author:lbb
 * @param array $merge
 * @param string $controller 控制器名称   可以不传 默认使用当前 如果分组 必须穿分组名称和控制器名称 例如 auth/admin
 * @param string $action     方法名称     可以不传 默认使用当前
 * @param array $outherData  其他标签数据  可以与配置中的数据进行合并
 * @return array|string
 */
function build_search_bar($merge = ['admin/player/point_control'], $controller = '', $action = '', $outherData = [])
{
    //控制器名称
    $controller = $controller ? strtolower($controller) : strtolower(CONTROLLER_NAME);

    //方法名称
    $action = $action ? strtolower($action) : strtolower(ACTION_NAME);

    //获取搜索配置
    $fieldConfig = C('VIEW.search');
	
    //获取标签配置
    $TagsConfig = C('VIEW.form_tags');
    //var_dump($fieldConfig);exit;
    //获取系统配置
    $fieldSystemConfig = get_config(strtolower(MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME));



    //如果控制器名称中含有分组名称则需要拆分分组数据
    if (strpos($controller, '/') !== FALSE) {

        //获取分组名称以及控制器名称
        [$group, $controller] = explode('/', $controller);
        //如果当前分组以及控制器中找不到配置则返回空
        if (!isset($fieldConfig[$group][$controller][$action])) {
            return '';
        }

        //合并配置以及传入的标签数组
        $fieldData = array_merge($fieldConfig[$group][$controller][$action], $outherData);



        //检查是否有需要合并的配置信息
        foreach ($merge as $key => $value) {
            $fieldSystemConfig = array_merge($fieldSystemConfig, get_config($value));
        }

        foreach ($_GET as $key=>$value)
        {
           $parameter[]=$key;
           $parameter[]=$value;
        }

        return array_map(function ($value) use ($fieldSystemConfig, $TagsConfig,$parameter) {
            //var_dump($_GET[key]);exit;
            $tags_extend = function ($data) {
                array_walk($data, function (&$value, $key) {
                    $value = "$key=\"$value\"";
                });
                return implode(' ', array_values($data));
            };

            $search_field = [
                'type' => $value['type'],
                'placeholder' => $value['placeholder'],
            ];

            $select_func = function ($fieldSystemConfig, $TagsConfig, $tags_extend, $value) {

                $configOptions = [];
                if (isset($fieldSystemConfig[$value['data-id']])) {
                    $configOptions = $fieldSystemConfig[$value['data-id']]??[];
                }

                $options =  $value['options']+$configOptions;


                unset($value['options']);

                array_walk($options, function (&$value, $key) {
                    $value = "<option value='$key'>$value</option>";
                });


                return sprintf($TagsConfig[$value['type']], $tags_extend($value), implode('', array_values($options)));
            };

            switch ($value['type']) {
                case 'text':
                    if( !empty($_GET) && $value['data-id']===$parameter[0] ){
                        $value['value']=$parameter[1];
                    }
                    $search_field['tag'] = sprintf($TagsConfig[$value['type']], $tags_extend($value));
                    break;
                case 'select':
                    $search_field['tag'] = $select_func($fieldSystemConfig, $TagsConfig, $tags_extend, $value);
                    break;
                case 'datetime':
                    foreach ($value['field'] as $fieldKey => $fieldValue) {
                        $search_field['tag'][] = sprintf($TagsConfig[$fieldValue['type']], $tags_extend($fieldValue));
                    }
                    break;
                case 'select_or_text':
                    foreach ($value['field'] as $fieldKey => $fieldValue) {
                        switch ($fieldValue['type']) {
                            case 'text':
                                $search_field['tag'][] = sprintf($TagsConfig[$fieldValue['type']], $tags_extend($fieldValue));
                                break;
                            case 'select':
                                $search_field['tag'][] = $select_func($fieldSystemConfig, $TagsConfig, $tags_extend, $fieldValue);
                                break;
                        }
                    }
                    break;
                case 'game_product_id':
                    $game = A('Player/User')->query_room();
                    unset($game[0]);
                    $value['options'] = $value['options'] + $game;

                    $search_field['tag'] = $select_func($fieldSystemConfig, $TagsConfig, $tags_extend, $value);
                    break;
            };

            return $search_field;
        }, $fieldData);
    }

}


/**
 * Author:lbb
 * @param $resultCode
 * @param $message
 * @param array $data
 */
function returnAjax($resultCode,$message,$data=array()){
    $result = array(
        'resultCode'=>$resultCode,
        'message'=>$message,
        'data'=>$data,
    );
    exit(json_encode($result));
}


/**
 * 通知服务器
 * Author:lbb
 * @param array $data
 * @param $action
 */
function send_server($data=[],$action)
{
    $object =new Notify();
    $object -> send_to_server($action, json_encode($data));
}


/**
 * Author:lbb
 * @param $head      Excel列名信息
 * @param $dataCsv   数据库数据
 * @param $filename  文件名称
 * @param int $limit 间隔行  每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
 */
function outCsv($head, $dataCsv, $filename, $limit = 100000)
{
    // 输出Excel文件头，可把user.csv换成你要的文件名
    header('Content-Type: application/csv');
    header("Content-Disposition: attachment;filename={$filename}.csv");

    // 打开PHP文件句柄，php://output 表示直接输出到浏览器
    $fp = fopen('php://output', 'w') or die("can't open php://output");

    // 输出Excel列名信息
    foreach ($head as $i => $v) {
        // CSV的Excel支持GBK编码，一定要转换，否则乱码
        $head[$i] = iconv('utf-8', 'gbk', $v);
    }

    // 将数据通过fputcsv写到文件句柄
    fputcsv($fp, $head);

    // 计数器
    $cnt = 0;

    // 逐行取出数据，不浪费内存
    $count = count($dataCsv);
    for ($t = 0; $t < $count; $t++) {

        $cnt++;
        if ($limit === $cnt) { //刷新一下输出buffer，防止由于数据过多造成问题
            ob_flush();
            flush();
            $cnt = 0;
        }
        $row = $dataCsv[$t];
        foreach ($row as $i => $v) {
            $row[$i] = iconv('utf-8', 'gbk', $v);
        }
        fputcsv($fp, $row);
    }
}


/**
 *    移动：134、135、136、137、138、139、150、151、157(TD)、158、159、187、188
 *    联通：130、131、132、152、155、156、185、186
 *    电信：133、153、180、189、（1349卫通）
 *   手机服务商函数 getPhoneType
 *@author by @tianxiao
 *@param  string  $phone   手机号字符串
 *@return  unsignedint   0中国移动，1中国联通  2中国电信  3未知
 **/
function getPhoneType($phone){
    $phone = trim($phone);
    $isChinaMobile = "/^134[0-8]\d{7}$|^(?:13[5-9]|147|15[0-27-9]|178|18[2-478])\d{8}$/"; //移动方面最新答复
    $isChinaUnion = "/^(?:13[0-2]|145|15[56]|176|18[56])\d{8}$/"; //向联通微博确认并未回复
    $isChinaTelcom = "/^(?:133|153|177|173|18[019])\d{8}$/"; //1349号段 电信方面没给出答复，视作不存在
    // $isOtherTelphone = "/^170([059])\\d{7}$/";//其他运营商
    $type = [
        0 => '中国移动',
        1 => '中国联通',
        2 => '中国电信',
        3 => '未知',
    ];
    if(preg_match($isChinaMobile, $phone)){
        return $type[0];
    }elseif(preg_match($isChinaUnion, $phone)){
        return $type[1];
    }elseif(preg_match($isChinaTelcom, $phone)){
        return $type[2];
    }else{
        return $type[3];
    }
}