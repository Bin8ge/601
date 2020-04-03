var Controller = {
    index: function () {

        Table.api.init();

        var table = $("#table");
        // 初始化表格

        table.bootstrapTable({
            url: '/admin/auto/game/index',

            columns: [
                [
                    {field: 'GameID', title: '游戏名称'},
                    {field: 'ProductID', title: '游戏等级'},
                    {field: 'MachineID', title: '房间序号'},
                    {field: 'win_lose_total', title: '累计输赢'},
                    {field: 'taxStock', title: '累计税收'},
                    {field: 'than', title: '累计吞吐率'},
                    {field: 'day_lose_win', title: '当日输赢'},
                    {field: 'day_tax', title: '当日税收'},
                    {field: 'day_than', title: '当日吞吐率'},
                    {field: 'BenchmarkStock', title: '库存基准'},
                    {field: 'BenchmarkStock', title: '输赢状态'},
                    {field: 'PublicStock', title: '公共库存'},
                    {field: 'JackpotStock', title: '奖池库存'},
                    {field: 'operate', title: '操作', table: table, events: Table.api.events.operate,formatter: function (value, row, index) {
                        return Controller.api.formatter.operate.call(this, value, row, index);
                        }}
                ]
            ],
        });

        // 为表格绑定事件
        Table.api.bindevent(table);
        return table;
    },


};