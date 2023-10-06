<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
if(isset($_GET['id'])) {
$id=isset($_GET['id'])?intval($_GET['id']):sysmsg("参数错误",2,'./',true);
$row=$DB->get_row("SELECT * FROM yixi_shop WHERE id='{$id}' limit 1");
if(!$row)sysmsg("平台不存在该商品",2,'./shoplist.php',true);
$title='编辑商品';
include_once './header.php';
?>
<style>
.image img {width: 120px;height: 120px;margin: 0.3em;box-shadow: 3px 3px 18px 1px #ccc}
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                编辑商品
            </div>
            <div class="card-body">
                <form class="form-horizontal layui-form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">商品类别：</label>
                        <select name="type" class="form-control" lay-search lay-filter="type"><option <?php echo $row['type'] == 1 ? 'selected ' : '' ?>value="1">软件类商品</option><option <?php echo $row['type'] == 2 ? 'selected ' : '' ?>value="2">插件类商品</option><option <?php echo $row['type'] == 3 ? 'selected ' : '' ?>value="3">源码类商品</option><option <?php echo $row['type'] == 4 ? 'selected ' : '' ?>value="3">其他类商品</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">商品名称：</label>
                        <input type="text" class="form-control" name="name" value="<?php echo $row['name'];?>" placeholder="请填写商品名称" lay-verType="tips" lay-verify="required">
                        <small>用于辨别商品，请取一个好听的名称吧</small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">商品售价：</label>
                        <input type="text" class="form-control" name="money" value="<?php echo $row['money'];?>" placeholder="请填写这个商品的售价" lay-verType="tips" lay-verify="required">
                        <small>未认证商品上架后会默认显示您设置的价格！</small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">提成比例：</label>
                        <input type="text" class="form-control" name="tcbl" value="<?php echo $row['tcbl'];?>" placeholder="请填写该商品的专属提成比例" lay-verType="tips" lay-verify="required">
                        <small>商品专属提成比例！</small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">商品图片：</label>
                        <input type="text" class="form-control" name="image" value="<?php echo $row['image'];?>" lay-verType="tips" lay-verify="required">
                        <small>图片外链可使用晴天内部图床 <a href="http://cdn.vue8.cn/" style="color: cornflowerblue;">点击进入晴天云盘(可做图片外链)</a><br><font color="darkmagenta">多张图片可用英文逗号(,)分割!如[图片链接1,图片链接2,图片链接3]</font></small>
                    </div>
                    <div class="form-group mb-3" id="iamge_le" style="display: none;">
                        <div class="image"></div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">版本号：</label>
                        <input type="text" class="form-control" name="version" value="<?php echo $row['version'];?>" placeholder="请填写商品版本号" lay-verType="tips" lay-verify="required">
                        <small>可编辑商品版本号,若和原来不一致,则提示更新</small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">版本日志：</label>
                        <textarea class="form-control" name="updatelog" style="height:100px;" placeholder="请填写商品版本日志,可留空,用于后续更新！" lay-verType="tips" lay-verify="required"><?php echo $row['updatelog'];?></textarea>
                        <small>投稿商品默认为1.0版本,可填写相关更新日志等,和商品介绍不同,后续升级版本可在我的商品内点按钮升级更新内容</small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">文件链接：</label>
                        <input type="text" class="form-control" name="filedata" value="<?php echo $row['filedata'];?>" placeholder="请填写文件下载链接" lay-verType="tips" lay-verify="required">
                        <small>卖家购买后的商品下载链接<font color="red">可填写压缩包下载链接,或直接蓝奏云链接等外链下载地址！</font></small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">客户端类型：</label>
                        <select name="proid" class="form-control" lay-search lay-filter="proid">
                        <option <?php echo $row['proid'] == 'android' ? 'selected ' : '' ?>value="android">Android</option>
						<option <?php echo $row['proid'] == 'ios' ? 'selected ' : '' ?>value="ios">IOS</option>
						<option <?php echo $row['proid'] == 'pc' ? 'selected ' : '' ?>value="pc">PC</option>
						<option <?php echo $row['proid'] == 'web' ? 'selected ' : '' ?>value="web">网站</option>
						<option <?php echo $row['proid'] == 'source' ? 'selected ' : '' ?>value="source">源码</option>
                        <option <?php echo $row['proid'] == 'other' ? 'selected ' : '' ?>value="other">其他</option>
                        </select>
                    </div>
                    <div id="frame_set1" style="display:none">
                        <div class="form-group mb-3">
                            <label for="example-input-normal" style="font-weight: 500">类型名称：</label>
                            <input type="text" class="form-control" name="system_name" value="<?php echo $row['system_name'];?>" placeholder="请填写其他类型的名称">
                            <small>若是选择其他类型,则需要填写此项！</small>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">商品介绍：</label>
                        <textarea class="form-control" name="recommend" style="height:100px;" placeholder="请填写文字介绍内容!推荐20字以内" lay-verType="tips" lay-verify="required"><?php echo $row['recommend'];?></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">是否认证：</label>
                        <select name="active" class="form-control" lay-search lay-filter="active"><option <?php echo $row['active'] == 1 ? 'selected ' : '' ?>value="1">1_是</option><option <?php echo $row['active'] == 0 ? 'selected ' : '' ?>value="0">0_否</option></select>
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_shopedit">保存内容</button>
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
    form.on('submit(submit_shopedit)', function (data) {
        layer.alert('是否要执行当前操作？', {
            icon: 3, btn: ['确定', '取消'], btn1: function (layero, index) {
                shopedit();
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
var images = $('input[name="image"]').val();
if (images != '') {
    $("#iamge_le").show(200);
    image_fenltt(images);
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
function shopedit() {
    var type = $("select[name='type']").val();
    var name = $("input[name='name']").val();
    var money = $("input[name='money']").val();
    var tcbl = $("input[name='tcbl']").val();
    var image = $("input[name='image']").val();
    var version = $("input[name='version']").val();
    var updatelog = $("textarea[name='updatelog']").val();
    var filedata = $("input[name='filedata']").val();
    var proid = $("select[name='proid']").val();
    var system_name = $("input[name='system_name']").val();
    var recommend = $("textarea[name='recommend']").val();
    var active = $("select[name='active']").val();
    var ii = layer.msg('正在修过中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type: "POST",
        url: "ajax.php?act=shopedit&id=<?php echo $id;?>",
        data : {type:type,name:name,money:money,tcbl:tcbl,image:image,version:version,updatelog:updatelog,filedata:filedata,proid:proid,system_name:system_name,recommend:recommend,active:active},
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