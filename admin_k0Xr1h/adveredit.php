<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
if(isset($_GET['id'])) {
$id=isset($_GET['id'])?intval($_GET['id']):sysmsg("参数错误",2,'./',true);
$row=$DB->get_row("SELECT * FROM yixi_adver WHERE id='{$id}' limit 1");
if(!$row)sysmsg("平台不存在该广告",2,'./adverlist.php',true);
$title='编辑用户广告';
include_once './header.php';
$rs = $DB->query("SELECT * FROM yixi_user WHERE 1");
$select = "";
$Admin = '<option '.($row['daili']==1?'selected ':'').'value="1">平台站长</option>';
while ($res = $DB->fetch($rs)) {
    $select.= '<option '.($row['daili']==$res['uid']?'selected ':'').'value="'.$res['uid'].'">UID:'.$res['uid'].' 用户名:'.$res['user'].'</option>';
}
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                编辑用户广告
            </div>
            <div class="card-body">
                <form class="form-horizontal layui-form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">广告标题：</label>
                        <input type="text" name="title" value="<?php echo $row['title']?>" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">跳转地址：</label>
                        <input type="text" name="url" value="<?php echo $row['url']?>" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">文字颜色：<div id="colorpicker"></div></label>
                        <input type="text" name="colour" value="<?php echo $row['colour']?>" id="colorpicker-form-input" placeholder="点击颜色选择器选择颜色" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">管理用户：</label>
                        <select name="daili" class="form-control" lay-search lay-filter="daili"><?php echo $Admin ?><?php echo $select ?></select>
                    </div>
                    <div class="form-group mb-3">
                        <div class="text-center">
                            <input type="radio" name="icon" id="icon" value="0"<?php if($row['icon']==0){echo' checked="checked"';}?>/>无
                            <input type="radio" name="icon" id="icon" value="1"<?php if($row['icon']==1){echo' checked="checked"';}?>/><img src="../assets/icon/tuij.gif">
                            <input type="radio" name="icon" id="icon" value="2"<?php if($row['icon']==2){echo' checked="checked"';}?>/><img src="../assets/icon/tj.gif">
                            <input type="radio" name="icon" id="icon" value="3"<?php if($row['icon']==3){echo' checked="checked"';}?>/><img src="../assets/icon/vip.gif">
                            <input type="radio" name="icon" id="icon" value="4"<?php if($row['icon']==4){echo' checked="checked"';}?>/><img src="../assets/icon/jing.gif">
                            <input type="radio" name="icon" id="icon" value="5"<?php if($row['icon']==5){echo' checked="checked"';}?>/><img src="../assets/icon/hoot.gif">
                            <input type="radio" name="icon" id="icon" value="6"<?php if($row['icon']==6){echo' checked="checked"';}?>/><img src="../assets/icon/zs.gif">
                            <input type="radio" name="icon" id="icon" value="7"<?php if($row['icon']==7){echo' checked="checked"';}?>/><img src="../assets/icon/hg.gif">
                            <input type="radio" name="icon" id="icon" value="8"<?php if($row['icon']==8){echo' checked="checked"';}?>/><img src="../assets/icon/hot.gif">
                            <input type="radio" name="icon" id="icon" value="9"<?php if($row['icon']==9){echo' checked="checked"';}?>/><img src="../assets/icon/guan.png">
                            <input type="radio" name="icon" id="icon" value="10"<?php if($row['icon']==10){echo' checked="checked"';}?>/><img src="../assets/icon/rz.png">
                            <input type="radio" name="icon" id="icon" value="11"<?php if($row['icon']==11){echo' checked="checked"';}?>/><img src="../assets/icon/hot.png">
                            <input type="radio" name="icon" id="icon" value="12"<?php if($row['icon']==12){echo' checked="checked"';}?>/><img src="../assets/icon/zan.png">
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">是否置顶：</label>
                        <select name="top" class="form-control" lay-search lay-filter="top"><option <?php echo $row['top'] == 1 ? 'selected ' : '' ?>value="1">1_是</option><option <?php echo $row['top'] == 0 ? 'selected ' : '' ?>value="0">0_否</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">是否显示：</label>
                        <select name="active" class="form-control" lay-search lay-filter="active"><option <?php echo $row['active'] == 1 ? 'selected ' : '' ?>value="1">1_是</option><option <?php echo $row['active'] == 0 ? 'selected ' : '' ?>value="0">0_否</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">到期时间：</label>
                        <input type="text" name="last" id="adver_datetime" value="<?php echo date('Y-m-d h:i:s', strtotime($row['last']))?>" class="layui-input" readonly required/>
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_adveredit">保存内容</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
include_once './bottom.php';
?>
<script type="text/javascript">
layui.use(['form', 'colorpicker', 'laydate'], function () {
    var form = layui.form;
    var colorpicker = layui.colorpicker;
    var laydate = layui.laydate;
    form.on('submit(submit_adveredit)', function (data) {
        layer.alert('是否要执行当前操作？', {
            icon: 3, btn: ['确定', '取消'], btn1: function (layero, index) {
                adveredit();
            }
        });
        return false;
    });
    colorpicker.render({
        elem: '#colorpicker',color: '#1c97f5',done: function(color){
            $('#colorpicker-form-input').val(color);
        }
    });
    laydate.render({
        elem: '#adver_datetime',type: 'datetime'
    });
});
function adveredit() {
    var title=$("input[name='title']").val();
    var url=$("input[name='url']").val();
    var colour=$("input[name='colour']").val();
    var daili = $("select[name='daili']").val();
    var icon=$("input[type='radio']:checked").val();
    var top = $("select[name='top']").val();
    var active = $("select[name='active']").val();
    var last=$("input[name='last']").val();
    if(title==""){
        layer.msg("请输入广告标题", {icon: 5});
        return false;
    }else if(url=="http://" || url=="https://"){
        layer.msg("请输入广告跳转地址", {icon: 5});
        return false;
    }else if(last==null){
        layer.msg("到期时间不能为空", {icon: 5});
        return false;
    }
    var ii = layer.msg('正在修改中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : "POST",
        url : "ajax.php?act=adveredit&id=<?php echo $id?>",
        data : {title:title,url:url,colour:colour,daili:daili,icon:icon,top:top,active:active,last:last},
        dataType : "json",
        success : function(data) {
            layer.close(ii);
            if(data.code == 0){
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
}
</script>
<?php
}else{
    sysmsg("参数错误",2,'./',true);
}
?>