var Controller = {
    index: function (url) {
        Table.api.init();

        var table = $("#table");
        // 初始化表格
        table.bootstrapTable({
            url: url,
            columns: [
                [
                    {field: 'name', title: '游戏房间'},
                    {field: 'machine_id', title: '游戏ID'},
                    {field: 'admin_id', title: '管理员ID'},
                    {field: 'createtime', title: '添加时间',formatter: Table.api.formatter.datetime},
                    {field: 'runtime', title: '执行时间',formatter: Table.api.formatter.datetime},
                    {field: 'public_stock', title: '修改数值'},
                    {field: 'status', title: '状态'},
                ]
            ],
        });

        // 为表格绑定事件
        Table.api.bindevent(table);
        return table;
    },
    api: {
        formatter: {
            IsAi: function (value, row) {
                if(row.IsAi === '0'){
                    row.IsAi = '否 '
                }else if(row.IsAi === '1'){
                    row.IsAi = '是 '
                }
                return row.IsAi ;
            },
        }
    }

};