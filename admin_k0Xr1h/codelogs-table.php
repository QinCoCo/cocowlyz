<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
?>
<?php
if(isset($_GET['kw'])) {
    $kw = daddslashes($_GET['kw']);
    if($_GET['type']==1)
        $sql=($_GET['method']==1)?" `hm` LIKE '%{$kw}%'":" `hm`='{$kw}'";
    elseif($_GET['type']==2)
        $sql=($_GET['method']==1)?" `code` LIKE '%{$kw}%'":" `code`='{$kw}'";
    elseif($_GET['type']==3)
        $sql=($_GET['method']==1)?" `ip` LIKE '%{$kw}%'":" `ip`='{$kw}'";
    else{
        $sql=($_GET['method']==1)?" `{$column}` LIKE '%{$kw}%'":" `{$column}`='{$kw}'";
    }
    $numrows=$DB->count("SELECT count(*) from yixi_code WHERE{$sql}");
    $con='包含 '.$kw.' 的共有 <b>'.$numrows.'</b> 个验证码发送日志';
    $link='&kw='.$_GET['kw'];
}else{
    $sql=" 1";
    $numrows=$DB->count("SELECT count(*) from yixi_code WHERE{$sql}");
    $con='平台共有 <b>'.$numrows.'</b> 个验证码发送日志';
}
?>
     <div style="white-space:nowrap;overflow-x: auto;">
        <table class="layui-table layuiadmin-page-table">
          <thead><tr><th>ID</th><th>类型</th><th>接收账号</th><th>IP</th><th>时间</th><th>状态</th></tr></thead>
          <tbody>
<?php
$pagesize=isset($_GET['num'])?intval($_GET['num']):30;
$pages=ceil($numrows/$pagesize);
$page=isset($_GET['page'])?intval($_GET['page']):1;
$offset=$pagesize*($page - 1);
$rs=$DB->query("SELECT * FROM yixi_code WHERE{$sql} order by id desc limit $offset,$pagesize");
while($res = $DB->fetch($rs))
{
if($res['status']==1) {
$zt='已使用';
}else{
$zt='未使用(可能过期了)';
}
echo '<tr><td>'.$res['id'].'</td><td>'.$res['title'].'</td><td>'.$res['hm'].'</td><td>'.$res['ip'].'</td><td>'.$res['date'].'</td><td>'.$zt.'</td></tr>';
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