<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
$title='用户广告列表';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                用户广告列表&nbsp;&nbsp;&nbsp;<a href="javascript:searchClear()" class="badge badge-danger-info" title="刷新广告列表"><i class="layui-icon layui-icon-refresh"></i> 刷新</a>
            </div>
            <div class="card-body">
                <div class="layui-elem-quote" id="blocktitle"></div>
                <form onsubmit="return searchOrder()" method="GET" class="form layui-form">
                    <div class="form-group mb-3">
                        <select class="form-control" lay-search name="type"><option value="0">全部</option><option value="1">广告标题</option><option value="2">跳转地址</option></select>
                    </div>
                    <div class="form-group mb-3" id="searchword">
                        <input type="text" class="form-control" name="kw" placeholder="搜索内容" value="">
                    </div>
                    <div class="form-group mb-3">
                        <select class="form-control" lay-search name="method"><option value="0">精确搜索</option><option value="1">模糊搜索</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <button class="btn btn-outline-primary" type="submit"><i class="layui-icon layui-icon-search"></i> 搜索</button>&nbsp;
                        <a href="adver.php" class="btn btn-outline-secondary"><i class="layui-icon layui-icon-add-circle"></i> 添加用户广告</a>
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
        history.replaceState({}, null, './adverlist.php');
    }else if(query != undefined){
        history.replaceState({}, null, './adverlist.php?'+query);
    }
    layer.closeAll();
    var ii = layer.msg('正在获取列表中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : 'GET',
        url : 'adverlist-table.php?'+query,
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
        url : 'ajax.php?act=adver_'+type+'&id='+id,
        dataType : 'json',
        success : function(data) {
        },
        error:function(data){
            layer.msg('服务器错误', {icon: 5});
            return false;
        }
    });
}
function adverdel(id) {
    var confirmobj = layer.confirm('你确实要删除此用户广告吗？', {
      btn: ['确定','取消']
    }, function(){
      $.ajax({
        type : 'GET',
        url : 'ajax.php?act=adverdel&id='+id,
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