<?php
$verifycode = 1;//验证码开关
if(!function_exists("imagecreate") || !file_exists('code.php'))$verifycode=0;
include_once '../includes/common.php';
if(isset($_GET['act']) && $_GET['act']=='login'){
	$user=daddslashes($_POST['user']);
	$pass=daddslashes($_POST['pass']);
	if($conf['admin_send_type']>0){
		$email=daddslashes($_POST['email']);
	}
	$code=daddslashes($_POST['code']);
	if(!$user || !$pass){
		exit('{"code":-1,"msg":"用户名或密码不能为空"}');
	}else if($user==$pass){
		exit('{"code":-1,"msg":"密码存在弱口令，禁止登陆！"}');
	}else if($user==$conf['kfqq']){
		exit('{"code":-1,"msg":"非法登陆,有意见联系QQ'.$conf['kfqq'].'"}');
	}
	if($conf['admin_send_type']>0){
		if(!$email || !$code){
			exit('{"code":-1,"msg":"邮箱或登录验证码不能为空"}');
		}
		if(!$conf['admin_email']){
			exit('{"code":-1,"msg":"管理员未绑定邮箱,禁止登陆！"}');
		}else if($email!=$conf['admin_email']){
			exit('{"code":-1,"msg":"此邮箱与管理员绑定的邮箱不符合,禁止登陆！"}');
		}
		$coderow=$DB->get_row("select * from yixi_code where code='".$code."' and hm='".$email."' order by id desc limit 1");
		if (!$coderow){
			exit('{"code":-1,"msg":"验证码不正确！"}');
		}
		if ($coderow['time']<TIMESTAMP-120 || $coderow['status']>0){
			exit('{"code":-1,"msg":"验证码已失效，请重新获取"}');
		}
	}else{
		if ($verifycode==1 && (!$code || strtolower($code) != $_SESSION['vc_admin_code'])) {
			unset($_SESSION['vc_admin_code']);
			@header('Content-Type: text/html; charset=UTF-8');
			exit('{"code":-1,"msg":"验证码错误！"}');
		}
	}
	if($conf['captcha_open_adminlogin']==1 && $conf['captcha_open']==1){
		if(isset($_POST['geetest_challenge']) && isset($_POST['geetest_validate']) && isset($_POST['geetest_seccode'])){
			require_once SYSTEM_ROOT.'class.geetestlib.php';
			$GtSdk = new GeetestLib($conf['captcha_id'], $conf['captcha_key']);
			$data = array(
				'user_id' => $cookiesid,
				'client_type' => "web",
				'ip_address' => $clientip
			);
			if ($_SESSION['gtserver'] == 1) {   //服务器正常
				$result = $GtSdk->success_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'], $data);
				if ($result) {
					//echo '{"status":"success"}';
				} else{
					exit('{"code":-1,"msg":"验证失败，请重新验证"}');
				}
			}else{  //服务器宕机,走failback模式
				if ($GtSdk->fail_validate($_POST['geetest_challenge'],$_POST['geetest_validate'],$_POST['geetest_seccode'])) {
					//echo '{"status":"success"}';
				}else{
					exit('{"code":-1,"msg":"验证失败，请重新验证"}');
				}
			}
		}else{
			exit('{"code":2,"type":1,"msg":"请先完成验证"}');
		}
	}else if($conf['captcha_open_adminlogin']==1 && $conf['captcha_open']==2){
		if(isset($_POST['token'])){
			require_once SYSTEM_ROOT.'class.dingxiang.php';
			$client = new CaptchaClient($conf['captcha_id'], $conf['captcha_key']);
			$client->setTimeOut(2);
			$response = $client->verifyToken($_POST['token']);
			if($response->result){
				/**token验证通过，继续其他流程**/
			}else{
				/**token验证失败**/
				exit('{"code":-1,"msg":"验证失败，请重新验证"}');
			}
		}else{
			exit('{"code":2,"type":2,"appid":"'.$conf['captcha_id'].'","msg":"请先完成验证"}');
		}
	}else if($conf['captcha_open_adminlogin']==1 && $conf['captcha_open']==3){
		if(isset($_POST['token'])){
			if(vaptcha_verify($conf['captcha_id'], $conf['captcha_key'], $_POST['token'], $clientip)){
				/**token验证通过，继续其他流程**/
			}else{
				/**token验证失败**/
				exit('{"code":-1,"msg":"验证失败，请重新验证"}');
			}
		}else{
			exit('{"code":2,"type":3,"appid":"'.$conf['captcha_id'].'","msg":"请先完成验证"}');
		}
	}else{
		if($user===$conf['admin_user'] && $pass===$conf['admin_pwd']) {
			if($conf['admin_send_type']==0){
				unset($_SESSION['vc_admin_code']);
			}
			$city=get_ip_city($clientip);
			$session=md5($user.$pass.$password_hash);
			$expiretime=TIMESTAMP+604800;
			$token=authcode("{$user}\t{$session}\t{$expiretime}", 'ENCODE', SYS_KEY);
			setcookie("admin_auth_token", $token, TIMESTAMP + 604800);
			saveSetting('adminlogin',$date);
			if ($conf['admin_remote_login_open'] == 1) {
				$citylist=explode(',',$conf['citylist']);
				if($conf['citylist'] && !in_array($city,$citylist)){
					log_result(1, '异地登录', '后台管理员:'.$user, 'IP:'.$clientip, '登录地点:'.$city);
				}
			}else{
				log_result(1, '后台管理员登录', '后台管理员:'.$user, 'IP:'.$clientip, '地点:'.$city);
			}
			if ($conf['admin_qq']) {
				$qq=$conf['admin_qq'];
			}else{
				$qq=$conf['kfqq'];
			}
			if ($conf['admin_login_open'] == 1) {
				$email = $qq.'@qq.com';
				$title = $conf['sitename'] . "-管理员登录通知！";
				$text = "嗨！站长，刚刚你平台后台操作登录成功，若不是你本人登录请尽快做好安全措施！";
				$msg = youfas($title,$text);
				$result = send_mail($email, $title, $msg);
			}
			$name=qqname($qq);
	if($conf['admin_send_type']>0){
		$DB->query("update `yixi_code` set `status` ='1' where `id`='{$coderow['id']}'");
	}
			exit('{"code":0,"msg":"登录平台成功！","qq":"'.$qq.'","name":"'.$name.'"}');
		}else {
			if($conf['admin_send_type']==0){
				unset($_SESSION['vc_admin_code']);
			}
			@header('Content-Type: text/html; charset=UTF-8');
			exit('{"code":-1,"msg":"用户名或密码不正确！"}');
		}
	}
}elseif(isset($_GET['act']) && $_GET['act']=='sendcode'){
	$email=daddslashes($_POST['email']);
	if(isset($_SESSION['admin_login_mail']) && $_SESSION['admin_login_mail']>TIMESTAMP-60){
		exit('{"code":-1,"msg":"请1分钟后在发送验证码！"}');
	}
	if(!$conf['admin_email']){
		exit('{"code":-1,"msg":"管理员未绑定邮箱,禁止登陆！"}');
	}else if($email!=$conf['admin_email']){
		exit('{"code":-1,"msg":"此邮箱与管理员绑定的邮箱不符合,禁止登陆！"}');
	}
	$rows=$DB->get_row("select * from yixi_code where hm='".$email."' order by id desc limit 1");
    if($rows['time']>TIMESTAMP-120){
		exit('{"code":-1,"msg":"两次发送邮件之间需要相隔120秒！"}');
	}
	$count=$DB->count("select count(*) from yixi_code where hm='".$email."' and time>'".(TIMESTAMP-3600*24)."'");
	if($count>$conf['mail_count']){
		exit('{"code":-1,"msg":"该邮箱发送次数过多，请更换邮箱！"}');
	}
	$count=$DB->count("select count(*) from yixi_code where ip='".$clientip."' and time>'".(TIMESTAMP-3600*24)."'");
	if($count>$conf['mail_countday']){
		exit('{"code":-1,"msg":"你今天发送次数过多，已被禁止接收验证码！"}');
	}
	$sub = $conf['sitename'].' - 登录验证码获取';
	$code = rand(1111111,9999999);//6位随机验证码
	$msg = '您的验证码是：'.$code;//发送的信息
	send_mail($email, $sub, $msg);
	if($DB->query("insert into `yixi_code` (`title`,`code`,`hm`,`time`,`date`,`ip`,`status`) values ('管理员登录验证码','".$code."','".$email."','".TIMESTAMP."','".$date."','".$clientip."','0')")){
		$_SESSION['admin_login_mail']=TIMESTAMP;
		exit('{"code":0,"msg":"发送验证码成功,请进入QQ邮箱查看！"}');
	}else{
		exit('{"code":-1,"msg":"发送验证码失败'.$DB->error().'"}');
	}
}elseif(isset($_GET['act']) && $_GET['act']=='qrlogin'){
	if($conf['admin_qqloginsm_open']!=1){
		@header('Content-Type: application/json; charset=UTF-8');
		exit('{"code":-1,"msg":"管理员未开启QQ一键扫码登录"}');
	}
	if(isset($_SESSION['saomalogin_qq']) && $qq=$_SESSION['saomalogin_qq']){
		unset($_SESSION['saomalogin_qq']);
		if($qq==$conf['admin_qq'] && $conf['admin_user']){
			$user=$conf['admin_user'];
			$pass=$conf['admin_pwd'];
			$city=get_ip_city($clientip);
			$session=md5($user.$pass.$password_hash);
			$expiretime=TIMESTAMP+604800;
			$token=authcode("{$user}\t{$session}\t{$expiretime}", 'ENCODE', SYS_KEY);
			setcookie("admin_auth_token", $token, TIMESTAMP + 604800);
			saveSetting('adminlogin',$date);
			if ($conf['admin_remote_login_open'] == 1) {
				$citylist=explode(',',$conf['citylist']);
				if($conf['citylist'] && !in_array($city,$citylist)){
					log_result(1, '异地QQ扫码登录', '后台管理员:'.$user, 'IP:'.$clientip, '登录地点:'.$city);;
				}
			}else{
				log_result(1, 'QQ扫码登录', '后台管理员:'.$user, 'IP:'.$clientip, '地点:'.$city);
			}
			if ($conf['admin_login_open'] == 1) {
				$title = $conf['sitename'] . "-管理员登录通知！";
				$text = "嗨！站长，刚刚你平台后台操作登录成功，若不是你本人登录请尽快做好安全措施！";
				$msg = youfas($title,$text);
				$result = send_mail($conf['mail_name'], $title, $msg);
			}
			if ($conf['admin_qq']) {
				$qq=$conf['admin_qq'];
			}else{
				$qq=$conf['kfqq'];
			}
			$name=qqname($qq);
			exit('{"code":0,"msg":"登录平台成功！","qq":"'.$qq.'","name":"'.$name.'"}');
		}else{
			@header('Content-Type: application/json; charset=UTF-8');
			exit('{"code":-1,"msg":"当前QQ不存在，该QQ可能未绑定为站长QQ"}');
		}
	}else{
		@header('Content-Type: application/json; charset=UTF-8');
		exit('{"code":-2,"msg":"验证失败，请重新扫码"}');
	}
}elseif(isset($_GET['act']) && $_GET['act']=='qrcode'){
	$image=trim($_POST['image']);
	$result = qrcodelogin($image);
	exit(json_encode($result));
}elseif(isset($_GET['logout'])){
	setcookie("admin_auth_token", "", TIMESTAMP - 604800);
	sysmsg("您已成功注销本次登陆！",1,'./login.php',true);
}elseif($islogin==1){
	sysmsg("您已登陆！",2,'./',true);
}
$title='管理员登录';
if($conf['admin_qq']!=""){
	$zzqq=$conf['admin_qq'];
}else{
	$zzqq=$conf['kfqq'];
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title><?php echo $title;?> - <?php echo $conf['sitename'];?></title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
  <link rel="stylesheet" href="../assets/layui/css/layui.css" media="all">
  <link rel="stylesheet" href="../assets/layui/style/admin.css" media="all">
  <link rel="stylesheet" href="../assets/layui/style/login.css" media="all">
</head>
<style type="text/css">
#LAY-user-login {
    width: 100%;
    height: 100%;
    background-size: 300%;
    animation: bganimation 30s infinite;
    font-family: '@font-face';
}
@font-face {
    font-family: 'Nunito';
    font-style: normal;
    font-weight: 400;
    src: local('Nunito Regular'), local('Nunito-Regular'), url(/assets/fonts/XRXV3I6Li01BKofIOuaBTMnFcQIG.woff2) format('woff2');
    unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+1EA0-1EF9, U+20AB;
}

