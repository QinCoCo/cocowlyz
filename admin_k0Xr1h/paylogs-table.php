<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
?>
<?php
if(isset($_GET['kw'])) {
	$kw = daddslashes($_GET['kw']);
	if($_GET['type']==1)
		$sql=($_GET['method']==1)?" `trade_no` LIKE '%{$kw}%'":" `trade_no`='{$kw}'";
	elseif($_GET['type']==2)
		$sql=($_GET['method']==1)?" `name` LIKE '%{$kw}%'":" `name`='{$kw}'";
	elseif($_GET['type']==3)
		$sql=($_GET['method']==1)?" `input` LIKE '%{$kw}%'":" `input`='{$kw}'";
	else{
		$sql=($_GET['method']==1)?" `{$column}` LIKE '%{$kw}%'":" `{$column}`='{$kw}'";
	}
	$numrows=$DB->count("SELECT count(*) from yixi_paysite WHERE{$sql}");
	$con='包含 '.$kw.' 的共有 <b>'.$numrows.'</b> 条支付记录';
	$link='&kw='.$_GET['kw'];
}else{
	$numrows=$DB->count("SELECT count(*) from yixi_paysite WHERE 1");
	$sql=" 1";
	$con='平台共有 <b>'.$numrows.'</b> 条支付记录';
}
?>
     <div style="white-space:nowrap;overflow-x: auto;">
        <table class="layui-table layuiadmin-page-table">
          <thead><tr><th>订单号</th><th>商品名称</th><th>订单数据</th><th>支付方式</th><th>金额</th><th>状态</th><th>时间</th></tr></thead>
          <tbody>
<?php
$pagesize=isset($_GET['num'])?intval($_GET['num']):30;
$pages=ceil($numrows/$pagesize);
$page=isset($_GET['page'])?intval($_GET['page']):1;
$offset=$pagesize*($page - 1);
$rs=$DB->query("SELECT * FROM yixi_pay WHERE{$sql} order by addtime desc limit $offset,$pagesize");
while($res = $DB->fetch($rs))
{
if ($res['status']==1) {
    $status= '<span class="layui-btn layui-btn-xs btn-success">成功</span>';
}else{
    $status= '<span class="layui-btn layui-btn-xs btn-danger">未付款</span>';
}
echo '<tr><td><b>'.$res['trade_no'].'</b></td><td>'.$res['name'].'</td><td>'.$res['input'].'</td><td>'.$res['types'].'</td><td>'.$res['money'].'</td><td>'.$status.'</td><td>'.$res['addtime'].'</td></tr>';
}
?>
          </tbody>
        </table>
      </div>
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