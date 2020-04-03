var Controller = {
    index: function () {

        // 初始化表格参数配置
        Table.api.init();

        var table = $("#table");

        // 初始化表格
        table.bootstrapTable({
            url: '/admin/auth/rec_log/index',
            columns: [
                [
                    {field: 'uid', title: '用户ID'},
                    {field: 'user_name', title: '用户昵称'},
                    {field: 'admin_name', title: '管理员'},
                    {field: 'createtime', title: '时间', formatter: Table.api.formatter.datetime},
                    {field: 'type', title: '类型'},
                    {field: 'handleNum', title: '金币数'},
                    {field: 'disc', title: '说明'},
                ]
            ]
        });

        // 为表格绑定事件
        Table.api.bindevent(table);
        return table;
    },
};