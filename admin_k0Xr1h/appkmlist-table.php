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
		$use = " AND `km_use`= 'n'";
	}else if ($use==2) {
		$use = " AND `km_use`= 'y'";
    }
	    $appid = " AND `appid`='{$appid}'";
    if($_GET['type']==1)
        $sql=($_GET['method']==1)?" `id` LIKE '%{$kw}%'{$appid}{$use}":"`id`='{$kw}'{$use}";
	elseif($_GET['type']==2)
        $sql=($_GET['method']==1)?" `kami` LIKE '%{$kw}%'{$appid}{$use}":"`kami`='{$kw}'{$appid}{$use}";
    else{
        $sql=($_GET['method']==1)?" `{$column}` LIKE '%{$kw}%'{$appid}{$use}":"`{$column}`='{$kw}'{$appid}{$use}";
    }
    $numrows=$DB->count("SELECT count(*) from yixi_appkm WHERE {$sql}");
    $con='包含 '.$kw.' 的共有 <b>'.$numrows.'</b> 个卡密';
    $link='&kw='.$kw;

}elseif(isset($_GET['appid'])&&($_GET['use'])) {
	$appid =intval($_GET['appid']);
	$use =intval($_GET['use']);
	$row=$DB->get_row("SELECT * FROM yixi_apps WHERE `id`='{$appid}' limit 1");
		if ($use==1) {
		$sql = "`km_use`= 'n' AND `appid`='{$appid}'";
		$name = $row['name'].'未使用';
    }else if ($use==2) {
		$sql = "`km_use`= 'y' AND `appid`='{$appid}'";
		$name = $row['name'].'已使用';
    }else if ($use==3) {
		$sql = "`end_time`< '".time()."' AND `appid`='{$appid}' or `amount`< '1'";
		$name = $row['name'].'已过期';
    }
    $numrows=$DB->count("SELECT count(*) from yixi_appkm WHERE {$sql}");
    $con='包含 '.$name.' 的共有 <b>'.$numrows.'</b> 个卡密';
    $link='&appid='.$appid.'&use='.$use;
}elseif(isset($_GET['appid'])) {
	$appid =intval($_GET['appid']);
	$sql = "`appid`='{$appid}'";
    $row=$DB->get_row("SELECT * FROM yixi_apps WHERE id='{$appid}' limit 1");
    $numrows=$DB->count("SELECT count(*) from yixi_appkm WHERE `appid`='{$appid}'");
    $con='包含 '.$row['name'].' 的共有 <b>'.$numrows.'</b> 个卡密';
    $link='&appid='.$appid;
}elseif(isset($_GET['use'])) {
	$use =intval($_GET['use']);
	if ($use==1) {
		$sql = "`km_use`= 'n'";
		$name = '未使用';
    }else if ($use==2) {
		$sql = "`km_use`= 'y'";
		$name = '已使用';
    }else if ($use==3) {
		$sql = "`end_time`< '".time()."' or `amount`< '1'";
		$name = '已过期';
    }
    $numrows=$DB->count("SELECT count(*) from yixi_appkm WHERE {$sql}");
    $con='包含 '.$name.' 的共有 <b>'.$numrows.'</b> 个卡密';
    $link='&use='.$use;
}elseif(isset($_GET['kw'])&&($_GET['type'])) {
	$kw = daddslashes($_GET['kw']);
    if($_GET['type']==1)
        $sql=($_GET['method']==1)?"`id` LIKE '%{$kw}%'":"`id`='{$kw}'";
	elseif($_GET['type']==2)
        $sql=($_GET['method']==1)?"`kami` LIKE '%{$kw}%'":"`kami`='{$kw}'";
    else{
        $sql=($_GET['method']==1)?"`{$column}` LIKE '%{$kw}%'":"`{$column}`='{$kw}'";
    }
    $numrows=$DB->count("SELECT count(*) from yixi_appkm WHERE {$sql}");
    $con='包含 '.$kw.' 的共有 <b>'.$numrows.'</b> 个卡密';
    $link='&kw='.$kw;
}elseif(isset($_GET['uid'])) {
    $upid=intval($_GET['uid']);
    $sql=" `upid`='{$upid}'";
    $numrows=$DB->count("SELECT count(*) from yixi_appkm WHERE{$sql}");
    $con='用户(UID:'.$upid.')共有 <b>'.$numrows.'</b> 张卡密';
    $link='&uid='.$_GET['uid'];
}else{
    $numrows=$DB->count("SELECT count(*) from yixi_appkm WHERE 1");
    $sql=" 1";
    $con='本系统共有 <b>'.$numrows.'</b> 个卡密';
}
?>  <form name="form1" id="form1">
     <div style="white-space:nowrap;overflow-x: auto;">
           <table class="layui-table layuiadmin-page-table">
               <thead><tr><th><div class="custom-control custom-checkbox"><input name="chkAll1" type="checkbox" id="chkAll1" onclick="selectAll(this);" value="checkbox" class="custom-control-input"><label class="custom-control-label" for="chkAll1"></label></div></th><th>ID</th><th>归属应用</th><th>卡密类型</th><th>卡密</th><th>详情</th><th>状态</th><th>添加时间</th><th>使用时间</th><th>是否有效</th><th>操作</th></tr></thead>
                   <tbody>
