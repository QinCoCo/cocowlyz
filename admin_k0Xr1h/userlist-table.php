<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
?>
<?php
if(isset($_GET['kw'])) {
    $kw = daddslashes($_GET['kw']);
    if($_GET['type']==1)
        $sql=($_GET['method']==1)?" `uid` LIKE '%{$kw}%'":" `uid`='{$kw}'";
    elseif($_GET['type']==2)
        $sql=($_GET['method']==1)?" `user` LIKE '%{$kw}%'":" `user`='{$kw}'";
    elseif($_GET['type']==3)
        $sql=($_GET['method']==1)?" `qq` LIKE '%{$kw}%'":" `qq`='{$kw}'";
    else{
        if(is_numeric($kw))$column='qq';
        $sql=($_GET['method']==1)?" `{$column}` LIKE '%{$kw}%'":" `{$column}`='{$kw}'";
    }
    $numrows=$DB->count("SELECT count(*) from yixi_user WHERE{$sql}");
    $con='包含 '.$kw.' 的共有 <b>'.$numrows.'</b> 个用户';
    $link='&kw='.$_GET['kw'];
}elseif(isset($_GET['uid'])) {
    $uid=intval($_GET['uid']);
    $sql=" `upuid`='{$uid}'";
    $numrows=$DB->count("SELECT count(*) from yixi_user WHERE{$sql}");
    $con='代理用户(UID:'.$uid.')共有 <b>'.$numrows.'</b> 个下级';
    $link='&uid='.$_GET['uid'];
}else{
    $numrows=$DB->count("SELECT count(*) from yixi_user WHERE 1");
    $sql=" 1";
    $con='平台共有 <b>'.$numrows.'</b> 个用户';
}
?>
     <div style="white-space:nowrap;overflow-x: auto;">
        <table class="layui-table layuiadmin-page-table">
          <thead><tr><th>用户UID</th><th>用户头像</th><th>用户权限</th><th>用户名</th><th>用户QQ</th><th>用户余额</th><th>添加时间</th><th>是否冻结</th><th>是否激活</th><th>操作</th></tr></thead>
          <tbody>
<?php
$pagesize=isset($_GET['num'])?intval($_GET['num']):30;
$pages=ceil($numrows/$pagesize);
$page=isset($_GET['page'])?intval($_GET['page']):1;
$offset=$pagesize*($page - 1);
$rs=$DB->query("SELECT * FROM yixi_user WHERE{$sql} order by uid desc limit $offset,$pagesize");
while($res = $DB->fetch($rs))
{
if($res['power']==5){
	$power_name='<span class="layui-btn layui-btn-xs btn-warning">荣耀会员</span>';
}elseif($res['power']==4){
	$power_name='<span class="layui-btn layui-btn-xs btn-default">星耀会员</span>';
}elseif($res['power']==3){
	$power_name='<span class="layui-btn layui-btn-xs btn-normal">钻石会员</span>';
}elseif($res['power']==2){
	$power_name='<span class="layui-btn layui-btn-xs btn-success">黄金会员</span>';
}elseif($res['power']==1){
	$power_name='<span class="layui-btn layui-btn-xs btn-info">白银会员</span>';
}else{
	$power_name='<span class="layui-btn layui-btn-xs btn-primary">普通用户</span>';
}
echo '<tr><td>'.$res['uid'].'</td><td><img src="http://q4.qlogo.cn/headimg_dl?dst_uin='.$res['qq'].'&spec=640" style="height: 50px;width: 50px" class="img-rounded img-circle img-thumbnail"></td><td>'.$power_name.'</td><td>'.$res['user'].'</td><td>'.$res['qq'].'&nbsp;<a href="http://wpa.qq.com/msgrd?v=3&uin='.$res['qq'].'&site=qq&menu=yes">[<img src="../assets/img/qqpay.png" width="24">]</a></td><td><span class="layui-btn layui-btn-xs btn-primary" style="background:linear-gradient(to right, #7C4DFF,#536DFE,#9575CD);" onclick="recharge('.$res['uid'].')"><font color="white">'.$res['rmb'].'</font></span></td><td>'.$res['addtime'].'</td><td><input type="checkbox" id="switchs'.$res['uid'].'" onclick="Active(\'active\','.$res['uid'].')"'.($res['active']==1?' checked ':' ').'data-switch="success"/><label for="switchs'.$res['uid'].'" data-on-label="是" data-off-label="否" class="mb-0 d-block"></label></td><td><input type="checkbox" id="switch'.$res['uid'].'" onclick="Active(\'status\','.$res['uid'].')"'.($res['status']==1?' checked ':' ').'data-switch="success"/><label for="switch'.$res['uid'].'" data-on-label="是" data-off-label="否" class="mb-0 d-block"></label></td><td><a href="./useredit.php?uid='.$res['uid'].'" class="layui-btn layui-btn-xs btn-info">编辑</a><span class="layui-btn layui-btn-xs btn-primary" onclick="czpass('.$res['uid'].')">重置密码</span><a href="./jklist.php?uid='.$res['uid'].'" class="layui-btn layui-btn-xs btn-warning">接口</a><a href="./applist.php?uid='.$res['uid'].'" class="layui-btn layui-btn-xs btn-primary">应用</a><a href="./record.php?uid='.$res['uid'].'" class="layui-btn layui-btn-xs btn-success">明细</a><span class="layui-btn layui-btn-xs btn-danger" onclick="userdel('.$res['uid'].')">删除</span><span class="layui-btn layui-btn-xs btn-default" onclick="sso('.$res['uid'].')">登录</span></td></tr>';
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