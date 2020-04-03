var Controller = {
    index: function (url) {
        Table.api.init();

        var table = $("#table");
        // 初始化表格
        table.bootstrapTable({
            url: url,
            columns: [
                [
                    {field: 'time', title: '开奖时间',formatter: Table.api.formatter.datetime},
                    {
                        field: 'room', title: '开奖房间', formatter: function (value, row, index) {
                            if (index === 0) {
                                $.each(row.statistics, function (index, value) {
                                    if (value == null) value = 0;
                                    $("." + index).html(value);
                                });
                            }
                            return value;
                        }
                    },
                    {field: 'uid', title: '中奖玩家ID'},
                    {field: 'gold', title: '中奖金额'},
                    {field: 'IsAi', title: '是否AI',formatter: Controller.api.formatter.IsAi},
                    {field: 'UserRedNum', title: '红包个数'},
                    {field: 'TatalRedNum', title: '红包总数'},
                    {field: 'UserJackPot', title: '玩家贡献奖池'},
                    {field: 'AiJackPot', title: 'AI贡献奖池'},
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