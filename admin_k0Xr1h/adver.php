<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
$title='添加用户广告';
include_once './header.php';
$rs = $DB->query("SELECT * FROM yixi_user WHERE 1");
$select = "";
$Admin = "<option value=\"1\">平台站长</option>";
while ($res = $DB->fetch($rs)) {
    $select.= "<option value=\"".$res["uid"]."\">UID:".$res["uid"]." 用户名:".$res["user"]."</option>";
}
if($conf['adver_time_type']==2){
    $last=date('Y-m-d', strtotime('+'.$conf['adver_time'].' years'));
}elseif($conf['adver_time_type']==1){
    $last=date('Y-m-d', strtotime('+'.$conf['adver_time'].' months'));
}else{
    $last=date('Y-m-d', strtotime('+'.$conf['adver_time'].' days'));
}
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                添加用户广告
            </div>
            <div class="card-body">
                <form class="form-horizontal layui-form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">广告标题：</label>
                        <input type="text" name="title" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">跳转地址：</label>
                        <input type="text" name="url" value="http://" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">文字颜色：<div id="colorpicker"></div></label>
                        <input type="text" name="colour" id="colorpicker-form-input" placeholder="点击颜色选择器选择颜色" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">管理用户：</label>
                        <select name="daili" lay-filter="daili"><?php echo $Admin ?><?php echo $select ?></select>
                    </div>
                    <div class="form-group mb-3">
                        <div class="text-center">
                            <input type="radio" name="icon" id="icon" value="0"/>无
                            <input type="radio" name="icon" id="icon" value="1"/><img src="../assets/icon/tuij.gif">
                            <input type="radio" name="icon" id="icon" value="2"/><img src="../assets/icon/tj.gif">
                            <input type="radio" name="icon" id="icon" value="3"/><img src="../assets/icon/vip.gif">
                            <input type="radio" name="icon" id="icon" value="4"/><img src="../assets/icon/jing.gif">
                            <input type="radio" name="icon" id="icon" value="5"/><img src="../assets/icon/hoot.gif">
                            <input type="radio" name="icon" id="icon" value="6"/><img src="../assets/icon/zs.gif">
                            <input type="radio" name="icon" id="icon" value="7"/><img src="../assets/icon/hg.gif">
                            <input type="radio" name="icon" id="icon" value="8"/><img src="../assets/icon/hot.gif">
                            <input type="radio" name="icon" id="icon" value="9"/><img src="../assets/icon/guan.png">
                            <input type="radio" name="icon" id="icon" value="10"/><img src="../assets/icon/rz.png">
                            <input type="radio" name="icon" id="icon" value="11"/><img src="../assets/icon/hot.png">
                            <input type="radio" name="icon" id="icon" value="12"/><img src="../assets/icon/zan.png">
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">到期时间：</label>
                        <input type="text" id="adver_datetime" name="last" value="<?php echo $last ?>" class="layui-input" readonly required/>
                    </div>
                    <!--<input type="submit" name="submit" value="添 加" class="btn btn-block btn-xs btn-outline-success">-->
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_adveredit">添 加</button>
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
                addver();
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
function addver() {
    var title=$("input[name='title']").val();
    var url=$("input[name='url']").val();
    var colour=$("input[name='colour']").val();
    var daili = $("select[name='daili']").val();
    var icon=$("input[type='radio']:checked").val();
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
    var ii = layer.msg('正在添加中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : "POST",
        url : "ajax.php?act=addver",
        data : {title:title,url:url,colour:colour,daili:daili,icon:icon,last:last},
        dataType : "json",
        success : function(data) {
            layer.close(ii);
            if(data.code == 0){
                layer.msg(data.msg, {
                    icon: 6,
                    end: function (layero, index) {
                        window.location.href = 'adverlist.php';
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