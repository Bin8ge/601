
    var expire =0;
     sign_obj='';
    var serverUrl='/admin/game/notice/oosUpload';

        $("div").on("change",'input[type="file"]',function(evt){
            $this=$(this);
            var files = evt.target.files;
            var file=files[0];
            if(file.size > 10*1024*1024 ){
                alert('too big');
                return false;
            }
            get_signature();
            if(sign_obj == '') {
                //alert(sign_obj);
               alert('签名error,请重试');
                return false;
            }

            var g_object_name=sign_obj.dir+random_string()+get_suffix(file.name);
            var filename=random_string()+get_suffix(file.name);
            var request = new FormData();
            request.append("OSSAccessKeyId",sign_obj.accessid);//Bucket 拥有者的Access Key Id。
            request.append("policy",sign_obj.policy);//policy规定了请求的表单域的合法性
            request.append("Signature",sign_obj.signature);//根据Access Key Secret和policy计算的签名信息，OSS验证该签名信息从而验证该Post请求的合法性
            request.append("key",g_object_name);//文件名字，可设置路径
            request.append("success_action_status",'200');// 让服务端返回200,不然，默认会返回204
            request.append('x-oss-object-acl', 'public-read');
            request.append('file', file);
            $.ajax({
                url : sign_obj.host,  //上传阿里地址
                data : request,
                processData: false,//默认true，设置为 false，不需要进行序列化处理
                cache: false,//设置为false将不会从浏览器缓存中加载请求信息
                async: false,//发送同步请求
                contentType: false,//避免服务器不能正常解析文件---------具体的可以查下这些参数的含义
                dataType: 'xml',//不涉及跨域  写json即可
                type : 'post',
                success : function(callbackHost, request) {     //callbackHost：success,request中就是 回调的一些信息，包括状态码什么的
                    var origin=sign_obj.host+'/'+g_object_name;
                    var src=origin;
                    //$this.closest('div').find('img').attr('src', src).show();
                    //$this.closest('div').find('.imgclose').show();
                    $('.showFileName1').html(filename);
                    $('#osspic').val(src);
                    $("#uploadimg").show();
                    $("#uploadimg").attr({src: src});
                    $("#uploadimg").css({width: "200px", height: "100px"});
                },
                error : function(returndata) {
                    alert('上传图片出错啦,请重试')
                }
            });
        });



    function random_string(len) {
        len = len || 32;
        var chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';
        var maxPos = chars.length;
        var pwd = '';
        for (i = 0; i < len; i++) {
            pwd += chars.charAt(Math.floor(Math.random() * maxPos));
        }
        return pwd;
    }

    function get_suffix(filename) {
        var pos = filename.lastIndexOf('.')
        var suffix = ''
        if (pos != -1) {
            suffix = filename.substring(pos)
        }
        return suffix;
    }
    //获取签名信息
    function send_request()
    {
        var xmlhttp = null;
        if (window.XMLHttpRequest)
        {
            xmlhttp=new XMLHttpRequest();
        }
        else if (window.ActiveXObject)
        {
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        if (xmlhttp!=null)
        {
            xmlhttp.open( "GET", serverUrl, false );
            xmlhttp.send( null );
            return xmlhttp.responseText
        }
        else
        {
            alert("Your browser does not support XMLHTTP.");
        }
    }

    function get_signature()
    {
        //可以判断当前expire是否超过了当前时间,如果超过了当前时间,就重新取一下.3s 做为缓冲
        now = timestamp = Date.parse(new Date()) / 1000;
        if (expire < now + 3)
        {
            var body = send_request();
            //console.log(body)
            var obj =JSON.parse(body);
            if(obj.status ==1  && obj.data.code == 1){
                sign_obj= obj.data;
                expire= parseInt(sign_obj['expire']);
                return true;
            }

            return true;
        }
        return false;
    };

