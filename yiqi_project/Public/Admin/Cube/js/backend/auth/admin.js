var Controller = {
    index: function () {

        // 初始化表格参数配置
        Table.api.init();

        var table = $("#table");

        //在表格内容渲染完成后回调的事件
        table.on('post-body.bs.table', function (e, json) {
            $("tbody tr[data-index]", this).each(function () {
                if (parseInt($("td:eq(1)", this).text()) == 1) {
                    $("input[type=checkbox]", this).prop("disabled", true);
                }
            });
        });

        // 初始化表格
        table.bootstrapTable({
            url: '/admin/auth/admin/index',
            columns: [
                [
                    {field: 'state', checkbox: true},
                    {field: 'id', title: 'ID'},
                    {field: 'username', title: '用户名'},
                    {field: 'nickname', title: '昵称'},
                    {field: 'groups_text', title: '所属组别'},
                    {field: 'email', title: 'Email'},
                    {field: 'status', title: '状态', formatter: Table.api.formatter.status},
                    {field: 'logintime', title: '最后登录', formatter: Table.api.formatter.datetime},
                    {field: 'operate', title: '操作', table: table, events: Table.api.events.operate,formatter: function (value, row, index) {
                            if(row.id == 1){
                                return '';
                            }
                            return Table.api.formatter.operate.call(this, value, row, index);
                        }}
                ]
            ]
        });

        // 为表格绑定事件
        Table.api.bindevent(table);
        return table;
    }

};