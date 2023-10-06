<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
$title='生成加款卡';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                生成加款卡
            </div>
            <div class="card-body">
                <form class="form-horizontal layui-form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">生成金额：</label>
                        <input type="text" name="money" class="form-control" placeholder="生成的加款卡金额" autocomplete="off" lay-verType="tips" lay-verify="required">
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">生成数量：</label>
                        <input type="number" name="num" class="form-control" placeholder="生成加款卡数量" autocomplete="off" lay-verType="tips" lay-verify="required">
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_addjkk">生 成</button>
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
    form.on('submit(submit_addjkk)', function (data) {
        layer.alert('是否要执行当前操作？', {
            icon: 3, btn: ['确定', '取消'], btn1: function (layero, index) {
                addjkk();
            }
        });
        return false;
    });
});
function addjkk() {
    var money = $("input[name='money']").val();
    var num = $("input[name='num']").val();
    var ii = layer.msg('正在生成中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type: "POST",
        url: "ajax.php?act=addjkk",
        data : {money:money,num:num},
        dataType: "json",
        success: function(data) {
            layer.close(ii);
            if (data.code == 0) {
                layer.msg(data.msg, {
                    icon: 6,
                    end: function (layero, index) {
                        window.location.href = 'jkklist.php';
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