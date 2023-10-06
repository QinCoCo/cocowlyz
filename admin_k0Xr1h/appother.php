<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
if(isset($_GET['id'])) {
$id=isset($_GET['id'])?intval($_GET['id']):sysmsg("参数错误",2,'./',true);
$row=$DB->get_row("SELECT * FROM yixi_apps WHERE id='{$id}' limit 1");
if(!$row)sysmsg("系统不存在该应用",2,'./applist.php',true);
$title='其他配置';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                其他配置
            </div>
            <div class="card-body">
                <form onsubmit="return appother();" method="post" class="form-horizontal layui-form">
					<div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">应用安全配置：</label>
                        <select name="mi_state" class="form-control" lay-search lay-filter="mi_state"><option <?php echo $row['mi_state'] == y ? 'selected ' : '' ?>value="y">开启</option><option <?php echo $row['mi_state'] == n ? 'selected ' : '' ?>value="n">关闭</option></select>
						<small><font color="red">说明：开启安全控制后，可对<font color="blue">应用数据</font>进行加密, 防止数据泄露</font></small>
                    </div>
					<div id="frame_set1" style="display:inherit">
					<div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">数据加密类型：</label>
                        <select name="mi_type" class="form-control" lay-search lay-filter="mi_type"><option value="0" <?php echo $row['mi_type'] == 0 ? 'selected="selected"' : '' ?>>不加密</option><option value="1" <?php echo $row['mi_type'] == 1 ? 'selected="selected"' : '' ?>>RC4加密</option><option value="3" <?php echo $row['mi_type'] == 3 ? 'selected="selected"' : '' ?>>RC4加密-2</option><option value="2" <?php echo $row['mi_type'] == 2 ? 'selected="selected"' : '' ?>>BASE64加密</option></select>
						<div class="input-group-prepend" id="frame_set2" style="display:inherit">
						<small><font color="red" id="amount_a">说明：该设置仅对数据传输全局加密，不影响其他安全设置</font></small>
						</div>
                    </div>
					<div class="form-group mb-3" id="frame_set3" style="display:inherit">
                            <label for="example-input-normal" style="font-weight: 500">RC4秘钥：</label>
                            <div class="input-group">
							<input type="text" class="form-control" <?php echo $row['rc4_key'] != NULL ? 'value="'.$row['rc4_key'].'"' : 'value="'.random(16).'"' ?> name="rc4_key" id="rc4_key">
							<div class="input-group-prepend">
							<button class="btn btn-success" type="button" id="change_rc4key" lay-search lay-filter="change_rc4key">更换</button>
							</div></div>
							<small><font color="red"">说明：用于RC4加解密</font></small>
                        </div>
					<div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">数据签名开关：</label>
                        <select name="mi_sign" class="form-control" lay-search lay-filter="mi_sign"><option <?php echo $row['mi_sign'] == y ? 'selected ' : '' ?>value="y">开启</option><option <?php echo $row['mi_sign'] == n ? 'selected ' : '' ?>value="n">关闭</option></select>
						<small><font color="red">说明：若使用数据签名，同时需提交对应签名，可有效防止数据被篡改</font></small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">时间差效验：</label>
                        <input type="text" class="form-control" name="mi_time" value="<?php echo $row['mi_time']?>" lay-verType="tips" lay-verify="required">
                    <small><font color="red">说明：对客户设备时间与服务器时间进行时差校验，设置 0 则不校验</font></small>
					</div>
				    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">签名放DATA里：</label>
                        <select name="mi_sign_in" class="form-control" lay-search lay-filter="mi_sign_in"><option <?php echo $row['mi_sign_in'] == y ? 'selected ' : '' ?>value="y">开启</option><option <?php echo $row['mi_sign_in'] == n ? 'selected ' : '' ?>value="n">关闭</option></select>
						<small><font color="red">说明：开启后，若开启数据加密，需同时将签名加密其中，关闭则需额外提交签名</font></small>
                    </div>
                    					            <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">调试模式：</label>
                        <select name="print_sign" class="form-control" lay-search lay-filter="print_sign"><option <?php echo $row['print_sign'] == y ? 'selected ' : '' ?>value="y">开启</option><option <?php echo $row['print_sign'] == n ? 'selected ' : '' ?>value="n">关闭</option></select>
						<small><font color="red">说明：该选项为调试模式，默认关闭 开启后将输出提交的数据签名</font></small>
                    </div>
                    </div>
					<div class="form-group mb-3">
								<label for="example-input-normal" style="font-weight: 500">卡密解绑扣时长</label>
								<div class="input-group">
									<input type="number" id="km_change_num" name="km_change_num" class="form-control" value='<?php echo $row['km_change_num']?>' placeholder="卡密解绑扣时长，设置 0 则不扣">
									<div class="input-group-prepend">
										<span class="input-group-text" id="amount_a">小时</span>
									</div>
								</div>
					</div>
					<div class="form-group mb-3">
								<label for="example-input-normal" style="font-weight: 500">次数卡密解绑扣次数</label>
								<div class="input-group">
									<input type="number" id="single_km_change_num" name="single_km_change_num" class="form-control" value='<?php echo $row['single_km_change_num']?>' placeholder="次数卡密解绑扣次数，设置 0 则不扣">
									<div class="input-group-prepend">
										<span class="input-group-text" id="amount_a">次</span>
									</div>
								</div>
					</div>
					<div class="form-group mb-3">
								<label for="example-input-normal" style="font-weight: 500">卡密可解绑次数</label>
								<div class="input-group">
									<input type="number" id="km_change" name="km_change" class="form-control" value='<?php echo $row['km_change']?>' placeholder="卡密可解绑次数，设置 0 则不限制">
									<div class="input-group-prepend">
										<span class="input-group-text" id="amount_a">次</span>
									</div>
								</div>
					</div>
					<div class="form-group mb-3">
								<label for="example-input-normal" style="font-weight: 500">永久卡密可解绑次数</label>
								<div class="input-group">
									<input type="number" id="longuse_km_change" name="longuse_km_change" class="form-control" value='<?php echo $row['longuse_km_change']?>' placeholder="永久卡密可解绑次数，设置 0 则不限制">
									<div class="input-group-prepend">
										<span class="input-group-text" id="amount_a">次</span>
									</div>
								</div>
					</div>
					<div class="form-group mb-3">
								<label for="example-input-normal" style="font-weight: 500">卡密换绑时间间隔</label>
								<div class="input-group">
									<input type="number" id="km_change_time" name="km_change_time" class="form-control" value='<?php echo $row['km_change_time']?>' placeholder="卡密换绑时间间隔，设置 0 则不限制">
									<div class="input-group-prepend">
										<span class="input-group-text" id="amount_a">小时</span>
									</div>
								</div>
					</div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_useredit">保存内容</button>
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
    form.on('submit(submit_useredit)', function (data) {
        layer.alert('是否要执行当前操作？', {
            icon: 3, btn: ['确定', '取消'], btn1: function (layero, index) {
                appother();
            }
        });
        return false;
    });
    form.on('select(mi_state)', function(data){ 
        if(data.value == 'y'){
            $("#frame_set1").show();
        }
		if(data.value == 'n'){
            $("#frame_set1").hide();
        }
    });
	form.on('select(mi_type)', function(data){ 
        if(data.value == '0'){
			$("#frame_set2").show();
			$("#frame_set3").hide()
            document.getElementById('amount_a').innerHTML="说明：该设置仅对数据传输全局加密，不影响其他安全设置";
        }
		if(data.value == '1'){
            $("#frame_set2").show();
			$("#frame_set3").show()
			document.getElementById('amount_a').innerHTML="说明：该RC4仅适用于e4a系列应用，结绳可用，如果中文乱码问题请切换为RC4-2";
        }
		if(data.value == '3'){
            $("#frame_set2").show();
			$("#frame_set3").show()
			document.getElementById('amount_a').innerHTML="说明：该RC4加密后16进制转字符串";
        }
		if(data.value == '2'){
			$("#frame_set2").show();
			$("#frame_set3").hide()
            document.getElementById('amount_a').innerHTML="说明：该设置仅为BASE64加密，安全性较低";
        }
    });
});
      $(document).on('click',"#change_rc4key",function(){
		var rc4key=randomString(16);2
		document.getElementById('rc4_key').value=(rc4key);
      });
