/**
 * 获取后台数据并生成折线图
 * @param ajaxUrl      请求地址
 * @param ajaxparam    参数
 * @param title        大标题
 * @param ytitle       y轴标题
 * @param dtitle       鼠标移动标题
 * @param tplId        替换模板ID
 * @param dtype        图像类型 默认线性图像
 * @param plotLinesVal  一条延伸到整个绘图区的线，标志着轴中一个特定值。 == 0 没有 大于0 有
 */
function getData(ajaxUrl,ajaxparam,title,ytitle,dtitle,tplId,dtype) {
    dtype=dtype||'line';
    tplId=tplId||'replace-tpl';
    $.ajax({
        //几个参数需要注意一下
        type: "get",//方法类型
        dataType: "json",//预期服务器返回的数据类型
        url: ajaxUrl ,//url
        data: ajaxparam,
        success: function (result) {
            if (result.resultCode == 200) {
                $("#"+tplId).replaceWith(result.data.content);  //html块替换html的div
                $('#container').highcharts({
                    chart: {
                        //zoomType: 'x'
                    },
                    title: {
                        text: title,                  //大标题
                        style:{
                            color:"#666666",
                            fontSize:"16px"
                        }
                    },
                    credits:{
                        enabled: false // 禁用版权信息
                    },

                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle'
                    },

                    xAxis: {
                        labels: {
                            //rotation: 0,
                            style: {
                                color: '#6D869F',
                                fontWeight: 'bold'
                            },
                            axisLabel:{
                                interval:0,//0：全部显示，1：间隔为1显示对应类目，2：依次类推，（简单试一下就明白了，这样说是不是有点抽象）
                                rotate:-30,//倾斜显示，-：顺时针旋转，+或不写：逆时针旋转
                            },
                            //x:45,//调节x偏移
                            //y:-35,
                            //rotation:25//调节倾斜角度偏移
                        },


                        categories:result.data.time, /*["2018-07-01","2018-07-02","2018-07-03","2018-07-04","2018-07-05","2018-07-06","2018-07-07","2018-07-08","2018-07-09","2018-07-10","2018-07-11","2018-07-12","2018-07-13","2018-07-14","2018-07-15","2018-07-16"],*/
                    },
                    yAxis: {
                        title: {
                            text: ytitle
                        },
                        allowDecimals: false,            //控制数轴是否显示小数。
                        min: 0,                            //控制数轴的最小值
                        //max: 10000,                        //控制数轴的最大值
                    },
                    tooltip: {
                        formatter: function() {
                            return '<b>'+ this.x +'</b><br/>'+
                                this.series.name +': '+ this.y +'<br/>'
                            //+'Total: '+ this.point.stackTotal;
                        }
                    },
                    plotOptions: {
                        column: {
                            //stacking: 'normal',
                            pointPadding: 0,
                            borderWidth: 0
                        }
                    },
                    series: [{
                        name: dtitle,
                        type: dtype,
                        data:result.data.total, /*[10,100,0,0,0,0,0,0,5,7,0,0,0,0,0,0],*/
                    },],
                     /*dataZoom:{
                         realtime:true, //拖动滚动条时是否动态的更新图表数据
                         height:25,//滚动条高度
                         start:40,//滚动条开始位置（共100等份）
                         end:65//结束位置（共100等份）
                     }*/
                });
            }else{
                layer.msg(result.message,{icon: 0});
            }
        },
        error : function() {
            alert("异常！");
        }
    })
}

/**
 * 获取后台数据并生成折线图  带y轴标志线的图
 * @param ajaxUrl      请求地址
 * @param ajaxparam    参数
 * @param title        大标题
 * @param ytitle       y轴标题
 * @param dtitle       鼠标移动标题
 * @param tplId        替换模板ID
 * @param dtype        图像类型 默认线性图像
 * @param plotLinesVal  一条延伸到整个绘图区的线，标志着轴中一个特定值。 == 0 没有 大于0 有
 */
