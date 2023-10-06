<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
?>
<?php
if(isset($_GET['kw'])&&($_GET['appid'])&&($_GET['filetype'])) {
    $kw = daddslashes($_GET['kw']);
	$appid =daddslashes($_GET['appid']);
	$filetype =daddslashes($_GET['filetype']);
	if ($filetype==1) {
		$filetype = " AND `type`= 'lanzou'";
	}else if ($filetype==2) {
		$filetype = " AND `type`= 'other'";
    }
	    $appid = " AND `appid`='{$appid}'";
    if($_GET['type']==1)
        $sql=($_GET['method']==1)?"`id` LIKE '%{$kw}%'{$appid}{$filetype}":"`id`='{$kw}'{$filetype}";
	elseif($_GET['type']==2)
        $sql=($_GET['method']==1)?"`file_url` LIKE '%{$kw}%'{$appid}{$filetype}":"`file_url`='{$kw}'{$appid}{$filetype}";
    else{
        $sql=($_GET['method']==1)?"`{$column}` LIKE '%{$kw}%'{$appid}{$filetype}":"`{$column}`='{$kw}'{$appid}{$filetype}";
    }
    $numrows=$DB->count("SELECT count(*) from yixi_appfile WHERE {$sql}");
    $con='包含 '.$kw.' 的共有 <b>'.$numrows.'</b> 个文件';
    $link='&kw='.$kw;

}elseif(isset($_GET['appid'])&&($_GET['filetype'])) {
	$appid =intval($_GET['appid']);
	$filetype =intval($_GET['filetype']);
	$row=$DB->get_row("SELECT * FROM yixi_apps WHERE id='{$appid}' limit 1");
		if ($filetype==1) {
		$sql = "`type`= 'lanzou' AND `appid`='{$appid}'";
		$name = $row['name'].'蓝奏云';
    }else if ($filetype==2) {
		$sql = "`type`= 'other' AND `appid`='{$appid}'";
		$name = $row['name'].'其他外链';
    }
    $numrows=$DB->count("SELECT count(*) from yixi_appfile WHERE {$sql}");
    $con='包含 '.$name.' 的共有 <b>'.$numrows.'</b> 个文件';
    $link='&appid='.$appid.'&filetype='.$filetype;
}elseif(isset($_GET['appid'])) {
	$appid =intval($_GET['appid']);
	$sql = "`appid`='{$appid}'";
    $row=$DB->get_row("SELECT * FROM yixi_apps WHERE id='{$appid}' limit 1");
    $numrows=$DB->count("SELECT count(*) from yixi_appfile WHERE `appid`='{$appid}'");
    $con='包含 '.$row['name'].' 的共有 <b>'.$numrows.'</b> 个文件';
    $link='&appid='.$appid;
}elseif(isset($_GET['filetype'])) {
	$filetype =intval($_GET['filetype']);
	if ($filetype==1) {
		$sql = "`type`= 'lanzou'";
		$name = '蓝奏云';
    }else if ($filetype==2) {
		$sql = "`type`= 'other'";
		$name = '其他外链';
    }
    $numrows=$DB->count("SELECT count(*) from yixi_appfile WHERE {$sql}");
    $con='包含 '.$name.' 的共有 <b>'.$numrows.'</b> 个文件';
    $link='&filetype='.$filetype;
}elseif(isset($_GET['kw'])&&($_GET['type'])) {
	$kw = daddslashes($_GET['kw']);
    if($_GET['type']==1)
        $sql=($_GET['method']==1)?"`id` LIKE '%{$kw}%'":"`id`='{$kw}'";
	elseif($_GET['type']==2)
        $sql=($_GET['method']==1)?"`file_url` LIKE '%{$kw}%'":"`file_url`='{$kw}'";
    else{
        $sql=($_GET['method']==1)?"`{$column}` LIKE '%{$kw}%'":"`{$column}`='{$kw}'";
    }
    $numrows=$DB->count("SELECT count(*) from yixi_appfile WHERE {$sql}");
    $con='包含 '.$kw.' 的共有 <b>'.$numrows.'</b> 个文件';
    $link='&kw='.$kw;
}elseif(isset($_GET['uid'])) {
    $uid=intval($_GET['uid']);
    $sql=" `uid`='{$uid}'";
    $numrows=$DB->count("SELECT count(*) from yixi_appfile WHERE{$sql}");
    $con='用户(UID:'.$upid.')共有 <b>'.$numrows.'</b> 个文件';
    $link='&uid='.$_GET['uid'];
}else{
    $numrows=$DB->count("SELECT count(*) from yixi_appfile WHERE 1");
    $sql=" 1";
    $con='当前共有 <b>'.$numrows.'</b> 个文件';
}
?>
     <div style="white-space:nowrap;overflow-x: auto;">
           <table class="layui-table layuiadmin-page-table">
               <thead><tr><th>ID</th><th>归属应用</th><th>云端类型</th><th>外链地址</th><th>添加时间</th><th>是否有效</th><th>操作</th></tr></thead>
                   <tbody>
<?php
$pagesize=isset($_GET['num'])?intval($_GET['num']):30;
$pages=ceil($numrows/$pagesize);
$page=isset($_GET['page'])?intval($_GET['page']):1;
$offset=$pagesize*($page - 1);
$rs=$DB->query("SELECT * FROM yixi_appfile WHERE {$sql} order by id desc limit $offset,$pagesize");
while($res = $DB->fetch($rs))
{
$program = $DB->get_row("select * from yixi_apps where id='" . $res['appid'] . "' limit 1");
if ($res['type'] == 'lanzou') {
    $type_name = '蓝奏云';
} else {
    $type_name = '其他外链';
}
echo '<tr><td>'.$res['id'].'</td><td><i class="layui-icon layui-icon-component"></i>'.$program['name'].'</td><td>'.$type_name.'</td><td><span id="btn_code" value="'.$res['file_url'].'">'.$res['file_url'].($res['lanzou_pass']!=NULL?' 密码:'.$res['lanzou_pass']:'').' <span class="layui-btn layui-btn-xs btn-success"  data-clipboard-text="'.$res['file_url'].($res['lanzou_pass']!=NULL?' 密码:'.$res['lanzou_pass']:'').'" data-clipboard-action="copy" data-clipboard-target="#btn_code" id="btn_code">复制</span></td><td>'.$res['addtime'].'</td><td><input type="checkbox" id="switchsss'.$res['id'].'" onclick="Active(\'fileactive\','.$res['id'].')"'.($res['state']==y?' checked ':' ').'data-switch="success"/><label for="switchsss'.$res['id'].'" data-on-label="是" data-off-label="否" class="mb-0 d-block"></label></td><td><span class="layui-btn layui-btn-xs btn-danger" onclick="filedel('.$res['id'].');">删除</a></td></tr>';
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