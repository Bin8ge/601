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
                    {field: 'uid', title: '用户ID',formatter: function (value, row, index) {

                            return '<a href="/admin/player/user/detail/uid/' + row.uid + '">' + row.uid + '</a>';
                        }},
                    {field: 'nickname', title: '昵称'},
                    {field: 'room', title: '房间ID'},
                    {field: 'roomname', title: '房间名称'},
                    {field: 'gold', title: '金币'},
                    {field: 'createtime', title: '登陆时间' , formatter: Table.api.formatter.datetime},
                    {field: 'addIp', title: '登陆IP'},
                    {field: 'phyAdress', title: '机器码'},
                ]
            ]
        });

        // 为表格绑定事件
        Table.api.bindevent(table);
        return table;
    }
};