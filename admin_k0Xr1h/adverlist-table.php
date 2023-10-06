<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
?>
<?php
if(isset($_GET['kw'])) {
    $kw = daddslashes($_GET['kw']);
    if($_GET['type']==1)
        $sql=($_GET['method']==1)?" AND `title` LIKE '%{$kw}%'":" AND `title`='{$kw}'";
    elseif($_GET['type']==2)
        $sql=($_GET['method']==1)?" AND `url` LIKE '%{$kw}%'":" AND `url`='{$kw}'";
    else{
        $sql=($_GET['method']==1)?" AND `{$column}` LIKE '%{$kw}%'":" AND `{$column}`='{$kw}'";
    }
    $numrows=$DB->count("SELECT count(*) from yixi_adver WHERE{$sql}");
    $con='包含 '.$kw.' 的共有 <b>'.$numrows.'</b> 个用户广告';
    $link='&kw='.$_GET['kw'];
}else{
    $numrows=$DB->count("SELECT count(*) from yixi_adver WHERE 1");
    $sql="";
    $con='平台共有 <b>'.$numrows.'</b> 个用户广告';
}
?>
     <div style="white-space:nowrap;overflow-x: auto;">
        <table class="layui-table layuiadmin-page-table">
          <thead><tr><th>广告ID</th><th>广告标题</th><th>跳转地址</th><th>文字颜色</th><th>管理用户</th><th>广告图标</th><th>添加时间</th><th>到期时间</th><th>是否置顶</th><th>是否激活</th><th>操作</th></tr></thead>
          <tbody>
<?php
$pagesize=isset($_GET['num'])?intval($_GET['num']):30;
$pages=ceil($numrows/$pagesize);
$page=isset($_GET['page'])?intval($_GET['page']):1;
$offset=$pagesize*($page - 1);
$rs=$DB->query("SELECT * FROM yixi_adver WHERE see=1{$sql} order by id asc limit $offset,$pagesize");
while($res = $DB->fetch($rs))
{
if($res['daili']==1){
    $daili="平台站长";
}else{
    $daili="UID：".$res['daili'];
}
if($res['icon']==0){
    $icon="无";
}elseif($res['icon']==1){
    $icon="<img src='../assets/icon/tuij.gif' draggable='false'>";
}elseif($res['icon']==2){
    $icon="<img src='../assets/icon/tj.gif' draggable='false'>";
}elseif($res['icon']==3){
    $icon="<img src='../assets/icon/vip.gif' draggable='false'>";
}elseif($res['icon']==4){
    $icon="<img src='../assets/icon/jing.gif' draggable='false'>";
}elseif($res['icon']==5){
    $icon="<img src='../assets/icon/hoot.gif' draggable='false'>";
}elseif($res['icon']==6){
    $icon="<img src='../assets/icon/zs.gif' draggable='false'>";
}elseif($res['icon']==7){
    $icon="<img src='../assets/icon/hg.gif' draggable='false'>";
}elseif($res['icon']==8){
    $icon="<img src='../assets/icon/hot.gif' draggable='false'>";
}elseif($res['icon']==9){
    $icon="<img src='../assets/icon/guan.png' draggable='false'>";
}elseif($res['icon']==10){
    $icon="<img src='../assets/icon/rz.png' draggable='false'>";
}elseif($res['icon']==11){
    $icon="<img src='../assets/icon/hot.png' draggable='false'>";
}elseif($res['icon']==12){
    $icon="<img src='../assets/icon/zan.png' draggable='false'>";
}
echo '<tr><td><b>'.$res['id'].'</b></td><td>'.substr($res['title'],0,20).'</td><td><a target="_blank" href="'.$res['url'].'"><i class="fa fa-internet-explorer"></i>'.substr($res['url'],0,20).'</a></td><td><span style="color:'.$res['colour'].';">'.$res['colour'].'</span></td><td>'.$daili.'</td><td>'.$icon.'</td><td>'.$res['date'].'</td><td>'.$res['last'].'</td><td><input type="checkbox" id="switchs'.$res['id'].'" onclick="Active(\'top\','.$res['id'].')"'.($res['top']==1?' checked ':' ').'data-switch="success"/><label for="switchs'.$res['id'].'" data-on-label="是" data-off-label="否" class="mb-0 d-block"></label></td><td><input type="checkbox" id="switch'.$res['id'].'" onclick="Active(\'active\','.$res['id'].')"'.($res['active']==1?' checked ':' ').'data-switch="success"/><label for="switch'.$res['id'].'" data-on-label="是" data-off-label="否" class="mb-0 d-block"></label></td><td><a href="./adveredit.php?&id='.$res['id'].'" class="layui-btn layui-btn-xs btn-info">编辑</a><a class="layui-btn layui-btn-xs btn-danger" onclick="adverdel('.$res['id'].')">删除</a></td></tr>';
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