function appother() {
    var mi_state = $("select[name='mi_state']").val();
    var mi_type = $("select[name='mi_type']").val();
    var mi_sign = $("select[name='mi_sign']").val();
    var mi_sign_in = $("select[name='mi_sign_in']").val();
    var print_sign = $("select[name='print_sign']").val();
    var mi_time = $("input[name='mi_time']").val();
	var rc4_key = $("input[name='rc4_key']").val();
	var km_change_num = $("input[name='km_change_num']").val();
	var single_km_change_num = $("input[name='single_km_change_num']").val();
	var km_change = $("input[name='km_change']").val();
	var longuse_km_change = $("input[name='longuse_km_change']").val();
	var km_change_time = $("input[name='km_change_time']").val();
    var ii = layer.msg('正在保存中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type: "POST",
        url: "ajax.php?act=editApp_other&id=<?php echo $id;?>",
        data : {mi_state:mi_state,mi_type:mi_type,mi_sign:mi_sign,mi_sign_in:mi_sign_in,print_sign:print_sign,mi_time:mi_time,rc4_key:rc4_key,km_change_num:km_change_num,single_km_change_num:single_km_change_num,km_change:km_change,longuse_km_change:longuse_km_change,km_change_time:km_change_time},
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
function randomString(len) {
			len = len || 32;
			var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';    /****默认去掉了容易混淆的字符oOLl,9gq,Vv,Uu,I1****/
			var maxPos = $chars.length;
			var pwd = '';
			for (i = 0; i < len; i++) {
				pwd += $chars.charAt(Math.floor(Math.random() * maxPos));
			}
			return pwd;
		}
</script>
<?php
}else{
    sysmsg("参数错误",2,'./',true);
}
?>