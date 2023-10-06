<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
if(isset($_GET['id'])) {
$id=isset($_GET['id'])?intval($_GET['id']):sysmsg("参数错误",2,'./',true);
$row=$DB->get_row("SELECT * FROM yixi_program WHERE id='{$id}' limit 1");
if(!$row)sysmsg("平台不存在该接口",2,'./prolist.php',true);
$title='编辑接口';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                编辑接口
            </div>
            <div class="card-body">
                <form class="form-horizontal layui-form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">接口名称：</label>
                        <input type="text" name="name" value="<?php echo $row['name']?>" class="form-control" placeholder="输入该接口名称" lay-verType="tips" lay-verify="required">
                    </div>
                    <div class="form-group mb-3">
                        <input type="file" id="file" onchange="fileUpload()" style="display:none;"/>
                        <label for="example-input-normal" style="font-weight: 500">接口图标：<span class="badge badge-success-lighten" onclick="fileView()">查看接口图标</span> <a href="javascript:fileSelect()" class="badge badge-danger-lighten">上传接口图标</a></label>
                        <input type="text" name="img" value="<?php echo $row['img']?>" class="form-control" placeholder="上传该接口图标" lay-verType="tips" lay-verify="required">
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">接口简介：</label>
                        <textarea name="desc" class="form-control" style="height:100px;" placeholder="简单概述一下该接口" lay-verType="tips" lay-verify="required"><?php echo $row['desc']?></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">接口别名：</label>
                        <input type="text" name="api_path" value="<?php echo $row['api_path']?>" class="form-control" placeholder="输入该接口的别名" lay-verType="tips" lay-verify="required">
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">接口返回方式：</label>
                        <input type="text" name="api_return" value="<?php echo $row['api_return']?>" class="form-control" placeholder="输入接口返回方式" lay-verType="tips" lay-verify="required">
                        <small><font color="red">说明：如<font color="blue">IMAGE、JSON</font>等</font></small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">接口请求格式：</label>
                        <input type="text" name="api_request" value="<?php echo $row['api_request']?>" class="form-control" placeholder="输入接口请求格式" lay-verType="tips" lay-verify="required">
                        <small><font color="red">说明：如<font color="blue">GET、POST</font>等</font></small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">接口是否付费：</label>
                        <select name="switch" class="form-control" lay-search lay-filter="switch"><option <?php echo $row['switch'] == y ? 'selected ' : '' ?>value="y">开启</option><option <?php echo $row['switch'] == n ? 'selected ' : '' ?>value="n">关闭</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">接口首页显示：</label>
                        <select name="visible" class="form-control" lay-search lay-filter="visible"><option <?php echo $row['visible'] == y ? 'selected ' : '' ?>value="y">开启</option><option <?php echo $row['visible'] == n ? 'selected ' : '' ?>value="n">关闭</option></select>
                    </div>
                    <div id="frame_set1" style="">
                        <div class="form-group mb-3">
                            <label for="example-input-normal" style="font-weight: 500">接口请求示例：</label>
                            <input type="text" name="api_url" value="<?php echo $row['api_url']?>" class="form-control">
                            <small><font color="red">说明：接口请求示例（显示用）如<font color="blue"><?php echo $siteurl.'/api.php?api=ini&app=10000';?></font></font></small>
                        </div>
                        <div class="form-group mb-3">
                            <label for="example-input-normal" style="font-weight: 500">接口版本号：</label>
                            <input type="text" name="version" value="<?php echo $row['version']?>" class="form-control">
                            <small><font color="red">说明：版本号类似于1.0 1.5 2.0 3.0 （显示用）</font></small>
                        </div>
                        <div class="form-group mb-3">
                            <label for="example-input-normal" style="font-weight: 500">接口版本信息：</label>
                            <textarea name="version_info" class="form-control" style="height:100px;"><?php echo $row['version_info']?></textarea>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">接口是否运行：</label>
                        <select name="active" class="form-control" lay-search lay-filter="active"><option <?php echo $row['active'] == y ? 'selected ' : '' ?>value="y">是</option><option <?php echo $row['active'] == n ? 'selected ' : '' ?>value="n">否</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">普通用户销售价格：</label>
                        <input type="text" name="ptprice" value="<?php echo $row['ptprice']?>" placeholder="输入该接口的普通用户销售价格" class="form-control" lay-verType="tips" lay-verify="required">
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">白银用户销售价格：</label>
                        <input type="text" name="byprice" value="<?php echo $row['byprice']?>" placeholder="输入该接口的白银会员销售价格" class="form-control" lay-verType="tips" lay-verify="required">
                        <small>价格应低于普通用户销售价格</small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">黄金用户销售价格：</label>
                        <input type="text" name="hjprice" value="<?php echo $row['hjprice']?>" placeholder="输入该接口的黄金会员销售价格" class="form-control" lay-verType="tips" lay-verify="required">
                        <small>价格应低于白银会员销售价格</small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">钻石用户销售价格：</label>
                        <input type="text" name="zsprice" value="<?php echo $row['zsprice']?>" placeholder="输入该接口的钻石会员销售价格" class="form-control" lay-verType="tips" lay-verify="required">
						<small>价格应低于黄金会员销售价格</small>
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_proedit">保存内容</button>
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
    form.on('submit(submit_proedit)', function (data) {
        layer.alert('是否要执行当前操作？', {
            icon: 3, btn: ['确定', '取消'], btn1: function (layero, index) {
                proedit();
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
        layer.msg("请先上传图片，才能预览", {icon: 5});
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
                layer.alert(data.msg, {icon: 5});
            }
        },
        error:function(data){
            layer.msg('服务器错误', {icon: 5});
            return false;
        }
    })
}
function proedit() {
    var name = $("input[name='name']").val();
    var img = $("input[name='img']").val();
    var desc = $("textarea[name='desc']").val();
    var api_path = $("input[name='api_path']").val();
    var api_url = $("input[name='api_url']").val();
    var api_return = $("input[name='api_return']").val();
    var switchauth = $("select[name='switch']").val();
    var visible = $("select[name='visible']").val();
    var api_request = $("input[name='api_request']").val();
    var version = $("input[name='version']").val();
    var version_info = $("textarea[name='version_info']").val();
    var active = $("select[name='active']").val();
    var ptprice = $("input[name='ptprice']").val();
    var byprice = $("input[name='byprice']").val();
    var hjprice = $("input[name='hjprice']").val();
    var zsprice = $("input[name='zsprice']").val();
    var ii = layer.msg('正在修改中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type: "POST",
        url: "ajax.php?act=proedit&id=<?php echo $id;?>",
        data : {name:name,img:img,desc:desc,api_path:api_path,api_url:api_url,api_return:api_return,switchauth:switchauth,visible:visible,api_request:api_request,version:version,version_info:version_info,active:active,ptprice:ptprice,byprice:byprice,hjprice:hjprice,zsprice:zsprice},
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