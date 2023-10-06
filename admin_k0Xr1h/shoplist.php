<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
$title='商品列表';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                商品列表&nbsp;&nbsp;&nbsp;<a href="javascript:searchClear()" class="badge badge-danger-info" title="刷新商品列表"><i class="layui-icon layui-icon-refresh"></i> 刷新</a>
            </div>
            <div class="card-body">
                <div class="layui-elem-quote" id="blocktitle"></div>
                <form onsubmit="return searchOrder()" method="GET" class="form layui-form">
                    <div class="form-group mb-3">
                        <select class="form-control" name="type" default="0"><option value="0">全部</option><option value="1">商品名称</option></select>
                    </div>
                    <div class="form-group mb-3" id="searchword">
                        <input type="text" class="form-control" name="kw" placeholder="搜索内容" value="">
                    </div>
                    <div class="form-group mb-3">
                        <select class="form-control" name="method" default="0"><option value="0">精确搜索</option><option value="1">模糊搜索</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <button class="btn btn-outline-primary" type="submit"><i class="layui-icon layui-icon-search"></i> 搜索</button>&nbsp;
                        <a href="addshop.php" class="btn btn-outline-secondary"><i class="layui-icon layui-icon-add-circle"></i> 添加商品</a>
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
        history.replaceState({}, null, './shoplist.php');
    }else if(query != undefined){
        history.replaceState({}, null, './shoplist.php?'+query);
    }
    layer.closeAll();
    var ii = layer.msg('正在获取商品列表中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : 'GET',
        url : 'shoplist-table.php?'+query,
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
function Active(id) {
    $.ajax({
        type : 'GET',
        url : 'ajax.php?act=shop_active&id='+id,
        dataType : 'json',
        success : function(data) {
        },
        error:function(data){
            layer.msg('服务器错误', {icon: 5});
            return false;
        }
    });
}
function image_msg(id) {
    $.getJSON('../ajax.php?act=image_shop&id=' + id, function (json) {
        layer.photos({photos: json, anim: 5});
    });
}
function shop_tcbl(id,tcbl) {
    var body='<input type="text" value="'+tcbl+'" id="tcbl" placeholder="请输入商品提成比例" class="form-control">';
    layer.confirm(body,{title:"修改商品提成比例",btn: ['确定','取消']}, function(){
        var tcbl=$("#tcbl").val();
        if(tcbl==''){
            layer.msg('请输入商品提成比例', {icon: 5});
            return false;
        }
        var ii = layer.msg('正在修改提成比例中,请稍后...', {icon: 16, time: 10 * 1000});
        $.ajax({
            type : "POST",
            url : "ajax.php?act=shop_tcbl",
            data : {id:id,tcbl:tcbl},
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
    });
}
function shop_money(id,money) {
    var body='<input type="text" value="'+money+'" id="money" placeholder="请输入商品价格" class="form-control">';
    layer.confirm(body,{title:"修改商品价格",btn: ['确定','取消']}, function(){
        var money=$("#money").val();
        if(money==''){
            layer.msg('请输入商品销售价格', {icon: 5});
            return false;
        }
        var ii = layer.msg('正在修改价格中,请稍后...', {icon: 16, time: 10 * 1000});
        $.ajax({
            type : "POST",
            url : "ajax.php?act=shop_money",
            data : {id:id,money:money},
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
    });
}
function shopdel(id) {
    var confirmobj = layer.confirm('你确实要删除此商品吗？', {
      btn: ['确定','取消']
    }, function(){
      $.ajax({
        type : 'GET',
        url : 'ajax.php?act=shopdel&id='+id,
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