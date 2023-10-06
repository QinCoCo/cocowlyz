<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
if(isset($_GET['uid'])) {
$uid=isset($_GET['uid'])?intval($_GET['uid']):sysmsg("参数错误",2,'./',true);
$row=$DB->get_row("SELECT * FROM yixi_user WHERE uid='{$uid}' limit 1");
if(!$row)sysmsg("平台不存在该用户",2,'./userlist.php',true);
$title='编辑用户';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                编辑用户
            </div>
            <div class="card-body">
                <form onsubmit="return useredit();" method="post" class="form-horizontal layui-form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">用户权限：</label>
                        <select name="power" class="form-control" lay-search lay-filter="power"><option <?php echo $row['power'] == 0 ? 'selected ' : '' ?>value="0">普通用户</option><option <?php echo $row['power'] == 1 ? 'selected ' : '' ?>value="1">白银会员</option><option <?php echo $row['power'] == 2 ? 'selected ' : '' ?>value="2">黄金会员</option><option <?php echo $row['power'] == 3 ? 'selected ' : '' ?>value="3">钻石会员</option><option <?php echo $row['power'] == 4 ? 'selected ' : '' ?>value="4">星耀会员</option><option <?php echo $row['power'] == 5 ? 'selected ' : '' ?>value="5">荣耀会员</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">用户名：</label>
                        <input type="text" class="form-control" name="user" value="<?php echo $row['user'];?>" lay-verType="tips" lay-verify="required">
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">用户余额：</label>
                        <input type="text" class="form-control" name="rmb" value="<?php echo $row['rmb'];?>" lay-verType="tips" lay-verify="required">
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">卡密额度：</label>
                        <input type="text" class="form-control" name="kami" value="<?php echo $row['kami'];?>" lay-verType="tips" lay-verify="required">
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">用户Q Q：</label>
                        <input type="text" class="form-control" name="qq" value="<?php echo $row['qq'];?>" lay-verType="tips" lay-verify="required">
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">重置密码：</label>
                        <input type="text" class="form-control" name="pwd" placeholder="不重置请留空">
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">是否激活：</label>
                        <select name="status" lay-filter="status"><option <?php echo $row['active'] == 1 ? 'selected ' : '' ?>value="1">1_是</option><option <?php echo $row['status'] == 0 ? 'selected ' : '' ?>value="0">0_否</option></select>
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_useredit">保存内容</button>
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
    form.on('submit(submit_useredit)', function (data) {
        layer.alert('是否要执行当前操作？', {
            icon: 3, btn: ['确定', '取消'], btn1: function (layero, index) {
                useredit();
            }
        });
        return false;
    });
});
function useredit() {
    var power = $("select[name='power']").val();
    var user = $("input[name='user']").val();
    var rmb = $("input[name='rmb']").val();
    var kami = $("input[name='kami']").val();
    var qq = $("input[name='qq']").val();
    var pwd = $("input[name='pwd']").val();
    var status = $("select[name='status']").val();
    if (qq.length < 5 || qq.length > 10 || isNaN(qq)) {
        layer.msg('请输入5~10位QQ账号', {icon: 5});
        return false;
    }
    var ii = layer.msg('正在修改中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type: "POST",
        url: "ajax.php?act=useredit&uid=<?php echo $uid;?>",
        data : {power:power,user:user,rmb:rmb,kami:kami,qq:qq,pwd:pwd,status:status},
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