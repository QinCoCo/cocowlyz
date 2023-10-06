<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
if(isset($_GET['id'])) {
$id=isset($_GET['id'])?intval($_GET['id']):sysmsg("参数错误",2,'./',true);
$row=$DB->get_row("SELECT * FROM yixi_paysite WHERE id='{$id}' limit 1");
if(!$row)sysmsg("平台不存在该易支付认证",2,'./paylist.php',true);
$title='编辑认证';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                添加易支付认证
            </div>
            <div class="card-body">
                <form class="form-horizontal layui-form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">归属程序：</label>
                        <select name="proid" class="form-control" lay-search lay-filter="proid">
                        <?php
                        $rs=$DB->query("SELECT * FROM yixi_program WHERE 1 order by id desc");
                        while($res = $DB->fetch($rs))
                        {
                        echo '<option '.($row['proid']==$res['id']?'selected ':'').'value="'.$res['id'].'">'.$res['name'].'</option>';
                        }
                        ?>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">站点名称：</label>
                        <input type="text" class="form-control" name="name" value="<?php echo $row['name']; ?>" placeholder="易支付认证的站名" lay-verType="tips" lay-verify="required">
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">认证Q Q：</label>
                        <input type="text" class="form-control" name="qq" value="<?php echo $row['qq']; ?>" placeholder="易支付认证的QQ" lay-verType="tips" lay-verify="required">
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">认证域名：</label>
                        <input type="text" class="form-control" name="url" value="<?php echo $row['url']; ?>" placeholder="易支付认证的域名" lay-verType="tips" lay-verify="required">
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">是否激活：</label>
                        <select name="active" class="form-control" lay-search lay-filter="active"><option <?php echo $row['active'] == 1 ? 'selected ' : '' ?>value="1">1_是</option><option <?php echo $row['active'] == 0 ? 'selected ' : '' ?>value="0">0_否</option></select>
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_payedit">保存内容</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
include_once './bottom.php';
?>
<script type="text/javascript">
layui.use(['form'], function () {
    var form = layui.form;
    form.on('submit(submit_payedit)', function (data) {
        layer.alert('是否要执行当前操作？', {
            icon: 3, btn: ['确定', '取消'], btn1: function (layero, index) {
                payedit();
            }
        });
        return false;
    });
});
function payedit() {
     var proid = $("select[name='proid']").val();
     var name = $("input[name='name']").val();
     var qq = $("input[name='qq']").val();
     var url = $("input[name='url']").val();
     var active = $("select[name='active']").val();
     if (qq.length < 5 || qq.length > 10 || isNaN(qq)) {
          layer.msg('请输入5~10位QQ账号', {icon: 5});
          return false;
     } else if (url == "") {
          layer.msg("请输入正确域名", {icon: 5});
          return false;
     }
     var ii = layer.msg('正在修改中,请稍后...', {icon: 16, time: 10 * 1000});
     $.ajax({
          type: "POST",
          url: "ajax.php?act=payedit&id=<?php echo $id;?>",
          data : {proid:proid,name:name,qq:qq,url:url,active:active},
          dataType: "json",
          success: function(data) {
               layer.close(ii);
               if (data.code == 0) {
                layer.msg(data.msg, {
                    icon: 6,
                    end: function (layero, index) {
                        window.location.reload();
                    }
                });
            } else {
                layer.msg(data.msg, {icon: 5});
            }
          },
          error:function(data){
               layer.msg('服务器错误', {icon: 5});
               return false;
          }
     });
     return false;
};
</script>
<?php
}else{
     sysmsg("参数错误",2,'./',true);
}
?>