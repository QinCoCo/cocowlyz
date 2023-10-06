<?php 
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
if($conf['admin_qqlogin_open']!=1){
    sysmsg("管理员未开启QQ一键快捷登录",2,'./login.php',true);
}
class Oauth{
    function __construct(){
        global $authurl;
        global $conf;
        $this->callback = $authurl.'admin/binding.php';//登录回调地址
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
        if($_GET['state'] !== $_SESSION['Oauth_state']){
            sysmsg("状态不匹配。你可能是csrf的受害者。",2,'./',true);
        }
        $keysArr = array("act" => "callback","code" => $_GET['code'],"redirect_uri" => $this->callback);
        $token_url = $allapi.'/social/connect.php?'.http_build_query($keysArr);
        $response = get_curl($token_url);
        $arr = json_decode($response,true);
        if(isset($arr['error_code'])){
            sysmsg("error:".$arr['error_code']."msg:".$arr['error_msg'],2,'./',true);
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
    if($conf['access_token']) {
        @header('Content-Type: text/html; charset=UTF-8');
        sysmsg("管理员账号已绑定QQ！",2,'./',true);
    }
    saveSetting('access_token',$access_token);
    @header('Content-Type: text/html; charset=UTF-8');
    sysmsg("绑定成功！",1,'./',true);
    unset($array);
}elseif(isset($_GET['jiebang'])){
    $access_token='';
    saveSetting('access_token',$access_token);
    @header('Content-Type: text/html; charset=UTF-8');
    sysmsg("您已成功解绑QQ！",2,'./',true);
}else{
    $Oauth->login();
}