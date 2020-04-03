//初始化
$(function(){
    var init_id = 1;
    indexAjaxComm(init_id,"contact/index");     //初始化页面 init_id==1
});

// 查看玩家填写的标题内容
function seeBtn( tpl ,fromId ,ajaxUrl,p){
    layer.open({
        type: 2,
        title: '聊天内容',
        maxmin: true,
        shadeClose: true, //点击遮罩关闭层
        btn: ['发送'],
        area : ['800px' ,'90%'],
        content: tpl,
        yes:function (index) {
            yesAjaxs(index,fromId,ajaxUrl)
        },
        end: function () {
            indexAjaxComm(p,'contact/index',$('#searForm').serialize())
        }

    });
}

function yesAjaxs(index,fromId,ajaxUrl) {
    var inputForm = $(window.frames["layui-layer-iframe" + index].document).contents().find('#'+fromId);
    inputForm.click(
        $.ajax({
            //几个参数需要注意一下
            type: "POST",//方法类型
            dataType: "json",//预期服务器返回的数据类型
            url: ajaxUrl ,//url
            data: inputForm.serialize(),
            success: function (result) {
                console.log(result);//打印服务端返回的数据(调试用)
                if (result.resultCode == 200) {
                    layer.msg('发送成功', {icon: 1});
                    window['layui-layer-iframe' + index].location.reload();
                }else{
                    layer.msg(result.message,{icon: 0});
                }
            },
            error : function() {
                layer.msg("异常！",{icon: 2});
            }
        })
    )
}