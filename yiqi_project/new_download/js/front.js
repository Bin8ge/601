//editRank()//设置iframe响应式高度
function setIframeHeight(iframe) {
if (iframe) {
var iframeWin = window.innerHeight;
$("#menuFrame").height(iframeWin-80);
}
};
 window.onload = function(){
	 setIframeHeight(document.getElementById('menuFrame'));
 }
 //点击移除按钮显示屏蔽名单
function showRemove(){
    $(".remove-container").show(800);
    $("#rankList").hide();
};
// 点击排行榜显示排行榜列表
function showRank(){
	$("#rankList").show(800);
	$(".remove-container").hide();
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
})
})
 $(function(){
 //点击搜索按钮展开、隐藏搜索区域	 
  $("#searBtn").click(function(){
   $(".table-search").toggle(800);
  });
  $("#searBtn1").click(function(){
   $(".table-search1").toggle(800);
  });
  $("#delBtn").click(function(){
	  var txt=  "确定要删除吗？";
		window.wxc.xcConfirm(txt, window.wxc.xcConfirm.typeEnum.confirm);
  }),
   $(".close").click(function(){
   $("#addModal").hide();
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
 });
 
//以下为添加、编辑弹窗展示
function addRoleGroup(){
      layer.open({
      type: 2,
      title: '添加',
      maxmin: true,
      shadeClose: true, //点击遮罩关闭层
	  btn: ['确认', '取消'],
      area : ['800px' ,'600px'],
      content: 'addRoleGroup.html'
    });
   
   
}
// 添加规则管理
function addRuleManage(){
   layer.open({
      type: 2,
      title: '添加',
      maxmin: true,
      shadeClose: true, //点击遮罩关闭层
	  btn: ['确认', '取消'],
      area : ['800px' ,'600px'],
      content: 'addRuleManage.html'
    });
}
// 添加管理员列表
function addManagerList(){
     layer.open({
      type: 2,
      title: '添加',
      maxmin: true,
      shadeClose: true, //点击遮罩关闭层
	  btn: ['确认', '取消'],
      area : ['800px' ,'600px'],
      content: 'addManagerList.html'
    });
}
// 显示管理员日志
function showDetail(){
      layer.open({
      type: 2,
      title: '详情',
      maxmin: true,
      shadeClose: true, //点击遮罩关闭层
	  btn: ['关闭'],
      area : ['800px' ,'600px'],
      content: 'addManagerLog.html'
    });
} 
 
//重置功能
function formReset()
{
document.getElementById("form").reset();
}
//搜索栏重置功能
function searFormReset()
{
document.getElementById("searForm").reset();
}
//删除提示
function delBtn(){
  layer.confirm('数据删除后将无法恢复，您确定吗？', {
  btn: ['确定','取消'] //按钮
}, function(){
  layer.msg('已删除', {icon: 1});
}, function(){
  layer.msg('谢谢提醒喔');
});
   }

//返回
function backPage(){
 history.back(-1);
};

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
  
// 取消点控
function cancleBtn(){
    layer.open({
      type: 2,
      title: '点控终止提示',
      maxmin: true,
      shadeClose: true, //点击遮罩关闭层
	  btn: ['确认', '取消'],
      area : ['800px' ,'600px'],
      content: 'canclePointContrl.html'
    });
  };
  
//点控终止原因
function abortReason() {
	  layer.open({
      type: 2,
      title: '点控终止原因',
      maxmin: true,
      shadeClose: true, //点击遮罩关闭层
	  btn: ['关闭'],
      area : ['800px' ,'600px'],
      content: 'pointAbortReason.html'
    });
};
 // 用户点控操作
function userPoint(){
    layer.open({
      type: 2,
      title: '用户点控',
      maxmin: true,
      shadeClose: true, //点击遮罩关闭层
	  btn: ['确认', '取消'],
      area : ['800px' ,'600px'],
      content: 'userPoint.html',
	  yes: function(index, layero) {
		layer.open({
		type: 2,
		title: '点控终止提示',
		maxmin: true,
		shadeClose: true,
	    btn: ['确认', '取消'],
		area : ['532px' ,'608px'],
		content: 'canclePointContrl.html'
    });
            // window.location.href='pointAbortReason.html';
                        },

    });
  }; 
//用户类型修改
function editCustomerType(){
    layer.open({
      type: 2,
      title: '用户类型修改',
      maxmin: true,
      shadeClose: true, //点击遮罩关闭层
	  btn: ['确认', '取消'],
      area : ['500px' ,'500px'],
      content: 'editCustomerType.html'
    });
}
// 添加新手卡
function createCard(){
    layer.open({
      type: 2,
      title: '生成新手卡',
      maxmin: true,
      shadeClose: true, //点击遮罩关闭层
	  btn: ['确认', '取消'],
      area : ['800px' ,'600px'],
      content: 'createCard.html'
    });
  }; 
  
 //解绑手机提示
function untiePhone(){
  layer.confirm('您确定解除手机绑定吗？', {
  btn: ['确定','取消'] //按钮
}, function(){
  layer.msg('解除成功', {icon: 1});
}, function(){
  layer.msg('谢谢提醒喔');
});
   };
   
//关联锁定
 function glLock(){
    layer.open({
      type: 2,
      title: '批量锁定用户',
      maxmin: true,
      shadeClose: true, //点击遮罩关闭层
	  btn: ['确认', '取消'],
      area : ['530px' ,'350px'],
      content: 'lockUser.html'
    });
  };
  
//确认修改
function confirmEdit(){
  layer.confirm('您确定要进行用户类型修改吗？', {
  btn: ['确定','取消'] //按钮
}, function(){
  layer.msg('修改成功', {icon: 1});
}, function(){
  layer.msg('谢谢提醒喔');
});
};

//新手卡列表中查看
 function checkTop(){
    layer.open({
      type: 2,
      title: '礼包内容',
      maxmin: true,
      shadeClose: true, //点击遮罩关闭层
	  btn: ['确认', '取消'],
      area : ['361px' ,'246px'],
      content: 'gistContent.html'
    });
  };

//自动控制参数编辑
function autoEdit(){
	  layer.open({
      type: 2,
      title: '参数编辑',
      maxmin: true,
      shadeClose: true, //点击遮罩关闭层
	  btn: ['确认', '取消'],
      area : ['800px' ,'600px'],
      content: 'editAutoControl.html'
    });
}
 
//排名管理中变更名次
function editRank(){
	  layer.open({
      type: 2,
      title: '变更名次',
      maxmin: true,
      shadeClose: true, //点击遮罩关闭层
	  btn: ['确认', '取消'],
      area : ['800px' ,'500px'],
      content: 'changeRank.html'
    });
} 
// 查看玩家填写的标题内容
 function checkContent(){
	  layer.open({
      type: 2,
      title: '玩家填写的标题内容',
      maxmin: true,
      shadeClose: true, //点击遮罩关闭层
	  btn: ['发送'],
      area : ['800px' ,'600px'],
      content: 'titleContent.html'
    });
 }
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
 //添加游戏公告
 function addNotice(){
	  layer.open({
      type: 2,
      title: '添加公告',
      maxmin: true,
      shadeClose: true, //点击遮罩关闭层
	  btn: ['确认', '取消'],
      area : ['800px' ,'600px'],
      content: 'addNotice.html'
    });
 }
 //添加跑马灯
function addHorse(){
	  layer.open({
      type: 2,
      title: '添加跑马灯',
      maxmin: true,
      shadeClose: true, //点击遮罩关闭层
	  btn: ['确认', '取消'],
      area : ['800px' ,'600px'],
      content: 'addHorseRaceLamp.html'
    });
}
// 修改实时值
function eidtRealTimeVal(){
	  layer.open({
      type: 2,
      title: '修改',
      maxmin: true,
      shadeClose: true, //点击遮罩关闭层
	  btn: ['确认', '取消'],
      area : ['400px' ,'200px'],
      content: 'eidtRealTimeVal.html'
    });
}
// 增加限制
function addLimit(){
	  layer.open({
      type: 2,
      title: '修改',
      maxmin: true,
      shadeClose: true, //点击遮罩关闭层
	  btn: ['确认', '取消'],
      area : ['900px' ,'500px'],
      content: 'addLimit.html'
    });
};
// 玩家详情>更改用户类型>添加新团队
function addTeamInner(){
	$(".edit-customer-form").show(500);
}
// 取消创建团队
function cancelToFound(){
	$(".edit-customer-form").hide(500);
}
// 添加新团队
function addTeam(){
	  layer.open({
      type: 2,
      title: '添加团队',
      maxmin: true,
      shadeClose: true, //点击遮罩关闭层
	  btn: ['确认', '取消'],
      area : ['800px' ,'400px'],
      content: 'addTeam.html'
    });
}
// 发送邮件
function sendEmail(){
	  layer.open({
      type: 2,
      title: '发送邮件',
      maxmin: true,
      shadeClose: true, //点击遮罩关闭层
	  btn: ['发送'],
      area : ['800px' ,'600px'],
      content: 'sendEmail.html'
    });
}
// 筹码赠送
function chipGift(){
	  layer.open({
      type: 2,
      title: '筹码赠送',
      maxmin: true,
      shadeClose: true, //点击遮罩关闭层
	  btn: ['确认', '取消'],
      area : ['800px' ,'400px'],
      content: 'chipGift.html'
    });
}
// 编辑活动
function editActive(){
	  layer.open({
      type: 2,
      title: '编辑活动',
      maxmin: true,
      shadeClose: true, //点击遮罩关闭层
	  btn: ['确认', '取消'],
      area : ['800px' ,'600px'],
      content: 'editActive.html'
    });
}