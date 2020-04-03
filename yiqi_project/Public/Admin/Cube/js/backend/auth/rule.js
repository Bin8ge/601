var Controller = {
    index: function () {

        // 初始化表格参数配置
        Table.api.init({pagination: false});

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
            url: '/admin/auth/rule/index',
            columns: [
                [
                    {field: 'state', checkbox: true,},
                    {field: 'id', title: 'ID'},
                    {field: 'name', title: '标题', align: 'left'},
                    {field: 'icon', title: '图标', formatter: Controller.api.formatter.icon},
                    {field: 'node', title: '规则', align: 'left', formatter: Controller.api.formatter.name},
                    {field: 'weigh', title: '权重'},
                    {field: 'status', title: '状态', formatter: Table.api.formatter.status},
                    {
                        field: 'type',
                        title: '菜单',
                        align: 'center',
                        formatter: Controller.api.formatter.menu
                    },
                    {
                        field: 'operate',
                        title: '操作',
                        table: table,
                        events: Table.api.events.operate,
                        formatter: Table.api.formatter.operate
                    }
                ]
            ]
        });

        // 为表格绑定事件
        Table.api.bindevent(table);
        return table;
    },
    api: {
        formatter: {
            menu: function (value, row, index) {
                return row.type == 'menu' ? '是' : '否';
            },
            icon: function (value, row, index) {
                return '<i class="fa ' + (row.icon ? row.icon : 'fa fa-circle-o') + ' fa-sty"></i>';
            }
        }
    }

};