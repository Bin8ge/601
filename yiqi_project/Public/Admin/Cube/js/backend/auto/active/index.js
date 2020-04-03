function submit_btn(id) {
    $.ajax({
        //几个参数需要注意一下
        type: "POST",//方法类型
        dataType: "json",//预期服务器返回的数据类型
        url: "active/edit" ,//url
        data: $(id).serialize(),
        success: function (result) {
            if (result.resultCode == 200) {
                layer.msg('保存成功', {icon: 1});
            }else{
                layer.msg(result.message,{icon: 0});
            }
        },
        error : function() {
            layer.msg('异常',{icon: 2});
        }
    });
}

/*function add_run() {
    $.ajax({
        //几个参数需要注意一下
        type: "POST",//方法类型
        dataType: "json",//预期服务器返回的数据类型
        url: "/Admin/script/script/fund" ,//url
        success: function (result) {
            if (result.resultCode == 200) {
                layer.msg(result.message, {icon: 1});
            }else{
                layer.msg(result.message,{icon: 0});
            }
        },
        error : function() {
            layer.msg('异常',{icon: 2});
        }
    });
}*/
