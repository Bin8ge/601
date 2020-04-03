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
                    {
                        field: 'uid', title: '用户ID', formatter: function (value, row) {
                            return '<a href="/admin/player/user/detail/uid/' + row.uid + '">' + row.uid + '</a>';
                        }
                    },
                    {field: 'nickname', title: '昵称'},
                    {field: 'reg_time', title: '注册时间', formatter: Table.api.formatter.datetime},
                    {field: 'login_time', title: '最后登录时间', formatter: Table.api.formatter.datetime},
                  /*  {field: 'gold', title: '资产'},
                    {field: 'user_lose_win_all', title: '总输赢'},*/
                    {field: 'title', title: '类型'},
                    {field: 'createtime', title: '操作时间', formatter: Table.api.formatter.datetime},
                    {field: 'admin_id', title: '操作用户'},
                    {field: 'remark', title: '说明'},
                ]
            ]
        });
        // 为表格绑定事件
        Table.api.bindevent(table);
        return table;
    },

};