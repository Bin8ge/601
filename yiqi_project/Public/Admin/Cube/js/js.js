$(document).ready(function(){
	// $("#box").find("#youxiguanli").css("marginBottom","50px");
	
    $('#box .panel-heading a').click(function (e) {

        e.preventDefault();
        $('#box .panel-heading a').removeClass('active');
        $(this).addClass('active');
        var hrefVal = $(this).attr('href');
        var headA = $(hrefVal).hasClass("in");
        $('#box .panel-heading a').children("span").removeClass('glyphicon glyphicon-minus add');
        $('#box .panel-heading a').children("span").addClass('glyphicon glyphicon-plus add');
        if(headA==true){
            //处理其他
            $(this).children("span").removeClass("glyphicon glyphicon-minus add");
            $(this).children("span").addClass("glyphicon glyphicon-plus add");
        }else {
            //处理其他
            $(this).children("span").removeClass("glyphicon glyphicon-plus add");
            $(this).children("span").addClass("glyphicon glyphicon-minus add");
        }

    });
	
	 $("#toggleBtn").click(function(){
		     // var contW = $(".content-wrap").width();
			 // alert(contW);
		     var asideNav = $(".col-lg-menu").css("display");
			 
		     if(asideNav == "block"){
					$(".col-lg-menu").hide();
					$(".content-wrap").css("width","100%")
			}else{
					$(".col-lg-menu").show();
					$(".content-wrap").css("width","")
					}
		  })



})

/* 返回 */
function backPage(){
    history.back(-1);
};

$(function() {
    $("#main_menu").mmenu({
        counters: true,
        classes: " mm-zoom-menu mm-zoom-panels",
        searchfield: false,
        footer: {
            add: true,
            content: "Powered by Real @2014"
        },
    });
});