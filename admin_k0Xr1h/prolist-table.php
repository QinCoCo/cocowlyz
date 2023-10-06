<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
?>
<?php
if(isset($_GET['kw'])) {
    $kw = daddslashes($_GET['kw']);
    if($_GET['type']==1)
        $sql=($_GET['method']==1)?" `id` LIKE '%{$kw}%'":" `id`='{$kw}'";
    elseif($_GET['type']==2)
        $sql=($_GET['method']==1)?" `name` LIKE '%{$kw}%'":" `name`='{$kw}'";
    else{
        $sql=($_GET['method']==1)?" `{$column}` LIKE '%{$kw}%'":" `{$column}`='{$kw}'";
    }
    $numrows=$DB->count("SELECT count(*) from yixi_program WHERE{$sql}");
    $con='包含 '.$kw.' 的共有 <b>'.$numrows.'</b> 个接口';
    $link='&kw='.$_GET['kw'];
}else{
    $numrows=$DB->count("SELECT count(*) from yixi_program WHERE 1");
    $sql=" 1";
    $con='平台共有 <b>'.$numrows.'</b> 个接口';
}
?>
     <div style="white-space:nowrap;overflow-x: auto;">
        <table class="layui-table layuiadmin-page-table">
          <thead><tr><th>接口图标</th><th>接口ID</th><th>接口名称</th><th>接口别名</th><th>接口价格</th><th>添加时间</th><th>是否免费调用</th><th>是否长期免费</th><th>是否首页显示</th><th>是否运行</th><th>操作</th></tr></thead>
          <tbody>
<?php
$pagesize=isset($_GET['num'])?intval($_GET['num']):30;
$pages=ceil($numrows/$pagesize);
$page=isset($_GET['page'])?intval($_GET['page']):1;
$offset=$pagesize*($page - 1);
$rs=$DB->query("SELECT * FROM yixi_program WHERE{$sql} order by id desc limit $offset,$pagesize");
while($res = $DB->fetch($rs))
{
if(preg_match("/^http(s)?:\\/\\/.+/",$res["img"]) && !empty($res["img"])){
    $img = $res["img"];
}else if(!preg_match("/^http(s)?:\\/\\/.+/",$res["img"]) && !empty($res["img"])){
    $img = '../'.$res["img"];
}else{
    $img = '../assets/img/Program/noimg.png';
}
echo '<tr><td><img src="'.$img.'" style="height: 50px;width: 50px" class="img-rounded img-circle img-thumbnail"></td><td>'.$res['id'].'</td><td>'.$res['name'].'</td><td>'.$res['api_path'].'</td><td><span onclick="getmoney('.$res['id'].')" title="修改程序价格">'.$res['ptprice'].'|'.$res['byprice'].'|'.$res['hjprice'].'|'.$res['zsprice'].'</span></td><td>'.$res['date'].'</td><td><input type="checkbox" id="switch'.$res['id'].'" onclick="Active(\'switch\','.$res['id'].')"'.($res['switch']==y?' checked ':' ').'data-switch="success"/><label for="switch'.$res['id'].'" data-on-label="是" data-off-label="否" class="mb-0 d-block"></label></td><td><input type="checkbox" id="switchss'.$res['id'].'" onclick="Active(\'longfree\','.$res['id'].')"'.($res['longfree']==y?' checked ':' ').'data-switch="success"/><label for="switchss'.$res['id'].'" data-on-label="是" data-off-label="否" class="mb-0 d-block"></label></td><td><input type="checkbox" id="switchsss'.$res['id'].'" onclick="Active(\'visible\','.$res['id'].')"'.($res['visible']==y?' checked ':' ').'data-switch="success"/><label for="switchsss'.$res['id'].'" data-on-label="是" data-off-label="否" class="mb-0 d-block"></label></td><td><input type="checkbox" id="switchssss'.$res['id'].'" onclick="Active(\'active\','.$res['id'].')"'.($res['active']==y?' checked ':' ').'data-switch="success"/><label for="switchssss'.$res['id'].'" data-on-label="是" data-off-label="否" class="mb-0 d-block"></label></td><td><a href="./proedit.php?id='.$res['id'].'" class="layui-btn layui-btn-xs btn-info">编辑</a><span class="layui-btn layui-btn-xs btn-danger" onclick="prodel('.$res['id'].')">删除</span></td></tr>';
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