<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
if(isset($_GET['id'])) {
$id=isset($_GET['id'])?intval($_GET['id']):sysmsg("参数错误",2,'./',true);
$row=$DB->get_row("SELECT * FROM yixi_blacklist WHERE id='{$id}' limit 1");
if(!$row)sysmsg("平台不存在该黑名单记录",2,'./blacklist.php',true);
$title='编辑云黑名单';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                编辑云黑名单
            </div>
            <div class="card-body">
                <form class="form-horizontal layui-form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">拉黑等级：</label>
                        <select name="level" class="form-control" lay-search lay-filter="level"><option <?php echo $row['level'] == 1 ? 'selected ' : '' ?>value="1">1级-低</option><option <?php echo $row['level'] == 2 ? 'selected ' : '' ?>value="2">2级-中</option><option <?php echo $row['level'] == 3 ? 'selected ' : '' ?>value="3">3级-高</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">拉黑Q Q：</label>
                        <input type="text" class="form-control" name="qq" value="<?php echo $row['qq'];?>" placeholder="输入拉黑QQ" lay-verType="tips" lay-verify="required">
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">拉黑原因：</label>
                        <textarea class="form-control" name="note" style="height:100px;" placeholder="输入拉黑原因" lay-verType="tips" lay-verify="required"><?php echo $row['note'];?></textarea>
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_blackedit">保存内容</button>
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
    form.on('submit(submit_blackedit)', function (data) {
        layer.alert('是否要执行当前操作？', {
            icon: 3, btn: ['确定', '取消'], btn1: function (layero, index) {
                blackedit();
            }
        });
        return false;
    });
});
function blackedit() {
    var level = $("select[name='level']").val();
    var qq = $("input[name='qq']").val();
    var note = $("textarea[name='note']").val();
    var ii = layer.msg('正在修改中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type: "POST",
        url: "ajax.php?act=blackedit&id=<?php echo $id;?>",
        data : {level:level,qq:qq,note:note},
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