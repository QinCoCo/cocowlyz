<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
$title='在线聊天室';
include_once './header.php';
?>
<link rel="stylesheet" href="../assets/layui/style/admin.css" media="all">
<link rel="stylesheet" href="../assets/layui/style/template.css" media="all">
<link rel="stylesheet" type="text/css" href="../assets/css/litewebchat.min.css" />
<style>
.lite-chatbox .name .cg{background-color:#F22525}
.lite-chatbox .name .sqs{background-color:#00C6FF}
img.qqlogo{border:1px solid #FFF;-moz-box-shadow:0 0 3px #AAA;-webkit-box-shadow:0 0 3px #AAA;border-radius: 50%;box-shadow:0 0 3px #AAA;padding:3px;margin-right: 3px;margin-left: 6px;margin-left: 3px;}
.clearfloat:after{display:block;clear:both;content:"";visibility:hidden;height:0}
.clearfloat{zoom:1;}
.clearfloat .right{float: right;}
.author-name{text-align: center;margin: 15px 0 5px 0;color: #888;}
.clearfloat .chat-message{max-width: 232px;text-align: left;padding: 8px 12px;border-radius: 6px;word-wrap:break-word;display: inline-block;position: relative;}
@-webkit-keyframes Glow {from {text-shadow: 0 0 10px #fff,0 0 20px #fff,0 0 30px #fff,0 0 40px red,0 0 70px red,0 0 80px red,}to {text-shadow: 0 0 5px #fff,0 0 10px #fff,0 0 15px #fff,0 0 20px red,0 0 35px red,}}
@keyframes Glow {from {text-shadow: 0 0 10px #fff,0 0 20px #fff,0 0 30px #fff,0 0 40px red,0 0 70px red,0 0 80px red,0 0 100px red,0 0 150px red;}to {text-shadow: 0 0 5px #fff,0 0 10px #fff,0 0 15px #fff,0 0 20px red,0 0 35px red,}}
.clearfloat .left .chat-message{background: #ef7e7e;min-height: 22px;z-index:999;color:white;top:4px;}
.clear-form{-webkit-animation: Glow 1.5s ease infinite alternate;animation: Glow 1.5s ease infinite alternate;}
.clearfloat .left .chat-message:before{position: absolute;content: "";top: 11.5px;left: -6px;border-top: 10px solid transparent;border-bottom: 10px solid transparent;border-right: 10px solid #ef7e7e;z-index:-1;}
.clearfloat .right{text-align: right;}
.clearfloat .right .chat-message{background: #01aaed;text-align: left;min-height: 22px;z-index:999;top:7px;}
.clearfloat .right .chat-message:before{position: absolute;content: "";top: 8px;right: -6px;border-top: 10px solid transparent;border-bottom: 10px solid transparent;border-left: 10px solid #01aaed;z-index:-1;}
.clearfloat .chat-avatars{display: inline-block;border-radius: 50%;vertical-align: top;}
.clearfloat .left .chat-avatars{margin-right: 10px;}
.clearfloat .right .chat-avatars{margin-left: 5px;}
.clearfloat-ts{margin-top:5px;text-align: center;border-radius: 5px;padding: 10px;background: rgba(216, 239, 159, 0.53);color: red;font-weight: bold;}
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                聊天公屏&nbsp;&nbsp;&nbsp;<a href="javascript:listTable()" class="badge badge-danger-info" title="刷新聊天公屏"><i class="layui-icon layui-icon-refresh"></i> 刷新</a>
            </div>
            <div class="card-body">
                <div class="clearfloat-ts" id="blocktitle"></div>
            </div>
            <div class="card-body">
                <div id="listTable"></div>
            </div>
            <div class="layadmin-message-fluid">
                <div class="layui-row">
                    <form onsubmit="return send();" method="post" class="layui-form">
                        <div class="layui-form-item layui-form-text">
                            <div class="layui-input-block">
                                <textarea name="con" placeholder="请输入发言内容" class="layui-textarea" lay-verType="tips" lay-verify="required"></textarea>
                            </div>
                        </div>
                        <div class="layui-form-item" style="overflow: hidden;">
                            <div class="layui-input-block layui-input-right">
                                <input type="submit" name="submit" value="发送" class="btn btn-block btn-xs btn-outline-info">
                            </div>
                            <div class="layadmin-messag-icon">
                                <a href="javascript:face_list();"><i class="layui-icon layui-icon-face-smile-b"></i></a>
                                <input type="file" id="file" onchange="chat_upload();" style="display:none;"/>
                                 <a href="javascript:chat_select();"><i class="layui-icon layui-icon-picture"></i></a>
                                 <a href="javascript:search();"><i class="layui-icon layui-icon-search"></i></a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include_once './bottom.php';
?>
<script type="text/javascript">
function listTable(query){
    var url = window.document.location.href.toString();
    var queryString = url.split("?")[1];
    query = query || queryString;
    if(query == 'start' || query == undefined){
        query = '';
        history.replaceState({}, null, './chat.php');
    }else if(query != undefined){
        history.replaceState({}, null, './chat.php?'+query);
    }
    layer.closeAll();
    var ii = layer.msg('正在获取聊天信息中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : 'GET',
        url : 'chat-table.php?'+query,
        dataType : 'html',
        cache : false,
        success : function(data) {
            layer.close(ii);
            $("#listTable").html(data)
        },
        error:function(data){
            layer.msg('服务器错误');
            return false;
        }
    });
}
function Addstr(str) {
    $("textarea[name='con']").val($("textarea[name='con']").val()+str);
}
<?php if ($conf['chat_notice']) {?>
function aa() {
    layer.open({
        type: 1,
        title: false,
        closeBtn: 0,
        area: '300px',
        shade: 0.8,
        id: 'LAY_layuipro',
        btn: ['好的了解'],
        btnAlign: 'c',
        moveOut: true,
        moveType: 0,
        btn1: function (layero, index) {listTable();},
        content: '<div style="background-color:#393D49;color:#eeeeee;padding:0.5em"><h3 style="text-align:center;padding-top:0.5em">平台公告</h3><hr><?php echo $conf['chat_notice'];?><hr/><center>客服QQ：<?php echo $conf['kfqq'];?></center></div>'
    });
};
aa();
<?php } else {?>
$(document).ready(function(){
    listTable();
})
<?php }?>
layui.use('util', function () {
    var util = layui.util;
    util.fixbar({
        <?php if ($conf['chat_notice']) {?>
        bar2: '&#xe667;',
        <?php }?>
        bar1: '&#xe60b;',
        click: function (type) {
            if (type === 'bar1') {
                layer.alert('换行:[br]<br>链接:[url=http://链接地址]名称[/url]<br>图片:[img]图片链接地址[/img]<br>移动文字:[move]内容[/move]<br>彩色文字:[color=颜色名]文字[/color]<br><hr>颜色代码如:<br><font color=green>green</font>,<font color=red>red</font>,<font color=brown> brown</font>,<font color=#CCC00> #CCC00</font>,<font color=#66CCCC>#66CCCC</font> <a href="http://tool.c7sky.com/webcolor" target="_blank" rel="nofollow">更多</a>', {
                    title: '聊天室说明',
                    icon: '3',
                    btn: ['知道了']
                });
            }
            <?php if ($conf['chat_notice']) {?>
            if (type === 'bar2') {
                aa()
            }
            <?php }?>
        }
    });
});
function search(){
    var body='<input type="text" value="" id="kw" placeholder="请输入关键词" class="form-control">';
    layer.confirm(body,{title:"搜索聊天记录",btn: ['确定','取消']}, function(){
        var kw=$("#kw").val();
        if(kw == ''){
            listTable('start');
        }else{
            listTable('kw='+kw);
        }
    });
}
function send(){
    var con = $("textarea[name='con']").val();
    if(con==''){layer.msg('发送内容不能为空');return false;}
    var ii = layer.msg('正在发送中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : "POST",
        url : "ajax.php?act=send",
        data : {"con":con},
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if (data.code == 0) {
                $("textarea[name='con']").val('');
                listTable();
            } else {
                layer.msg(data.msg, {icon: 5});
            }
        },
        error:function(data){
            layer.close(ii);
            layer.msg('服务器错误', {icon: 5});
        }
    });
}
function chat_select(){
    $("#file").trigger("click");
}
function chat_upload(){
    var fileObj = $("#file")[0].files[0];
    if (typeof (fileObj) == "undefined" || fileObj.size <= 0) {
        return;
    }
    var formData = new FormData();
    formData.append("do","upload");
    formData.append("file",fileObj);
    var ii = layer.msg('正在发送表情中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        url: "ajax.php?act=uploadchatimg",
        data: formData,
        type: "POST",
        dataType: "json",
        cache: false,
        processData: false,
        contentType: false,
        success: function (data) {
            layer.close(ii);
            if(data.code == 0){
                listTable();
            }else{
                layer.msg(data.msg, {icon: 5});
            }
        },
        error:function(data){
            layer.msg('服务器错误', {icon: 5});
            return false;
        }
    });
}
function add_face(src){
    var confirmobj = layer.confirm('<center><img src="'+src+'" width="100%" style="border-radius: 5px;max-width: 150px;"></center>',{
        title:'图片选项',
        btn:['添加到表情','不了']
    }, function(){
        var ii = layer.msg('正在添加中,请稍后...', {icon: 16, time: 10 * 1000});
        $.ajax({
            type : "POST",
            url : "ajax.php?act=add_face",
            data : {"face":src},
            dataType : 'json',
            timeout:10000,
            success : function(data) {
                layer.close(ii);
                if (data.code == 0) {
                    layer.msg(data.msg, {icon: 6});
                } else {
                    layer.msg(data.msg, {icon: 5});
                }
            },
            error:function(data){
                layer.close(ii);
                layer.msg('服务器错误', {icon: 5});
            }
        });
    }, function(){
        layer.close(confirmobj);
    });
}
function face_list(){
    var ii = layer.msg('正在获取表情包中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : "POST",
        url : "ajax.php?act=face_list",
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if(data.code==0){
                layer.open({
                    type: 1,
                    title: '我的表情包',
                    skin: 'layui-layer-rim',
                    content:'<div class="card-body"><div class="row text-center"><div class="col-xs-6 col-sm-4" style="margin-bottom: 20px;">' + data.face_list + '</div></div></div>'
                });
            }else{
                layer.msg(data.msg, {icon: 5});
            }
        },
        error:function(data){
            layer.close(ii);
            layer.msg('服务器错误', {icon: 5});
        }
    });
}
function send_face(src){
    var ii = layer.msg('正在发送中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : "POST",
        url : "ajax.php?act=send_face",
        data : {"face":src},
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if (data.code == 0) {
                listTable();
            } else {
                layer.msg(data.msg, {icon: 5});
            }
        },
        error:function(data){
            layer.close(ii);
            layer.msg('服务器错误', {icon: 5});
        }
    });
}
function chat_site(uid) {
    var ii = layer.msg('正在用户信息中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : 'GET',
        url : 'ajax.php?act=chat_site&uid='+uid,
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if(data.code == 0){
                var confirmobj = layer.confirm(data.data, {
                    btn: ['@他','了解']
                }, function(){
                    Addstr('@'+data.user+' ');
                }, function(){
                    layer.close(confirmobj);
                });
            }else{
                layer.msg(data.msg, {icon: 5});
            }
        } ,
        error:function(data){
            layer.close(ii);
            layer.msg('服务器错误！', {icon: 5});
            return false;
        }
    });
}
</script>