<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
$title='添加接口';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                添加接口
            </div>
            <div class="card-body">
                <form  class="form-horizontal layui-form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">接口名称：</label>
                        <input type="text" name="name" class="form-control" placeholder="输入该接口名称" lay-verType="tips" lay-verify="required">
                    </div>
                    <div class="form-group mb-3">
                        <input type="file" id="file" onchange="fileUpload()" style="display:none;"/>
                        <label for="example-input-normal" style="font-weight: 500">接口图标：<span class="badge badge-success-lighten" onclick="fileView()">查看接口图标</span> <a href="javascript:fileSelect()" class="badge badge-danger-lighten">上传接口图标</a></label>
                        <input type="text" name="img" class="form-control" placeholder="上传该接口图标" lay-verType="tips" value="assets/img/Program/program_5dea83d4a6aa5a5472583fbd512b68c8.png" lay-verify="required">
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">接口简介：</label>
                        <textarea name="desc" class="form-control" style="height:100px;" placeholder="简单概述一下该接口" lay-verType="tips" lay-verify="required"></textarea>
                    </div>
					<div class="form-group mb-3">
                            <label for="example-input-normal" style="font-weight: 500">接口版本号：</label>
                            <input type="text" name="version" class="form-control" lay-verType="tips" value="1.0" lay-verify="required">
                            <small><font color="red">说明：版本号类似于1.0 1.5 2.0 3.0 （显示用）</font></small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">接口别名：</label>
                        <input type="text" name="api_path" class="form-control" placeholder="输入该接口的别名" lay-verType="tips" lay-verify="required">
						<small><font color="red">说明：作为接口参数(选择对应API)</font></small>
                    </div>
					<div id="frame_set1" style="">
                      <div class="form-group mb-3">
                          <label for="example-input-normal" style="font-weight: 500">接口请求示例：</label>
                          <input type="text" name="api_url" class="form-control" lay-verType="tips" value="<?php echo $_SERVER['REQUEST_SCHEME'].'s://'.$_SERVER['HTTP_HOST'].'/api.php?api=';?>" lay-verify="required">
                          <small><font color="red">说明：接口请求示例（显示用）如<font color="blue"><?php echo $_SERVER['REQUEST_SCHEME'].'s://'.$_SERVER['HTTP_HOST'].'/api.php?api=ini&app=10000';?></font></font></small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">接口返回格式：</label>
						<select name="api_return" class="form-control" lay-search lay-filter="switch"><option value="JSON">JSON</option><option value="TEXT">TEXT</option><option value="IMAGE">IMAGE</option></select>
						<small><font color="red">说明：如<font color="blue">IMAGE、JSON</font>等</font></small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">接口请求方式：</label>
						<select name="api_request" class="form-control" lay-search lay-filter="switch"><option value="GET">GET</option><option value="POST">POST</option><option value="GET/POST">GET/POST</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">接口是否付费：</label>
                        <select name="switch" class="form-control" lay-search lay-filter="switch"><option value="n">关闭</option><option value="y">开启</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">接口首页显示：</label>
                        <select name="visible" class="form-control" lay-search lay-filter="visible"><option value="y">开启</option><option value="n">关闭</option></select>
                    </div>
                        <div class="form-group mb-3">
                            <label for="example-input-normal" style="font-weight: 500">接口版本信息：</label>
                            <textarea name="version_info" class="form-control" style="height:100px;"><?php echo date("Y.m.d").".第一代版本诞生.";?></textarea>
                        </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">接口是否运行：</label>
                        <select name="active" class="form-control" lay-search lay-filter="active"><option value="y">开启</option><option value="n">关闭</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">普通用户销售价格：</label>
                        <input type="text" name="ptprice" placeholder="输入该接口的普通用户销售价格" class="form-control" lay-verType="tips" value="0.00" lay-verify="required">
                    </div>
					<div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">白银会员销售价格：</label>
                        <input type="text" name="byprice" placeholder="输入该接口的白银会员销售价格" class="form-control" lay-verType="tips" value="0.00" lay-verify="required">
                    </div>
					<div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">黄金会员销售价格：</label>
                        <input type="text" name="hjprice" placeholder="输入该接口的黄金会员销售价格" class="form-control" lay-verType="tips" value="0.00" lay-verify="required">
                    </div>
					<div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">钻石会员销售价格：</label>
                        <input type="text" name="zsprice" placeholder="输入该接口的钻石会员销售价格" class="form-control" lay-verType="tips" value="0.00" lay-verify="required">
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_addpro">添 加</button>
                </form>
            </div>
            <div class="card-footer">
                <span class="layui-icon layui-icon-tips"></span> 添加成功后，将会在api/api和template/模板/doc中生成一个以别名命名用于存放api和doc的文件夹
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
    form.on('submit(submit_addpro)', function (data) {
        layer.alert('是否要执行当前操作？', {
            icon: 3, btn: ['确定', '取消'], btn1: function (layero, index) {
                addpro();
            }
        });
        return false;
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
        title: '接口图标查看',
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
        url: "ajax.php?act=uploadproimg",
        data: formData,
        type: "POST",
        dataType: "json",
        cache: false,
        processData: false,
        contentType: false,
        success: function (data) {
            layer.close(ii);
            if(data.code == 0){
                layer.msg('上传接口图标成功', {icon: 6});
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
function addpro() {
    var name = $("input[name='name']").val();
    var img = $("input[name='img']").val();
    var desc = $("textarea[name='desc']").val();
    var api_path = $("input[name='api_path']").val();
    var api_url = $("input[name='api_url']").val();
    var api_return = $("select[name='api_return']").val();
    var switchauth = $("select[name='switch']").val();
    var visible = $("select[name='visible']").val();
    var api_request = $("select[name='api_request']").val();
    var version = $("input[name='version']").val();
    var version_info = $("textarea[name='version_info']").val();
    var active = $("select[name='active']").val();
    var ptprice = $("input[name='ptprice']").val();
    var byprice = $("input[name='byprice']").val();
    var hjprice = $("input[name='hjprice']").val();
    var zsprice = $("input[name='zsprice']").val();
    var ii = layer.msg('正在添加中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type: "POST",
        url: "ajax.php?act=addpro",
        data : {name:name,img:img,desc:desc,api_path:api_path,api_url:api_url,api_return:api_return,switchauth:switchauth,visible:visible,api_request:api_request,version:version,version_info:version_info,active:active,ptprice:ptprice,byprice:byprice,hjprice:hjprice,zsprice:zsprice},
        dataType: "json",
        success: function(data) {
            layer.close(ii);
            if (data.code == 0) {
                layer.msg(data.msg, {
                    icon: 6,
                    end: function (layero, index) {
                        window.location.href = 'prolist.php';
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