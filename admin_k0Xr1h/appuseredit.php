<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
if(isset($_GET['uid'])) {
$uid=isset($_GET['uid'])?intval($_GET['uid']):sysmsg("参数错误",2,'./',true);
$appid=isset($_GET['id'])?intval($_GET['id']):sysmsg("参数错误",2,'./',true);
$row=$DB->get_row("SELECT * FROM yixi_appuser WHERE uid='{$uid}' limit 1");
if(!$row)sysmsg("验证系统不存在该用户",2,'./appuserlist.php?id='.$appid,true);
$title='编辑用户';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                编辑用户
            </div>
            <div class="card-body">
                <form onsubmit="return appuseredit();" method="post" class="form-horizontal">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">用户名称：</label>
                        <input type="text" class="form-control" name="name" value="<?php echo $row['name'];?>">
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">用户帐号：</label>
                        <input type="text" class="form-control" name="user" value="<?php echo $row['user'];?>" disabled>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">用户Q Q：</label>
                        <input type="text" class="form-control" name="qq" value="<?php echo $row['qq'];?>">
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">重置密码：</label>
                        <input type="text" class="form-control" name="pwd" value="" placeholder="不重置请留空">
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">用户状态：</label>
                        <select name="status" class="form-control" default="<?php echo $row['status'];?>"><option value="y">激活</option><option value="n">封禁</option></select>
                    </div>
                    <input type="submit" name="submit" value="修 改" class="btn btn-block btn-xs btn-outline-success">
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
function appuseredit() {
	var name = $("input[name='name']").val();
	var qq = $("input[name='qq']").val();
	var pwd = $("input[name='pwd']").val();
	var status = $("select[name='status']").val();
	if (qq.length < 5 || qq.length > 10 || isNaN(qq)) {
		layer.msg('请输入5~10位QQ账号', {icon: 5});
		return false;
	}
	var ii = layer.msg('正在编辑用户中,请稍后...', {icon: 16, time: 10 * 1000});
	$.ajax({
		type: "POST",
		url: "ajax.php?act=Appuseredit&uid=<?php echo $uid;?>",
		data : {name:name,qq:qq,pwd:pwd,status:status},
		dataType: "json",
		success: function(data) {
			layer.close(ii);
			if (data.code == 0) {
				layer.msg(data.msg, {icon: 6});
				setTimeout(tz,2000)
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
function tz(){
window.location.href = './appuserlist.php?id=<?php echo $appid;?>';
}
</script>
<?php
}else{
	sysmsg("参数错误",2,'./',true);
}
?>