var Controller = {
    index: function (url) {

        // 初始化表格参数配置
        Table.api.init({
            pk: 'uid', sortName: 'daily_gold',
        });

        var table = $("#table");

        // 初始化表格
        table.bootstrapTable({
            url: url,
            columns: [
                [
                    {field: 'uid', title: '用户id',formatter: function (value, row) {
                            return '<a href="/admin/player/user/detail/uid/' + row.uid + '">' + row.uid + '</a>';
                        }},
                    {field: 'nickname', title: '用户昵称'},
                    {field: 'total_gold', title: '总资产'},
                    {field: 'daily_gold', title: '当日输赢'},
                ]
            ]
        });

        // 为表格绑定事件
        Table.api.bindevent(table);
        return table;
    },

};