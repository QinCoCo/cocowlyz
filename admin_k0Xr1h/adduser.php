<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
$title='添加用户';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                添加用户
            </div>
            <div class="card-body">
                <form class="form-horizontal layui-form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">用户权限：</label>
                        <select name="power" class="form-control" lay-search lay-filter="power"><option value="0">普通用户</option><option value="1">白银会员</option><option value="2">黄金会员</option><option value="3">钻石会员</option><option value="4">星耀会员</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">用户名：</label>
                        <input type="text" class="form-control" name="user" lay-verType="tips" lay-verify="required">
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">用户密码：</label>
                        <input type="text" class="form-control" name="pwd" value="123456" lay-verType="tips" lay-verify="required">
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">用户余额：</label>
                        <input type="text" class="form-control" name="rmb" value="0" lay-verType="tips" lay-verify="required">
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">卡密额度：</label>
                        <input type="text" class="form-control" name="kami" value="0" lay-verType="tips" lay-verify="required">
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">用户Q Q：</label>
                        <input type="text" class="form-control" name="qq" lay-verType="tips" lay-verify="required">
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_adduser">添 加</button>
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
    form.on('submit(submit_adduser)', function (data) {
        adduser();
        return false;
    });
});
function adduser() {
    var power = $("select[name='power']").val();
    var user = $("input[name='user']").val();
    var pwd = $("input[name='pwd']").val();
    var rmb = $("input[name='rmb']").val();
    var kami = $("input[name='kami']").val();
    var qq = $("input[name='qq']").val();
    if (qq.length < 5 || qq.length > 10 || isNaN(qq)) {
        layer.msg('请输入5~10位QQ账号', {icon: 5});
        return false;
    }
    var ii = layer.msg('正在添加中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type: "POST",
        url: "ajax.php?act=adduser",
        data : {power:power,user:user,pwd:pwd,rmb:rmb,kami:kami,qq:qq},
        dataType: "json",
        success: function(data) {
            layer.close(ii);
            if (data.code == 0) {
                layer.msg(data.msg, {
                    icon: 6,
                    end: function (layero, index) {
                        window.location.href = 'userlist.php';
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