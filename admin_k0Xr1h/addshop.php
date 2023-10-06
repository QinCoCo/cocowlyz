<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
$title='添加商品';
include_once './header.php';
?>
<style>
.image img {width: 120px;height: 120px;margin: 0.3em;box-shadow: 3px 3px 18px 1px #ccc}
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                添加商品赚钱
            </div>
            <div class="card-body">
                <form class="form-horizontal layui-form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">商品类别：</label>
                        <select name="type" class="form-control" lay-search lay-filter="type"><option value="1">软件类商品</option><option value="2">插件类商品</option><option value="3">源码类商品</option><option value="4">其他类商品</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">商品名称：</label>
                        <input type="text" class="form-control" name="name" placeholder="请填写商品名称" lay-verType="tips" lay-verify="required">
                        <small>用于辨别商品，请取一个好听的名称吧</small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">商品售价：</label>
                        <input type="text" class="form-control" name="money" placeholder="请填写这个商品的售价" lay-verType="tips" lay-verify="required">
                        <small>未认证商品上架后会默认显示您设置的价格！</small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">商品图片：</label>
                        <input type="text" class="form-control" name="image" lay-verType="tips" lay-verify="required">
                        <small>图片外链可使用墨惜图床 <a href="https://tc.838356132.xyz/" style="color: cornflowerblue;">点击进入</a><br><font color="darkmagenta">多张图片可用英文逗号(,)分割!如[图片链接1,图片链接2,图片链接3]</font></small>
                    </div>
                    <div class="form-group mb-3" id="iamge_le" style="display: none;">
                        <div class="image"></div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">版本日志：</label>
                        <textarea class="form-control" name="updatelog" style="height:100px;" placeholder="请填写商品版本日志,可留空,用于后续更新！" lay-verType="tips" lay-verify="required"></textarea>
                        <small>投稿商品默认为1.0版本,可填写相关更新日志等,和商品介绍不同,后续升级版本可在我的商品内点按钮升级更新内容</small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">文件链接：</label>
                        <input type="text" class="form-control" name="filedata" placeholder="请填写文件下载链接" lay-verType="tips" lay-verify="required">
                        <small>卖家购买后的商品下载链接<font color="red">可填写压缩包下载链接,或直接蓝奏云链接等外链下载地址！</font></small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">客户端类型：</label>
                        <select name="proid" class="form-control" lay-search lay-filter="proid"><option value="0">请选择客户端</option>
                        <option value="android">Android</option>
						<option value="ios">IOS</option>
						<option value="pc">PC</option>
						<option value="web">网站</option>
						<option value="source">源码</option>
                        <option value="other">其他</option>
                        </select>
                    </div>
                    <div id="frame_set1" style="display:none">
                        <div class="form-group mb-3">
                            <label for="example-input-normal" style="font-weight: 500">类型名称：</label>
                            <input type="text" class="form-control" name="system_name" placeholder="请填写其他类型的名称">
                            <small>若是选择其他类型,则需要填写此项！</small>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">商品介绍：</label>
                        <textarea class="form-control" name="recommend" style="height:100px;" placeholder="请填写文字介绍内容!推荐20字以内" lay-verType="tips" lay-verify="required"></textarea>
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_addshop">添 加</button>
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
    form.on('submit(submit_addshop)', function (data) {
        layer.alert('是否要执行当前操作？', {
            icon: 3, btn: ['确定', '取消'], btn1: function (layero, index) {
                addshop();
            }
        });
        return false;
    });
    form.on('select(proid)', function(data){ 
        if(data.value == 'other'){
            $("#frame_set1").show();
        }else{
            $("#frame_set1").hide();
        }
    });
});
function image_fenltt(image) {
    var image_arr = image.split(",");
    var content = "";
    for (a in image_arr) {
        content += '<img layer-pid="' + a + '" alt="' + a + '" layer-src="' + image_arr[a] + '" src="' + image_arr[a] + '" />';
    }
    $(".image").html(content);
}
$('input[name="image"]').bind('input propertychange', function () {
    var image_log = $('input[name="image"]').val();
    if (image_log != '') {
        $("#iamge_le").show(200);
        image_fenltt(image_log);
    } else {
        $("#iamge_le").hide(200);
    }
});
function addshop() {
    var type = $("select[name='type']").val();
    var name = $("input[name='name']").val();
    var money = $("input[name='money']").val();
    var image = $("input[name='image']").val();
    var updatelog = $("textarea[name='updatelog']").val();
    var filedata = $("input[name='filedata']").val();
    var proid = $("select[name='proid']").val();
    var system_name = $("input[name='system_name']").val();
    var recommend = $("textarea[name='recommend']").val();
    var ii = layer.msg('正在添加中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type: "POST",
        url: "ajax.php?act=addshop",
        data : {type:type,name:name,money:money,image:image,updatelog:updatelog,filedata:filedata,proid:proid,system_name:system_name,recommend:recommend},
        dataType: "json",
        success: function(data) {
            layer.close(ii);
            if (data.code == 0) {
                layer.msg(data.msg, {
                    icon: 6,
                    end: function (layero, index) {
                        window.location.href = 'shoplist.php';
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