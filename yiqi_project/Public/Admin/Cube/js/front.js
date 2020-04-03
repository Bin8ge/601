//设置iframe响应式高度
function setIframeHeight(iframe) {
if (iframe) {
var iframeWin = window.innerHeight;
$("#menuFrame").height(iframeWin-80);
}
};
 window.onload = function(){
	 setIframeHeight(document.getElementById('menuFrame'));
 }

$(function(){
    $("#close").click(function(){
        $(".remove-container").hide(1000);
    });
    $("#close1").click(function(){
        $("#rankList").hide(1000);
    });
    $("#totalBtn").click(function(){
        $("#recordTotal").toggle(800);
    });

});
 $(function() {
     //点击搜索按钮展开、隐藏搜索区域
     $("#searBtn").click(function () {
         $(".table-search").toggle(800);
     });
     $("#searBtn1").click(function () {
         $(".table-search1").toggle(800);
     });
     $("#delBtn").click(function () {
         var txt = "确定要删除吗？";
         window.wxc.xcConfirm(txt, window.wxc.xcConfirm.typeEnum.confirm);
     });
     $(".close").click(function () {
         $("#addModal").hide();
     });
 });


//用户类型选择
  $("#userType").on('change',function(){
    $("#confirmEdit").show(900);
 });
 // 房间详细人数展示
 $(".room1").click(function(){
     $("#room1").toggle();
   });
   $(".room2").click(function(){
     $("#room2").toggle();
   });
   $(".room3").click(function(){
     $("#room3").toggle();
   });
   $(".room4").click(function(){
     $("#room4").toggle();
   });
   //placeholderIE9以下无法显示的兼容性处理
   $("[placeholder]").focus(function() {
      var input = $(this);
      if (input.val() == input.attr("placeholder")) {
        input.val("");
        input.removeClass("placeholder");
      }
    })
    .blur(function() {
      var input = $(this);
      if (input.val() == "" || input.val() == input.attr("placeholder")) {
        input.addClass("placeholder");
        input.val(input.attr("placeholder"));
      }
    })
    .blur()
    .parents("form")
    .submit(function() {
      $(this).find("[placeholder]").each(function() {
        var input = $(this);
        if (input.val() == input.attr("placeholder")) {
          input.val("");
        }
      });
 });



//搜索栏重置功能
function searFormReset()
{
document.getElementById("searForm").reset();
}


//编辑资源
$(function (){
    $('#bjzy').on('click', function(){
    layer.open({
      type: 2,
      title: '编辑资源',
      maxmin: true,
      shadeClose: true, //点击遮罩关闭层
	  btn: ['确认', '取消'],
      area : ['800px' , '600px'],
      content: 'editResource.html'
    });
  });
  });

 


function feedback(){
	  layer.open({
      type: 2,
      title: '评分反馈',
      maxmin: true,
      shadeClose: true, //点击遮罩关闭层
	  btn: ['关闭'],
      area : ['600px' ,'400px'],
      content: 'serviceFeedBack.html'
    });
}

