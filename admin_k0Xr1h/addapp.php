<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
$title='添加应用';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                添加应用
            </div>
            <div class="card-body">
                <form  class="form-horizontal layui-form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">应用名称：</label>
                        <input type="text" name="name" class="form-control" placeholder="输入该应用名称" lay-verType="tips" lay-verify="required">
                    </div>
                    <div class="form-group mb-3">
                        <input type="file" id="file" onchange="fileUpload()" style="display:none;"/>
                        <label for="example-input-normal" style="font-weight: 500">应用图标：<span class="badge badge-success-lighten" onclick="fileView()">查看应用图标</span> <a href="javascript:fileSelect()" class="badge badge-danger-lighten">上传应用图标</a></label>
                        <input type="text" name="img" class="form-control" placeholder="上传该应用图标" lay-verType="tips" value="assets/img/Program/program_5dea83d4a6aa5a5472583fbd512b68c8.png" lay-verify="required">
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">应用公告：</label>
                        <textarea name="appgg" class="form-control" style="height:100px;" placeholder="此处为应用公告，没有可先不填" lay-verType="tips"></textarea>
                    </div>
					<div class="form-group mb-3">
                            <label for="example-input-normal" style="font-weight: 500">应用版本：</label>
                            <input type="text" name="version" class="form-control" lay-verType="tips" value="1.0" lay-verify="required">
                            <small><font color="red">说明：版本号类似于1.0 1.5 2.0 3.0 （返回应用信息时使用）</font></small>
                    </div>
					<div class="form-group mb-3">
                            <label for="example-input-normal" style="font-weight: 500">应用版本信息：</label>
                            <textarea name="version_info" class="form-control" placeholder="此处为应用版本信息，没有可先不填" style="height:100px;"></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">应用是否付费：</label>
                        <select name="switch" class="form-control" lay-search lay-filter="switch"><option value="y">开启</option><option value="n">关闭</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">应用验证IP：</label>
                        <select name="ipauth" class="form-control" lay-search lay-filter="ipauth"><option value="n">关闭</option><option value="y">开启</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">应用是否运行：</label>
                        <select name="active" class="form-control" lay-search lay-filter="active"><option value="y">开启</option><option value="n">关闭</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">应用卡密销售价格：</label>
                        <input type="text" name="sqprice" placeholder="输入该应用的卡密销售价格" class="form-control" lay-verType="tips" value="0.01" lay-verify="required">
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">应用代理商销售价格：</label>
                        <input type="text" name="sqsprice" placeholder="输入该应用的代理商销售价格" class="form-control" lay-verType="tips" value="0.01" lay-verify="required">
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">应用超管销售价格：</label>
                        <input type="text" name="cgprice" placeholder="输入该应用的超管销售价格" class="form-control" lay-verType="tips" value="0.01" lay-verify="required">
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">应用代理商添加卡密价格：</label>
                        <input type="text" name="sqprice2" placeholder="输入该应用的代理商添加卡密的价格" class="form-control" lay-verType="tips" value="0.01" lay-verify="required">
                        <small>价格应低于卡密销售价格</small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">应用超管添加卡密价格：</label>
                        <input type="text" name="sqprice3" placeholder="输入该应用的超管添加卡密的价格" class="form-control" lay-verType="tips" value="0.01" lay-verify="required">
                        <small>价格应低于代理商添加卡密价格</small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">应用超管添加代理商价格：</label>
                       <input type="text" name="sqsprice2" placeholder="输入该应用的超管添加代理商的价格" class="form-control" lay-verType="tips"value="0.01"  lay-verify="required">
                       <small>价格应低于代理商销售价格</small>
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_addapp">添 加</button>
                </form>
            </div>
            <div class="card-footer">
                <span class="layui-icon layui-icon-tips"></span> 若不会填写可先保持默认，之后修改即可
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
    form.on('submit(submit_addapp)', function (data) {
        layer.alert('是否要执行当前操作？', {
            icon: 3, btn: ['确定', '取消'], btn1: function (layero, index) {
                addapp();
            }
        });
        return false;
    });
   
    form.on('select(safe)', function(data){ 
        if(data.value == '1'){
            $("#frame_set1").show();
        }else{
            $("#frame_set1").hide();
        }
    });
});
function fileSelect(){
    $("#file").trigger("click");
}
function fileView(){
    var img = $("input[name='img']").val();
    if(img=='') {
        layer.alert("请先上传图片，才能预览");
        return;
    }
    if(img.indexOf('http') == -1)img = '../'+img;
    layer.open({
        type: 1,
        area: ['360px', '400px'],
        title: '应用图标查看',
        shade: 0.3,
        anim: 1,
        shadeClose: true,
        content: '<center><img width="300px" src="'+img+'"></center>'
    });
}
function fileUpload(){
    var fileObj = $("#file")[0].files[0];
    if (typeof (fileObj) == "undefined" || fileObj.size <= 0) {
        return;
    }
    var formData = new FormData();
    formData.append("do","upload");
    formData.append("type","program");
    formData.append("file",fileObj);
    var ii = layer.msg('正在上传中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        url: "ajax.php?act=uploadappimg",
        data: formData,
        type: "POST",
        dataType: "json",
        cache: false,
        processData: false,
        contentType: false,
        success: function (data) {
            layer.close(ii);
            if(data.code == 0){
                layer.msg('上传应用图标成功', {icon: 6});
                $("input[name='img']").val(data.url);
            }else{
                layer.msg(data.msg, {icon: 5});
            }
        },
        error:function(data){
            layer.msg('服务器错误', {icon: 5});
            return false;
        }
    })
}
function addapp() {
    var name = $("input[name='name']").val();
    var img = $("input[name='img']").val();
	var appgg = $("textarea[name='appgg']").val();
    var switchauth = $("select[name='switch']").val();
    var ipauth = $("select[name='ipauth']").val();
    var version = $("input[name='version']").val();
    var version_info = $("textarea[name='version_info']").val();
    var active = $("select[name='active']").val();
    var sqprice = $("input[name='sqprice']").val();
    var sqprice2 = $("input[name='sqprice2']").val();
    var sqprice3 = $("input[name='sqprice3']").val();
    var sqsprice = $("input[name='sqsprice']").val();
    var sqsprice2 = $("input[name='sqsprice2']").val();
    var cgprice = $("input[name='cgprice']").val();
    var ii = layer.msg('正在添加中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type: "POST",
        url: "ajax.php?act=addapp",
        data : {name:name,img:img,appgg:appgg,switchauth:switchauth,ipauth:ipauth,version:version,version_info:version_info,active:active,sqprice:sqprice,sqprice2:sqprice2,sqprice3:sqprice3,sqsprice:sqsprice,sqsprice2:sqsprice2,cgprice:cgprice},
        dataType: "json",
        success: function(data) {
            layer.close(ii);
            if (data.code == 0) {
                layer.msg(data.msg, {
                    icon: 6,
                    end: function (layero, index) {
                        window.location.href = 'applist.php';
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