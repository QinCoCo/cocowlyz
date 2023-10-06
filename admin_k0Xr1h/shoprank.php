<?php
/**
 * 商品销量排行
**/
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
$title='商品销量排行';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                商品销量排行
            </div>
            <div class="card-body">
                <div style="white-space:nowrap;overflow-x: auto;">
                    <table class="layui-table layuiadmin-page-table">
                        <thead><tr><th>排名</th><th>图片预览</th><th>商品ID</th><th>商品名称</th><th>销售数量</th></thead>
                        <tbody>
                        <?php
                        $rs=$DB->query("SELECT * FROM yixi_shop WHERE 1 order by count desc limit 20");
                        $i=1;
                        while($res = $DB->fetch($rs))
                        {
                        echo '<tr><td><span class="badge badge-danger">'.$i.'</span></td><td><img onclick="image_msg('.$res['id'].')" src="'.$shopimg[0].'" style="height: 65px;width: 65px" class="img-rounded img-circle img-thumbnail"></td><td><b>'.$res['id'].'</b></td><td>'.$res['name'].'</td><td>'.$res['count'].'</td></tr>';
                        $i++;
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
         </div>
    </div>
</div>
<?php
include_once './bottom.php';
?>
<script type="text/javascript">
function image_msg(id) {
    $.getJSON('../ajax.php?act=image_shop&id=' + id, function (json) {
        layer.photos({photos: json, anim: 5});
    });
}
</script>