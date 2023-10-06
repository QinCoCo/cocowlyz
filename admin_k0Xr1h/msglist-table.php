<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
?>
<?php
if(isset($_GET['kw'])) {
    $kw = daddslashes($_GET['kw']);
    if($_GET['type']==1)
        $sql=($_GET['method']==1)?" `title` LIKE '%{$kw}%'":" `title`='{$kw}'";
    else{
        $sql=($_GET['method']==1)?" `{$column}` LIKE '%{$kw}%'":" `{$column}`='{$kw}'";
    }
    $numrows=$DB->count("SELECT count(*) from yixi_message WHERE{$sql}");
    $con='包含 '.$kw.' 的共有 <b>'.$numrows.'</b> 条通知';
    $link='&kw='.$_GET['kw'];
}else{
    $numrows=$DB->count("SELECT count(*) from yixi_message WHERE 1");
    $sql=" 1";
    $con='平台共有 <b>'.$numrows.'</b> 条通知';
}
?>
     <div style="white-space:nowrap;overflow-x: auto;">
        <table class="layui-table layuiadmin-page-table">
          <thead><tr><th>通知ID</th><th>通知标题</th><th>已查阅人数</th><th>发布时间</th><th>是否显示</th><th>操作</th></tr></thead>
          <tbody>
<?php
$pagesize=isset($_GET['num'])?intval($_GET['num']):30;
$pages=ceil($numrows/$pagesize);
$page=isset($_GET['page'])?intval($_GET['page']):1;
$offset=$pagesize*($page - 1);
$rs=$DB->query("SELECT * FROM yixi_message WHERE{$sql} order by id desc limit $offset,$pagesize");
while($res = $DB->fetch($rs))
{
echo '<tr><td>'.$res['id'].'</td><td>'.$res['title'].'</td><td>'.$res['count'].'</td><td>'.$res['addtime'].'</td><td><input type="checkbox" id="switch'.$res['id'].'" onclick="Active('.$res['id'].')"'.($res['active']==1?' checked ':' ').'data-switch="success"/><label for="switch'.$res['id'].'" data-on-label="是" data-off-label="否" class="mb-0 d-block"></label></td><td><span class="layui-btn layui-btn-xs btn-success" onclick="show('.$res['id'].')">查看</span><a href="./msgedit.php?id='.$res['id'].'" class="layui-btn layui-btn-xs btn-info">编辑</a><span class="layui-btn layui-btn-xs btn-danger" onclick="msgdel('.$res['id'].')">删除</span></td></tr>';
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