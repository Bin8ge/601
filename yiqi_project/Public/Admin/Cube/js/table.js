var Table = {
    list: {},
    // Bootstrap-table 基础配置
    defaults: {
        url: '',
        sidePagination: 'server',
        method: 'get', //请求方法
        showSearchButton: true,
        idTable: 'commonTable',
        pageSize: 100,
        pageList: [ 100,200,'All'],
        pagination: true,
        locale: 'zh-CN',
        //ss:11,
        pk: 'id',
        sortName: 'id',
        sortOrder: 'desc',
        checkOnInit: true, //是否在初始化时判断
        paginationFirstText: '第一页',
        paginationPreText: '上一页',
        paginationNextText: '下一页',
        paginationLastText: '最后一页',
    },
    config: {
        firsttd: 'tbody tr td:first-child:not(:has(div.card-views))',
        toolbar: '.toolbar',
        refreshbtn: '.btn-refresh',
        addbtn: '.btn-add',
        editbtn: '.btn-edit',
        delbtn: '.btn-del',
        importbtn: '.btn-import',
        multibtn: '.btn-multi',
        disabledbtn: '.btn-disabled',
        editonebtn: '.btn-editone',
        dragsortfield: 'weigh',
    },
    // Bootstrap-table 列配置
    columnDefaults: {
        align: 'center',
        valign: 'middle',
    },
    api: {
        init: function (defaults) {
            // 写入bootstrap-table默认配置
            $.extend(true, $.fn.bootstrapTable.defaults, Table.defaults,defaults);
            // 写入bootstrap-table column配置
            $.extend($.fn.bootstrapTable.columnDefaults, Table.columnDefaults);
            // 写入bootstrap-table locale配置
            $.extend($.fn.bootstrapTable.locales[Table.defaults.locale]);

            var BootstrapTable = $.fn.bootstrapTable.Constructor,
                _initToolbar = BootstrapTable.prototype.initToolbar;

            var sprintf = $.fn.bootstrapTable.utils.sprintf;

            BootstrapTable.prototype.initToolbar = function () {
                _initToolbar.apply(this, Array.prototype.slice.apply(arguments));
            }
        },
        // 绑定事件
        bindevent: function (table) {
            var options = table.bootstrapTable('getOptions');
            //getQueryVariable("generalizeId"),
            var strs=GetRequest();
            options.queryParams = function (params) {
                if(strs === undefined){
                    params['filter111'] = 111;
                }else{
                    params[strs[0]] = strs[1];
                }

                return params;
            }
            function GetRequest() {
                var url = location.search;
                if (url.indexOf("?") != -1) {
                    var str = url.substr(1);
                    strs = str.split("=");
                    return   strs;
                }
            }
            function getQueryVariable(variable)
            {
                var query = window.location.search.substring(1);
                var vars = query.split("&");
                for (var i=0;i<vars.length;i++) {
                    var pair = vars[i].split("=");

                    if(pair[0] == variable){
                        return pair[1];
                    }
                }
                return(false);
            }
        },

        // 单元格元素事件
        events: {
            operate: {
                'click .btn-detail': function (e, value, row, index) {
                    var title = $(this).attr('data-title');
                    var width = $(this).attr('data-width');
                    var height = $(this).attr('data-height');
                    var url = $(this).attr('data-url');
                    var form_id = $(this).attr('data-form-id');

                    layer.open({
                        type: 2,
                        shade: false,
                        shadeClose: true,
                        btn: ['关闭'],
                        title: title,
                        area: [width, height],
                        content: url
                    });
                },
                'click .btn-edit': function (e, value, row, index) {
                    var title = $(this).attr('data-title');
                    var width = $(this).attr('data-width');
                    var height = $(this).attr('data-height');
                    var url = $(this).attr('data-url');
                    var form_id = $(this).attr('data-form-id');

                    layer.open({
                        type: 2,
                        shade: false,
                        shadeClose: true,
                        btn: ['确认', '取消'],
                        title: title,
                        area: [width, height],
                        content: url,
                        yes: function (index, layero) {
                            var iframeWin = window[layero.find('iframe')[0]['name']];
                            iframeWin.$("#" + form_id).submit();
                        }
                    });
                },
                'click .btn-del': function (e, value, row, index) {
                    var action = $(this).attr("data-url");
                    var checkbox_id = [];
                    var title = "";

                    checkbox_id[0] = $(this).attr("data-id");
                    title = "确定删除此项?";

                    if (checkbox_id.length > 0) {
                        layer.confirm(title, function (index) {
                            $.get(action, function (data) {
                                backend.layer_msg(data.status, data.info, 1);
                            });
                        });
                    }
                },
                'click .btn-disable': function (e, value, row, index) {
                    var action = $(this).attr("data-url");
                    var checkbox_id = [];
                    var title = "";

                    checkbox_id[0] = $(this).attr("data-id");
                    title = "确定禁用此项?";

                    if (checkbox_id.length > 0) {
                        layer.confirm(title, function (index) {
                            console.log(tableconfig);
                            $.get(action, function (data) {
                                backend.layer_msg(data.status, data.info, 1);
                            });
                        });
                    }
                },

                'click .btn-release': function (e, value, row, index) {
                    var action = $(this).attr("data-url");
                    var checkbox_id = [];
                    var title = "";

                    checkbox_id[0] = $(this).attr("data-id");
                    title = "确定发布此项?";

                    if (checkbox_id.length > 0) {
                        layer.confirm(title, function (index) {
                            $.get(action, function (data) {
                                backend.layer_msg(data.status, data.info, 1);

                            });
                        });
                    }
                },
                'click .btn-back': function (e, value, row, index) {
                    var action = $(this).attr("data-url");
                    var checkbox_id = [];
                    var title = "";

                    checkbox_id[0] = $(this).attr("data-id");
                    title = "确定退回此项礼物赠送?";

                    if (checkbox_id.length > 0) {
                        layer.confirm(title, function (index) {
                            console.log(tableconfig);
                            $.get(action, function (data) {
                                backend.layer_msg(data.status, data.info, 1);
                            });
                        });
                    }
                }
            }
        },
        // 单元格数据格式化
        formatter: {
            status: function (value, row, index) {
                //颜色状态数组,可使用red/yellow/aqua/blue/navy/teal/olive/lime/fuchsia/purple/maroon
                var colorArr = {1: 'normal', 0: 'yc',normal: 'normal', hidden: 'yc', deleted: 'danger', locked: 'info'};
                var colorTitle = {1: '正常', 0: '隐藏',normal: '正常', hidden: '隐藏', deleted: '删除', locked: '锁定'};
                //如果字段列有定义custom
                if (typeof this.custom !== 'undefined') {
                    colorArr = $.extend(colorArr, this.custom);
                }
                value = value === null ? '' : value.toString();
                var color = value && typeof colorArr[value] !== 'undefined' ? colorArr[value] : 'primary';
                var title = colorTitle[value];
                value = value.charAt(0).toUpperCase() + value.slice(1);
                //渲染状态
                var html = '<span class="text-' + color + '"><i class="fa fa-circle"></i> ' + title + '</span>';
                return html;
            },
            datetime: function (value, row, index) {
                return value > 0  ? moment(parseInt(value) * 1000).format("YYYY-MM-DD HH:mm:ss") : "-";
            },
            operate: function (value, row, index) {
                var button = [];

                $.each(Config.operation, function (action, action_data) {
                    //样式名称
                    var class_name = action_data.class;
                    //url地址
                    var url = action_data.url;
                    //操作方法名称
                    var text = action_data.text;
                    //图标
                    var icon = "";

                    if (action == "detail") {
                        icon = action_data.icon;
                        icon = "<i class='"+icon+"'></i>&nbsp;"
                    }
                    var extend = ["data-title='" + text + "'"];
                    $.each(action_data.data, function (key, value) {

                        //判断如果url为空则自动补充
                        if (value == "" && key == 'url') {
                            value = url;
                            if (action == "edit" || action == "detail") {
                                value += '/id/' + row.id;
                            } else if (action == "delete" || action == "back") {
                                value += '/ids/' + row.id;
                            }
                        }
                        //判断如果表单id为空则自动补充
                        if (key == 'form-id' && value == "") {
                            value = (url + '/id/' + row.id).replace(/\//g, '');
                        }
                        extend.push("data-" + key + "=" + "'" + value + "'")
                    });
                    button.push("<a href='javascript:;' class='" + class_name + "' " + extend.join(" ") + " >" + icon+text + "</a>");
                });
                return button.join("")
            }
        }
    }
};