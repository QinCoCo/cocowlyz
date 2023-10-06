<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
if(isset($_GET['id'])) {
$id=isset($_GET['id'])?intval($_GET['id']):sysmsg("参数错误",2,'./',true);
$row=$DB->get_row("SELECT * FROM yixi_message WHERE id='{$id}' limit 1");
if(!$row)sysmsg("平台不存在该通知",2,'./msglist.php',true);
$title='编辑通知';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                编辑通知
            </div>
            <div class="card-body">
                <form class="form-horizontal layui-form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">接收用户类型：</label>
                        <select name="type" class="form-control" lay-search lay-filter="type"><option <?php echo $row['type'] == 0 ? 'selected ' : '' ?>value="0">全部用户</option><option <?php echo $row['type'] == 1 ? 'selected ' : '' ?>value="1">所有普通用户</option><option <?php echo $row['type'] == 2 ? 'selected ' : '' ?>value="2">所有代理</option><option <?php echo $row['type'] == 3 ? 'selected ' : '' ?>value="3">所有全能管理员</option><option <?php echo $row['type'] == 4 ? 'selected ' : '' ?>value="4">所有超级管理员</option><option <?php echo $row['type'] == 5 ? 'selected ' : '' ?>value="5">所有授权商</option><option <?php echo $row['type'] == 6 ? 'selected ' : '' ?>value="6">超级管理员</option><option <?php echo $row['type'] == 7 ? 'selected ' : '' ?>value="7">授权商</option></select>
                    </div>
                    <div id="frame_set1" style="display:none">
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
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">通知标题：</label>
                        <input type="text" name="title" value="<?php echo $row['title']?>" class="form-control" lay-verType="tips" lay-verify="required">
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">通知内容：</label>
                        <textarea class="form-control" name="content" rows="8" style="width:100%;" lay-verType="tips" lay-verify="required"><?php echo $row['content']?></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">是否显示：</label>
                        <select name="active" class="form-control" lay-search lay-filter="active"><option <?php echo $row['active'] == 1 ? 'selected ' : '' ?>value="1">1_是</option><option <?php echo $row['active'] == 0 ? 'selected ' : '' ?>value="0">0_否</option></select>
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_msgedit">保存内容</button>
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
    form.on('submit(submit_msgedit)', function (data) {
        layer.alert('是否要执行当前操作？', {
            icon: 3, btn: ['确定', '取消'], btn1: function (layero, index) {
                msgedit();
            }
        });
        return false;
    });
    form.on('select(type)', function(data){ 
        if(data.value >= '9'){
            $("#frame_set1").show();
        }else{
            $("#frame_set1").hide();
        }
    });
});
function msgedit() {
    var title = $("input[name='title']").val();
    var type = $("select[name='type']").val();
    var proid = $("select[name='proid']").val();
    var content = $("textarea[name='content']").val();
    var active = $("select[name='active']").val();
    var ii = layer.msg('正在修改中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type: "POST",
        url: "ajax.php?act=msgedit&id=<?php echo $id;?>",
        data : {title:title,type:type,proid:proid,content:content,active:active},
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