<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
$appid=intval($_GET['id']);
?>
<?php
if(isset($_GET['kw'])) {
	$kw = daddslashes($_GET['kw']);
	if($_GET['type']==1)
		$sql=($_GET['method']==1)?" AND `uid` LIKE '%{$kw}%'":" AND `uid`='{$kw}'";
	elseif($_GET['type']==2)
		$sql=($_GET['method']==1)?" AND `name` LIKE '%{$kw}%'":" AND `user`='{$kw}'";
	elseif($_GET['type']==3)
		$sql=($_GET['method']==1)?" AND `user` LIKE '%{$kw}%'":" AND `user`='{$kw}'";
	elseif($_GET['type']==4)
		$sql=($_GET['method']==1)?" AND `qq` LIKE '%{$kw}%'":" AND `qq`='{$kw}'";
	else{
		if(is_numeric($kw))$column='qq';
		$sql=($_GET['method']==1)?" AND `{$column}` LIKE '%{$kw}%'":" AND `{$column}`='{$kw}' AND `appid`='{$appid}'";
	}
	$numrows=$DB->count("SELECT count(*) from yixi_appuser WHERE `appid`='{$appid}'{$sql}");
	$con='包含 '.$kw.' 的共有 <b>'.$numrows.'</b> 个用户';
	$link='&kw='.$_GET['kw'];
}else{
	$numrows=$DB->count("SELECT count(*) from yixi_appuser WHERE `appid`='{$appid}'");
	$con='我共有 <b>'.$numrows.'</b> 个用户';
}
?>
     <div style="white-space:nowrap;overflow-x: auto;">
        <table class="layui-table layuiadmin-page-table">
          <thead><tr><th>用户UID</th><th>用户头像</th><th>用户名称</th><th>用户帐号</th><th>用户QQ</th><th>用户积分</th><th>用户余额</th><th>添加时间</th><th>是否激活</th><th>操作</th></tr></thead>
          <tbody>
<?php
$pagesize=isset($_GET['num'])?intval($_GET['num']):30;
$pages=ceil($numrows/$pagesize);
$page=isset($_GET['page'])?intval($_GET['page']):1;
$offset=$pagesize*($page - 1);
$rs=$DB->query("SELECT * FROM yixi_appuser WHERE `appid`='{$appid}'{$sql} order by uid desc limit $offset,$pagesize");
while($res = $DB->fetch($rs))
{
echo '<tr><td>'.$res['uid'].'</td><td><img src="http://q4.qlogo.cn/headimg_dl?dst_uin='.$res['qq'].'&spec=640" style="height: 50px;width: 50px" class="img-rounded img-circle img-thumbnail"></td><td>'.$res['name'].'</td><td>'.$res['user'].'</td><td>'.$res['qq'].'&nbsp;<a href="http://wpa.qq.com/msgrd?v=3&uin='.$res['qq'].'&site=qq&menu=yes">[<img src="../assets/img/qqpay.png" width="24">]</a></td><td><span class="layui-btn layui-btn-xs btn-primary" style="background:linear-gradient(to right, #7C4DFF,#536DFE,#9575CD);" onclick="AppUserFenCharge('.$res['uid'].')"><font color="white">'.$res['fen'].'</font></span></td><td><span class="layui-btn layui-btn-xs btn-primary" style="background:linear-gradient(to right, #7C4DFF,#536DFE,#9575CD);" onclick="AppUserReCharge('.$res['uid'].')"><font color="white">'.$res['rmb'].'</font></span></td><td>'.$res['reg_time'].'</td><td><input type="checkbox" id="switch'.$res['uid'].'" onclick="Active('.$res['uid'].')"'.($res['status']==y?' checked ':' ').'data-switch="success"/><label for="switch'.$res['uid'].'" data-on-label="是" data-off-label="否" class="mb-0 d-block"></label></td><td><a href="./appuseredit.php?id='.intval($_GET['id']).'&uid='.$res['uid'].'" class="layui-btn layui-btn-xs btn-info">编辑</a><span class="layui-btn layui-btn-xs btn-primary" onclick="czpass('.$res['uid'].')">重置密码</span><span class="layui-btn layui-btn-xs btn-danger" onclick="userdel('.$res['uid'].');">删除</a></td></tr>';
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