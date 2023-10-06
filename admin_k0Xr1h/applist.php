<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
$title='应用列表';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                应用列表&nbsp;&nbsp;&nbsp;<a href="javascript:searchClear()" class="badge badge-danger-info" title="刷新应用列表"><i class="layui-icon layui-icon-refresh"></i> 刷新</a>
            <a href="applist.php?uid=1" class="badge badge-primary-lighten">我的应用</a>
            </div>
            <div class="card-body">
                <div class="layui-elem-quote" id="blocktitle"></div>
                <form onsubmit="return searchOrder()" method="GET" class="form layui-form">
                    <div class="form-group mb-3">
                        <select class="form-control" name="type" lay-search><option value="0">全部</option><option value="1">APPID</option><option value="2">应用名称</option></select>
                    </div>
                    <div class="form-group mb-3" id="searchword">
                        <input type="text" class="form-control" name="kw" placeholder="搜索内容" value="">
                    </div>
                    <div class="form-group mb-3">
                        <select class="form-control" name="method" lay-search><option value="0">精确搜索</option><option value="1">模糊搜索</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <button class="btn btn-outline-primary" type="submit"><i class="layui-icon layui-icon-search"></i> 搜索</button>&nbsp;
                        <a href="addapp.php" class="btn btn-outline-secondary"><i class="layui-icon layui-icon-add-circle"></i> 添加应用</a>
                    </div>
                </form>
            <div id="listTable"></div>
        </div>
    </div>
</div>
<?php
include_once './bottom.php';
?>
<script src="https://cdn.bootcss.com/clipboard.js/2.0.4/clipboard.js"></script>
<script type="text/javascript">
function listTable(query){
    var url = window.document.location.href.toString();
    var queryString = url.split("?")[1];
    query = query || queryString;
    if(query == 'start' || query == undefined){
        query = '';
        history.replaceState({}, null, './applist.php');
    }else if(query != undefined){
        history.replaceState({}, null, './applist.php?'+query);
    }
    layer.closeAll();
    var ii = layer.msg('正在获取应用列表中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : 'GET',
        url : 'applist-table.php?'+query,
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
function Active(type,id) {
    $.ajax({
        type : 'GET',
        url : 'ajax.php?act=app_'+type+'&id='+id,
        dataType : 'json',
        success : function(data) {
        },
        error:function(data){
            layer.msg('服务器错误', {icon: 5});
            return false;
        }
    });
}
function getmoney(id) {
    var ii = layer.msg('正在获取中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : 'GET',
        url : 'ajax.php?act=getAppPromoney&id='+id,
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if(data.code == 0){
                layer.open({
                  type: 1,
                  title: '修改应用价格',
                  skin: 'layui-layer-rim',
                  content: data.data
                });
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
function getappnote(id) {
    var ii = layer.msg('正在获取中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : 'GET',
        url : 'ajax.php?act=getAppNote&id='+id,
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if(data.code == 0){
                layer.open({
                  type: 1,
                  title: '备注',
                  skin: 'layui-layer-rim',
                  content: data.data
                });
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
function getappversion(id) {
    var ii = layer.msg('正在获取中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : 'GET',
        url : 'ajax.php?act=getAppVersion&id='+id,
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if(data.code == 0){
                layer.open({
                  type: 1,
                  title: '应用版本',
                  skin: 'layui-layer-rim',
                  content: data.data
                });
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
function saveappnote(id) {
    var appnote=$("#appnote").val();
    $('#save').val('Loading');
    var ii = layer.msg('正在修改中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : "POST",
        url : "ajax.php?act=editAppNote",
        data : {id:id,appnote:appnote},
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if(data.code == 0){
                layer.msg('保存成功！', {
                    icon: 6,
                    end: function (layero, index) {
                        listTable();
                    }
                });
            } else {
                layer.msg(data.msg, {icon: 5});
            }
            $('#save').val('保存');
        } 
    });
}
function saveappversion(id) {
    var version=$("#version").val();
    $('#save').val('Loading');
    var ii = layer.msg('正在修改中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : "POST",
        url : "ajax.php?act=editAppVersion",
        data : {id:id,version:version},
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if(data.code == 0){
                layer.msg('保存成功！', {
                    icon: 6,
                    end: function (layero, index) {
                        listTable();
                    }
                });
            } else {
                layer.msg(data.msg, {icon: 5});
            }
            $('#save').val('保存');
        } 
    });
}
function saveInfo(id) {
    var sqprice=$("#sqprice").val();
    var sqprice2=$("#sqprice2").val();
    var sqprice3=$("#sqprice3").val();
    var sqsprice=$("#sqsprice").val();
    var sqsprice2=$("#sqsprice2").val();
    var cgprice=$("#cgprice").val();
    if(sqprice=='' || sqprice2=='' || sqprice3=='' || sqsprice=='' || sqsprice2=='' || cgprice==''){layer.msg('请确保每项不能为空！', {icon: 5});return false;}
    $('#save').val('Loading');
    var ii = layer.msg('正在修改中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : "POST",
        url : "ajax.php?act=editAppPromoney",
        data : {id:id,sqprice:sqprice,sqprice2:sqprice2,sqprice3:sqprice3,sqsprice:sqsprice,sqsprice2:sqsprice2,cgprice:cgprice},
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if(data.code == 0){
                layer.msg('保存成功！', {
                    icon: 6,
                    end: function (layero, index) {
                        listTable();
                    }
                });
            } else {
                layer.msg(data.msg, {icon: 5});
            }
            $('#save').val('保存');
        } 
    });
}
function appdel(id) {
    var confirmobj = layer.confirm('删除该应用将会删除所有关于该应用的配置、用户、代理、卡密等等，你确实要删除此应用吗？', {
      btn: ['确定','取消']
    }, function(){
      $.ajax({
        type : 'GET',
        url : 'ajax.php?act=appdel&id='+id,
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
function appkeyChange(id) {
    var confirmobj = layer.confirm('更换APPKEY后，之前的APPKEY将无法使用，你确实要更换吗？', {
      btn: ['确定','取消']
    }, function(){
      $.ajax({
        type : 'GET',
        url : 'ajax.php?act=appkeyChange&id='+id,
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
$(document).ready(function(){
    var items = $("select[default]");
    for (i = 0; i < items.length; i++) {
        $(items[i]).val($(items[i]).attr("default")||0);
    }
    listTable();
})
</script>
<script>
    var codeClipboard = new ClipboardJS('#btn_code');
   codeClipboard.on('success', function (e) {
        layer.msg("APPKEY复制成功", {icon: 6});
        e.clearSelection();
    });
    codeClipboard.on('error', function (e) {
        layer.msg('复制失败,请手动复制~', {icon: 5});
    });
</script>