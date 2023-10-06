<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
function display_type($type){
	global $conf;
	$types = explode('|', $conf['workorder_type']);
	if($type==0 || !array_key_exists($type-1,$types))
		return '其它问题';
	else
		return $types[$type-1];
}
function display_status($status){
	if($status==1)
		return '<font color="red">处理中</font>';
	elseif($status==2)
		return '<font color="green">已完成</font>';
	else
		return '<font color="blue">待处理</font>';
}
?>
<?php
if(isset($_GET['status'])){
	$status = intval($_GET['status']);
	$sql = " status={$status}";
    if($type==1){
        $typename = '处理中';
    }elseif($type==2){
        $typename = '已完成';
    }else{
        $typename = '待处理';
    }
	$numrows=$DB->count("SELECT count(*) from yixi_workorder WHERE{$sql}");
	$con=$typename.' 的共有 <b>'.$numrows.'</b> 条工单';
	$link='&kw='.$_GET['kw'];
}elseif(isset($_GET['kw'])) {
	$kw = daddslashes($_GET['kw']);
	if($_GET['type']==1)
		$sql=($_GET['method']==1)?" `uid` LIKE '%{$kw}%'":" `uid`='{$kw}'";
	elseif($_GET['type']==2)
		$sql=($_GET['method']==1)?" `content` LIKE '%{$kw}%'":" `content`='{$kw}'";
	else{
		$sql=($_GET['method']==1)?" `{$column}` LIKE '%{$kw}%'":" `{$column}`='{$kw}'";
	}
	$numrows=$DB->count("SELECT count(*) from yixi_workorder WHERE{$sql}");
	$con='包含 '.$kw.' 的共有 <b>'.$numrows.'</b> 条工单';
	$link='&kw='.$_GET['kw'];
}else{
	$numrows=$DB->count("SELECT count(*) from yixi_workorder WHERE 1");
	$sql=" 1";
	$con='平台共有 <b>'.$numrows.'</b> 条工单';
}
?>
    <form name="form1" id="form1">
     <div style="white-space:nowrap;overflow-x: auto;">
        <table class="layui-table layuiadmin-page-table">
          <thead><tr><th><div class="custom-control custom-checkbox"><input name="chkAll1" type="checkbox" id="chkAll1" onclick="selectAll(this);" value="checkbox" class="custom-control-input"><label class="custom-control-label" for="chkAll1"></label></div></th><th>ID</th><th>UID</th><th>类型</th><th>问题描述</th><th>状态</th><th>提交时间</th><th>操作</th></tr></thead>
          <tbody>
<?php
$pagesize=isset($_GET['num'])?intval($_GET['num']):30;
$pages=ceil($numrows/$pagesize);
$page=isset($_GET['page'])?intval($_GET['page']):1;
$offset=$pagesize*($page - 1);
$rs=$DB->query("SELECT * FROM yixi_workorder WHERE{$sql} order by id desc limit $offset,$pagesize");
while($res = $DB->fetch($rs))
{
$content=explode('*',$res['content']);
$content=mb_substr($content[0], 0, 16, 'utf-8');
echo '<tr><td><div class="custom-control custom-checkbox"><input type="checkbox" name="checkbox[]" id="workorder'.$res['id'].'" value="'.$res['id'].'" class="custom-control-input"><label class="custom-control-label" for="workorder'.$res['id'].'"></label></div><td>'.$res['id'].'</td><td><a href="./userlist.php?type=1&kw='.$res['uid'].'&method=0" target="_blank">'.$res['uid'].'</a></td><td>'.display_type($res['type']).'</td><td><a href="workorder-details.php?id='.$res['id'].'">'.htmlspecialchars($content).'</a></td><td>'.display_status($res['status']).'</td><td>'.$res['addtime'].'</td><td><a href="workorder-details.php?id='.$res['id'].'" class="layui-btn layui-btn-xs btn-info">查看</a><a href="javascript:delworkorder('.$res['id'].')" class="layui-btn layui-btn-xs btn-danger">删除</a></td></tr>';
}
?>
          </tbody>
        </table>
        <div class="form-group mb-3">
        <input type="hidden" name="content"/>
        <label for="example-input-normal" style="font-weight: 500">操作：
        <select class="form-control" style="display: inline;width: auto;" name="aid"><option selected>批量操作</option><option value="1">&gt;改为待处理</option><option value="2">&gt;改为已完成</option><option value="3">&gt;批量回复</option><option value="4">&gt;删除选中</option></select>
        <button class="btn btn-sm btn-primary" type="button" onclick="change()">确定</button>
        </label>
        </div>
      </div>
	  </form>
<div class="text-center">
<?php
#分页
$pageList=new Page($numrows,$pagesize,1,$link);
echo $pageList->showPage();
?>
</div>
<script>
$("#blocktitle").html('<?php echo $con?>');
</script>