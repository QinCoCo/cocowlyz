<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
$title='工单列表';
include_once './header.php';
$count1=$DB->count('SELECT count(*) FROM yixi_workorder WHERE 1');
$count2=$DB->count('SELECT count(*) FROM yixi_workorder WHERE status=0');
$count3=$DB->count('SELECT count(*) FROM yixi_workorder WHERE status=1');
$count4=$DB->count('SELECT count(*) FROM yixi_workorder WHERE status=2');
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                工单列表&nbsp;&nbsp;&nbsp;
                <span class="badge badge-primary-lighten" onclick="listTable('start')">全部(<?php echo $count1?>)</span>&nbsp;
                <span class="badge badge-info-lighten" onclick="listTable('status=0')">待处理(<?php echo $count2?>)</span>&nbsp;
                <span class="badge badge-warning-lighten" onclick="listTable('status=1')">处理中(<?php echo $count3?>)</span>&nbsp;
                <span class="badge badge-success-lighten" onclick="listTable('status=2')">已完成(<?php echo $count4?>)</span>
                <a href="javascript:searchClear()" class="badge badge-danger-info pull-left" title="刷新工单列表"><i class="layui-icon layui-icon-refresh"></i> 刷新</a>
            </div>
            <div class="card-body">
                <div class="layui-elem-quote" id="blocktitle"></div>
                <form onsubmit="return searchOrder()" method="GET" class="form layui-form">
                    <div class="form-group mb-3">
                        <select class="form-control" name="type" lay-search><option value="0">全部</option><option value="1">用户UID</option><option value="2">问题描述</option></select>
                    </div>
                    <div class="form-group mb-3" id="searchword">
                        <input type="text" class="form-control" name="kw" placeholder="搜索内容" value="">
                    </div>
                    <div class="form-group mb-3">
                        <select class="form-control" name="method" lay-search><option value="0">精确搜索</option><option value="1">模糊搜索</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <button class="btn btn-outline-primary" type="submit"><i class="layui-icon layui-icon-search"></i> 搜索</button>
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
function selectAll(checkbox) {
    $('input[type=checkbox]').prop('checked', $(checkbox).prop('checked'));
}
function listTable(query){
    var url = window.document.location.href.toString();
    var queryString = url.split("?")[1];
    query = query || queryString;
    if(query == 'start' || query == undefined){
        query = '';
        history.replaceState({}, null, './workorder.php');
    }else if(query != undefined){
        history.replaceState({}, null, './workorder.php?'+query);
    }
    layer.closeAll();
    var ii = layer.msg('正在添加中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : 'GET',
        url : 'workorder-table.php?'+query,
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
function change(){
    if($("select[name='aid']").val() == 3 && $("input[name='content']").val()==''){
        layer.prompt({title: '请输入回复的内容', formType: 2}, function(text, index){
            layer.close(index);
            $("input[name='content']").val(text);
            change()
        });
        return false;
    }
    var ii = layer.msg('正在操作中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : 'POST',
        url : 'ajax.php?act=workorder_change',
        data : $('#form1').serialize(),
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
            layer.msg('请求超时', {icon: 5});
        }
    });
    return false;
}
function delworkorder(id) {
    var confirmobj = layer.confirm('你确实要删除此工单吗？', {
      btn: ['确定','取消']
    }, function(){
      $.ajax({
        type : 'GET',
        url : 'ajax.php?act=delworkorder&id='+id,
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
            layer.msg('服务器错误' ,{icon :5});
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