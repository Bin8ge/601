//删除
function del_btn(postId,editData,url,msg,successMsg){
    msg = msg||'数据删除后将无法恢复，您确定吗？';
    successMsg = successMsg||'已删除';
    layer.confirm(msg, {
        btn: ['确定','取消'] //按钮
    }, function(){
        $.ajax({
            //几个参数需要注意一下
            type: "POST",//方法类型
            dataType: "json",//预期服务器返回的数据类型
            url: url ,//url
            data: {postId:postId,editData:editData},
            success: function (result) {
                if (result.resultCode == 200) {
                    layer.msg(successMsg,{icon: 1});
                    window.parent.location.reload();
                }else{
                    layer.msg(result.message,{icon: 0});
                }
            },
            error : function() {
                layer.msg('异常',{icon: 2});
            }
        });
    }, function(){
        layer.msg('谢谢提醒喔');
    });
}

/**
 * 添加 编辑
 * @param title   页面标题
 * @param tpl     html页面
 * @param fromId  from表单ID
 * @param ajaxUrl ajax请求地址
 */
function add_btn(title,tpl,fromId,ajaxUrl,isRefresh){
    isRefresh = isRefresh || 0;
    layer.open({
        type: 2,
        title: title,
        maxmin: true,
        shadeClose: true, //点击遮罩关闭层
        btn: ['确认', '取消'],
        area : ['800px' ,'600px'],
        content: tpl,
        yes:function (index) {
            //console.log(ajaxUrl);
            yesAjax(index,fromId,ajaxUrl,isRefresh)
        }
    });
}

//保存ajax
function yesAjax(index, fromId, ajaxUrl, isRefresh) {
    var inputForm = $(window.frames["layui-layer-iframe" + index].document).contents().find('#' + fromId);
    inputForm.click(
        $.ajax({
            //几个参数需要注意一下
            type: "POST",//方法类型
            dataType: "json",//预期服务器返回的数据类型
            url: ajaxUrl,//url
            data: inputForm.serialize(),
            success: function (result) {
                if (result.resultCode == 200) {
                    layer.msg('保存成功', {icon: 1});
                    if (isRefresh === 0) {
                        window.parent.location.reload();
                    }
                } else {
                    layer.msg(result.message, {icon: 0});
                }
            },
            error: function () {
                layer.msg('异常', {icon: 2});
            }
        })
    )
}


/**
 * 分页
 * @param p      当前页码
 * @param url    ajax请求地址
 * @param param  传递参数
 * @param tplId   替换模板ID
 */
function indexAjaxComm(p,url,param,tplId){
    //把数据传递到要替换的控制器方法中，这里你还可以传入查询字段，比如phone，name等，后台通过【是否为空】来做过滤
    param=param||$('#searForm').serialize();
    //console.log(url);
    tplId=tplId||'replace-tpl';
    $.ajax({
        url:url,
        type:"GET",
        async:true,
        dataType:"JSON",
        data:'p='+p+'&'+param,　　//把查询字段和值 通过这里传给后台ajax，实现多条件ajax查询分页
        success:function(data){
            if (data.resultCode == 400)
            {
                layer.msg(data.message,{icon: 0});
            }
            //用get方法发送信息到ajax中的deal_show_comment方法
            $("#"+tplId).replaceWith(data.data.content);  //html块替换html的div
        },
        error:function(data){
            //console.log(data);
            layer.msg("ajax not run~",{icon: 0});
        }
    });
}


/**
 * 分页
 * @param p      当前页码
 * @param url    ajax请求地址
 * @param param  传递参数
 * @param tplId   替换模板ID
 */
function indexAjaxComms(p,url,param,tplId){
    //把数据传递到要替换的控制器方法中，这里你还可以传入查询字段，比如phone，name等，后台通过【是否为空】来做过滤
    param=param||$('#searForm').serialize();
    //console.log(url);
    tplId=tplId||'replace-tpl';
    $.ajax({
        url:url,
        type:"GET",
        async:false,
        dataType:"JSON",
        data:'p='+p+'&'+param,　　//把查询字段和值 通过这里传给后台ajax，实现多条件ajax查询分页
        success:function(data){
            if (data.resultCode == 400)
            {
                layer.msg(data.message,{icon: 0});
            }
            //用get方法发送信息到ajax中的deal_show_comment方法
            $("#"+tplId).replaceWith(data.data.content);  //html块替换html的div
        },
        error:function(data){
            //console.log(data);
            layer.msg("ajax not run~",{icon: 0});
        }
    });
}

/**
 * 公共ajax方法
 * @param url   ajax请求地址
 * @param data  参数
 */
function ajaxComm(url,data,loc_url) {
    $.ajax({
        type: "POST",//方法类型
        dataType: "json",//预期服务器返回的数据类型
        url: url ,//url
        data: data,
        success: function (result) {
            if (result.resultCode == 200) {
                layer.msg('保存成功', {icon: 1});

                if (loc_url){
                    window.location.href = loc_url;
                }
            }else{
                layer.msg(result.message,{icon: 0});
            }
        },
        error : function() {
            layer.msg('异常',{icon: 2});
        }
    })
}


//搜索切换
function selectBtn(data) {
    var name = $(data).val();
    $('#change-input').attr('name',name)
}


//初始化
function init(url,param){
    indexAjaxComm(1,url,param);     //初始化页面 init_id==1
};

//查看
function view_btn(title,tpl,fromId,id,ajaxUrl){
    layer.open({
        type: 2,
        title: title,
        maxmin: true,
        shadeClose: true, //点击遮罩关闭层
        btn: ['确认', '取消'],
        area : ['800px' ,'600px'],
        content: tpl,
        
    });
}

function show_btn(title,tpl){
    layer.open({
        type: 2,
        title: title,
        maxmin: true,
        shadeClose: true, //点击遮罩关闭层
        area : ['800px' ,'600px'],
        content: tpl
    });
}

//匹配参数
function GetQueryString(name)
{
    //alert(02)
    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);//search,查询？后面的参数，并匹配正则
    if(r!=null)return  unescape(r[0]); return null;
}
function getQueryString2(name) {
    var reg = new RegExp('(^|&)' + name + '=([^&]*)(&|$)', 'i');
    var r = window.location.search.substr(1).match(reg);
    if (r != null) {
        return unescape(r[2]);
    }
    return null;
}
