// 全选/取消全部
$(".from-check-all").click(function () {
    if (this.checked == true) {
        $(".checkbox-id").each(function () {
            this.checked = true;
        });
    } else {
        $(".checkbox-id").each(function () {
            this.checked = false;
        });
    }
});

//绑定搜索显示按钮
$("#searBtn").on('click', function () {
    $(".table-search").toggle(800);
});

//后台操作类
var backend = {
    /**
     * 刷新验证码方法
     * @param id
     */
    refresh_verify_bind: function (id) {
        $(id).on('click', function (e) {
            var img = $(this).attr("src");
            $(this).attr("src", img + '/rand/' + Math.random());
        });
    },
    /**
     * 绑定回车事件
     * @param id
     * @param form_id
     */
    keydown_bind: function (id, form_id) {
        $(id).on('keydown', function (e) {
            var curKey = e.which;
            if (curKey == 13) {
                $(form_id).click();
            }
        });
    },
    /**
     * 登录表单绑定事件
     * @param form_id
     * @param action
     */
    login_form_bind: function (form_id, action, verify_id) {

        $(form_id).on('click', function () {
            var username = $('input[name=username]').val();
            var password = $('input[name=password]').val();
            var verify = $('input[name=verify]').val();
            if (username == '') {
                layer.msg('用户名不能为空', {time: 1000});
                return
            }
            if (password == '') {
                layer.msg('密码不能为空', {time: 1000});
                return
            }
            if (verify == '') {
                layer.msg('验证码不能为空', {time: 1000});
            }
            $.post(action, {username: username, password: password, verify: verify}, function (data) {
                if (data.status != '1') {
                    var img = $(verify_id).attr("src");
                    //console.log(img);
                    $(verify_id).attr("src", img + '/rand/' + Math.random());
                    layer.msg(data.content, {time: 1000});
                } else {
                    window.location.href = data.url;
                }

            }, 'json')
        });
    },
    /**
     * 获取数组数量
     * @param o 数组对象
     * @returns {*}
     */
    array_count: function (o) {
        var t = typeof o;
        if (t == 'string') {
            return o.length;
        } else if (t == 'object') {
            var n = 0;
            for (var i in o) {
                n++;
            }
            return n;
        }
        return false;
    },

    confirm_bind: function (id,title) {

        $(id).on('click', function () {
            var action = $(this).attr("data-url");
            var uid = $(this).attr("data-id");
            action = action + '/uid/' + uid;
            layer.confirm(title, function (index) {
                $.get(action, function (data) {
                    backend.layer_msg(data.status, data.info, 0);
                    window.location.reload();
                });
            });
        });
    },
    select_change_bind: function (id, value) {
        $(id).val(value);

        $(id).on('change', function () {
            var action = $(this).attr("data-url");
            var title = $(this).attr("data-title");
            var uid = $(this).attr("data-id");

            action = action + '/uid/' + uid + '/level/' + $(this).val();

            layer.confirm(title, function (index) {
                $.get(action, function (data) {
                    backend.layer_msg(data.status, data.info, 0);
                    window.location.reload();
                });
            });
        });
    },
    /**
     * 绑定删除按钮
     * @param id
     */
    delete_bind: function (id) {
        $(id).on('click', function () {
            var action = $(this).attr("data-url");
            var checkbox_id = [];

            $.each(tableconfig.bootstrapTable('getSelections'), function (index, value) {
                checkbox_id.push(value.id);
            });

            var title = "确定删除选中的 " + backend.array_count(checkbox_id) + " 项?";

            if (checkbox_id.length > 0) {
                layer.confirm(title, function (index) {
                    //console.log(checkbox_id);
                    $.get(action + '/ids/' + checkbox_id.join(','), function (data) {
                        backend.layer_msg(data.status, data.info, 1);
                    });
                });
            }
        });
    },
    /**
     * 绑定窗口调用方法
     * @param title    窗口标题
     * @param id       按钮id
     */
    layer_bind: function (id) {
        $(id).on('click', function () {
            var title = $(this).attr('data-title');
            var width = $(this).attr('data-width');
            var height = $(this).attr('data-height');
            var url = $(this).attr('data-url');
            var form_id = $(this).attr('data-form-id');

            layer.open({
                type: 2,
                shade: 0.5,
                shadeClose: true,
                btn: ['确认', '取消'],
                title: title,
                area: [width, height],
                content: [url , 'yes'],
                yes: function (index, layero) {
                    //console.log(url);
                    var iframeWin = window[layero.find('iframe')[0]['name']];
                    //console.log(layero.find('iframe')[0]['name']);
                    //console.log(iframeWin);
                    iframeWin.$("#" + form_id).submit();
                }
            });
        });
    },

    /**
     * 调用layer提示框 根据状态判断是否关闭父窗口
     * @param status  1 成功 0 失败
     * @param message 提示框信息
     */
    layer_msg: function (status, message, is_pagefirst, is_parent) {
        //获取子窗口索引
        var index = parent.layer.getFrameIndex(window.name);
        var page_first = {};

        //返回信息
        parent.layer.msg(message, {time: 1000}, function () {
            //如果成功则刷新父页面
            if (is_parent == 1) {
                if (status > 0) {
                    parent.layer.close(index);
                    parent.location.reload();
                }
            } else {
                if (status > 0) {
                    parent.layer.close(index);
                    if (is_pagefirst == 1) {
                        page_first['pageNumber'] = 1;
                    }
                    //window.location.href = data.url;
                    parent.tableconfig.bootstrapTable('refreshOptions', page_first);
                }
            }

        })
    },
    /**
     * 设置nice-validate插件的规则
     * @param form_id  表单id
     * @param validate 规则对象
     */
    form_set_validate: function (id, validate) {
        // 验证规则
        $('#' + id).validator({
            dataFilter: function (data) {
                if (data.status === 1) return "";
                else return data.info;
            },
            fields: JSON.parse(validate)
        });
    },


    /**
     * from表单提交事件
     * @param action  提交的网址
     * @param param    提交数据
     */
    form_post: function (action, param, is_parent) {
        //表单验证通过后进行post提交
        //console.log(action);
        $.post(action, param, function (data) {
            backend.layer_msg(data.status, data.info, 0, is_parent)
        });
    },
    /**
     * 表单验证方法绑定 调用submit时触发事件
     * @param form_id 表单id
     * @param action  表单提交url
     */
    form_validate_bind: function (id, action, is_parent) {

        //console.log(id);
        //将验证方法绑定到表单对象上
        $('#' + id).on('valid.form', function (e, result) {
            //console.log(action);
            backend.form_post(action, $(this).serialize(), is_parent)
            return false;
        });
    },

    /**
     * 调用表单验证
     * @param form_id 表单id
     * @param action  表单提交url
     */
    form_validate: function (id, action, validate, is_parent) {

        //加入验证规则
        //console.log(id, action, validate, is_parent)
        this.form_set_validate(id, validate);

        //将表单绑定在验证事件上
        this.form_validate_bind(id, action, is_parent);
    },
    /**
     * 将ul绑定到ztree插件上
     * @param id
     * @param data
     */
    ztree_bind: function (id, data) {
        $.fn.zTree.init($(id), {
            check: {
                //开启checkbox
                enable: true
            },
            data: {
                simpleData: {
                    //使用简单模型
                    enable: true
                }
            },
            callback: {
                //点击节点触发事件
                onCheck: backend.ztree_onclick
            }
        }, data);
    },
    /**
     * 每次点击节点后， 弹出该节点的 tId、name 的信息
     * @param event
     * @param treeId
     * @param treeNode
     */
    ztree_onclick: function (event, treeId, treeNode) {
        var treeObj = $.fn.zTree.getZTreeObj(treeId),
            nodes = treeObj.getCheckedNodes(true),
            rules = [];
        for (var i = 0; i < nodes.length; i++) {
            rules.push(nodes[i].id);
        }
        $("#group_rules").val(rules.join(','));
        console.log(rules);
    },
    /**
     * 通过select重新加载ztree
     * @param id
     */
    ztree_select_bind: function (id, ztree_id) {
        $(id).on('change', function (e, result) {
            var action_url = $(this).attr('data-url');
            var id = $(this).attr('data-id');
            var pid = $(this).val();

            param = {pid: pid};
            if (id != "") {
                param['id'] = id;
            }
            if (pid) {
                $.post(action_url, param, function (data) {
                    //console.log(data);
                    backend.ztree_bind(ztree_id, JSON.parse(data))
                });
            }
            return false;
        });
    },
    search_bind: function (id, is_request) {
        $(id).on('click', function () {
            var filter = {};
            var option = {};
            var rand = Math.random();
            $(".search-field").each(function () {
                if ($(this).val()) {
                    //过滤条件
                    filter[$(this).attr("data-id")] = $(this).val();
                    //过滤操作符
                    option[$(this).attr("data-id")] = $(this).attr("serach-option");
                }
            });
            if (is_request === 1) {
                $($(this).attr('data-search-hidden-class')).hide();
                $($(this).attr('data-table-hidden-class')).show();
                //console.log($(this).attr('data-search-again-id'));
                $($(this).attr('data-search-again-id')).show();
                //设置查询参数
                var options = tableconfig.bootstrapTable('getOptions');
                options.queryParams = function (params) {
                    params['filter'] = JSON.stringify(filter);
                    params['option'] = JSON.stringify(option);
                    return params;
                }

                //重新刷新表格
                tableconfig.bootstrapTable('refreshOptions', {pageNumber: 1});
            } else {
                //设置查询参数
                var options = tableconfig.bootstrapTable('getOptions');
                options.queryParams = function (params) {
                    params['filter'] = JSON.stringify(filter);
                    params['option'] = JSON.stringify(option);
                    return params;
                }

                //重新刷新表格
                tableconfig.bootstrapTable('refreshOptions', {pageNumber: 1});
            }

        })
    },
    /**
     * 选择框联动方法
     * @param id
     * @param data
     */
    select_linkage: function (id, data) {
        $(id).on('change', function () {
            var options = [];
            $.each(data[$(this).val()], function (index, value) {
                options.push('<option value="' + index + '">' + value + '</option>');
            });

            var linkage = $(this).attr('data-linkage');
            $(linkage).html(options);
        });
    },
    status_set: function (id) {
        var url = $(id).attr('data-url');
        var field = $(id).attr('data-field');
        var value = $(id).attr('data-value');
        var title1 = $(id).attr('data-title').split('|');
        $(id).html(title1[value]);

        if(value == 1 ){
            $(id).css({'border':'.1rem solid #18bc9c','background':'#18bc9c'});
        }else{
            $(id).css({'border':'.1rem solid #ff6600','background':'#ff6600'});
        }
        $(id).on('click', function () {
            var nickname = $(id).attr('data-nickname');
            url = url + '/field/' + field + '/value/' + value;
            var title = "确认对玩家" + $(id).attr('data-nickname') + " " + title1[value];
            layer.confirm(title, function (index) {
                $.get(url, function (data) {
                    backend.layer_msg(data.status, data.info, 0);
                    window.location.reload();
                });
            })

        });

    },

    /**
     *二次确认提示框
     */
    showMsg: function (options) {
        var defaults = {
            title: '提示',
            width: 600,
            height: 800,
            content: '这是一个提示',
            okBtnText: '确定'
        }
        var opts = $.extend({}, defaults, options);
        var $this = $(this);
        $this.html('');
        var $html;
        bindData();
        show();
        function bindData() {
            var $cancelBtn = $('<button type="button" class="btn btn-primary">确定</button>');
            $html = $('<div class="modal fade" tabindex="-1" role="dialog"  aria-labelledby="exampleModalLabel">'
                + '<div class="modal-dialog" style="width:' + opts.width + 'px" role="document">'
                + '<div class="modal-content">'
                + ' <div class="modal-header">'
                + '  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>'
                + '<h4 class="modal-title" id="exampleModalLabel">' + opts.title + '</h4>'
                + ' </div>'
                + '<div myId="content" class="modal-body">'
                + '</div>'
                + '<div myId="dialog-btn" class="modal-footer">'
                + ' </div>'
                + '</div>'
                + '</div>'
                + ' </div>');
            $cancelBtn.click(function () {
                $html.modal('show').modal('hide');
            });
            $html.find("div[myId='content']").append(opts.content);
            $html.find("div[myId='dialog-btn']").append($cancelBtn);
            $this.append($html);
        }
        function show() {
            $html.modal('toggle').modal('show');
        }
    }


}



