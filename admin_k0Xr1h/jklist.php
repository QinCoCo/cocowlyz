<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
$title='接口列表';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                接口列表&nbsp;&nbsp;&nbsp;<a href="javascript:searchClear()" class="badge badge-danger-info" title="刷新接口列表"><i class="layui-icon layui-icon-refresh"></i> 刷新</a>
            <a href="jklist.php?uid=1" class="badge badge-primary-lighten">我的接口</a>
            </div>
            <div class="card-body">
                <div class="layui-elem-quote" id="blocktitle"></div>
                <form onsubmit="return searchOrder()" method="GET" class="form layui-form">
				<div class="form-row">
								<div class="form-group col-md-5">
								<select name="appid" class="form-control" lay-search>
								<option value="0">全部</option>
                                   <?php
                                   $rs=$DB->query("SELECT * FROM yixi_apps WHERE 1 order by id desc");
                                   while($res = $DB->fetch($rs))
                                  {
                                  echo '<option value="'.$res['id'].'">'.$res['name'].'</option>';
                                  }
                                   ?>
                                   </select>
							</div>
							<div class="form-group col-md-7">
								<select class="form-control" name="use" lay-search>
									<option value="0">全部</option><option value="1">未激活</option><option value="2">已激活</option>
								</select>
							</div>
							</div>
					<div class="form-group mb-3">
                        <select class="form-control" name="type" lay-search><option value="0">全部</option><option value="1">接口ID</option><option value="2">累计调用</option></select>
                    </div>
                    <div class="form-group mb-3" id="searchword">
                        <input type="text" class="form-control" name="kw" placeholder="搜索内容" value="">
                    </div>
                    <div class="form-group mb-3">
                        <select class="form-control" name="method" lay-search><option value="0">精确搜索</option><option value="1">模糊搜索</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <button class="btn btn-outline-primary" type="submit"><i class="layui-icon layui-icon-search"></i> 搜索</button>&nbsp;
                        <a href="addjk.php" class="btn btn-outline-secondary"><i class="layui-icon layui-icon-add-circle"></i> 添加接口</a>&nbsp;
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
        history.replaceState({}, null, './jklist.php');
    }else if(query != undefined){
        history.replaceState({}, null, './jklist.php?'+query);
    }
    layer.closeAll();
    var ii = layer.msg('正在获取接口列表中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : 'GET',
        url : 'jklist-table.php?'+query,
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
	var zappid=$("select[name='appid']").val();
	var zuse=$("select[name='use']").val();
    var ztype=$("select[name='type']").val();
    var zkw=$("input[name='kw']").val();
    var method=$("select[name='method']").val();
    if(appid=='0'&&use=='0'&&kw=='0'){
        listTable('start');
    }else{
       if(zappid=='0'){
		var appid='';
	  }else {
        var appid='appid='+zappid;
	  }
	  if(zuse=='0'){
		var use='';
	  }else {
        var use='&use='+zuse;
	  }
	  if(ztype=='0'){
		var type='';
	  }else {
        var type='&type='+ztype;
	  }
	  if(zkw==''){
		var kw='';
	  }else {
        var kw='&kw='+zkw;
	  }
        listTable(appid+use+type+kw+'&method='+method);
    }
    return false;
}
function searchClear(){
    $("select[name='type']").val(0);
    $("input[name='kw']").val('');
    $("select[name='method']").val(0);
    listTable('start');
}
function getjknote(id) {
    var ii = layer.msg('正在获取中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : 'GET',
        url : 'ajax.php?act=getjkNote&id='+id,
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
function savejknote(id) {
    var jknote=$("#jknote").val();
    $('#save').val('Loading');
    var ii = layer.msg('正在修改中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : "POST",
        url : "ajax.php?act=editjkNote",
        data : {id:id,jknote:jknote},
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
function Active(id) {
    $.ajax({
        type : 'GET',
        url : 'ajax.php?act=jk_active&id='+id,
        dataType : 'json',
        success : function(data) {
        },
        error:function(data){
            layer.msg('服务器错误', {icon: 5});
            return false;
        }
    });
}
function jkdel(id) {
    var confirmobj = layer.confirm('你确实要删除此接口吗？', {
      btn: ['确定','取消']
    }, function(){
      $.ajax({
        type : 'GET',
        url : 'ajax.php?act=jkdel&id='+id,
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