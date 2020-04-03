var Controller = {
    index: function (url) {

        // 初始化表格参数配置
        Table.api.init({
            pk: 'uid', sortName: 'uid', rowStyle: function (row, index) {
                if (row.point_control_type == 0 & row.point_control_status == 1) {
                    strclass = {css: {'background-color': '#a6f4a6'}};
                }
                else if (row.point_control_type == 1& row.point_control_status == 1) {
                    strclass = {css: {'background-color': '#ff9797'}};
                }
                else {
                    return {};
                }
                return strclass
            }
        });

        var table = $("#table");

        // 初始化表格
        table.bootstrapTable({
            url: url,
            columns: [
                [
                    {field: 'uid', title: '推广号',formatter: function (value, row, index) {

                        return '<a href="/admin/player/user/detail/uid/' + row.uid + '">' + row.uid + '</a>';
                    }},
                    {field: 'nickname', title: '用户昵称'},
                    {field: 'is_online', title: '在线状态', formatter: Controller.api.formatter.is_online},
                    {field: 'level', title: '用户等级', formatter: Controller.api.formatter.level},
                    {field: 'createtime', title: '注册时间', formatter: Table.api.formatter.datetime},
                    {field: 'login_createtime', title: '最后登录时间', formatter: Table.api.formatter.datetime},
                    {field: 'lose_win_total_all', title: '总输赢'},
                    {field: 'lose_win_total_today', title: '当日输赢'},
                    {field: 'accept_present_give_num', title: '接收礼物'}
                ]
            ]
        });

        // 为表格绑定事件
        Table.api.bindevent(table);
        return table;
    },
    api: {
        formatter: {
            is_online: function (value, row, index) {
                if (index == 0) {
                    $.each(row.statistics, function (index, value) {
                        $("."+index).html(value);
                    });
                }
                return row.is_online == 1 ? '在线' : '离线';
            },
            is_closure: function (value, row, index) {
                if (index == 0) {
                    $.each(row.statistics, function (index, value) {
                        $("."+index).html(value);
                    });
                }
                return row.is_closure == 1 ? '正常' : '冻结';
            },
            level: function (value, row, index) {
                if(row.level==0){
                    return  '普通用户';
                }else if(row.level==1){
                    return  'VIP1';
                }else if(row.level==2){
                    return  'VIP2';
                }else{
                    return  'VIP3';
                }
            },

        }
    }
};