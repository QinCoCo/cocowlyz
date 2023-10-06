<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
$title='添加易支付认证';
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
                        echo '<option value="'.$res['id'].'">'.$res['name'].'</option>';
                        }
                        ?>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">站点名称：</label>
                        <input type="text" class="form-control" name="name" placeholder="易支付认证的站名" lay-verType="tips" lay-verify="required">
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">认证Q Q：</label>
                        <input type="text" class="form-control" name="qq" placeholder="易支付认证的QQ" lay-verType="tips" lay-verify="required">
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">认证域名：</label>
                        <input type="text" class="form-control" name="url" placeholder="易支付认证的域名" lay-verType="tips" lay-verify="required">
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_addpay">添 加</button>
                </form>
            </div>
            <div class="card-footer">
                <span class="layui-icon layui-icon-tips"></span> 加入易支付认证!
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
    form.on('submit(submit_addpay)', function (data) {
        layer.alert('是否要执行当前操作？', {
            icon: 3, btn: ['确定', '取消'], btn1: function (layero, index) {
                addpay();
            }
        });
        return false;
    });
});
function addpay() {
    var proid = $("select[name='proid']").val();
    var name = $("input[name='name']").val();
    var qq = $("input[name='qq']").val();
    var url = $("input[name='url']").val();
    if (qq.length < 5 || qq.length > 10 || isNaN(qq)) {
        layer.msg('请输入5~10位QQ账号', {icon: 5});
        return false;
    }
    var ii = layer.msg('正在添加中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type: "POST",
        url: "ajax.php?act=addpay",
        data : {proid:proid,name:name,qq:qq,url:url},
        dataType: "json",
        success: function(data) {
            layer.close(ii);
            if (data.code == 0) {
                layer.msg(data.msg, {
                    icon: 6,
                    end: function (layero, index) {
                        window.location.href = 'paylist.php';
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