function getDatas(p,ajaxUrl,plotVal,ajaxparam,title,ytitle,dtitle,tplId,dtype) {
    ajaxparam=ajaxparam||$('#searForm').serialize();
    title=title||'库存变化';
    ytitle=ytitle||'血池';
    dtitle=dtitle||'血池';
    tplId=tplId||'replace-tpl';
    dtype=dtype||'line';
    plotVal=plotVal||0;
    $.ajax({
        //几个参数需要注意一下
        type: "get",//方法类型
        dataType: "json",//预期服务器返回的数据类型
        url: ajaxUrl ,//url
        data: 'p='+p+'&'+ajaxparam,
        success: function (result) {
            if (result.resultCode === 200) {
                $("#"+tplId).replaceWith(result.data.content);  //html块替换html的div
                $('#container').highcharts({
                    chart: {
                        //zoomType: 'x'
                    },
                    title: {
                        text: title,                  //大标题
                        style:{
                            color:"#666666",
                            fontSize:"16px"
                        }
                    },
                    credits:{
                        enabled: false // 禁用版权信息
                    },

                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle'
                    },

                    xAxis: {
                        labels: {
                            //rotation: 0,
                            style: {
                                color: '#6D869F',
                                fontWeight: 'bold'
                            },
                            axisLabel:{
                                interval:0,//0：全部显示，1：间隔为1显示对应类目，2：依次类推，（简单试一下就明白了，这样说是不是有点抽象）
                                rotate:-30,//倾斜显示，-：顺时针旋转，+或不写：逆时针旋转
                            }
                            //x:45,//调节x偏移
                            //y:-35,
                            //rotation:25//调节倾斜角度偏移
                        },
                        categories:result.data.time, /*["2018-07-01","2018-07-02","2018-07-03","2018-07-04","2018-07-05","2018-07-06","2018-07-07","2018-07-08","2018-07-09","2018-07-10","2018-07-11","2018-07-12","2018-07-13","2018-07-14","2018-07-15","2018-07-16"],*/
                    },
                    yAxis: {
                        title: {
                            text: ytitle
                        },
                        allowDecimals: false,            //控制数轴是否显示小数。
                        min: result.data.min,            //控制数轴的最小值
                        max: result.data.max,            //控制数轴的最大值
                        plotLines: [{   		         //一条延伸到整个绘图区的线，标志着轴中一个特定值。
                            color: '#FF0000',
                            dashStyle: 'Dash', //Dash,Dot,Solid,默认Solid
                            width: 2,
                            value: plotVal,  //y轴显示位置
                            zIndex: 5
                        }]

                    },
                    tooltip: {
                        formatter: function() {
                            return '<b>'+ this.x +'</b><br/>'+
                                this.series.name +': '+ this.y +'<br/>'
                            //+'Total: '+ this.point.stackTotal;
                        }
                    },
                    plotOptions: {
                        column: {
                            //stacking: 'normal',
                            pointPadding: 0,
                            borderWidth: 0
                        }
                    },
                    series: [{
                        name: dtitle,
                        type: dtype,
                        data:result.data.total, /*[10,100,0,0,0,0,0,0,5,7,0,0,0,0,0,0],*/
                    },],
                    /* dataZoom:{
                         realtime:true, //拖动滚动条时是否动态的更新图表数据
                         height:25,//滚动条高度
                         start:40,//滚动条开始位置（共100等份）
                         end:65//结束位置（共100等份）
                     }*/
                });
            }else{
                layer.msg(result.message,{icon: 0});
            }
        },
        error : function() {
            alert("数据异常！");
        }
    })
}


/**
 * 获取后台数据并生成折线图
 * @param ajaxUrl      请求地址
 * @param ajaxparam    参数
 * @param title        大标题
 * @param ytitle       y轴标题
 * @param dtitle       鼠标移动标题
 * @param tplId        替换模板ID
 * @param dtype        图像类型 默认线性图像
 * @param plotLinesVal  一条延伸到整个绘图区的线，标志着轴中一个特定值。 == 0 没有 大于0 有
 */
function getDataPie(ajaxUrl,ajaxparam,title,ytitle,dtitle,tplId,dtype) {
    dtype=dtype||'line';
    tplId=tplId||'replace-tpl';
    $.ajax({
        //几个参数需要注意一下
        type: "get",//方法类型
        dataType: "json",//预期服务器返回的数据类型
        url: ajaxUrl ,//url
        data: ajaxparam,
        success: function (result) {
            if (result.resultCode == 200) {
                $("#"+tplId).replaceWith(result.data.content);  //htm块替l换html的div

                $('#container').highcharts({
                    chart: {
                        //zoomType: 'x'
                    },
                    title: {
                        text: title,                  //大标题
                        style:{
                            color:"#666666",
                            fontSize:"16px"
                        }
                    },
                    credits:{
                        enabled: false // 禁用版权信息
                    },

                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle'
                    },


                    xAxis: {
                        labels: {
                            //rotation: 0,
                            style: {
                                color: '#6D869F',
                                fontWeight: 'bold'
                            },
                            axisLabel:{
                                interval:0,//0：全部显示，1：间隔为1显示对应类目，2：依次类推，（简单试一下就明白了，这样说是不是有点抽象）
                                rotate:-30,//倾斜显示，-：顺时针旋转，+或不写：逆时针旋转
                            },
                        },
                        categories:result.data.time,
                       /* tickInterval: 3,*/

                    },
                    yAxis: {
                        title: {
                            text: ytitle
                        },
                        allowDecimals: false,                               //控制数轴是否显示小数。
                        min: 0,                                             //控制数轴的最小值
                        //max: 10000,                                       //控制数轴的最大值
                    },
                    tooltip: {
                        formatter: function() {
                            return '<b>'+ this.x +'</b><br/>'+
                                this.series.name +': '+ this.y +'<br/>'
                            //+'Total: '+ this.point.stackTotal;
                        }
                    },
                    plotOptions: {
                        column: {
                            //stacking: 'normal',
                            pointPadding: 0,
                            borderWidth: 0
                        }
                    },
                    series: [{
                        name: dtitle,
                        type: dtype,
                        data:result.data.total,
                    },],
                });
                $('#container1').highcharts({
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie'
                    },
                    title: {
                        text: '游戏平台当前在线人数('+result.data.sum+')'
                    },
                    credits:{
                        enabled: false // 禁用版权信息
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>',
                                style: {
                                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                                }
                            }
                        }
                    },
                    series: [{
                        name: '百分比',
                        colorByPoint: true,
                        data: result.data.people
                    }]
                });
            }else{
                layer.msg(result.message,{icon: 0});
            }
        },
        error : function() {
            alert("异常！");
        }
    })
}


