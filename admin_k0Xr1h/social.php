<?php
include_once '../includes/common.php';
if($conf['admin_qqlogin_open']!=1){
    sysmsg("管理员未开启QQ一键快捷登录",2,'./login.php',true);
}
class Oauth{
    function __construct(){
        global $authurl;
        global $conf;
        $this->callback = $authurl.'admin/social.php';//登录回调地址
    }
    public function login(){
        global $allapi;
        $state = md5(uniqid(rand(), TRUE));
        $_SESSION['Oauth_state'] = $state;
        $keysArr = array("act" => "login","media_type" => $_GET['type'],"redirect_uri" => $this->callback,"state" => $state);
        $login_url = $allapi.'social/connect.php?'.http_build_query($keysArr);
        header("Location:$login_url");
    }
    public function callback(){
        global $allapi;
        //--------验证state防止CSRF攻击
        if($_GET['state'] != $_SESSION['Oauth_state']){
            sysmsg("状态不匹配。你可能是csrf的受害者。",2,'./login.php',true);
        }
        $keysArr = array("act" => "callback","code" => $_GET['code'],"redirect_uri" => $this->callback);
        $token_url = $allapi.'/social/connect.php?'.http_build_query($keysArr);
        $response = get_curl($token_url);
        $arr = json_decode($response,true);
        if(isset($arr['error_code'])){
            sysmsg("error:".$arr['error_code']."msg:".$arr['error_msg'],2,'./login.php',true);
        }
        $_SESSION['Oauth_access_token']=$arr["access_token"];
        $_SESSION['Oauth_social_uid']=$arr["social_uid"];
        return $arr;
    }
}
$Oauth = new Oauth();
header("Content-Type: text/html; charset=UTF-8");
if($_GET['code']){
    $array = $Oauth->callback();
    $media_type = $array['media_type'];
    $access_token = $array['access_token'];
    $social_uid = $array['social_uid'];
    if(!$conf['access_token']) {
        @header('Content-Type: text/html; charset=UTF-8');
        sysmsg("该QQ未绑定管理员账号！",2,'./login.php',true);
    }elseif($access_token===$conf['access_token']){
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
                log_result(1, '异地QQ快捷登录', '后台管理员:'.$user, 'IP:'.$clientip, '登录地点:'.$city);
            }
        }else{
            log_result(1, '管理员QQ快捷登录', '后台管理员:'.$user, 'IP:'.$clientip, '地点:'.$city);
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
        @header('Content-Type: text/html; charset=UTF-8');
        sysmsg("登陆平台成功！",1,'./',true);
    }
} else {
    $Oauth->login();
}