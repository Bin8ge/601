$('#add-btn').click(function add_btn() {
    $.ajax({
        //几个参数需要注意一下
        type: "POST",//方法类型
        dataType: "json",//预期服务器返回的数据类型
        url: "transaction/index" ,//url
        data: $('#add-form').serialize(),
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
})
$('#ref-btn').click(function ref_btn() {
    window.location.reload();
})