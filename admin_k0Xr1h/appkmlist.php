<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
$title='卡密列表';
include_once './header.php';
$numrows=$DB->count("SELECT count(*) from yixi_appkm WHERE 1");
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                应用列表&nbsp;&nbsp;&nbsp;<a href="javascript:searchClear()" class="badge badge-danger-info" title="刷新应用列表"><i class="layui-icon layui-icon-refresh"></i> 刷新</a>
				<span class="badge badge-primary-lighten" onclick="app_kmqk();">清空所有</span>
				<span class="badge badge-primary-lighten" onclick="app_kmqkme();">清空我的卡密</span>
                <span class="badge badge-warning-lighten" onclick="app_kmqk1();">清空已使用</span>
                <span class="badge badge-warning-lighten" onclick="app_kmqkme1();">清空我的已使用</span>
                <span class="badge badge-success-lighten" onclick="app_kmqk2();">清空未使用</span>
                <span class="badge badge-success-lighten" onclick="app_kmqkme2();">清空我的未使用</span>
				<span class="badge badge-info-lighten" onclick="app_kmqk3();">清空已过期</span>
                <span class="badge badge-info-lighten" onclick="app_kmqkme3();">清空我的已过期</span>
                <a href="appkmlist.php?uid=1" class="badge badge-primary-lighten">我的卡密</a>
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
									<option value="0">全部</option><option value="1">未使用</option><option value="2">已使用</option><option value="3">已过期</option>
								</select>
							</div>
						</div>
					<div class="form-group mb-3">
                        <select class="form-control" name="type" lay-search><option value="0">全部</option><option value="1">卡密ID</option><option value="2">卡密</option></select>
                    </div>
                    <div class="form-group mb-3" id="searchword">
                        <input type="text" class="form-control" name="kw" placeholder="搜索内容" value="">
                    </div>
                    <div class="form-group mb-3">
                        <select class="form-control" name="method" lay-search><option value="0">精确搜索</option><option value="1">模糊搜索</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <button class="btn btn-outline-primary" type="submit"><i class="layui-icon layui-icon-search"></i> 搜索</button>&nbsp;
                        <a href="addappkm.php<?php if (isset($_GET['appid']))echo'?appid='.intval($_GET['appid']);?>" class="btn btn-outline-secondary"><i class="layui-icon layui-icon-add-circle"></i> 添加卡密</a>
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
function selectAll(checkbox) {
    $('input[type=checkbox]').prop('checked', $(checkbox).prop('checked'));
}
function listTable(query){
    var url = window.document.location.href.toString();
    var queryString = url.split("?")[1];
    query = query || queryString;
    if(query == 'start' || query == undefined){
        query = '';
        history.replaceState({}, null, './appkmlist.php');
    }else if(query != undefined){
        history.replaceState({}, null, './appkmlist.php?'+query);
    }
    layer.closeAll();
    var ii = layer.msg('正在获取卡密列表中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : 'GET',
        url : 'appkmlist-table.php?'+query,
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
function app_kmqk() {
    var confirmobj = layer.confirm('你确定要清空所有卡密吗？', {
      btn: ['确定','取消']
    }, function(){
      $.ajax({
        type : 'GET',
        url : 'ajax.php?act=app_kmqk',
        dataType : 'json',
        success : function(data) {
            if(data.code == 0){
                layer.msg(data.msg, {
                    icon: 6,
                    end: function (layero, index) {
                        window.location.reload();
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
function app_kmqkme() {
    var confirmobj = layer.confirm('你确定要清空我的卡密吗？', {
      btn: ['确定','取消']
    }, function(){
      $.ajax({
        type : 'GET',
        url : 'ajax.php?act=app_kmqkme',
        dataType : 'json',
        success : function(data) {
            if(data.code == 0){
                layer.msg(data.msg, {
                    icon: 6,
                    end: function (layero, index) {
                        window.location.reload();
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
function change(){
    var ii = layer.msg('正在操作中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : 'POST',
        url : 'ajax.php?act=appkm_change',
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
            }
			if(data.code == 1){
                layer.open({
                  type: 1,
                  title: '导出卡密',
                  skin: 'layui-layer-rim',
                  content: data.data
                });
			layer.msg(data.msg, {icon: 6});
			}
        },
        error:function(data){
            layer.msg('请求超时', {icon: 5});
        }
    });
    return false;
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
function app_kmqk1() {
    var confirmobj = layer.confirm('你确定要清空所有已使用的卡密吗？', {
      btn: ['确定','取消']
    }, function(){
      $.ajax({
        type : 'GET',
        url : 'ajax.php?act=app_kmqk1',
        dataType : 'json',
        success : function(data) {
            if(data.code == 0){
                layer.msg(data.msg, {
                    icon: 6,
                    end: function (layero, index) {
                        window.location.reload();
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
function app_kmqkme1() {
    var confirmobj = layer.confirm('你确定要清空我的已使用的卡密吗？', {
      btn: ['确定','取消']
    }, function(){
      $.ajax({
        type : 'GET',
        url : 'ajax.php?act=app_kmqkme1',
        dataType : 'json',
        success : function(data) {
            if(data.code == 0){
                layer.msg(data.msg, {
                    icon: 6,
                    end: function (layero, index) {
                        window.location.reload();
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
function app_kmqk2() {
    var confirmobj = layer.confirm('你确定要清空所有未使用的卡密吗？', {
      btn: ['确定','取消']
    }, function(){
      $.ajax({
        type : 'GET',
        url : 'ajax.php?act=app_kmqk2',
        dataType : 'json',
        success : function(data) {
            if(data.code == 0){
                layer.msg(data.msg, {
                    icon: 6,
                    end: function (layero, index) {
                        window.location.reload();
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
function app_kmqkme2() {
    var confirmobj = layer.confirm('你确定要清空我的未使用的卡密吗？', {
      btn: ['确定','取消']
    }, function(){
      $.ajax({
        type : 'GET',
        url : 'ajax.php?act=app_kmqkme2',
        dataType : 'json',
        success : function(data) {
            if(data.code == 0){
                layer.msg(data.msg, {
                    icon: 6,
                    end: function (layero, index) {
                        window.location.reload();
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
function app_kmqk3() {
    var confirmobj = layer.confirm('你确定要清空所有已过期的卡密吗？', {
      btn: ['确定','取消']
    }, function(){
      $.ajax({
        type : 'GET',
        url : 'ajax.php?act=app_kmqk3',
        dataType : 'json',
        success : function(data) {
            if(data.code == 0){
                layer.msg(data.msg, {
                    icon: 6,
                    end: function (layero, index) {
                        window.location.reload();
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
function app_kmqkme3() {
    var confirmobj = layer.confirm('你确定要清空我的已过期的卡密吗？', {
      btn: ['确定','取消']
    }, function(){
      $.ajax({
        type : 'GET',
        url : 'ajax.php?act=app_kmqkme3',
        dataType : 'json',
        success : function(data) {
            if(data.code == 0){
                layer.msg(data.msg, {
                    icon: 6,
                    end: function (layero, index) {
                        window.location.reload();
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
function kmdel(id) {
    var confirmobj = layer.confirm('你确定要删除这张卡密吗？', {
      btn: ['确定','取消']
    }, function(){
      $.ajax({
        type : 'GET',
        url : 'ajax.php?act=appkm_del&id='+id,
        dataType : 'json',
        success : function(data) {
            if(data.code == 0){
                layer.msg(data.msg, {
                    icon: 6,
                    end: function (layero, index) {
                        window.location.reload();
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
        layer.msg("卡密复制成功", {icon: 6});
        e.clearSelection();
    });
    codeClipboard.on('error', function (e) {
        layer.msg('复制失败,请手动复制~', {icon: 5});
    });
</script>