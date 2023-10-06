<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
$title='发布新通知';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                发布新通知
            </div>
            <div class="card-body">
                <form class="form-horizontal layui-form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">接收用户类型：</label>
                        <select name="type" class="form-control" lay-search lay-filter="type"><option value="0">全部用户</option><option value="1">所有普通用户</option><option value="2">所有会员</option><option value="3">所有白银会员</option><option value="4">所有黄金会员</option><option value="5">所有钻石会员</option><option value="6">所有星耀会员</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">通知标题：</label>
                        <input type="text" name="title" class="form-control" lay-verType="tips" lay-verify="required">
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">通知内容：</label>
                        <textarea class="form-control" name="content" rows="8" style="width:100%;" lay-verType="tips" lay-verify="required"></textarea>
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_addmsg">发 布</button>
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
    form.on('submit(submit_addmsg)', function (data) {
        layer.alert('是否要执行当前操作？', {
            icon: 3, btn: ['确定', '取消'], btn1: function (layero, index) {
                addmsg();
            }
        });
        return false;
    });
});
function addmsg() {
    var type = $("select[name='type']").val();
    var title = $("input[name='title']").val();
    var content = $("textarea[name='content']").val();
    var ii = layer.msg('正在发布中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type: "POST",
        url: "ajax.php?act=addmsg",
        data : {type:type,title:title,content:content},
        dataType: "json",
        success: function(data) {
            layer.close(ii);
            if (data.code == 0) {
                layer.msg(data.msg, {
                    icon: 6,
                    end: function (layero, index) {
                        window.location.href = 'msglist.php';
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