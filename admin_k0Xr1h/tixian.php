<?php
/**
 * 余额提现处理
**/
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
$title='余额提现处理';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                余额提现列表&nbsp;&nbsp;&nbsp;<span class="badge badge-danger-lighten" onclick="listTable('types=2')">QQ钱包</span>&nbsp;<span class="badge badge-info-lighten" onclick="listTable('types=1')">微信</span>&nbsp;<span class="badge badge-warning-lighten" onclick="listTable('types=0')">支付宝</span><a href="javascript:searchClear()" class="badge badge-danger-info pull-left" title="刷新余额提现列表"><i class="layui-icon layui-icon-refresh"></i> 刷新</a>
            </div>
            <div class="card-body">
                <div class="layui-elem-quote" id="blocktitle"></div>
                <form onsubmit="return searchOrder()" method="GET" class="form layui-form">
                    <div class="form-group mb-3">
                        <select class="form-control" name="type" default="0"><option value="0">全部</option><option value="1">提现账号</option><option value="2">提现姓名</option><option value="3">提现备注</option></select>
                    </div>
                    <div class="form-group mb-3" id="searchword">
                        <input type="text" class="form-control" name="kw" placeholder="搜索内容" value="">
                    </div>
                    <div class="form-group mb-3">
                        <select class="form-control" name="method" default="0"><option value="0">精确搜索</option><option value="1">模糊搜索</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <button class="btn btn-outline-primary" type="submit"><i class="layui-icon layui-icon-search"></i> 搜索</button>
                        <?php if($conf['user_daifu']>0){?>&nbsp;<a href="javascript:config()" class="btn btn-outline-secondary"><i class="layui-icon layui-icon-set"></i> 自动转账配置</a>&nbsp;
                        <a href="javascript:pl_config()" class="btn btn-outline-secondary"><i class="layui-icon layui-icon-list"></i> 批量转账选中记录</a>
                        <?php }?>
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
        history.replaceState({}, null, './tixian.php');
    }else if(query != undefined){
        history.replaceState({}, null, './tixian.php?'+query);
    }
    layer.closeAll();
    var ii = layer.msg('正在获取提现记录中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : 'GET',
        url : 'tixian-table.php?'+query,
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
function inputInfo(id) {
    var ii = layer.msg('正在获取中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : 'GET',
        url : 'ajax.php?act=getTixian&id='+id,
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if(data.code == 0){
                layer.open({
                  type: 1,
                  title: '修改数据',
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
function saveInfo(id) {
    var type=$("#type").val();
    var name=$("#name").val();
    var account=$("#account").val();
    var remarks=$("#remarks").val();
    if(account=='' || name==''){layer.msg('请确保每项不能为空！', {icon: 5});return false;}
    $('#save').val('Loading');
    var ii = layer.msg('正在修改中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : "POST",
        url : "ajax.php?act=editTixian",
        data : {id:id,type:type,name:name,account:account,remarks:remarks},
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
function skimg(uid){
    layer.open({
        type: 1,
        area: ['360px', '400px'],
        title: '站点'+uid+'的收款图查看',
        shade: 0.3,
        anim: 1,
        shadeClose: true, //开启遮罩关闭
        content: '<center><img width="300px" src="../assets/img/skimg/sk_'+uid+'.png"></center>'
    });
}
function back(id, money) {
    var confirmobj = layer.confirm('你确实要将'+money+'元退回到该分站余额吗？', {
      btn: ['确定','取消']
    }, function(){
        var ii = layer.msg('正在退回中,请稍后...', {icon: 16, time: 10 * 1000});
        $.ajax({
            type : "POST",
            url : "ajax.php?act=opTixian",
            data : {id:id,op:'back'},
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
            } 
        });
    }, function(){
      layer.close(confirmobj);
    });
}
function delItem(id) {
    var confirmobj = layer.confirm('你确实要删除此记录吗？', {
      btn: ['确定','取消']
    }, function(){
        $.ajax({
            type : "POST",
            url : "ajax.php?act=opTixian",
            data : {id:id,op:'delete'},
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
            } 
        });
    }, function(){
      layer.close(confirmobj);
    });
}
function operation(id,op) {
    if(op == 'back'){
    }
    var ii = layer.msg('正在操作中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : "POST",
        url : "ajax.php?act=opTixian",
        data : {id:id,op:op},
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
        } 
    });
}
$(document).ready(function(){
    listTable();
})
</script>