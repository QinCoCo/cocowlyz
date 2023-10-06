<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
$title='用户列表';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                用户列表&nbsp;&nbsp;&nbsp;<a href="javascript:searchClear()" class="badge badge-danger-info" title="刷新用户列表"><i class="layui-icon layui-icon-refresh"></i> 刷新</a>
            </div>
            <div class="card-body">
                <div class="layui-elem-quote" id="blocktitle"></div>
                <form onsubmit="return searchOrder()" method="GET" class="form layui-form">
                    <div class="form-group mb-3">
                        <select class="form-control" name="type" lay-search><option value="0">全部</option><option value="1">用户UID</option><option value="2">用户名</option><option value="3">用户QQ</option></select>
                    </div>
                    <div class="form-group mb-3" id="searchword">
                        <input type="text" class="form-control" name="kw" placeholder="搜索内容" value="">
                    </div>
                    <div class="form-group mb-3">
                        <select class="form-control" name="method" lay-search><option value="0">精确搜索</option><option value="1">模糊搜索</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <button class="btn btn-outline-primary" type="submit"><i class="layui-icon layui-icon-search"></i> 搜索</button>&nbsp;
                        <a href="adduser.php" class="btn btn-outline-secondary"><i class="layui-icon layui-icon-add-circle"></i> 添加用户</a>
                    </div>
                </form>
            <div id="listTable"></div>
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
        history.replaceState({}, null, './userlist.php');
    }else if(query != undefined){
        history.replaceState({}, null, './userlist.php?'+query);
    }
    layer.closeAll();
    var ii = layer.msg('正在获取用户列表中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : 'GET',
        url : 'userlist-table.php?'+query,
        dataType : 'html',
        cache : false,
        success : function(data) {
            layer.close(ii);
            $("#listTable").html(data)
        },
        error:function(data){
            layer.msg('服务器错误', {icon: 5});
            return false;
        }
    });
}
function searchOrder(){
    var type=$("select[name='type']").val();
    var kw=$("input[name='kw']").val();
    var method=$("select[name='method']").val();
    if(kw==''){
        listTable('start');
    }else{
        listTable('type='+type+'&kw='+kw+'&method='+method);
    }
    return false;
}
function searchClear(){
    $("select[name='type']").val(0);
    $("input[name='kw']").val('');
    $("select[name='method']").val(0);
    listTable('start');
}
function recharge(uid){
    var body='<div class="form-group"><select class="form-control" id="actdo"><option value="0">加款</option><option value="1">扣款</option></select></div><input type="text" id="rmb" placeholder="请输入金额" class="form-control">';
    layer.confirm(body,{title:"余额充值",btn: ['确定','取消']}, function(){
        var actdo=$("#actdo").val();
        var rmb=$("#rmb").val();
        if(rmb==''){
            layer.msg('请输入金额', {icon: 5});
            return false;
        }
        var ii = layer.msg('正在操作中,请稍后...', {icon: 16, time: 10 * 1000});
        $.ajax({
            type : "POST",
            url : "ajax.php?act=user_recharge",
            data : {uid:uid,actdo:actdo,rmb:rmb},
            dataType : 'json',
            success : function(data) {
                layer.close(ii);
                if(data.code == 0){
                    layer.msg('修改余额成功!', {
                        icon: 6,
                        end: function (layero, index) {
                            listTable();
                        }
                    });
                } else {
                    layer.msg(data.msg, {icon: 5});
                }
            },
            error:function(data){
                layer.close(ii);
                layer.msg('服务器错误', {icon: 5});
                return false;
            }
        });
    });
}
function Active(type,uid) {
    $.ajax({
        type : 'GET',
        url : 'ajax.php?act=user_'+type+'&uid='+uid,
        dataType : 'json',
        success : function(data) {
        },
        error:function(data){
            layer.msg('服务器错误', {icon: 5});
            return false;
        }
    });
}
function czpass(uid) {
    var confirmobj = layer.confirm('你确实要将此用户的密码重置为123456吗？', {
      btn: ['确定','取消']
    }, function(){
        var ii = layer.msg('正在重置中,请稍后...', {icon: 16, time: 10 * 1000});
        $.ajax({
            type : 'GET',
            url : 'ajax.php?act=user_czpass&uid='+uid,
            dataType : 'json',
            success : function(data) {
                layer.close(ii);
                if(data.code == 0){
                    layer.msg(data.msg, {
                        icon: 6,
                        end: function (layero, index) {
                            listTable();
                        }
                    });
                } else {
                    layer.msg(data.msg, {icon: 5});
                }
            },
            error:function(data){
                layer.msg('服务器错误', {icon: 5});
                return false;
            }
        });
    }, function(){
        layer.close(confirmobj);
    });
}
function userdel(uid) {
    var confirmobj = layer.confirm('你确实要删除此用户吗？', {
      btn: ['确定','取消']
    }, function(){
      $.ajax({
        type : 'GET',
        url : 'ajax.php?act=userdel&uid='+uid,
        dataType : 'json',
        success : function(data) {
            if(data.code == 0){
                layer.msg(data.msg, {
                    icon: 6,
                    end: function (layero, index) {
                        listTable();
                    }
                });
            } else {
                layer.msg(data.msg, {icon: 5});
            }
        },
        error:function(data){
            layer.msg('服务器错误', {icon: 5});
            return false;
        }
      });
    }, function(){
      layer.close(confirmobj);
    });
}
function sso(uid) {
    var confirmobj = layer.confirm('你确实要登录此用户后台吗？', {
      btn: ['确定','取消']
    }, function(){
        var ii = layer.msg('正在登录中,请稍后...', {icon: 16, time: 10 * 1000});
        $.ajax({
            type : 'GET',
            url : 'ajax.php?act=sso&uid='+uid,
            dataType : 'json',
            success : function(data) {
                layer.close(ii);
                if(data.code == 0){
                    layer.msg(data.msg, {
                        icon: 6,
                        end: function (layero, index) {
                            window.location.href = '../user';
                        }
                    });
                } else {
                    layer.msg(data.msg, {icon: 5});
                }
            },
            error:function(data){
                layer.msg('服务器错误', {icon: 5});
                return false;
            }
        });
    }, function(){
        layer.close(confirmobj);
    });
}
$(document).ready(function(){
    var items = $("select[default]");
    for (i = 0; i < items.length; i++) {
        $(items[i]).val($(items[i]).attr("default")||0);
    }
    listTable();
})
</script>