<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
?>
<?php
if(isset($_GET['kw'])) {
    $kw = daddslashes($_GET['kw']);
    if($_GET['type']==1)
        $sql=($_GET['method']==1)?" `qq` LIKE '%{$kw}%'":" `qq`='{$kw}'";
    elseif($_GET['type']==2)
        $sql=($_GET['method']==1)?" `note` LIKE '%{$kw}%'":" `note`='{$kw}'";
    else{
        if(is_numeric($kw))$column='qq';
        $sql=($_GET['method']==1)?" `{$column}` LIKE '%{$kw}%'":" `{$column}`='{$kw}'";
    }
    $numrows=$DB->count("SELECT count(*) from yixi_blacklist WHERE{$sql}");
    $con='包含 '.$kw.' 的共有 <b>'.$numrows.'</b> 个黑名单';
    $link='&kw='.$_GET['kw'];
}else{
    $sql=" 1";
    $numrows=$DB->count("SELECT count(*) from yixi_blacklist WHERE{$sql}");
    $con='平台共有 <b>'.$numrows.'</b> 个黑名单';
}
?>
     <div style="white-space:nowrap;overflow-x: auto;">
        <table class="layui-table layuiadmin-page-table">
          <thead><tr><th>拉黑ID</th><th>拉黑者头像</th><th>拉黑QQ</th><th>黑名单等级</th><th>添加时间</th><th>操作</th></tr></thead>
          <tbody>
<?php
$pagesize=isset($_GET['num'])?intval($_GET['num']):30;
$pages=ceil($numrows/$pagesize);
$page=isset($_GET['page'])?intval($_GET['page']):1;
$offset=$pagesize*($page - 1);
$rs=$DB->query("SELECT * FROM yixi_blacklist WHERE{$sql} order by id desc limit $offset,$pagesize");
while($res = $DB->fetch($rs))
{
echo '<tr><td>'.$res['id'].'</td><td><img src="http://q4.qlogo.cn/headimg_dl?dst_uin='.$res['qq'].'&spec=640" style="height: 50px;width: 50px" class="img-rounded img-circle img-thumbnail"></td><td>'.$res['qq'].'&nbsp;<a href="http://wpa.qq.com/msgrd?v=3&uin='.$res['qq'].'&site=qq&menu=yes">[<img src="../assets/img/qqpay.png" width="24">]</a></td><td>'.$res['level'].'级</td><td>'.$res['date'].'</td><td><a href="./blackedit.php?id='.$res['id'].'" class="layui-btn layui-btn-xs btn-info">编辑</a><span class="layui-btn layui-btn-xs btn-danger" onclick="blackdel('.$res['id'].')">删除</span></td></tr>';
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