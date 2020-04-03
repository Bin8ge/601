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
                    {field: 'name', title: '礼包名称'},
                 /*   {field: 'platform', title: '使用平台',formatter: Controller.api.formatter.platform},
                    {field: 'channel', title: '使用渠道',formatter: Controller.api.formatter.channel},*/
                    {field: 'content', title: '礼包内容', formatter: Controller.api.formatter.content},
                    {field: 'awardNum', title: '生成数量'},
                    {field: 'getNum', title: '激活数量'},
                    {field: 'addtime', title: '生成时间', formatter: Table.api.formatter.datetime},
                    {field: 'is_repeat', title: '是否可多次使用', formatter: Controller.api.formatter.is_repeat},
                    {field: 'admin_id', title: '操作员'},
                    {field: 'is_release', title: '发布状态', formatter: Controller.api.formatter.is_release},
                    {field: 'status', title: '礼包状态', formatter: Controller.api.formatter.status},
                    {
                        field: 'operate',
                        title: '操作',
                        table: table,
                        events: Table.api.events.operate,
                        formatter: function (value, row, index) {

                            return Controller.api.formatter.operate.call(this, value, row, index);
                        }
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
            platform: function (value, row) {
                return  row.platform

            },
            channel: function (value, row) {
                return  row.channel;

            },
            content: function (value, row, index) {
                return "<a href=\"#\" onclick=\"layer.confirm('" + row.content + "', function (index) {});\">查看</a>";
            },
            status: function (value, row, index) {
                return row.status === '1' ? '正常' : '禁用';
            },
            is_repeat: function (value, row, index) {
                return row.is_repeat === '1' ? '是' : '否';
            },
            is_release: function (value, row, index) {
                return row.is_release === '1' ? '已发布' : '未发布';
            },
            operate: function (value, row, index, config) {

                var button = [];
                var operation = {};
                if (row.is_release === '1') {
                    //detail  详情   release  发布   edit  编辑  disable  启用
                    operation['detail'] = Config.operation['detail'];
                    //console.log(Config.operation['disable'])
                    operation['disable'] = Config.operation['disable'];
                } else {
                    if (row.status === '1') {

                        operation['release'] = Config.operation['release'];
                    }else{
                        operation['disable'] = Config.operation['disable'];
                    }
                    operation['edit'] = Config.operation['edit'];
                }

                $.each(operation, function (action, action_data) {
                    //样式名称
                    var class_name = action_data.class;
                    //url地址
                    var url = action_data.url;
                    //操作方法名称
                    var text = action_data.text;
                    //图标
                    var icon = "";
                    if (row.status === '0' && action === "disable") {
                        text = '启用';
                    }

                    if (action === "detail") {
                        class_name = class_name.replace('btn-detail', '');
                    }
                    var extend = ["data-title='" + text + "'"];
                    $.each(action_data.data, function (key, value) {

                        //判断如果url为空则自动补充
                        if (value === "" && key === 'url') {
                            value = url;
                            if (action === "edit" || action === "detail" || action === "release") {
                                value += '/id/' + row.id;
                            } else if (action === "delete" || action === "back" || action === "disable") {
                                value += '/ids/' + row.id;
                            }
                        }
                        //判断如果表单id为空则自动补充
                        if (key === 'form-id' && value === "") {
                            value = (url + '/id/' + row.id).replace(/\//g, '');
                        }
                        extend.push("data-" + key + "=" + "'" + value + "'")
                    });

                    var href = "javascript:;";

                    if (action === "detail") {
                        href = url + '/id/' + row.id;
                    }

                    button.push("<a href='" + href + "' class='" + class_name + "' " + extend.join(" ") + " >" + icon + text + "</a>");
                });
                return button.join("")
            }

        }
    }
};