<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
$title='添加用户';
include_once './header.php';
if(!$_GET['id'])showmsg('警告', '获取APPID失败！', 2, './appuserlist.php');
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                 添加用户
            </div>
            <div class="card-body">
                <form onsubmit="return adduser();" method="post" class="form-horizontal">
                        <div class="form-group mb-3">
                            <label for="example-input-normal" style="font-weight: 500">用户名：</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
						<div class="form-group mb-3">
                            <label for="example-input-normal" style="font-weight: 500">帐号：</label>
                            <input type="text" class="form-control" name="user" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="example-input-normal" style="font-weight: 500">密码：</label>
                            <input type="text" class="form-control" name="pwd" value="123456" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="example-input-normal" style="font-weight: 500">用户Q Q：</label>
                            <input type="text" class="form-control" name="qq" required>
                        </div>
                    <input type="submit" name="submit" value="添加用户" class="btn btn-block btn-xs btn-outline-success">
                </form>
            </div>
        </div>
    </div>
</div>
<?php
include_once './bottom.php';
?>
<script type="text/javascript">
var items = $("select[default]");
for (i = 0; i < items.length; i++) {
    $(items[i]).val($(items[i]).attr("default")||0);
}
function GetRequest() {
            var url = location.search; //获取url中"?"符后的字串
            var theRequest = new Object();
            if (url.indexOf("?") != -1) {
                var str = url.substr(1);
                strs = str.split("&");
                for(var i = 0; i < strs.length; i ++) {
                    theRequest[strs[i].split("=")[0]]=decodeURI(strs[i].split("=")[1]);
                }
            }
            return theRequest;
        } 
function adduser() {
	var Request = new Object();
        Request = GetRequest();
    var appid = Request['id'];
    var name = $("input[name='name']").val();
    var user = $("input[name='user']").val();
    var pwd = $("input[name='pwd']").val();
    var qq = $("input[name='qq']").val();
    if (qq.length < 5 || qq.length > 10 || isNaN(qq)) {
        layer.msg('请输入5~10位QQ账号', {icon: 5});
        return false;
    }
    var ii = layer.msg('正在添加用户中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type: "POST",
        url: "ajax.php?act=addappuser",
        data : {appid:appid,name:name,user:user,pwd:pwd,qq:qq},
        dataType: "json",
        success: function(data) {
            layer.close(ii);
            if (data.code == 0) {
                layer.msg(data.msg, {
                    icon: 6,
                    end: function (layero, index) {
                        window.location.href = './appuserlist.php?id='+appid;
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