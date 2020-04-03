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
                    {field: 'uid', title: '推广号',formatter: function (value, row) {
                        return '<a href="/admin/player/user/detail/uid/' + row.uid + '">' + row.uid + '</a>';
                    }},
                    {field: 'nickname', title: '昵称',formatter: function (value, row) {
                        return '<a href="/admin/player/user/detail/uid/' + row.uid + '">' + row.nickname + '</a>';
                    }},
                    {field: 'title', title: 'CM操作'},
                    {field: 'admin_id', title: '操作用户'},
                    {field: 'createtime', title: '操作时间', formatter: Table.api.formatter.datetime},

                ]
            ]
        });

        // 为表格绑定事件
        Table.api.bindevent(table);
        return table;
    },
};