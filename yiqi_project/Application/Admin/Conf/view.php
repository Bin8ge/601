<?php
/**
 * 模板页配置信息
 * User: 1010
 * Date: 2018/5/23
 * Time: 16:24
 */

return [
    //模板列表配置
    'list' => [
        //操作按钮配置
        'button' => [
            //添加
            'add' => [
                'class1' => 'btn btn-custom btn-add',
                'icon' => 'fa fa-plus',
                'data' => [
                    'form-id' => '',
                    'url' => '',
                    'width' => '800px',
                    'height' => '600px',
                ]
            ],

            //检索
            'search' => [
                'class1' => 'btn btn-custom btn-search',
                'data' => [
                    'form-id' => '',
                    'url' => '',
                    'width' => '800px',
                    'height' => '600px',
                ]
            ],

            //编辑
            'edit' => [
                'class2' => 'btn btn-xs btn-edit marg-left btn-editone  btn-edit',
                'data' => [
                    'form-id' => '',
                    'url' => '',
                    'width' => '800px',
                    'height' => '600px',
                ]
            ],

            //编辑
            'see' => [
                'class2' => 'btn btn-xs btn-info btn-dlone marg-left btn-see',
                'data' => [
                    'form-id' => '',
                    'url' => '',
                    'width' => '800px',
                    'height' => '90%',
                ]
            ],

            //详情
            'detail' => [
                'class2' => 'btn btn-xs btn-info btn-dlone marg-left btn-detail',
                'data' => [
                    'form-id' => '',
                    'url' => '',
                    'width' => '800px',
                    'height' => '600px',
                ]
            ],

            //删除
            'delete' => [
                'class1' => 'btn btn-del-lg btn-del',
                'class2' => 'btn btn-xs btn-del-xs marg-left btn-dlone btn-del',
                'icon' => 'fa fa-trash-o',
                'data' => [
                    'url' => '',
                ]
            ],

            //禁用
            'disable' => [
                'class1' => 'btn btn-del-lg btn-disable',
                'class2' => 'btn btn-xs btn-del-xs marg-left btn-dlone btn-disable',
                'icon' => 'fa fa-trash-o',
                'data' => [
                    'url' => '',
                ]
            ],

            //发布
            'release' => [
                'class2' => 'btn btn-xs btn-edit marg-left btn-editone  btn-release',
                'data' => [
                    'form-id' => '',
                    'url' => '',
                    'width' => '800px',
                    'height' => '600px',
                ]
            ],


            //退回
            'back' => [
                'class2' => 'btn btn-xs btn-del-xs marg-left btn-dlone btn-back',
                'data' => [
                    'url' => '',
                ]
            ],

            //锁定
            'lock' => [
                'class2' => 'green-a btn-lock',
                'data' => [
                    'form-id' => '',
                    'url' => '',
                    'width' => '800px',
                    'height' => '600px',
                ]
            ],

            //解绑手机
            'unbind_mobile' => [
                'class2' => 'green-a btn-unbind_mobile',
                'data' => [
                    'url' => '',
                ]
            ],
            //强踢
            'kick' => [
                'class2' => 'green-a btn-kick',
                'data' => [
                    'url' => '',
                ]
            ],

            #银行
            'account' => [
                'class2' => 'green-a btn-account',
                'data' => [
                    'url' => '',
                ]
            ],

            #转账
            'bank' => [
                'class2' => 'green-a btn-bank',
                'data' => [
                    'form-id' => '',
                    'url' => '',
                    'width' => '800px',
                    'height' => '600px',
                ]
            ],

            //编辑资源
            'edit_resource' => [
                'class2' => 'green-a btn-edit_resource',
                'data' => [
                    'form-id' => '',
                    'url' => '',
                    'width' => '800px',
                    'height' => '600px',
                ]
            ],
			//改绑手机
            'change_mobile' => [
                'class2' => 'green-a btn-change_mobile',
                'data' => [
                    'form-id' => 'changemobilefrom',
                    'url' => '',
                    'width' => '800px',
                    'height' => '600px',
                ]
            ],
            //绑定手机
            'bind_mobile' => [
                'class2' => 'green-a btn-bind_mobile',
                'data' => [
                    'form-id' => 'bindemobilefrom',
                    'url' => '',
                    'width' => '800px',
                    'height' => '600px',
                ]
            ],
			//取消点控
            'cancel_point' => [
                'class2' => 'green-a btn-cancel_point',
                'data' => [
                    'form-id' => 'cancelfrom',
                    'url' => '',
                    'width' => '800px',
                    'height' => '600px',
                ]
            ],

            //添加
            'csv' => [
                'class1' => 'btn btn-custom btn-add',
                'icon' => '',
                'data' => [
                    'form-id' => '',
                    'url' => '',
                    'width' => '800px',
                    'height' => '600px',
                ]
            ],

            //改绑手机
            'safe_mobile' => [
                'class2' => 'green-a btn-change_mobile',
                'data' => [
                    'form-id' => '',
                    'url' => '',
                    'width' => '800px',
                    'height' => '600px',
                ]
            ],

            //编辑
            'bang' => [
                'class2' => 'btn btn-xs btn-del-xs marg-left btn-bang',
                'data' => [
                    'form-id' => '',
                    'url' => '',
                    'width' => '800px',
                    'height' => '600px',
                ]
            ],

            'csv' => [
                'class1' => 'btn btn-custom btn-csv',
                'icon' => 'fa fa-plus',
                'data' => [
                    'url' => '',
                    'width' => '800px',
                    'height' => '600px',
                ]
            ],




        ]
    ],

    //列表搜索配置
    'search' => [
        //权限管理
        'auth' => [
            //管理员管理
            'admin' => [
                //列表查询
                'index' => [
                    'id' => [
                        'type' => 'text',
                        'class' => 'form-control form-control-extend search-field keydown',
                        'data-id' => 'id',
                        'placeholder' => 'ID',
                        'serach-option' => '=',
                    ],

                    'username' => [
                        'type' => 'text',
                        'class' => 'form-control form-control-extend search-field keydown',
                        'data-id' => 'username',
                        'placeholder' => '用户名',
                        'serach-option' => 'LIKE',
                    ],

                    'email' => [
                        'type' => 'text',
                        'class' => 'form-control form-control-extend search-field keydown',
                        'data-id' => 'email',
                        'placeholder' => 'Email',
                        'serach-option' => 'LIKE',
                    ],

                    'status' => [
                        'type' => 'select',
                        'class' => 'form-control form-control-extend search-field',
                        'data-id' => 'status',
                        'serach-option' => '=',
                        'placeholder' => '状态',
                        'options' => [
                            '' => '全部'
                        ]
                    ],

                    'logintime' => [
                        'type' => 'datetime',
                        'placeholder' => '最后登录',
                        'field' => [
                            'logintime-start' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field Wdate',
                                'id' => 'BeginDay',
                                'data-id' => 'logintime-start',
                                'placeholder' => '开始时间',
                                'serach-option' => '>=',
                                'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd HH:00:00',startDate:'%y-%M-%d 00:00:00',maxDate:'#F{\$dp.\$D(\'EndDay\')||\'%y-%M-{%d}\'}',readOnly:true})"
                            ],

                            'logintime-end' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field Wdate',
                                'id' => 'EndDay',
                                'data-id' => 'logintime-end',
                                'placeholder' => '结束时间',
                                'serach-option' => '<=',
                                'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd HH:00:00',startDate:'%y-%M-%d 00:00:00' ,minDate:'#F{\$dp.\$D(\'BeginDay\')}',maxDate:'%y-%M-{%d}',readOnly:true})"
                            ],
                        ]
                    ],
                ]
            ],
            'admin_log' => [
                //列表查询
                'index' => [
                    'id' => [
                        'type' => 'text',
                        'class' => 'form-control form-control-extend search-field keydown',
                        'data-id' => 'username',
                        'placeholder' => '用户名',
                        'serach-option' => 'LIKE',
                    ],

                    'username' => [
                        'type' => 'text',
                        'class' => 'form-control form-control-extend search-field keydown',
                        'data-id' => 'title',
                        'placeholder' => '标题',
                        'serach-option' => 'LIKE',
                    ],

                    'email' => [
                        'type' => 'text',
                        'class' => 'form-control form-control-extend search-field keydown',
                        'data-id' => 'ip',
                        'placeholder' => 'IP',
                        'serach-option' => '=',
                    ],

                    'createtime' => [
                        'type' => 'datetime',
                        'placeholder' => '创建时间',
                        'field' => [
                            'createtime-start' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field Wdate',
                                'id' => 'BeginDay',
                                'data-id' => 'createtime-start',
                                'placeholder' => '开始时间',
                                'serach-option' => '>=',
                                'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd HH:00:00',startDate:'%y-%M-%d 00:00:00' ,maxDate:'#F{\$dp.\$D(\'EndDay\')||\'%y-%M-{%d}\'}',readOnly:true})"
                            ],

                            'createtime-end' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field Wdate',
                                'id' => 'EndDay',
                                'data-id' => 'createtime-end',
                                'placeholder' => '结束时间',
                                'serach-option' => '<=',
                                'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd HH:00:00',startDate:'%y-%M-%d 00:00:00' ,minDate:'#F{\$dp.\$D(\'BeginDay\')}',maxDate:'%y-%M-{%d}',readOnly:true})"
                            ],
                        ]
                    ],
                ]
            ],
            'rec_log' => [
                //列表查询
                'index' => [
                    'uid' => [
                        'type' => 'text',
                        'class' => 'form-control form-control-extend search-field keydown',
                        'data-id' => 'uid',
                        'placeholder' => '用户ID',
                        'serach-option' => '=',
                    ],

                    'admin_id' => [
                        'type' => 'text',
                        'class' => 'form-control form-control-extend search-field keydown',
                        'data-id' => 'admin_name',
                        'placeholder' => '管理员',
                        'serach-option' => 'LIKE',
                    ],


                    'createtime' => [
                        'type' => 'datetime',
                        'placeholder' => '时间',
                        'field' => [
                            'createtime-start' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field Wdate',
                                'id' => 'BeginDay',
                                'data-id' => 'createtime-start',
                                'placeholder' => '开始时间',
                                'serach-option' => '>=',
                                'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd 00:00:00',startDate:'%y-%M-%d' ,maxDate:'#F{\$dp.\$D(\'EndDay\')||\'%y-%M-{%d}\'}',readOnly:true})"
                            ],

                            'createtime-end' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field Wdate',
                                'id' => 'EndDay',
                                'data-id' => 'createtime-end',
                                'placeholder' => '结束时间',
                                'serach-option' => '<=',
                                'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd 23:59:59',startDate:'%y-%M-%d' ,minDate:'#F{\$dp.\$D(\'BeginDay\')}',maxDate:'%y-%M-{%d}',readOnly:true})"
                            ],
                        ]
                    ],
                ]
            ]
        ],

        //用户管理
        'player' => [
            //全部用户
            'user' => [
                //查看
                'index' => [

                    'id_or_nickname' => [
                        'type' => 'select_or_text',
                        'placeholder' => '用户ID或者用户昵称',
                        'field' => [
                            'select_or_text_select' => [
                                'type' => 'select',
                                'class' => 'form-control form-control-extend search-field',
                                'data-id' => 'select_or_text_select',
                                'serach-option' => '=',
                                'placeholder' => '状态',
                                'options' => [
                                    'uid' => '用户ID',
                                    'nickname' => '用户昵称',
                                ]
                            ],

                            'select_or_text_text' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field keydown',
                                'data-id' => 'select_or_text_text',
                                'placeholder' => '用户ID或者用户昵称',
                                'serach-option' => 'LIKE',
                            ],
                        ]
                    ],

                    'level' => [
                        'type' => 'select',
                        'class' => 'form-control form-control-extend search-field',
                        'data-id' => 'level',
                        'serach-option' => '=',
                        'placeholder' => '用户类型',
                        'options' => [
                            '' => '全部'
                        ]
                    ],

                    'ponit_control_type' => [
                        'type' => 'select',
                        'class' => 'form-control form-control-extend search-field',
                        'data-id' => 'point_control_type',
                        'serach-option' => '=',
                        'placeholder' => '点控类型',
                        'options' => [
                            '' => '全部'
                        ]
                    ],

                    'ponit_control_status' => [
                        'type' => 'select',
                        'class' => 'form-control form-control-extend search-field',
                        'data-id' => 'point_control_status',
                        'serach-option' => '=',
                        'placeholder' => '点控状态',
                        'options' => [
                            '' => '全部'
                        ]
                    ],

                    'createtime' => [
                        'type' => 'datetime',
                        'placeholder' => '注册时间',
                        'field' => [
                            'createtime-start' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field Wdate',
                                'id' => 'BeginDay',
                                'data-id' => 'createtime-start',
                                'placeholder' => '开始时间',
                                'serach-option' => '>=',
                                'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd HH:00:00',startDate:'%y-%M-%d 00:00:00' ,maxDate:'#F{\$dp.\$D(\'EndDay\')||\'%y-%M-{%d}\'}',readOnly:true})"
                            ],

                            'createtime-end' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field Wdate',
                                'id' => 'EndDay',
                                'data-id' => 'createtime-end',
                                'placeholder' => '结束时间',
                                'serach-option' => '<=',
                                'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd HH:00:00',startDate:'%y-%M-%d 00:00:00' ,minDate:'#F{\$dp.\$D(\'BeginDay\')}',maxDate:'%y-%M-{%d}',readOnly:true})"
                            ],
                        ]
                    ],
                ],
                //搜索用户
                'search' => [
                    //在线状态
                    'is_online' => [
                        'type' => 'select',
                        'class' => 'form-control form-control-extend search-field',
                        'data-id' => 'is_online',
                        'serach-option' => '=',
                        'placeholder' => '在线状态',
                        'options' => [
                            '' => '全部',
                            0=>'离线',
                            1=>'在线',
                        ]
                    ],
                    //用户id
                    'uid' => [
                        'type' => 'text',
                        'class' => 'form-control form-control-extend search-field keydown',
                        'data-id' => 'uid',
                        'serach-option' => '=',
                        'placeholder' => '玩家id'
                    ],
                    //用户昵称
                    'nickname' => [
                        'type' => 'text',
                        'class' => 'form-control form-control-extend search-field keydown',
                        'data-id' => 'nickname',
                        'serach-option' => 'LIKE',
                        'placeholder' => '玩家昵称'
                    ],
                    //绑定手机号
                    'mobile' => [
                        'type' => 'text',
                        'class' => 'form-control form-control-extend search-field keydown',
                        'data-id' => 'mobile',
                        'serach-option' => '=',
                        'placeholder' => '绑定手机号'
                    ],
                    //用户类型
                    'levels' => [
                        'type' => 'select',
                        'class' => 'form-control form-control-extend search-field',
                        'data-id' => 'levels',
                        'serach-option' => '=',
                        'placeholder' => '用户类型',
                        'options' => [
                            '' => '全部',
                            '0' => '普通用户',
                            '1' => 'VIP用户',
                        ]
                    ],
                    #账户状态
                    'is_closure' => [
                        'type' => 'select',
                        'class' => 'form-control form-control-extend search-field',
                        'data-id' => 'is_closure',
                        'serach-option' => '=',
                        'placeholder' => '账户状态',
                        'options' => [
                            '' => '全部',
                            '0' => '封停账户',
                            '1' => '正常账户',
                        ]
                    ],

                    //赠送状态
                    'is_send_presend' => [
                        'type' => 'select',
                        'class' => 'form-control form-control-extend search-field',
                        'data-id' => 'is_send_presend',
                        'serach-option' => '=',
                        'placeholder' => '赠送状态',
                        'options' => [
                            '' => '全部',
                            '0' => '禁止赠送',
                            '1' => '允许赠送',
                        ]
                    ],
                    //点控类型
                    'ponit_control_type' => [
                        'type' => 'select',
                        'class' => 'form-control form-control-extend search-field',
                        'data-id' => 'point_control_type',
                        'serach-option' => '=',
                        'placeholder' => '点控类型',
                        'options' => [
                            '' => '全部'
                        ]
                    ],
                    //点控状态
                    'ponit_control_status' => [
                        'type' => 'select',
                        'class' => 'form-control form-control-extend search-field',
                        'data-id' => 'point_control_status',
                        'serach-option' => '=',
                        'placeholder' => '点控状态',
                        'options' => [
                            '' => '全部'
                        ]
                    ],
                    #推广模式
                    'fund_type' => [
                        'type' => 'select',
                        'class' => 'form-control form-control-extend search-field',
                        'data-id' => 'fund_type',
                        'serach-option' => '=',
                        'placeholder' => '推广模式',
                        'options' => [
                            '' => '全部',
                            '0' => '查找上级',
                            '1' => '搜索下级',
                        ]
                    ],
                    //绑定手机
                    'mobiles' => [
                        'type' => 'select',
                        'class' => 'form-control form-control-extend search-field',
                        'data-id' => 'mobiles',
                        'serach-option' => '=',
                        'placeholder' => '是否绑定手机',
                        'options' => [
                            '' => '全部',
                            '0' => '是',
                            '1' => '否',
                        ]
                    ],
                    //绑定微信
                    'openId' => [
                        'type' => 'select',
                        'class' => 'form-control form-control-extend search-field',
                        'data-id' => 'openId',
                        'serach-option' => '=',
                        'placeholder' => '是否绑定微信',
                        'options' => [
                            '' => '全部',
                            '0' => '是',
                            '1' => '否',
                        ]
                    ],
                    //总资产
                    'gold' => [
                        'type' => 'datetime',
                        'placeholder' => '总资产',
                        'field' => [
                            'gold-start' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field keydown',
                                'data-id' => 'gold-start',
                                'placeholder' => '最小范围',
                                'serach-option' => '>=',
                            ],

                            'gold-end' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field keydown',
                                'data-id' => 'gold-end',
                                'placeholder' => '最大范围',
                                'serach-option' => '<=',
                            ],
                        ]
                    ],
                    //总输赢
                    'user_lose_win_all' => [
                        'type' => 'datetime',
                        'placeholder' => '总输赢',
                        'field' => [
                            'user_lose_win_all-start' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field keydown',
                                'data-id' => 'user_lose_win_all-start',
                                'placeholder' => '最小范围',
                                'serach-option' => '>=',
                            ],

                            'user_lose_win_all-end' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field keydown',
                                'data-id' => 'user_lose_win_all-end',
                                'placeholder' => '最大范围',
                                'serach-option' => '<=',
                            ],
                        ]
                    ],
                    //当日输赢
                    'daily_gold' => [
                        'type' => 'datetime',
                        'placeholder' => '当日输赢',
                        'field' => [
                            'daily_gold-start' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field keydown',
                                'data-id' => 'daily_gold-start',
                                'placeholder' => '最小范围',
                                'serach-option' => '>=',
                            ],

                            'daily_gold-end' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field keydown',
                                'data-id' => 'daily_gold-end',
                                'placeholder' => '最大范围',
                                'serach-option' => '<=',
                            ],
                        ]
                    ],
                    //礼物赠送
                    'total_send' => [
                        'type' => 'datetime',
                        'placeholder' => '礼物赠送',
                        'field' => [
                            'total_send-start' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field keydown',
                                'data-id' => 'total_send-start',
                                'placeholder' => '最小范围',
                                'serach-option' => '>=',
                            ],

                            'total_send-end' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field keydown',
                                'data-id' => 'total_send-end',
                                'placeholder' => '最大范围',
                                'serach-option' => '<=',
                            ],
                        ]
                    ],
                    //礼物接收
                    'total_receive' => [
                        'type' => 'datetime',
                        'placeholder' => '礼物接收',
                        'field' => [
                            'total_receive-start' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field keydown',
                                'data-id' => 'total_receive-start',
                                'placeholder' => '最小范围',
                                'serach-option' => '>=',
                            ],

                            'total_receive-end' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field keydown',
                                'data-id' => 'total_receive-end',
                                'placeholder' => '最大范围',
                                'serach-option' => '<=',
                            ],
                        ]
                    ],
                    //赠送接收差
                    'accept_present_diff_num' => [
                        'type' => 'datetime',
                        'placeholder' => '赠送接收差',
                        'field' => [
                            'accept_present_diff_num-start' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field keydown',
                                'data-id' => 'accept_present_diff_num-start',
                                'placeholder' => '最小范围',
                                'serach-option' => '>=',
                            ],

                            'accept_present_diff_num-end' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field keydown',
                                'data-id' => 'accept_present_diff_num-end',
                                'placeholder' => '最大范围',
                                'serach-option' => '<=',
                            ],
                        ]
                    ],
                    //注册时间
                    'createtime' => [
                        'type' => 'datetime',
                        'placeholder' => '注册时间',
                        'field' => [
                            'createtime-start' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field Wdate ',
                                'id' => 'createtime_BeginDay',
                                'data-id' => 'createtime-start',
                                'placeholder' => '开始时间',
                                'serach-option' => '>=',
                                'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd HH:m:00',startDate:'%y-%M-%d HH:m:0' ,maxDate:'#F{\$dp.\$D(\'createtime_EndDay\')||\'%y-%M-{%d}\'}',readOnly:true})"
                            ],

                            "createtime-end" => [
                                "type" => "text",
                                "class" => "form-control form-control-extend search-field Wdate",
                                "id" => "createtime_EndDay",
                                "data-id" => "createtime-end",
                                "placeholder" => "结束时间",
                                "serach-option" => "<=",
                                "onclick" => "WdatePicker({dateFmt:'yyyy-MM-dd HH:m:00',startDate:'%y-%M-%d HH:m:0' ,minDate:'#F{\$dp.\$D(\'createtime_BeginDay\')}',maxDate:'%y-%M-{%d}',readOnly:true})"
                            ],
                        ]
                    ],
                    //最后登录时间
                    'logintime' => [
                        'type' => 'datetime',
                        'placeholder' => '最后登录时间',
                        'field' => [
                            'logintime-start' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field Wdate',
                                'id' => 'logintime_BeginDay',
                                'data-id' => 'logintime-start',
                                'placeholder' => '开始时间',
                                'serach-option' => '>=',
                                'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd HH:00:00',startDate:'%y-%M-%d 00:00:00' ,maxDate:'#F{\$dp.\$D(\'logintime_EndDay\')||\'%y-%M-{%d}\'}',readOnly:true})"
                            ],

                            'logintime-end' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field Wdate',
                                'id' => 'logintime_EndDay',
                                'data-id' => 'logintime-end',
                                'placeholder' => '结束时间',
                                'serach-option' => '<=',
                                'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd HH:00:00',startDate:'%y-%M-%d 00:00:00',minDate:'#F{\$dp.\$D(\'logintime_BeginDay\')}',maxDate:'%y-%M-{%d}',readOnly:true})"
                            ],
                        ]
                    ],
                ],
            ],
            //在线用户列表
            'online_user'=>[
                'index' => [
                    'id_or_nickname' => [
                        'type' => 'select_or_text',
                        'placeholder' => '用户ID或者用户昵称',
                        'field' => [
                            'select_or_text_select' => [
                                'type' => 'select',
                                'class' => 'form-control form-control-extend search-field',
                                'data-id' => 'select_or_text_select',
                                'serach-option' => '=',
                                'placeholder' => '状态',
                                'options' => [
                                    'uid' => '用户ID',
                                    'nickname' => '用户昵称',
                                ]
                            ],

                            'select_or_text_text' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field keydown',
                                'data-id' => 'select_or_text_text',
                                'placeholder' => '用户ID或者用户昵称',
                                'serach-option' => 'LIKE',
                            ],
                        ]
                    ],

                    /*'level' => [
                        'type' => 'select',
                        'class' => 'form-control form-control-extend search-field',
                        'data-id' => 'level',
                        'serach-option' => '=',
                        'placeholder' => '用户类型',
                        'options' => [
                            '' => '全部'
                        ]
                    ],*/

                    'room' => [
                        'type' => 'game_product_id',
                        'class' => 'form-control form-control-extend search-field',
                        'data-id' => 'room',
                        'serach-option' => '=',
                        'placeholder' => '游戏',
                        'options' => [
                            '' => '全部',
                        ]
                    ],
                    
                    'ponit_control_type' => [
                        'type' => 'select',
                        'class' => 'form-control form-control-extend search-field',
                        'data-id' => 'point_control_type',
                        'serach-option' => '=',
                        'placeholder' => '点控类型',
                        'options' => [
                            '' => '全部'
                        ]
                    ],
                    
                    'ponit_control_status' => [
                        'type' => 'select',
                        'class' => 'form-control form-control-extend search-field',
                        'data-id' => 'point_control_status',
                        'serach-option' => '=',
                        'placeholder' => '点控状态',
                        'options' => [
                            '' => '全部'
                        ]
                    ],
                    
                    /*'createtime' => [
                        'type' => 'datetime',
                        'placeholder' => '注册时间',
                        'field' => [
                            'createtime-start' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field Wdate',
                                'id' => 'BeginDay',
                                'data-id' => 'createtime-start',
                                'placeholder' => '开始时间',
                                'serach-option' => '>=',
                                'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd HH:00:00',startDate:'%y-%M-%d 00:00:00' ,maxDate:'#F{\$dp.\$D(\'EndDay\')||\'%y-%M-{%d}\'}',readOnly:true})"
                            ],

                            'createtime-end' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field Wdate',
                                'id' => 'EndDay',
                                'data-id' => 'createtime-end',
                                'placeholder' => '结束时间',
                                'serach-option' => '<=',
                                'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd HH:00:00',startDate:'%y-%M-%d 00:00:00' ,minDate:'#F{\$dp.\$D(\'BeginDay\')}',maxDate:'%y-%M-{%d}',readOnly:true})"
                            ],
                        ]
                    ],*/
                ],
            ],

            //关怀用户列表
            'focus'=>[
                'index' => [
                    'id_or_nickname' => [
                        'type' => 'select_or_text',
                        'placeholder' => '用户ID或者用户昵称',
                        'field' => [
                            'select_or_text_select' => [
                                'type' => 'select',
                                'class' => 'form-control form-control-extend search-field',
                                'data-id' => 'select_or_text_select',
                                'serach-option' => '=',
                                'placeholder' => '状态',
                                'options' => [
                                    'uid' => '用户ID',
                                    'nickname' => '用户昵称',
                                ]
                            ],

                            'select_or_text_text' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field keydown',
                                'data-id' => 'select_or_text_text',
                                'placeholder' => '用户ID或者用户昵称',
                                'serach-option' => 'LIKE',
                            ],
                        ]
                    ],

                    /*'level' => [
                        'type' => 'select',
                        'class' => 'form-control form-control-extend search-field',
                        'data-id' => 'level',
                        'serach-option' => '=',
                        'placeholder' => '用户类型',
                        'options' => [
                            '' => '全部'
                        ]
                    ],*/

                    /*  'room' => [
                          'type' => 'game_product_id',
                          'class' => 'form-control form-control-extend search-field',
                          'data-id' => 'room',
                          'serach-option' => '=',
                          'placeholder' => '游戏',
                          'options' => [
                              '' => '全部',
                          ]
                      ],

                      'ponit_control_type' => [
                          'type' => 'select',
                          'class' => 'form-control form-control-extend search-field',
                          'data-id' => 'point_control_type',
                          'serach-option' => '=',
                          'placeholder' => '点控类型',
                          'options' => [
                              '' => '全部'
                          ]
                      ],

                      'ponit_control_status' => [
                          'type' => 'select',
                          'class' => 'form-control form-control-extend search-field',
                          'data-id' => 'point_control_status',
                          'serach-option' => '=',
                          'placeholder' => '点控状态',
                          'options' => [
                              '' => '全部'
                          ]
                      ],*/

                    /*'createtime' => [
                        'type' => 'datetime',
                        'placeholder' => '注册时间',
                        'field' => [
                            'createtime-start' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field Wdate',
                                'id' => 'BeginDay',
                                'data-id' => 'createtime-start',
                                'placeholder' => '开始时间',
                                'serach-option' => '>=',
                                'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd HH:00:00',startDate:'%y-%M-%d 00:00:00' ,maxDate:'#F{\$dp.\$D(\'EndDay\')||\'%y-%M-{%d}\'}',readOnly:true})"
                            ],

                            'createtime-end' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field Wdate',
                                'id' => 'EndDay',
                                'data-id' => 'createtime-end',
                                'placeholder' => '结束时间',
                                'serach-option' => '<=',
                                'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd HH:00:00',startDate:'%y-%M-%d 00:00:00' ,minDate:'#F{\$dp.\$D(\'BeginDay\')}',maxDate:'%y-%M-{%d}',readOnly:true})"
                            ],
                        ]
                    ],*/
                ],
            ],

            //在线设备关联查询
            'online_mac' => [
                'index' => [
                    'is_online' => [
                        'type' => 'select',
                        'class' => 'form-control form-control-extend search-field',
                        'data-id' => 'is_online',
                        'serach-option' => '=',
                        'placeholder' => '用户类型',
                        'options' => [
                            '1' => '在线'
                        ]
                    ]
                ],
            ],
            //交易列表
            'send_present' => [
                'index' => [
                    'uid_or_take_uid' => [
                        'type' => 'select_or_text',
                        'placeholder' => '用户ID或者用户昵称',
                        'field' => [
                            'select_or_text_select' => [
                                'type' => 'select',
                                'class' => 'form-control form-control-extend search-field',
                                'data-id' => 'select_or_text_select',
                                'serach-option' => '=',
                                'placeholder' => '状态',
                                'options' => [
                                    'uids'     => '全部',
                                    'uid'      => '赠送者',
                                    'take_uid' => '接收者',
                                ]
                            ],

                            'select_or_text_text' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field keydown',
                                'data-id' => 'select_or_text_text',
                                'placeholder' => '用户ID',
                                'serach-option' => '=',
                            ],
                        ]
                    ],

                    #赠送者类型
                    'levels' => [
                        'type' => 'select',
                        'class' => 'form-control form-control-extend search-field',
                        'data-id' => 'levels',
                        'serach-option' => '=',
                        'placeholder' => '赠送者类型',
                        'options' => [
                            '' => '全部',
                            '0' => '普通用户',
                            '1' => 'VIP',
                        ]
                    ],

                    #接收者类型
                    'levelss' => [
                        'type' => 'select',
                        'class' => 'form-control form-control-extend search-field',
                        'data-id' => 'levelss',
                        'serach-option' => '=',
                        'placeholder' => '接收者类型',
                        'options' => [
                            '' => '全部',
                            '0' => '普通用户',
                            '1' => 'VIP',
                        ]
                    ],

                    #交易额度
                    'send_gold' => [
                        'type' => 'datetime',
                        'placeholder' => '交易额度',
                        'field' => [
                            'send_gold-start' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field keydown',
                                'data-id' => 'send_gold-start',
                                'placeholder' => '最小范围',
                                'serach-option' => '>=',
                            ],

                            'send_gold-end' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field keydown',
                                'data-id' => 'send_gold-end',
                                'placeholder' => '最大范围',
                                'serach-option' => '<=',
                            ],
                        ]
                    ],

                    'createtime' => [
                        'type' => 'datetime',
                        'placeholder' => '交易时间',
                        'field' => [
                            'createtime-start' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field Wdate ',
                                'id' => 'createtime_BeginDay',
                                'data-id' => 'createtime-start',
                                'placeholder' => '开始时间',
                                'serach-option' => '>=',
                                'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd HH:00:00',startDate:'%y-%M-%d 00:00:00' ,maxDate:'#F{\$dp.\$D(\'createtime_EndDay\')||\'%y-%M-%d\'}',readOnly:true})"
                            ],

                            'createtime-end' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field Wdate',
                                'id' => 'createtime_EndDay',
                                'data-id' => 'createtime-end',
                                'placeholder' => '结束时间',
                                'serach-option' => '<=',
                                'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd HH:00:00',startDate:'%y-%M-%d 00:00:00' ,minDate:'#F{\$dp.\$D(\'createtime_BeginDay\')}',maxDate:'%y-%M-{%d}',readOnly:true})"
                            ],
                        ]
                    ]
                ],
            ],

            //交易记录
            'trading' => [
                'index' => [
                    'uid' => [
                        'type' => 'text',
                        'class' => 'form-control form-control-extend search-field keydown',
                        'data-id' => 'a.uid',
                        'serach-option' => '=',
                        'placeholder' => '用户ID'
                    ],
                    'levels' => [
                        'type' => 'select',
                        'class' => 'form-control form-control-extend search-field',
                        'data-id' => 'levels',
                        'serach-option' => '=',
                        'placeholder' => '用户类型',
                        'options' => [
                            '' => '全部',
                            '0' => '普通用户',
                            '1' => 'VIP用户',
                        ]
                    ],
                    'createtime' => [
                        'type' => 'datetime',
                        'placeholder' => '时间',
                        'field' => [
                            'createtime-start' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field Wdate ',
                                'id' => 'createtime_BeginDay',
                                'data-id' => 'createtime-start',
                                'placeholder' => '开始时间',
                                'serach-option' => '>=',
                                'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd 00:00:00',startDate:'%y-%M-%d',maxDate:'#F{\$dp.\$D(\'createtime_EndDay\')||\'%y-%M-{%d-1}\'}',readOnly:true})"
                            ],

                            'createtime-end' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field Wdate',
                                'id' => 'createtime_EndDay',
                                'data-id' => 'createtime-end',
                                'placeholder' => '结束时间',
                                'serach-option' => '<=',
                                'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd 23:59:59',startDate:'%y-%M-%d' ,minDate:'#F{\$dp.\$D(\'createtime_BeginDay\')}',maxDate:'%y-%M-{%d}',readOnly:true})"
                            ],
                        ]
                    ],
                ],
            ],

            //锁定日志
            'lock' => [
                'index'=>  [
                    'generalizeId' => [
                        'type' => 'text',
                        'class' => 'form-control form-control-extend search-field keydown',
                        'data-id' => 'uid',
                        'serach-option' => '=',
                        'placeholder' => '用户ID'
                    ],
                    'admin_id' => [
                        'type' => 'text',
                        'class' => 'form-control form-control-extend search-field keydown',
                        'data-id' => 'admin_id',
                        'serach-option' => '=',
                        'placeholder' => '管理员ID'
                    ],
                    'type' => [
                        'type' => 'select',
                        'class' => 'form-control form-control-extend search-field',
                        'data-id' => 'type',
                        'serach-option' => '=',
                        'placeholder' => '类型',
                        'options' => [
                            '' => '全部',
                            'lock' => '用户锁定',
                            'unlock' => '解除锁定',
                            'present' => '禁止赠送',
                            'no_present' => '允许赠送',
                        ]
                    ],

                    'createtime' => [
                        'type' => 'datetime',
                        'placeholder' => '时间',
                        'field' => [
                            'createtime-start' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field Wdate ',
                                'id' => 'createtime_BeginDay',
                                'data-id' => 'createtime-start',
                                'placeholder' => '开始时间',
                                'serach-option' => '>=',
                                'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd 00:00:00',startDate:'%y-%M-%d 00:00:00' ,maxDate:'#F{\$dp.\$D(\'createtime_EndDay\')||\'%y-%M-{%d}\'}',readOnly:true})"
                            ],

                            'createtime-end' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field Wdate',
                                'id' => 'createtime_EndDay',
                                'data-id' => 'createtime-end',
                                'placeholder' => '结束时间',
                                'serach-option' => '<=',
                                'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd 23:59:59',startDate:'%y-%M-%d 00:00:00' ,minDate:'#F{\$dp.\$D(\'createtime_BeginDay\')}',maxDate:'%y-%M-%d',readOnly:true})"
                            ],
                        ]
                    ]
                ],
            ],
            //登陆日志
            'login' => [
                'index' => [
                    'generalizeId' => [
                        'type' => 'text',
                        'class' => 'form-control form-control-extend search-field keydown',
                        'data-id' => 'uid',
                        'serach-option' => '=',
                        'placeholder' => '用户ID'
                    ],
                    'phyAdress' => [
                        'type' => 'text',
                        'class' => 'form-control form-control-extend search-field keydown',
                        'data-id' => 'phyAdress',
                        'serach-option' => '=',
                        'placeholder' => '机器码'
                    ],
                    'room' => [
                        'type' => 'game_product_id',
                        'class' => 'form-control form-control-extend search-field',
                        'data-id' => 'room',
                        'serach-option' => '=',
                        'placeholder' => '游戏',
                        'options' => [
                            '' => '全部',
                        ]
                    ],
                    'createtime' => [
                        'type' => 'datetime',
                        'placeholder' => '登陆时间',
                        'field' => [
                            'createtime-start' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field Wdate ',
                                'id' => 'createtime_BeginDay',
                                'data-id' => 'createtime-start',
                                'placeholder' => '开始时间',
                                'serach-option' => '>=',
                                'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd HH:00:00',startDate:'%y-%M-%d 00:00:00' ,maxDate:'#F{\$dp.\$D(\'createtime_EndDay\')||\'%y-%M-{%d}\'}',readOnly:true})"
                            ],

                            'createtime-end' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field Wdate',
                                'id' => 'createtime_EndDay',
                                'data-id' => 'createtime-end',
                                'placeholder' => '结束时间',
                                'serach-option' => '<=',
                                'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd HH:00:00',startDate:'%y-%M-%d 00:00:00' ,minDate:'#F{\$dp.\$D(\'createtime_BeginDay\')}',maxDate:'%y-%M-{%d}',readOnly:true})"
                            ],
                        ]
                    ],
                ],
            ],

            'mac' => [
                'index' => [
                    'uid' => [
                        'type' => 'text',
                        'class' => 'form-control form-control-extend search-field keydown',
                        'data-id' => 'uid',
                        'placeholder' => '用户ID',
                        'serach-option' => '=',
                        'id'=>'uid'
                    ],

                    'phyAdress' => [
                        'type' => 'text',
                        'class' => 'form-control form-control-extend search-field keydown',
                        'data-id' => 'phyAdress',
                        'placeholder' => '设备码',
                        'serach-option' => '=',
                    ],
                    'addIp' => [
                        'type' => 'text',
                        'class' => 'form-control form-control-extend search-field keydown',
                        'data-id' => 'addIp',
                        'placeholder' => 'IP',
                        'serach-option' => '=',
                    ],
                ],
            ],
        ],

        //系统管理
        'game' => [
            'award' => [
                'detail' => [
                    'id_or_nickname' => [
                        'type' => 'select_or_text',
                        'placeholder' => '用户ID或者用户昵称',
                        'field' => [
                            'select_or_text_select' => [
                                'type' => 'select',
                                'class' => 'form-control form-control-extend search-field',
                                'data-id' => 'select_or_text_select',
                                'serach-option' => '=',
                                'placeholder' => '状态',
                                'options' => [
                                    'yq_award_code.uid' => '用户ID',
                                    'yq_user.nickname' => '用户昵称',
                                ]
                            ],

                            'select_or_text_text' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field keydown',
                                'data-id' => 'select_or_text_text',
                                'placeholder' => '用户ID或者用户昵称',
                                'serach-option' => 'LIKE',
                            ],
                        ]
                    ],

                    'code' => [
                        'type' => 'text',
                        'class' => 'form-control form-control-extend search-field keydown',
                        'data-id' => 'code',
                        'placeholder' => '卡号',
                        'serach-option' => '=',
                    ],

                    'isget' => [
                        'type' => 'select',
                        'class' => 'form-control form-control-extend search-field',
                        'data-id' => 'isget',
                        'serach-option' => '=',
                        'placeholder' => '领取状态',
                        'options' => [
                            '' => '全部',
                            '未领取',
                            '已领取',
                        ]
                    ],
                ],
            ],
            'contact' => [
                'index' => [
                    'id_or_nickname' => [
                        'type' => 'select_or_text',
                        'placeholder' => '用户ID或者用户昵称',
                        'field' => [
                            'select_or_text_select' => [
                                'type' => 'select',
                                'class' => 'form-control form-control-extend search-field',
                                'data-id' => 'select_or_text_select',
                                'serach-option' => '=',
                                'placeholder' => '状态',
                                'options' => [
                                    'uid' => '用户ID',
                                    'nickname' => '用户昵称',
                                ]
                            ],

                            'select_or_text_text' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field keydown',
                                'data-id' => 'select_or_text_text',
                                'placeholder' => '用户ID或者用户昵称',
                                'serach-option' => 'LIKE',
                            ],
                        ]
                    ],
                    'createtime' => [
                        'type' => 'datetime',
                        'placeholder' => '时间',
                        'field' => [
                            'createtime-start' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field Wdate ',
                                'id' => 'createtime_BeginDay',
                                'data-id' => 'a.createtime-start',
                                'placeholder' => '开始时间',
                                'serach-option' => '>=',
                                'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd HH:00:00',startDate:'%y-%M-%d 00:00:00' ,maxDate:'#F{\$dp.\$D(\'createtime_EndDay\')||\'%y-%M-{%d}\'}',readOnly:true})"
                            ],

                            'createtime-end' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field Wdate',
                                'id' => 'createtime_EndDay',
                                'data-id' => 'a.createtime-end',
                                'placeholder' => '结束时间',
                                'serach-option' => '<=',
                                'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd HH:00:00',startDate:'%y-%M-%d 00:00:00' ,minDate:'#F{\$dp.\$D(\'createtime_BeginDay\')}',maxDate:'%y-%M-{%d}',readOnly:true})"
                            ],
                        ]
                    ],
                ],

            ],

        ],

        //游戏管理
        'auto' => [
            'red' => [
                'index' => [
                    'uid' => [
                        'type' => 'text',
                        'class' => 'form-control form-control-extend search-field keydown',
                        'data-id' => 'uid',
                        'placeholder' => '中奖玩家ID',
                        'serach-option' => '=',
                    ],

                    'ProductID' => [
                        'type' => 'select',
                        'class' => 'form-control form-control-extend search-field',
                        'data-id' => 'ProductID',
                        'serach-option' => '=',
                        'placeholder' => '类型',
                        'options' => [
                            '' => '全部',
                            '100301' => '初级',
                            '100302' => '中级',
                            '100303' => '高级',
                            '100304' => '专家',
                            '100305' => '大师',
                        ]
                    ],

                    'IsAi' => [
                        'type' => 'select',
                        'class' => 'form-control form-control-extend search-field',
                        'data-id' => 'IsAi',
                        'serach-option' => '=',
                        'placeholder' => '是否AI中奖',
                        'options' => [
                            '' => '全部',
                            '0' => '否',
                            '1' => '是',
                        ]
                    ],

                    'time' => [
                        'type' => 'datetime',
                        'placeholder' => '时间',
                        'field' => [
                            'time-start' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field Wdate ',
                                'id' => 'time_BeginDay',
                                'data-id' => 'time-start',
                                'placeholder' => '开始时间',
                                'serach-option' => '>=',
                                'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd HH:00:00',startDate:'%y-%M-%d 00:00:00' ,maxDate:'#F{\$dp.\$D(\'time_EndDay\')||\'%y-%M-{%d}\'}',readOnly:true})"
                            ],

                            'time-end' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field Wdate',
                                'id' => 'time_EndDay',
                                'data-id' => 'time-end',
                                'placeholder' => '结束时间',
                                'serach-option' => '<=',
                                'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd HH:00:00',startDate:'%y-%M-%d 00:00:00' ,minDate:'#F{\$dp.\$D(\'time_BeginDay\')}',maxDate:'%y-%M-{%d}',readOnly:true})"
                            ],
                        ]
                    ],
                ],
            ],

            'fund' => [
                'index' => [
                    'uid' => [
                        'type' => 'text',
                        'class' => 'form-control form-control-extend search-field keydown',
                        'data-id' => 'yq_fund.uid',
                        'placeholder' => '玩家ID',
                        'serach-option' => '=',
                    ],
                    //用户类型
                    'levels' => [
                        'type' => 'select',
                        'class' => 'form-control form-control-extend search-field',
                        'data-id' => 'levels',
                        'serach-option' => '=',
                        'placeholder' => '用户类型',
                        'options' => [
                            '' => '全部',
                            '0' => '普通用户',
                            '1' => 'VIP用户',
                        ]
                    ],

                    //用户类型
                    'fund_type' => [
                        'type' => 'select',
                        'class' => 'form-control form-control-extend search-field',
                        'data-id' => 'fund_type',
                        'serach-option' => '=',
                        'placeholder' => '变化类型',
                        'options' => [
                            '' => '全部',
                            '1' => '交易税返利',
                            '2' => '押注返利',
                            '3' => '领取基金',
                            '4' => '输赢返利',
                        ]
                    ],

                    'createtime' => [
                        'type' => 'datetime',
                        'placeholder' => '时间',
                        'field' => [
                            'createtime-start' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field Wdate ',
                                'id' => 'createtime_BeginDay',
                                'data-id' => 'yq_fund.createtime-start',
                                'placeholder' => '开始时间',
                                'serach-option' => '>=',
                                'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd 00:00:00',startDate:'%y-%M-%d',maxDate:'#F{\$dp.\$D(\'createtime_EndDay\')||\'%y-%M-{%d-1}\'}',readOnly:true})"
                            ],

                            'createtime-end' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field Wdate',
                                'id' => 'createtime_EndDay',
                                'data-id' => 'yq_fund.createtime-end',
                                'placeholder' => '结束时间',
                                'serach-option' => '<=',
                                'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd 23:59:59',startDate:'%y-%M-%d' ,minDate:'#F{\$dp.\$D(\'createtime_BeginDay\')}',maxDate:'%y-%M-{%d}',readOnly:true})"
                            ],
                        ]
                    ],
                ],
            ],

            'experience' => [
                'index' => [
                    'uid' => [
                        'type' => 'text',
                        'class' => 'form-control form-control-extend search-field keydown',
                        'data-id' => 'yq_newuserlog.uid',
                        'placeholder' => '玩家ID',
                        'serach-option' => '=',
                    ],

                    'pid' => [
                        'type' => 'game_product_id',
                        'class' => 'form-control form-control-extend search-field',
                        'data-id' => 'pid',
                        'serach-option' => '=',
                        'placeholder' => '游戏',
                        'options' => [
                            '' => '全部',
                        ]
                    ],

                    'gametype' => [
                        'type' => 'select',
                        'class' => 'form-control form-control-extend search-field',
                        'data-id' => 'gametype',
                        'serach-option' => '=',
                        'placeholder' => '修正类型',
                        'options' => [
                            '' => '全部',
                            '1' => '新手体验',
                            '2' => '充值体验',
                            '3' => '波动修正',
                        ]
                    ],

                    'createtime' => [
                        'type' => 'datetime',
                        'placeholder' => '时间',
                        'field' => [
                            'createtime-start' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field Wdate ',
                                'id' => 'createtime_BeginDay',
                                'data-id' => 'yq_newuserlog.createtime-start',
                                'placeholder' => '开始时间',
                                'serach-option' => '>=',
                                'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd 00:00:00',startDate:'%y-%M-%d',maxDate:'#F{\$dp.\$D(\'createtime_EndDay\')||\'%y-%M-{%d-1}\'}',readOnly:true})"
                            ],

                            'createtime-end' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field Wdate',
                                'id' => 'createtime_EndDay',
                                'data-id' => 'yq_newuserlog.createtime-end',
                                'placeholder' => '结束时间',
                                'serach-option' => '<=',
                                'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd 23:59:59',startDate:'%y-%M-%d' ,minDate:'#F{\$dp.\$D(\'createtime_BeginDay\')}',maxDate:'%y-%M-{%d}',readOnly:true})"
                            ],
                        ]
                    ],
                ],
            ],
        ],

        #代理团队
        'team' => [
            'member' => [
                'index'=>[

                    'uid' => [
                        'type' => 'text',
                        'class' => 'form-control form-control-extend search-field keydown',
                        'data-id' => 'b.uid',
                        'placeholder' => '用户ID',
                        'serach-option' => '=',
                    ],

                    'teamName' => [
                        'type' => 'text',
                        'class' => 'form-control form-control-extend search-field keydown',
                        'data-id' => 'teamName',
                        'placeholder' => '团队昵称查询',
                        'serach-option' => '=',
                    ],

                    'day_time' => [
                        'type' => 'datetime',
                        'placeholder' => '时间',
                        'field' => [
                            'time-start' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field Wdate ',
                                'id' => 'day_time_BeginDay',
                                'data-id' => 'day_time-start',
                                'placeholder' => '开始时间',
                                'serach-option' => '>=',
                                'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd HH:00:00',startDate:'%y-%M-%d 00:00:00' ,maxDate:'#F{\$dp.\$D(\'day_time_EndDay\')||\'%y-%M-{%d}\'}',readOnly:true})"
                            ],

                            'day_time-end' => [
                                'type' => 'text',
                                'class' => 'form-control form-control-extend search-field Wdate',
                                'id' => 'day_time_EndDay',
                                'data-id' => 'day_time-end',
                                'placeholder' => '结束时间',
                                'serach-option' => '<=',
                                'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd HH:00:00',startDate:'%y-%M-%d 00:00:00' ,minDate:'#F{\$dp.\$D(\'day_time_BeginDay\')}',maxDate:'%y-%M-{%d}',readOnly:true})"
                            ],
                        ]
                    ],
                ]
            ],
            'trading' => [

                'index' => [
                    'uid' => [
                        'type' => 'text',
                        'class' => 'form-control form-control-extend search-field keydown',
                        'data-id' => 'h.uid',
                        'placeholder' => '用户ID',
                        'serach-option' => '=',
                    ],
                    /* 'day_time' => [
                         'type' => 'datetime',
                         'placeholder' => '时间',
                         'field' => [
                             'time-start' => [
                                 'type' => 'text',
                                 'class' => 'form-control form-control-extend search-field Wdate ',
                                 'id' => 'day_time_BeginDay',
                                 'data-id' => 'day_time-start',
                                 'placeholder' => '开始时间',
                                 'serach-option' => '>=',
                                 'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd HH:00:00',startDate:'%y-%M-%d 00:00:00' ,maxDate:'#F{\$dp.\$D(\'day_time_EndDay\')||\'%y-%M-{%d}\'}',readOnly:true})"
                             ],

                             'day_time-end' => [
                                 'type' => 'text',
                                 'class' => 'form-control form-control-extend search-field Wdate',
                                 'id' => 'day_time_EndDay',
                                 'data-id' => 'day_time-end',
                                 'placeholder' => '结束时间',
                                 'serach-option' => '<=',
                                 'onclick' => "WdatePicker({dateFmt:'yyyy-MM-dd HH:00:00',startDate:'%y-%M-%d 00:00:00' ,minDate:'#F{\$dp.\$D(\'day_time_BeginDay\')}',maxDate:'%y-%M-{%d}',readOnly:true})"
                             ],
                         ]
                     ],*/
                ]

            ]
        ],
    ],

    //表单标签设置
    'form_tags' => [
        'text' => '<input %s />',
        'select' => '<select %s>%s</select>',
        'game_product_id' => '<select %s>%s</select>',
    ],

];