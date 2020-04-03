var Controller = {
    index: function () {
        Table.api.init({
            pk: 'uid', sortName: 'uid',rowStyle: function (row) {

                if (row.point_type === '0' && row.point_status === '1') {
                    strclass = {css: {'background-color': '#a6f4a6'}};
                } else if (row.point_type === '1' && row.point_status === '1') {
                    strclass = {css: {'background-color': '#ff9797'}};
                } else {
                    return {};
                }
                return strclass
            }
        });

        var table = $("#table");
        // 初始化表格
        table.bootstrapTable({
            url: '/admin/player/trading/index',
            columns: [
                [
                    {
                        field: 'uid', title: '用户ID', formatter: function (value, row, index) {
                            return '<a href="/admin/player/user/detail/uid/' + row.uid + '">' + row.uid + '</a>';
                        }
                    },
                    {
                        field: 'nickname', title: '用户昵称', formatter: function (value, row, index) {
                            return '<a href="/admin/player/user/detail/uid/' + row.uid + '">' + row.nickname + '</a>';
                        }
                    },
                    {field: 'level', title: '用户类型'},
                    {field: 'user_gold', title: '总资产',sortable : true},
                    {field: 'user_lose_win_all', title: '总输赢',sortable : true},
                    {field: 'daily_gold', title: '当日输赢',sortable : true},
                    {field: 'total_take', title: '接收',sortable : true},
                    {field: 'total_send', title: '赠送',sortable : true},
                    {field: 'total_diff', title: '顺差(赠送-接收)',sortable : true},
                    {field: 'total_take_num', title: '接收笔数'},
                    {field: 'total_send_num', title: '赠送笔数'},
                    {field: 'total_send_people', title: '接收人数'},
                    {field: 'total_take_people', title: '赠送人数'},

                ]
            ],
        });

        // 为表格绑定事件
        Table.api.bindevent(table);
        return table;
    },

};