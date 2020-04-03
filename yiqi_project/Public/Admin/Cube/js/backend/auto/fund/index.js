var Controller = {
    index: function (url) {
        Table.api.init();

        var table = $('#table');
        // 初始化表格
        table.bootstrapTable({
            url: url,
            columns: [
                [
                    {field: 'createtime', title: '时间',formatter: Table.api.formatter.datetime},
                    {
                        field: 'uid', title: '用户ID',formatter: function (value, row, index) {
                            if (index === 0) {
                                $.each(row.statistics, function (index, value) {
                                    if (value == null) value = 0;
                                    $("." + index).html(value);
                                });
                            }
                            return '<a href="/admin/player/user/detail/uid/' + row.uid + '">' + row.uid + '</a>';
                        }
                    },
                    {field: 'level',title: '用户类型'},
                    {field: 'fund_change', title: '推广基金变化值'},
                    {field: 'fund_type', title: '推广基金变化类型'},
                ]
            ],
        });

        // 为表格绑定事件
        Table.api.bindevent(table);
        return table;
    },
   /* api: {
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
    }*/

};