.FontColor {
    background-image: -webkit-linear-gradient(125deg, #29b9ff, #ff5b40);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-align: center;
    font-size: 2em;
    height: 3em;
    line-height: 3em;
    color: #745dff;
}
.LoginIcon {color: coral;cursor: pointer;}
.LgoinBtn {background: #76e633;}
.LgoinBtnDe {background: #CCCCCC;}
.visitor {width: 2em;height: 2em;margin: 0em auto;box-shadow: 3px 3px 8px 0px #ccc;border-radius: 30rem;cursor: pointer;margin-top: 0em;}
</style>
<body>
<style>
    @media screen and (max-width: 788px) {
        .nbi {
            width: 94% !important;
            margin-top: -3.5em;
        }
    }
</style>
<div class="layadmin-user-login layadmin-user-display-show" id="LAY-user-login" style="display: none;width: 100%;height: auto;background-image: url(https://bing.ioliu.cn/v1/rand?w=1920&h=1080);-webkit-background-size: cover和-o-background-size: cover;">
  <div class="layadmin-user-login-main nbi" style="background-color: white;border-radius: 0.3em;opacity: 0.9;">
    <a href="/" class="layui-icon layui-icon-left" style="position: absolute;margin: 0.6em;background-image: -webkit-linear-gradient(125deg, #29b9ff, #ff5b40);-webkit-background-clip: text;-webkit-text-fill-color: transparent;"></a>
      <div class="layadmin-user-login-box layadmin-user-login-header">
        <h2 class="FontColor"><?php echo $conf['sitename'];?></h2>
        <p><img src="//q4.qlogo.cn/headimg_dl?dst_uin=<?php echo $zzqq;?>&spec=100" width="18" height="18"/><?php echo $title;?></p>
      </div>
      <div class="layadmin-user-login-box layadmin-user-login-body layui-form">
        <div class="layui-form-item">
          <label class="layadmin-user-login-icon layui-icon layui-icon-username"></label>
          <input type="text" name="user" id="user" lay-verify="required" placeholder="用户名" class="layui-input">
        </div>
        <div class="layui-form-item">
          <label class="layadmin-user-login-icon layui-icon layui-icon-password"></label>
          <input type="password" name="pass" id="pass" lay-verify="required" placeholder="密码" class="layui-input">
        </div>
        <?php if($conf['admin_send_type']>0){?>
        <?php if($conf['admin_send_type']==1){?>
        <div class="layui-form-item">
          <label class="layadmin-user-login-icon layui-icon layui-icon-email"></label>
          <input type="text" name="email" id="email" lay-verify="required" placeholder="邮箱" class="layui-input">
        </div>
        <div class="layui-form-item">
          <div class="layui-row">
            <div class="layui-col-xs7">
              <label class="layadmin-user-login-icon layui-icon layui-icon-vercode"></label>
              <input type="text" name="code" id="code" lay-verify="required" placeholder="邮箱验证码" class="layui-input">
            </div>
            <div class="layui-col-xs5">
              <div style="margin-left: 10px;">
                <span class="layui-btn layui-btn-primary layui-btn-fluid" id="sendcode">获取验证码</span>
              </div>
            </div>
          </div>
        </div>
        <?php }}else{?>
        <div class="layui-form-item">
          <div class="layui-row">
            <div class="layui-col-xs7">
              <label class="layadmin-user-login-icon layui-icon layui-icon-vercode"></label>
              <input type="text" name="code" id="code" lay-verify="required" placeholder="图片验证码" class="layui-input">
            </div>
            <div class="layui-col-xs5">
              <div style="margin-left: 10px;">
                <img id="codeimg" src="./code.php?r=<?php echo TIMESTAMP;?>" height="38" onclick="this.src='./code.php?r='+Math.random();" title="点击更换验证码" class="layadmin-user-login-codeimg">
              </div>
            </div>
          </div>
        </div>
        <?php }?>
        <?php if($conf['captcha_open_adminlogin']==1 && $conf['captcha_open']>=1){?>
			<input type="hidden" name="captcha_type" value="<?php echo $conf['captcha_open']?>"/>
			<?php if($conf['captcha_open']>=2){?><input type="hidden" name="appid" value="<?php echo $conf['captcha_id']?>"/><?php }?>
			<div id="captcha" style="margin: auto;"><div id="captcha_text">正在加载验证码</div>
            <div id="captcha_wait">
                <div class="loading">
                    <div class="loading-dot"></div>
                    <div class="loading-dot"></div>
                    <div class="loading-dot"></div>
                    <div class="loading-dot"></div>
                </div>
            </div></div>
			<div id="captchaform"></div>
			<br/>
		<?php }?>
		<div class="layui-form-item" style="margin-bottom: 20px;">
            <input type="checkbox" name="remember" lay-skin="primary" checked="checked" title="记住密码">
            <a onclick="saoma()" href="#" class="layadmin-user-jump-change layadmin-link" style="margin-top: 7px;">扫码登陆</a>
        </div>
        <button class="layui-btn layui-btn-fluid layui-anim layui-anim-upbit" id="submit_login" style="background-color: #ff7235">登 入</button>
        <div class="layui-trans layui-form-item layadmin-user-login-other"><label>快捷登录</label>
           <a href="./social.php"><i class="layui-icon layui-icon-login-qq LoginIcon"></i></a>
           <a href="/" class="layadmin-user-jump-change layadmin-link">返回首页</a>
        </div>
      </div>
    </div>
    <div class="layui-trans layadmin-user-login-footer" style="color: white;">
      <p>© 2020 <a href="/" target="_blank" style="color: white;"><?php echo $conf['sitename'];?></a></p>
    </div>
  </div>
<script src="//lib.baomitu.com/jquery/3.4.1/jquery.min.js"></script>
<script src="//lib.baomitu.com/layer/2.3/layer.js"></script>
<script src="../assets/layui/layui.all.js"></script>
<script>
function invokeSettime(obj){
    var countdown=120;
    settime(obj);
    function settime(obj) {
        if (countdown == 0) {
            $(obj).attr("data-lock", "false");
            $(obj).text("获取验证码");
            countdown = 120;
            return;
        } else {
			$(obj).attr("data-lock", "true");
            $(obj).attr("disabled",true);
            $(obj).text("" + countdown + " s 重发");
            countdown--;
        }
        setTimeout(function() {
            settime(obj) 
        }
        ,1000)
    }
}
<?php if($conf['admin_send_type']>0){?>
$("#sendcode").click(function(){
	if ($(this).attr("data-lock") === "true") return;
	var email=$("input[name='email']").val();
	if(email==''){layer.msg('邮箱不能为空！');return false;}
	var reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/;
	if(!reg.test(email)){layer.msg('邮箱格式不正确！');return false;}
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : "POST",
		url : "login.php?act=sendcode",
		data : {email:email},
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code == 0){
				new invokeSettime("#sendcode");
				layer.alert(data.msg, {icon: 1});
			}else{
				layer.alert(data.msg, {icon: 2});
			}
		} 
	});
});
<?php }?>
var handlerEmbed = function (captchaObj) {
	captchaObj.appendTo('#captcha');
	captchaObj.onReady(function () {
		$("#captcha_wait").hide();
	}).onSuccess(function () {
		var result = captchaObj.getValidate();
		if (!result) {
			return alert('请完成验证');
		}
		$("#captchaform").html('<input type="hidden" name="geetest_challenge" value="'+result.geetest_challenge+'" /><input type="hidden" name="geetest_validate" value="'+result.geetest_validate+'" /><input type="hidden" name="geetest_seccode" value="'+result.geetest_seccode+'" />');
	});
};
var handlerEmbed2 = function (token) {
	if (!token) {
		return alert('请完成验证');
	}
	$("#captchaform").html('<input type="hidden" name="token" value="'+token+'" />');
};
var handlerEmbed3 = function (vaptchaObj) {
	vaptchaObj.render();
	$('#captcha_text').hide();
	vaptchaObj.listen('pass', function() {
		var token = vaptchaObj.getToken();
		if (!token) {
			return alert('请完成验证');
		}
		$("#captchaform").html('<input type="hidden" name="token" value="'+token+'" />');
	})
};
$(document).ready(function(){
	var captcha_type = $("input[name='captcha_type']").val();
	$("#submit_login").click(function(){
		var user = $("input[name='user']").val();
		var pass = $("input[name='pass']").val();
		<?php if($conf['admin_send_type']>0){?>
		var phone = $("input[name='phone']").val();
		var email = $("input[name='email']").val();
		<?php }?>
		var code = $("input[name='code']").val();
		if(user=='' || pass=='' || code==''){
			layer.alert('请确保各项都不能为空！');return false;
		}
		<?php if($conf['admin_send_type']>0){?>
		<?php if($conf['admin_send_type']==1){?>
		if(email==''){
			layer.alert('管理员绑定的邮箱不能为空！');return false;
		}
		<?php }?>
		var data = {user:user, pass:pass, email:email, code:code};
		<?php }else{?>
		var data = {user:user, pass:pass, code:code};
		<?php }?>
		if(captcha_type == 1){
			var geetest_challenge = $("input[name='geetest_challenge']").val();
			var geetest_validate = $("input[name='geetest_validate']").val();
			var geetest_seccode = $("input[name='geetest_seccode']").val();
			if(geetest_challenge == undefined){
				layer.alert('请先完成滑动验证！'); return false;
			}
			var adddata = {geetest_challenge:geetest_challenge, geetest_validate:geetest_validate, geetest_seccode:geetest_seccode};
		}else if(captcha_type == 2||captcha_type == 3){
			var token = $("input[name='token']").val();
			if(token == undefined){
				layer.alert('请先完成滑动验证！'); return false;
			}
			var adddata = {token:token};
		}
		var ii = index = layer.msg('正在登录中...', {icon: 16,time: 2000000});
		$.ajax({
			type : "POST",
			url : "login.php?act=login",
			data : Object.assign(data, adddata),
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if(data.code == 0){
					layer.msg('<center><img src="https://q4.qlogo.cn/g?b=qq&nk='+data.qq+'&s=640" height="60"></center><hr>你好'+data.name+',欢迎回来！', {
						btn: '进入后台', btnAlign: 'c', end: function (layero, index) {
							window.location.href = './'
						}
					})
				}else{
					layer.alert(data.msg, {icon: 2});
				}
			} 
		});
	});
	if(captcha_type == 1){
		$.getScript("//static.geetest.com/static/tools/gt.js", function() {
			$.ajax({
				url: "ajax.php?act=captcha&t=" + (new Date()).getTime(),
				type: "get",
				dataType: "json",
				success: function (data) {
					$('#captcha_text').hide();
					$('#captcha_wait').show();
					initGeetest({
						gt: data.gt,
						challenge: data.challenge,
						new_captcha: data.new_captcha,
						product: "popup",
						width: "100%",
						offline: !data.success
					}, handlerEmbed);
				}
			});
		});
	}else if(captcha_type == 2){
		var appid = $("input[name='appid']").val();
		$.getScript("//cdn.dingxiang-inc.com/ctu-group/captcha-ui/index.js", function() {
			var myCaptcha = _dx.Captcha(document.getElementById('captcha'), {
				appId: appid,
				type: 'basic',
				style: 'oneclick',
				width: "",
				success: handlerEmbed2
			})
			myCaptcha.on('ready', function () {
				$('#captcha_text').hide();
			})
		});
	}else if(captcha_type == 3){
		var appid = $("input[name='appid']").val();
		$.getScript("//v.vaptcha.com/v3.js", function() {
			vaptcha({
				vid: appid,
				type: 'click',
				container: '#captcha',
				offline_server: 'https://management.vaptcha.com/api/v3/demo/offline'
			}).then(handlerEmbed3);
		});
	}
});
function saoma() { //扫码登陆
	layer.open({type: 1, offset: '188px',anim: 6, title: '请先使用QQ手机版扫描二维码',content: '<div class="form-group" style="text-align: center;"><div style="margin-top:1rem;margin-bottom:1rem;color:#666" id="login"><span id="loginmsg">请使用QQ手机版扫描二维码</span><span id="loginload" style="color: #790909;">.</span></div><div id="qrimg"></div></br><div class="list-group-item" id="mobile" style="display:none;"><button type="button" id="mlogin" onclick="mloginurl()" class="layui-btn layui-btn-fluid layui-btn-normal layui-btn-lg">跳转QQ快捷登录</button></div></div><script src="../assets/js/qrlogin.js?ver=<?php echo VERSION ?>"><\/script>'});
}
</script>
</body>
</html>