<?php
$pagesize=isset($_GET['num'])?intval($_GET['num']):30;
$pages=ceil($numrows/$pagesize);
$page=isset($_GET['page'])?intval($_GET['page']):1;
$offset=$pagesize*($page - 1);
$rs=$DB->query("SELECT * FROM yixi_appkm WHERE {$sql} order by id desc limit $offset,$pagesize");
while($res = $DB->fetch($rs))
{
$program = $DB->get_row("select * from yixi_apps where id='" . $res['appid'] . "' limit 1");
//$status=$res['user']==NULL?'<font color="green">未使用</font>':'<font color="red">已使用</font>';
if ($res['state'] == 'n') {
    $status = '<font color="red">已封禁</font>';
} else if ($res['km_use'] !== 'n') {
    $status = '<font color="red">已使用</font>';
} else {
    $status = '<font color="green">未使用</font>';
}
if($res['type'] == 'code'){
 if($res['end_time']!=NULL&&$res['end_time'] < time()){
    $status = '<font color="red">已过期</font>';
 }
} else if ($res['type'] == 'single'){
 if($res['amount'] <= 0){
    $status = '<font color="red">已过期</font>';
 }
}
if ($res['state'] == 'n') {
    $usetime = '卡密已封禁';
} else if ($res['km_use'] !== 'n') {
    $usetime = date('Y/m/d H:i:s',$res['use_time']);
} else {
    $usetime = '卡密未使用';
}
if ($res['type'] == 'code') {
if($res['km_time']=='hour'){
	$km_code=$res['amount'].'小时';
}else if($res['km_time']=='day'){
	$km_code=$res['amount'].'天';
}else if($res['km_time']=='week'){
	$km_code=$res['amount'].'周';
}else if($res['km_time']=='month'){
	$km_code=$res['amount'].'个月';
}else if($res['km_time']=='season'){
	$km_code=$res['amount'].'个季';
}else if($res['km_time']=='year'){
	$km_code=$res['amount'].'年';
}else if($res['km_time']=='longuse'){
	$km_code='永久卡';
}else if($res['km_time']=='vipcard'){
	$km_code='贵宾卡';
}
    $type_name = '单码卡密';
    $xq = '可用于<font color="red">'.$program['name'].'</font>['.$km_code.']单码卡密登录';
} else if ($res['type'] == 'vip') {
    $type_name = '会员卡密';
    $xq = '可兑换<font color="red">'.$program['name'].'</font>['.$res['amount'].'天]会员';
} else if ($res['type'] == 'fen') {
    $type_name = '积分卡密';
    $xq = '可兑换<font color="red">'.$program['name'].'</font>['.$res['amount'].']积分';
} else if ($res['type'] == 'single') {
    $type_name = '次数卡密';
    $xq = '可用于<font color="red">'.$program['name'].'</font>['.$res['amount'].']次单码卡密登录';
} else if ($res['type'] == 'svipcard') {
    $type_name = '至尊卡';
    $xq = '可用于<font color="red">所有应用</font>单码卡密登录';

} else {
    $type_name = '未知类型';
    $xq = '未知的卡密类型';
}
echo '<tr><td><div class="custom-control custom-checkbox"><input type="checkbox" name="checkbox[]" id="workorder'.$res['id'].'" value="'.$res['id'].'" class="custom-control-input"><label class="custom-control-label" for="workorder'.$res['id'].'"></label></div></td><td>'.$res['id'].'</td><td><i class="layui-icon layui-icon-component"></i>'.$program['name'].'</td><td>'.$type_name.'</td><td>'.$res['kami'].' <span class="layui-btn layui-btn-xs btn-success" data-clipboard-text="'.$res['kami'].'" data-clipboard-action="copy" data-clipboard-target="#btn_code" id="btn_code">复制</span></td><td>'.$xq.'</td><td>'.$status.'</td><td>'.$res['addtime'].'</td><td>'.$usetime.'</td><td><input type="checkbox" id="switchsss'.$res['id'].'" onclick="Active(\'kmactive\','.$res['id'].')"'.($res['state']==y?' checked ':' ').'data-switch="success"/><label for="switchsss'.$res['id'].'" data-on-label="是" data-off-label="否" class="mb-0 d-block"></label></td><td><span class="layui-btn layui-btn-xs btn-danger" onclick="kmdel('.$res['id'].');">删除</a></td></tr>';
}
?>
          </tbody>
        </table>
		<div class="form-group mb-3">
        <input type="hidden" name="content"/>
        <label for="example-input-normal" style="font-weight: 500">操作：
        <select class="form-control" style="display: inline;width: auto;" name="aid"><option selected>批量操作</option><option value="1">&gt;改为封禁</option><option value="2">&gt;改为激活</option><option value="3">&gt;删除选中</option><option value="4">&gt;导出选中</option><option value="5">&gt;导出全部</option></select>
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