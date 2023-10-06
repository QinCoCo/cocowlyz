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
    $numrows=$DB->count("SELECT count(*) from yixi_apps WHERE {$sql}");
    $con='包含 '.$kw.' 的共有 <b>'.$numrows.'</b> 个应用';
    $link='&kw='.$_GET['kw'];
}elseif(isset($_GET['uid'])) {
    $uid=intval($_GET['uid']);
    $sql=" `uid`='{$uid}'";
    $numrows=$DB->count("SELECT count(*) from yixi_apps WHERE {$sql}");
    $con='用户(UID:'.$uid.')共有 <b>'.$numrows.'</b> 个应用';
    $link='&uid='.$_GET['uid'];
}else{
    $numrows=$DB->count("SELECT count(*) from yixi_apps WHERE 1");
    $sql=" 1";
    $con='本系统共有 <b>'.$numrows.'</b> 个应用';
}
?>
     <div style="white-space:nowrap;overflow-x: auto;">
        <table class="layui-table layuiadmin-page-table">
          <thead><tr><th>应用图标</th><th>应用名称</th><th>备注</th><th>APPID</th><th>APPKEY</th><th>应用版本</th><th>应用价格</th><th>添加时间</th><th>是否付费使用</th><th>是否验证用户IP</th><th>是否验证设备</th><th>是否运行</th><th>操作</th></tr></thead>
          <tbody>
<?php
$pagesize=isset($_GET['num'])?intval($_GET['num']):30;
$pages=ceil($numrows/$pagesize);
$page=isset($_GET['page'])?intval($_GET['page']):1;
$offset=$pagesize*($page - 1);
$rs=$DB->query("SELECT * FROM yixi_apps WHERE {$sql} order by id desc limit $offset,$pagesize");
while($res = $DB->fetch($rs))
{
$users = $DB->get_row("select * from yixi_user where uid='" . $res['uid'] . "' limit 1");
$usersnum=$DB->count("SELECT count(*) from yixi_apps WHERE uid='" . $res['uid'] . "' limit 1");
$users2 = $DB->get_row("select * from yixi_user where uid='" . $users['upuid'] . "' limit 1");
$usersnum2=$DB->count("SELECT count(*) from yixi_apps WHERE uid='" . $users['upuid'] . "' limit 1");
if($res['uid']=="1") {
$users['user']="admin";
$users['qq']=$conf['admin_qq'];
}
if($users['upuid']=="1") {
$users2['user']="admin";
$users2['qq']=$conf['admin_qq'];
}
if($res['note']==NULL){$note='<i class="layui-icon layui-icon-dialogue"></i>备注';}else{$note=$res['note'];}
if(preg_match("/^http(s)?:\\/\\/.+/",$res["img"]) && !empty($res["img"])){
    $img = $res["img"];
}else if(!preg_match("/^http(s)?:\\/\\/.+/",$res["img"]) && !empty($res["img"])){
    $img = '../'.$res["img"];
}else{
    $img = '../assets/img/Program/noimg.png';
}
echo '<tr><td><img src="'.$img.'" style="height: 50px;width: 50px" class="img-rounded img-circle img-thumbnail"></td><td onclick="layer.alert(\'用户UID：'.$res['uid'].'\n\r归属用户：'.$users['user'].'\n\rQQ：'.$users['qq'].'\n\r应用数量：'.$usersnum.'\n\r用户上级：'.$users2['user'].'\n\r上级QQ：'.$users2['qq'].'\n\r应用数量：'.$usersnum2.'\')">'.$res['name'].'</td><td><span onclick="getappnote('.$res['id'].')" title="备注">'.$note.'</span></td><td>'.$res['id'].'</td><td>'.$res['appkey'].' <span class="layui-btn layui-btn-xs btn-success" data-clipboard-text="'.$res['appkey'].'" data-clipboard-action="copy" data-clipboard-target="#btn_code" id="btn_code">复制</span><span class="layui-btn layui-btn-xs btn-danger" onclick="appkeyChange ('.$res['id'].')">更换</span></td><td><span onclick="getappversion('.$res['id'].')" title="APP版本">'.$res['version'].'</span></td><td><span onclick="getmoney('.$res['id'].')" title="修改程序价格">'.$res['sqprice'].'|'.$res['sqsprice'].'|'.$res['cgprice'].'|'.$res['sqprice2'].'|'.$res['sqprice3'].'|'.$res['sqsprice2'].'</span></td><td>'.$res['date'].'</td><td><input type="checkbox" id="switch'.$res['id'].'" onclick="Active(\'switch\','.$res['id'].')"'.($res['switch']==y?' checked ':' ').'data-switch="success"/><label for="switch'.$res['id'].'" data-on-label="是" data-off-label="否" class="mb-0 d-block"></label></td><td><input type="checkbox" id="switchs'.$res['id'].'" onclick="Active(\'ipauth\','.$res['id'].')"'.($res['ipauth']==y?' checked ':' ').'data-switch="success"/><label for="switchs'.$res['id'].'" data-on-label="是" data-off-label="否" class="mb-0 d-block"></label></td><td><input type="checkbox" id="switchss'.$res['id'].'" onclick="Active(\'login_check\','.$res['id'].')"'.($res['logon_check_in']==y?' checked ':' ').'data-switch="success"/><label for="switchss'.$res['id'].'" data-on-label="是" data-off-label="否" class="mb-0 d-block"></label></td><td><input type="checkbox" id="switchsss'.$res['id'].'" onclick="Active(\'active\','.$res['id'].')"'.($res['active']==y?' checked ':' ').'data-switch="success"/><label for="switchsss'.$res['id'].'" data-on-label="是" data-off-label="否" class="mb-0 d-block"></label></td><td><a href="./appedit.php?id='.$res['id'].'" class="layui-btn layui-btn-xs btn-info">应用编辑</a><a href="./appuserlist.php?id='.$res['id'].'" class="layui-btn layui-btn-xs btn-primary">用户列表</a><a href="./appkmlist.php?appid='.$res['id'].'" class="layui-btn layui-btn-xs btn-normal">卡密列表</a><a href="./appother.php?id='.$res['id'].'" class="layui-btn layui-btn-xs btn-warning">其他配置</a><a href="./appupdate.php?id='.$res['id'].'" class="layui-btn layui-btn-xs btn-success">更新配置</a><span class="layui-btn layui-btn-xs btn-danger" onclick="appdel('.$res['id'].')">删除</span></td></tr>';
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