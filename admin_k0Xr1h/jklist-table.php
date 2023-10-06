<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
?>
<?php
if(isset($_GET['kw'])&&($_GET['appid'])&&($_GET['use'])) {
    $kw = daddslashes($_GET['kw']);
	$appid =daddslashes($_GET['appid']);
	$use =daddslashes($_GET['use']);
	if ($use==1) {
		$use = " AND `active`= 'n'";
	}else if ($use==2) {
		$use = " AND `active`= 'y'";
    }
	    $appid = " AND `appid`='{$appid}'";
    if($_GET['type']==1)
        $sql=($_GET['method']==1)?"`id` LIKE '%{$kw}%'{$appid}{$use}":"`id`='{$kw}'{$use}";
	elseif($_GET['type']==2)
        $sql=($_GET['method']==1)?" `total` LIKE '%{$kw}%'{$appid}{$use}":"`total`='{$kw}'{$appid}{$use}";
    else{
        $sql=($_GET['method']==1)?"`{$column}` LIKE '%{$kw}%'{$appid}{$use}":"`{$column}`='{$kw}'{$appid}{$use}";
    }
    $numrows=$DB->count("SELECT count(*) from yixi_userjk WHERE {$sql}");
    $con='包含 '.$kw.' 的共有 <b>'.$numrows.'</b> 个接口';
    $link='&kw='.$kw;
}elseif(isset($_GET['appid'])&&($_GET['use'])) {
	$appid =intval($_GET['appid']);
	$use =intval($_GET['use']);
	$row=$DB->get_row("SELECT * FROM yixi_apps WHERE id='{$appid}' limit 1");
		if ($use==1) {
		$sql = "`active`= 'n' AND `appid`='{$appid}'";
		$name = $row['name'].'未激活';
    }else if ($use==2) {
		$sql = "`active`= 'y' AND `appid`='{$appid}'";
		$name = $row['name'].'已激活';
    }
    $numrows=$DB->count("SELECT count(*) from yixi_userjk WHERE '{$sql}");
    $con='包含 '.$name.' 的共有 <b>'.$numrows.'</b> 个接口';
    $link='&appid='.$appid.'&use='.$use;
}elseif(isset($_GET['appid'])) {
	$appid =intval($_GET['appid']);
	$sql = "`appid`='{$appid}'";
    $row=$DB->get_row("SELECT * FROM yixi_apps WHERE id='{$appid}' limit 1");
    $numrows=$DB->count("SELECT count(*) from yixi_userjk WHERE `appid`='{$appid}'");
    $con='包含 '.$row['name'].' 的共有 <b>'.$numrows.'</b> 个接口';
    $link='&appid='.$appid;
}elseif(isset($_GET['use'])) {
	$use =intval($_GET['use']);
	if ($use==1) {
		$sql = "`active`= 'n'";
		$name = '未激活';
    }else if ($use==2) {
		$sql = "`active`= 'y'";
		$name = '已激活';
    }
    $numrows=$DB->count("SELECT count(*) from yixi_userjk WHERE {$sql}");
    $con='包含 '.$name.' 的共有 <b>'.$numrows.'</b> 个接口';
    $link='&use='.$use;
}elseif(isset($_GET['kw'])&&($_GET['type'])) {
	$kw = daddslashes($_GET['kw']);
    if($_GET['type']==1)
        $sql=($_GET['method']==1)?"`id` LIKE '%{$kw}%'":"`id`='{$kw}'";
	elseif($_GET['type']==2)
        $sql=($_GET['method']==1)?"`total` LIKE '%{$kw}%'":"`total`='{$kw}'";
    else{
        $sql=($_GET['method']==1)?"`{$column}` LIKE '%{$kw}%'":"`{$column}`='{$kw}'";
    }
    $numrows=$DB->count("SELECT count(*) from yixi_userjk WHERE {$sql}");
    $con='包含 '.$kw.' 的共有 <b>'.$numrows.'</b> 个接口';
    $link='&kw='.$kw;
}elseif(isset($_GET['uid'])) {
    $uid=intval($_GET['uid']);
    $sql="`uid`='{$uid}'";
    $numrows=$DB->count("SELECT count(*) from yixi_userjk WHERE {$sql}");
    $con='代理用户(UID:'.$uid.')共有 <b>'.$numrows.'</b> 个接口';
    $link='&uid='.$_GET['uid'];
}else{
    $numrows=$DB->count("SELECT count(*) from yixi_userjk WHERE 1");
    $sql=" 1";
    $con='本系统共有 <b>'.$numrows.'</b> 个接口';
}
?>
     <div style="white-space:nowrap;overflow-x: auto;">
        <table class="layui-table layuiadmin-page-table">
          <thead><tr><th>接口ID</th><th>归属应用</th><th>归属用户</th><th>接口名称</th><th>接口说明</th><th>备注</th><th>请求地址</th><th>累计调用</th><th>开通时间</th><th>到期时间</th><th>是否激活</th><th>操作</th></tr></thead>
          <tbody>
<?php
$pagesize=isset($_GET['num'])?intval($_GET['num']):30;
$pages=ceil($numrows/$pagesize);
$page=isset($_GET['page'])?intval($_GET['page']):1;
$offset=$pagesize*($page - 1);
$rs=$DB->query("SELECT * FROM yixi_userjk WHERE {$sql} order by id desc limit $offset,$pagesize");
while($res = $DB->fetch($rs))
{
$program = $DB->get_row("select * from yixi_program where id='" . $res['proid'] . "' limit 1");
$app = $DB->get_row("select * from yixi_apps where id='" . $res['appid'] . "' limit 1");
$users = $DB->get_row("select * from yixi_user where uid='" . $res['uid'] . "' limit 1");
if($res['uid']=="1") {
$users['user']="admin";
$users['qq']=$conf['admin_qq'];
}
if($res['note']==NULL){$note='<i class="layui-icon layui-icon-dialogue"></i>备注';}else{$note=$res['note'];}
echo '<tr><td onclick="layer.alert(\'用户QQ：'.$users['qq'].'\n\r授权码：'.$res['authcode'].'\n\r特征码：'.$res['sign'].'\n\rTOKEN密钥：'.$res['token'].'\')">'.$res['id'].'</td><td>'.$app['name'].'</td><td>'.$users['user'].'</td><td>'.$program['name'].'</td><td>'.$program['desc'].'</td><td><span onclick="getjknote('.$res['id'].')" title="备注">'.$note.'</span></td><td><a href="../api.php?api='.$program['api_path'].'&app='.$res['appid'].'" target="_blank">api.php?api='.$program['api_path'].'&app='.$res['appid'].'</a></td><td>'.$res['total'].'次</td><td>'.$res['date'].'</td><td>'.$res['endtime'].'</td><td><input type="checkbox" id="switch'.$res['id'].'" onclick="Active('.$res['id'].')"'.($res['active']==y?' checked ':' ').'data-switch="success"/><label for="switch'.$res['id'].'" data-on-label="是" data-off-label="否" class="mb-0 d-block"></label></td><td><a href="../doc.php?act='.$program['api_path'].'" target="_blank" class="layui-btn layui-btn-xs btn-warning">文档</a><span class="layui-btn layui-btn-xs btn-danger" onclick="jkdel('.$res['id'].')">删除</span></td></tr>';
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