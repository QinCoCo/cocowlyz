<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
?>
<?php
if(isset($_GET['kw'])) {
	$kw = daddslashes($_GET['kw']);
	if($_GET['type']==1)
		$sql=($_GET['method']==1)?" `name` LIKE '%{$kw}%'":" `name`='{$kw}'";
	elseif($_GET['type']==2)
		$sql=($_GET['method']==1)?" `qq` LIKE '%{$kw}%'":" `qq`='{$kw}'";
	elseif($_GET['type']==3)
		$sql=($_GET['method']==1)?" `url` LIKE '%{$kw}%'":" `url`='{$kw}'";
	else{
		if(is_numeric($kw))$column='qq';
		elseif(strpos($kw,'.')!==false)$column='url';
		$sql=($_GET['method']==1)?" `{$column}` LIKE '%{$kw}%'":" `{$column}`='{$kw}'";
	}
	$numrows=$DB->count("SELECT count(*) from yixi_paysite WHERE{$sql}");
	$con='包含 '.$kw.' 的共有 <b>'.$numrows.'</b> 个易支付认证域名';
	$link='&kw='.$_GET['kw'];
}elseif(isset($_GET['qq'])) {
	$qq=daddslashes($_GET['qq']);
	$sql=" `qq`='{$qq}'";
	$numrows=$DB->count("SELECT count(*) from yixi_paysite WHERE{$sql}");
	$con='QQ '.$_GET['qq'].' 共有 <b>'.$numrows.'</b> 个易支付认证域名';
	$link='&qq='.$_GET['qq'];
}elseif(isset($_GET['uid'])) {
	$uid=intval($_GET['uid']);
	$sql=" `uid`='{$uid}'";
	$numrows=$DB->count("SELECT count(*) from yixi_paysite WHERE{$sql}");
	$con='代理用户(UID:'.$uid.')共有 <b>'.$numrows.'</b> 个易支付认证域名';
	$link='&uid='.$_GET['uid'];
}else{
	$numrows=$DB->count("SELECT count(*) from yixi_paysite WHERE 1");
	$sql=" 1";
	$con='平台共有 <b>'.$numrows.'</b> 个易支付认证域名';
}
?>
     <div style="white-space:nowrap;overflow-x: auto;">
        <table class="layui-table layuiadmin-page-table">
          <thead><tr><th>认证ID</th><th>归属程序</th><th>站点名称</th><th>认证用户头像</th><th>认证ＱＱ</th><th>认证域名</th><th>认证时间</th><th>是否激活</th><th>操作</th></tr></thead>
          <tbody>
<?php
$pagesize=isset($_GET['num'])?intval($_GET['num']):30;
$pages=ceil($numrows/$pagesize);
$page=isset($_GET['page'])?intval($_GET['page']):1;
$offset=$pagesize*($page - 1);
$rs=$DB->query("SELECT * FROM yixi_paysite WHERE{$sql} order by id desc limit $offset,$pagesize");
while($res = $DB->fetch($rs))
{
$program = $DB->get_row("select * from yixi_program where id='" . $res['proid'] . "' limit 1");
echo '<tr><td>'.$res['id'].'</td><td>'.$program['name'].'</td><td>'.$res['name'].'</td><td><img src="http://q4.qlogo.cn/headimg_dl?dst_uin='.$res['qq'].'&spec=640" style="height: 50px;width: 50px" class="img-rounded img-circle img-thumbnail"></td><td><span onclick="listTable(\'qq='.$res['qq'].'\')">'.$res['qq'].'</span>&nbsp;<a href="http://wpa.qq.com/msgrd?v=3&uin='.$res['qq'].'&site=qq&menu=yes">[<img src="../assets/img/qqpay.png" width="24">]</a></td><td><a href="http://'.$res['url'].'/" target="_blank">'.$res['url'].'</a></td><td>'.$res['date'].'</td><td><input type="checkbox" id="switch'.$res['id'].'" onclick="Active('.$res['id'].')"'.($res['active']==1?' checked ':' ').'data-switch="success"/><label for="switch'.$res['id'].'" data-on-label="是" data-off-label="否" class="mb-0 d-block"></label></td><td><a href="./payedit.php?id='.$res['id'].'" class="layui-btn layui-btn-xs btn-info">编辑</a><span class="layui-btn layui-btn-xs btn-danger" onclick="paydel('.$res['id'].')">删除</span></td></tr>';
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