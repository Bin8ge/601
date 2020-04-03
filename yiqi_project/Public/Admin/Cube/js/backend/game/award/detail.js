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
                    {field: 'code', title: '卡号'},
                    {field: 'isget', title: '领取状态',formatter: Controller.api.formatter.isget},
                    {field: 'content', title: '领取内容'},
                    {field: 'uid', title: '用户ID',formatter: function (value, row, index) {
                        if(row.uid==null){

                        }else{
                            return '<a href="/admin/player/user/detail/uid/' + row.uid + '">' + row.uid + '</a>';
                        }

                    }},
                    {field: 'nickname', title: '用户昵称'},
                    {field: 'status', title: '奖品状态',formatter: Controller.api.formatter.status},
                    {field: 'gettime', title: '领取时间', formatter: Table.api.formatter.datetime}
                ]
            ]
        });

        // 为表格绑定事件
        Table.api.bindevent(table);
        return table;
    },
    api: {
        formatter: {
            isget: function (value, row, index) {
                if (index == 0) {
                    $.each(row.statistics, function (index, value) {
                        $("." + index).html(value);
                    });
                }
                return row.isget == 1 ? '已领取' : '未领取';
            },
            status: function (value, row, index) {
                return row.status == 1 ? '正常' : '禁用';
            },
        }
    }
};