function getPie(ajaxUrl,ajaxparam,tplId)
{
    tplId=tplId||'replace-tpl';
    $.ajax({
        //几个参数需要注意一下
        type: "get",//方法类型
        dataType: "json",//预期服务器返回的数据类型
        url: ajaxUrl ,//url
        data: ajaxparam,
        success: function (result) {
            $("#"+tplId).replaceWith(result.data.content);  //htm块替l换html的div
            if (result.resultCode == 200) {
                $('#container1').highcharts({
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie'
                    },
                    title: {
                        text: '首次进入游戏人数('+result.data.total_people+')'
                    },
                    credits:{
                        enabled: false // 禁用版权信息
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>',
                                style: {
                                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                                }
                            }
                        }
                    },
                    series: [{
                        name: '百分比',
                        colorByPoint: true,
                        data: result.data.people
                    }]
                });
                $('#container2').highcharts({
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie'
                    },
                    title: {
                        text: '注册人数('+result.data.total+')'
                    },
                    credits:{
                        enabled: false // 禁用版权信息
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>',
                                style: {
                                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                                }
                            }
                        }
                    },
                    series: [{
                        name: '百分比',
                        colorByPoint: true,
                        data: result.data.user
                    }]
                });
            }else{
                layer.msg(result.message,{icon: 0});
            }
        },
        error : function() {
            alert("异常！");
        }
    })

}

function getColumn(ajaxUrl,ajaxparam,tplId)
{
    tplId=tplId||'replace-tpl';
    $.ajax({
        //几个参数需要注意一下
        type: "get",//方法类型
        dataType: "json",//预期服务器返回的数据类型
        url: ajaxUrl ,//url
        data: ajaxparam,
        success: function (result) {
            $("#"+tplId).replaceWith(result.data.content);  //htm块替l换html的div
            if (result.resultCode == 200) {
                $('#container1').highcharts({
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: '交易额度'
                    },
                    subtitle: {
                        text: ''
                    },
                    xAxis: {
                        categories: result.data.times,
                        crosshair: true
                    },
                    credits: {
                        enabled: false     //不显示LOGO
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: '交易额度'
                        }
                    },
                    tooltip: {
                        // head + 每个 point + footer 拼接成完整的 table
                        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                        '<td style="padding:0"><b>{point.y}</b></td></tr>',
                        footerFormat: '</table>',
                        shared: true,
                        useHTML: true
                    },
                    plotOptions: {
                        column: {
                            borderWidth: 0
                        }
                    },
                    series: [{
                        name: '交易额度',
                        data: result.data.gold
                    }]
                });
                $('#container2').highcharts({
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: '交易笔数'
                    },
                    subtitle: {
                        text: ''
                    },
                    xAxis: {
                        categories: result.data.times,
                        crosshair: true
                    },
                    credits: {
                        enabled: false     //不显示LOGO
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: '交易笔数'
                        }
                    },
                    tooltip: {
                        // head + 每个 point + footer 拼接成完整的 table
                        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                        '<td style="padding:0"><b>{point.y}</b></td></tr>',
                        footerFormat: '</table>',
                        shared: true,
                        useHTML: true
                    },
                    plotOptions: {
                        column: {
                            borderWidth: 0
                        }
                    },
                    series: [{
                        name: '交易笔数',
                        data: result.data.num
                    }]
                });
            }else{
                layer.msg(result.message,{icon: 0});
            }
        },
        error : function() {
            alert("异常！");
        }
    })

}