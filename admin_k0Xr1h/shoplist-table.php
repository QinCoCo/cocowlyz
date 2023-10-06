<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
?>
<?php
if(isset($_GET['kw'])) {
    $kw = daddslashes($_GET['kw']);
    if($_GET['type']==1)
        $sql=($_GET['method']==1)?" `name` LIKE '%{$kw}%'":" `name`='{$kw}'";
    else{
        $sql=($_GET['method']==1)?" `{$column}` LIKE '%{$kw}%'":" `{$column}`='{$kw}'";
    }
    $numrows=$DB->count("SELECT count(*) from yixi_shop WHERE{$sql}");
    $con='包含 '.$kw.' 的共有 <b>'.$numrows.'</b> 个商品';
    $link='&kw='.$_GET['kw'];
}else{
    $numrows=$DB->count("SELECT count(*) from yixi_shop WHERE 1");
    $sql=" 1";
    $con='平台共有 <b>'.$numrows.'</b> 个商品';
}
?>
     <div style="white-space:nowrap;overflow-x: auto;">
        <table class="layui-table layuiadmin-page-table">
          <thead><tr><th>商品ID</th><th>商品名称</th><th>图片浏览</th><th>客户端</th><th>提成比例</th><th>商品类型</th><th>商品售价</th><th>每单提成</th><th>是否认证</th><th>认证时间</th><th>上架时间</th><th>操作</th></tr></thead>
          <tbody>
<?php
$pagesize=isset($_GET['num'])?intval($_GET['num']):30;
$pages=ceil($numrows/$pagesize);
$page=isset($_GET['page'])?intval($_GET['page']):1;
$offset=$pagesize*($page - 1);
$flag=false;
$rs=$DB->query("SELECT * FROM yixi_shop WHERE{$sql} order by id desc limit $offset,$pagesize");
while($res = $DB->fetch($rs))
{
    $flag=true;
    if ($res['type'] == 1) {
        $type_name = '软件类商品';
    } else if ($res['type'] == 2) {
        $type_name = '插件类商品';
    } else if ($res['type'] == 3) {
        $type_name = '源码类商品';
	} else if ($res['type'] == 4) {
        $type_name = '其他类商品';
    } else {
        $type_name = '未知类商品';
    }
    if ($res['proid'] == 'other') {
        $pro_name = $res['system_name'];
    } else {
        if($res['proid'] == 'android'){
        $pro_name = 'Android';
	    }elseif($res['proid'] == 'ios'){
	    $pro_name = 'IOS';
	    }elseif($res['proid'] == 'pc'){
	    $pro_name = 'PC';
	    }elseif($res['proid'] == 'web'){
	    $pro_name = '网站';
	    }elseif($res['proid'] == 'source'){
	    $pro_name = '源码';
	    }elseif($res['proid'] == 'other'){
	    $pro_name = '其他';
	    }else{$pro_name = '未知';
	    }
    }
    if ($res['active'] == 1) {
        $rzdate = $res['rzdate'];
    } else {
        $rzdate = '未认证商品';
    }
    $mdtc = round($res['money']*$res['tcbl']/100, 2);
    $shopimg = explode(',', $res['image']);
    echo '<tr><td>'.$res['id'].'</td><td>'.$res['name'].'</td><td><img onclick="image_msg('.$res['id'].')" src="'.$shopimg[0].'" style="height: 65px;width: 65px" class="img-rounded img-circle img-thumbnail"></td><td>'.$pro_name.'</td><td onclick="shop_tcbl('.$res['id'].','.$res['tcbl'].');">'.$res['tcbl'].'%</td><td>'.$type_name.'</td><td onclick="shop_money('.$res['id'].','.$res['money'].');">'.$res['money'].' 元</td><td>'.$mdtc.' 元</td><td><input type="checkbox" id="switch'.$res['id'].'" onclick="Active('.$res['id'].')"'.($res['active']==1?' checked ':' ').'data-switch="success"/><label for="switch'.$res['id'].'" data-on-label="是" data-off-label="否" class="mb-0 d-block"></label></td><td>'.$rzdate.'</td><td>'.$res['sjdate'].'</td><td><a href="./shopedit.php?id='.$res['id'].'" class="layui-btn layui-btn-xs btn-info">编辑</a><span class="layui-btn layui-btn-xs btn-danger" onclick="shopdel('.$res['id'].')">删除</span></td></tr>';
}
if(!$flag)echo '<tr class="no-records-found"><td colspan="99">一个商品都没有，赶紧去添加吧！</td></tr>';
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