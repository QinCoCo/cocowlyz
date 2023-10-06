<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
$title='添加云黑名单';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                添加云黑名单
            </div>
            <div class="card-body">
                <form class="form-horizontal layui-form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">拉黑等级：</label>
                        <select name="level" class="form-control" lay-search lay-filter="level"><option value="1">1级-低</option><option value="2">2级-中</option><option value="3">3级-高</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">拉黑Q Q：</label>
                        <input type="number" class="form-control" name="qq" placeholder="输入拉黑QQ" lay-verType="tips" lay-verify="required">
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">拉黑原因：</label>
                        <textarea class="form-control" name="note" style="height:100px;" placeholder="输入拉黑原因" lay-verType="tips" lay-verify="required"></textarea>
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_addblack">添 加</button>
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
    form.on('submit(submit_addblack)', function (data) {
        layer.alert('是否要执行当前操作？', {
            icon: 3, btn: ['确定', '取消'], btn1: function (layero, index) {
                addblack();
            }
        });
        return false;
    });
});
function addblack() {
    var level = $("select[name='level']").val();
    var qq = $("input[name='qq']").val();
    var note = $("textarea[name='note']").val();
    var ii = layer.msg('正在拉黑中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type: "POST",
        url: "ajax.php?act=addblack",
        data : {level:level,qq:qq,note:note},
        dataType: "json",
        success: function(data) {
            layer.close(ii);
            if (data.code == 0) {
                layer.msg(data.msg, {
                    icon: 6,
                    end: function (layero, index) {
                        window.location.href = 'blacklist.php';
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