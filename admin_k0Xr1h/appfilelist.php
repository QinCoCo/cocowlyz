<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
$title='文件列表';
include_once './header.php';
$numrows=$DB->count("SELECT count(*) from yixi_appfile WHERE 1");
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                文件列表&nbsp;&nbsp;&nbsp;<a href="javascript:searchClear()" class="badge badge-danger-info" title="刷新应用列表"><i class="layui-icon layui-icon-refresh"></i> 刷新</a>
				<a href="appfilelist.php?uid=1" class="badge badge-info-lighten">我的文件</a>
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
								<select class="form-control" name="filetype" lay-search>
									<option value="0">全部</option><option value="1">蓝奏云</option><option value="2">其他外链</option>
								</select>
							</div>
						</div>
					<div class="form-group mb-3">
                        <select class="form-control" name="type" lay-search><option value="0">全部</option><option value="1">文件ID</option><option value="2">外链地址</option></select>
                    </div>
                    <div class="form-group mb-3" id="searchword">
                        <input type="text" class="form-control" name="kw" placeholder="搜索内容" value="">
                    </div>
                    <div class="form-group mb-3">
                        <select class="form-control" name="method" lay-search><option value="0">精确搜索</option><option value="1">模糊搜索</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <button class="btn btn-outline-primary" type="submit"><i class="layui-icon layui-icon-search"></i> 搜索</button>&nbsp;
                        <a href="addappfile.php?appid=<?php echo intval($_GET['appid']);?>" class="btn btn-outline-secondary"><i class="layui-icon layui-icon-add-circle"></i> 添加文件</a>
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
        history.replaceState({}, null, './appfilelist.php');
    }else if(query != undefined){
        history.replaceState({}, null, './appfilelist.php?'+query);
    }
    layer.closeAll();
    var ii = layer.msg('正在获取卡密列表中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : 'GET',
        url : 'appfilelist-table.php?'+query,
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
	var zfiletype=$("select[name='filetype']").val();
    var ztype=$("select[name='type']").val();
    var zkw=$("input[name='kw']").val();
    var method=$("select[name='method']").val();
    if(appid=='0'&&filetype=='0'&&kw=='0'){
        listTable('start');
    }else{
       if(zappid=='0'){
		var appid='';
	  }else {
        var appid='appid='+zappid;
	  }
	  if(zfiletype=='0'){
		var filetype='';
	  }else {
        var filetype='&filetype='+zfiletype;
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
        listTable(appid+filetype+type+kw+'&method='+method);
    }
    return false;
}
function searchClear(){
    $("select[name='type']").val(0);
	$("select[name='filetype']").val(0);
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
function filedel(id) {
    var confirmobj = layer.confirm('你确定要删除这个文件吗？', {
      btn: ['确定','取消']
    }, function(){
      $.ajax({
        type : 'GET',
        url : 'ajax.php?act=appfile_del&id='+id,
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
        layer.msg("文件外链复制成功", {icon: 6});
        e.clearSelection();
    });
    codeClipboard.on('error', function (e) {
        layer.msg('复制失败,请手动复制~', {icon: 5});
    });
</script>