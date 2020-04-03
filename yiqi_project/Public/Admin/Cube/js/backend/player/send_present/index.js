var Controller = {
    index: function (url) {

        // 初始化表格参数配置
        Table.api.init();
        var table = $("#table");

        // 初始化表格
        table.bootstrapTable({
            url: url,
            columns: [
                [

                    {field: 'uid', title: '赠送者ID',formatter: function (value, row, index) {
                        /*if (index == 0) {
                            $.each(row.statistics, function (index, value) {
                                if (value == null) value = 0;
                                $("." + index).html(value);
                            });
                        }*/
                        if (!row.uid){
                            return '-';
                        }
                        return '<a href="/admin/player/user/detail/uid/' + row.uid + '">' + row.uid + '</a>';
                    }},
                    {field: 'send_nickname', title: '赠送者昵称'},
                    {field: 'send_level', title: '赠送者类型'},
                    {
                        field: 'take_uid', title: '接收者ID', formatter: function (value, row) {
                            if (!row.take_uid){
                                return '-';
                            }
                            return '<a href="/admin/player/user/detail/uid/' + row.take_uid + '">' + row.take_uid + '</a>';
                        }
                    },
                    {field: 'take_nickname', title: '接收者昵称'},
                    {field: 'take_level', title: '接收者类型'},
                    {field: 'send_gold', title: '赠送金币'},
                    {field: 'tax_gold', title: '交易税'},
                    {field: 'take_gold', title: '接收金币'},
                    {field: 'createtime', title: '赠送时间', formatter: Table.api.formatter.datetime},
                /*    {field: 'updatetime', title: '接收时间', formatter: Table.api.formatter.datetime},
                    {field: 'is_get', title: '状态', formatter: Controller.api.formatter.is_get},*/
                    {field: 'operate', title: '操作', table: table, events: Table.api.events.operate,formatter: function (value, row, index) {
                        return Controller.api.formatter.operate.call(this, value, row, index);
                        }}
                ]
            ],

        });

        // 为表格绑定事件
        Table.api.bindevent(table);
        return table;
    },
    api: {
        formatter: {
            is_get: function (value, row) {
                if (!row.is_get){
                    return '-';
                }
                return row.is_get == 1 ? '已领取' : '未领取';
            },
            operate: function (value, row) {
                var button = [];
                var operation = {};

                operation['back'] = Config.operation['back'];

                $.each(operation, function (action, action_data) {

                    //url地址
                    var url = "/admin/player/send_present/send_back";

                    //图标
                    var icon = "";


                    if ( row.is_back==1) {
                        text = '已退回';
                        var extend = ["data-title='" + text + "'"];
                        button.push("<p " + extend.join(" ") + " >" + icon + text + "</p>");
                    }else{


                            text = '退回';

                            var param = row.id+','+"'"+url+"'";

                            button.push('<button type="button" id='+row.id+' onclick="back('+param+')" class="btn btn-xs btn-info btn-dlone marg-left" >' + text + "</button>");


                    }
                });
                return button.join("")
            }
        }
    }

};