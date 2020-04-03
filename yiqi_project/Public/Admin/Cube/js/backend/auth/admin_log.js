var Controller = {
    index: function () {

        // 初始化表格参数配置
        Table.api.init();

        var table = $("#table");

        // 初始化表格
        table.bootstrapTable({
            url: '/admin/auth/admin_log/index',
            columns: [
                [
                    {field: 'state', checkbox: true},
                    {field: 'id', title: 'ID'},
                    {field: 'username', title: '用户名'},
                    {field: 'url', title: 'URL'},
                    {field: 'title', title: '标题'},
                    {field: 'ip', title: 'IP'},
                    {field: 'browser', title: 'Browser', formatter: Controller.api.formatter.browser},
                    {field: 'createtime', title: '创建时间', formatter: Table.api.formatter.datetime},
                    {field: 'operate', title: '操作', table: table, events: Table.api.events.operate,formatter: function (value, row, index) {
                            return Table.api.formatter.operate.call(this, value, row, index);
                        }}
                ]
            ]
        });

        // 为表格绑定事件
        Table.api.bindevent(table);
        return table;
    },
    api: {
        formatter: {
            browser: function (value, row, index) {
                return row.useragent.split(" ")[0];
            },
        },
    }
};