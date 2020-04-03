var Controller = {
    index: function () {

        // 初始化表格参数配置
        Table.api.init({
            pk: 'uid', sortName: 'uid', rowStyle: function (row) {

                if (row.point_control_type === '0' && row.point_control_status === '1') {
                    strclass = {css: {'background-color': '#a6f4a6'}};
                } else if (row.point_control_type === '1' && row.point_control_status === '1') {
                    strclass = {css: {'background-color': '#ff9797'}};
                } else {
                    return {};
                }
                return strclass
            }
        });

        var table = $("#table");

        // 初始化表格
        table.bootstrapTable({
            url: '/admin/player/user/index',
            columns: [
                [
                    {field: 'is_online', title: '在线状态'},
                    {
                        field: 'uid', title: '用户ID', formatter: function (value, row, index) {
                            /*if (index == 0) {
                                $.each(row.statistics, function (index, value) {
                                    if (value == null) value = 0;
                                    $("." + index).html(value);
                                });
                            }*/
                        return '<a href="/admin/player/user/detail/uid/' + row.uid + '">' + row.uid + '</a>';
                        }
                    },
                    {
                        field: 'nickname', title: '用户昵称', formatter: function (value, row) {
                            return '<a href="/admin/player/user/detail/uid/' + row.uid + '">' + row.nickname + '</a>';
                        }
                    },
                    {field: 'level', title: '用户类型'},
                    {field: 'gold', title: '总资产',sortable : true},
                    {field: 'user_lose_win_all', title: '总输赢',sortable : true},
                    {field: 'daily_gold', title: '当日输赢',sortable : true},
                    {field: 'daily_stake', title: '当日押注',sortable : true},
                    {field: 'total_send', title: '总赠送',sortable : true},
                    {field: 'total_receive', title: '总接收',sortable : true},
                    {field: 'createtime', title: '注册时间', sortable : true,formatter: Table.api.formatter.datetime},
                    {field: 'logintime', title: '最后登录时间', sortable : true,formatter: Table.api.formatter.datetime},
                    {field: 'point_control_status', title: '点控状态',formatter: Controller.api.formatter.point_control_status},
                    {field: 'point_control_type', title: '点控类型',formatter: Controller.api.formatter.point_control_type},
                    {field: 'point_control_progress', title: '点控进度'},
                    {field: 'point_control_controlSum', title: '点控目标'},
                    {field: 'point_control_start_time', title: '点控开始时间',formatter: Table.api.formatter.datetime},
                ]
            ],
        });

        // 为表格绑定事件
        Table.api.bindevent(table);
        return table;
    },
    //重新变更搜索
    search_again: function (id, searchbutton_id) {
        $(id).on('click', function () {
            $(this).hide();
            $($(searchbutton_id).attr('data-search-hidden-class')).show();
            $($(searchbutton_id).attr('data-table-hidden-class')).hide();
        });
    },
    api: {
        formatter: {
            point_control_type: function (value, row) {
                if(row.point_control_type === '0'){
                    row.point_control_type = '控赢 '
                }else if(row.point_control_type === '1'){
                    row.point_control_type = '控输 '
                }
                return row.point_control_type ;
            },
            point_control_status: function (value, row) {
                if(row.point_control_status === '0'){
                    row.point_control_status = '点控取消 '
                }else if(row.point_control_status === '1'){
                    row.point_control_status = '点控中 '
                }else if(row.point_control_status === '2') {
                    row.point_control_status = '点控完成 '
                }else{
                    row.point_control_status = '-'
                }
                return row.point_control_status;
            },
        }
    }

};