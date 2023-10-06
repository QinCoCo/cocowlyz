<?php
include_once '../includes/common.php';
$act=isset($_GET['act'])?daddslashes($_GET['act']):null;

@header('Content-Type: application/json; charset=UTF-8');

function jc_hm($len = 1)
{
    $str = "123456789";
    $strlen = strlen($str);
    $randstr = "";
    for ($i = 0; $i < $len; $i++) {
        $randstr .= $str[mt_rand(0, $strlen - 1)];
    }
    return $randstr;
}

switch($act){
case 'sso':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $uid=intval($_GET['uid']);
    $userrow=$DB->get_row("select * from yixi_user where uid='$uid' limit 1");
    if(!$userrow)exit('{"code":-1,"msg":"该用户记录不存在！"}');
    $session=md5($userrow['user'].$userrow['pwd'].$password_hash);
    $expiretime=TIMESTAMP+604800;
    $token=authcode("{$uid}\t{$session}\t{$expiretime}", 'ENCODE', SYS_KEY);
    setcookie("user_auth_token", $token, TIMESTAMP + 604800, '/user');
    exit('{"code":0,"msg":"登录用户成功！"}');
break;
case 'count':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $count=$DB->count("SELECT count(*) FROM yixi_tixian WHERE status=0");
    $count2=$DB->count("SELECT count(*) FROM yixi_workorder WHERE status=0");
    exit('{"code":0,"count":'.$count.',"count2":'.$count2.'}');
break;
case 'qdcount':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $day=date("Y-m-d");
    $lastday = date("Y-m-d",strtotime("-1 day"));
    $count1=$DB->count("SELECT count(*) FROM yixi_qiandao WHERE date='$day'");
    $count2=$DB->count("SELECT count(*) FROM yixi_qiandao WHERE date='$lastday'");
    $count3=$DB->count("SELECT count(*) FROM yixi_qiandao");
    $count4=$DB->count("SELECT sum(reward) FROM yixi_qiandao WHERE date='$day'");
    $count5=$DB->count("SELECT sum(reward) FROM yixi_qiandao WHERE date='$lastday'");
    $count6=$DB->count("SELECT sum(reward) FROM yixi_qiandao");
    $result=array("count1"=>$count1,"count2"=>$count2,"count3"=>$count3,"count4"=>round($count4,2),"count5"=>round($count5,2),"count6"=>round($count6,2));
    exit(json_encode($result));
break;
case 'checkdwz':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $url = $_POST['url'];
    $data = get_curl($url);
    if(json_decode($data,true)){
        exit('{"code":1}');
    }elseif($data){
        exit('{"code":2}');
    }else{
        exit('{"code":0}');
    }
break;
case "send"://聊天室
    if($conf['chat_open']!=1)exit('{"code":-1,"msg":"聊天室正在维护更新，敬请期待！"}');
    if($islogin!=1)exit('{"code":-1,"msg":"请登录后在发言！"}');
    $con=addslashes($_POST['con']);
    $timerow=$DB->get_row("select * from yixi_chat where 1 order by id desc limit 1");
    $sql = "INSERT INTO `yixi_chat`(`uid`, `user`, `qq`, `content`, `date`, `time`, `time2`, `ip`) VALUES ('1','".$conf['admin_user']."','".$conf['admin_qq']."','".$con."','".$date."','".TIMESTAMP."','".$timerow['time']."','".$clientip."')";
    if ($DB->query($sql)) {
        exit('{"code":0,"msg":"发言成功！"}');
    } else {
        exit('{"code":-1,"msg":"发言失败！' . $DB->error().'"}');
    }
break;
case 'uploadchatimg':
    if($conf['chat_open']!=1)exit('{"code":-1,"msg":"聊天室正在维护更新，敬请期待！"}');
    if($islogin!=1)exit('{"code":-1,"msg":"请登录后在发言！"}');
    if($_POST['do']=='upload'){
        $filename = $_FILES['file']['name'];
        $ext = substr($filename, strripos($filename, '.') + 1);
        $arr = array('png', 'jpg', 'gif', 'jpeg', 'webp', 'bmp');
        if (!in_array($ext , $arr)) {
            exit('{"code":-1,"msg":"只支持上传图片文件"}');
        }
        $filename = md5_file($_FILES['file']['tmp_name']).'.png';
        $fileurl = '../assets/img/chat/'.$filename;
        $con = '[img]'.$fileurl.'[/img]';
        $timerow=$DB->get_row("select * from yixi_chat where 1 order by id desc limit 1");
        $sql = "INSERT INTO `yixi_chat`(`uid`, `user`, `qq`, `content`, `date`, `time`, `time2`, `ip`) VALUES ('1','".$conf['admin_user']."','".$conf['admin_qq']."','".$con."','".$date."','".TIMESTAMP."','".$timerow['time']."','".$clientip."')";
        if(copy($_FILES['file']['tmp_name'], ROOT.'assets/img/chat/'.$filename) && $DB->query($sql)){
            exit('{"code":0,"msg":"succ"}');
        }else{
            exit('{"code":-1,"msg":"发图失败，请确保有本地写入权限' . $DB->error().'"}');
        }
    }
    exit('{"code":-1,"msg":"null"}');
break;
case 'add_face':
    if($conf['chat_open']!=1)exit('{"code":-1,"msg":"聊天室正在维护更新，敬请期待！"}');
    if($islogin!=1)exit('{"code":-1,"msg":"请登录后在发言！"}');
    $face=addslashes($_POST['face']);
    $faces=$DB->get_row("select * from yixi_face where face='".$face."' limit 1");
    if ($faces) {
        exit('{"code":-1,"msg":"该表情已存在！"}');
    }
    $sql = "INSERT INTO `yixi_face`(`uid`, `face`, `date`, `ip`) VALUES ('1','".$face."','".$date."','".$clientip."')";
    if ($DB->query($sql)) {
        exit('{"code":0,"msg":"添加表情成功！"}');
    } else {
        exit('{"code":-1,"msg":"添加表情失败！' . $DB->error().'"}');
    }
break;
case 'send_face':
    if($conf['chat_open']!=1)exit('{"code":-1,"msg":"聊天室正在维护更新，敬请期待！"}');
    if($islogin!=1)exit('{"code":-1,"msg":"请登录后在发言！"}');
    $face=addslashes($_POST['face']);
    $con = '[img]'.$face.'[/img]';
    $timerow=$DB->get_row("select * from yixi_chat where 1 order by id desc limit 1");
    $sql = "INSERT INTO `yixi_chat`(`uid`, `user`, `qq`, `content`, `date`, `time`, `time2`, `ip`) VALUES ('1','".$conf['admin_user']."','".$conf['admin_qq']."','".$con."','".$date."','".TIMESTAMP."','".$timerow['time']."','".$clientip."')";
    if ($DB->query($sql)) {
        exit('{"code":0,"msg":"发送表情成功！"}');
    } else {
        exit('{"code":-1,"msg":"发送表情失败！' . $DB->error().'"}');
    }
break;
case 'face_list':
    if($conf['chat_open']!=1)exit('{"code":-1,"msg":"聊天室正在维护更新，敬请期待！"}');
    if($islogin!=1)exit('{"code":-1,"msg":"请登录后在发言！"}');
    $count = $DB->count("select count(*) from yixi_face where uid=1");
    if($count==0)exit('{"code":-1,"msg":"您还没有添加表情包呢"}');
    $rs=$DB->query("SELECT * FROM yixi_face WHERE uid=1 order by id desc");
    while($res = $DB->fetch($rs))
    {
        $data.='<img src="'.$res['face'].'" style="height: 120px;width: 120px" class="img-rounded img-circle img-thumbnail" onclick="send_face(\''.$res['face'].'\')">';
    }
    $result=array("code"=>0,"msg"=>"succ","face_list"=>$data);
    exit(json_encode($result));
break;
case 'chat_site':
    if($islogin!=1)exit('{"code":-1,"msg":"请登录后在发言！"}');
    $uid=intval($_GET['uid']);
    if($uid==1){
        if($conf['admin_qq']){
            $qq=$conf['admin_qq'];
        }else{
            $qq=$conf['kfqq'];
        }
        $user=$conf['admin_user'];
        $power='<li class="list-group-item">权限：<font color="orange">平台站长</font></li>';
    }else{
        $row=$DB->get_row("select * from yixi_user where uid='".$uid."' limit 1");
        if(!$row){
            exit('{"code":-1,"msg":"当前用户不存在！"}');
        }
        $qq=$row['qq'];
        $user=$row['user'];
        if($row['power']==4){
            $power='<li class="list-group-item">权限：<font color="yellow">星耀会员</font></li>';
		}elseif($row['power']==3){
            $power='<li class="list-group-item">权限：<font color="green">钻石会员</font></li>';
        }elseif($row['power']==2){
            $power='<li class="list-group-item">权限：<font color="purple">黄金会员</font></li>';
        }elseif($row['power']==1){
            $power='<li class="list-group-item">权限：<font color="blue">白银会员</font></li>';
        }else{
            $power='<li class="list-group-item">权限：<font color="red">普通用户</font></li>';
        }
    }
    $data = '<center><li class="list-group-item"><img src="//q4.qlogo.cn/headimg_dl?dst_uin='.$qq.'&spec=100" alt="Avatar" width="60" height="60" style="border:1px solid #FFF;-moz-box-shadow:0 0 3px #AAA;-webkit-box-shadow:0 0 3px #AAA;border-radius: 50%;box-shadow:0 0 3px #AAA;padding:3px;margin-right: 3px;margin-left: 6px;"><br/>昵称：'.qqname($qq).'</li></center><li class="list-group-item">代理ID：'.$uid.'</li><li class="list-group-item">用户名：'.$user.'</li><li class="list-group-item">QQ：'.$qq.'</li>'.$power;
    $result=array("code"=>0,"msg"=>"succ","data"=>$data,"user"=>$user);
    exit(json_encode($result));
break;
case 'addjkk':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $money = daddslashes($_POST['money']);
    $num = daddslashes($_POST['num']);
    if ($num>100) {
        exit('{"code":-1,"msg":"一次性最多只能生成100张卡密！"}');
    }
    if ($money=="" || $money<0) {
        exit('{"code":-1,"msg":"金额输入不规范！"}');
    }
    if (!$num) {
        exit('{"code":-1,"msg":"请确保各项都不为空！"}');
    }
    for ($i = 0; $i < $num; $i++) {
        $km=random(15);
        $DB->query("insert into `yixi_jkklist` (`km`,`money`,`addtime`) values ('".$km."','".$money."','".$date."')");
    }
    exit('{"code":0,"msg":"生成加款卡成功！"}');
break;
case 'addxsk':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $proid = intval($_POST['proid']);
    $value = daddslashes($_POST['value']);
    $num = daddslashes($_POST['num']);
    if(!$proid){
        exit('{"code":-1,"msg":"请选择您要生成的程序！"}');
    }
    $program = $DB->get_row("select * from yixi_program where id='" . $proid . "' limit 1");
    if (!$program) {
        exit('{"code":-1,"msg":"该程序不存在！"}');
    }
    if ($num>100) {
        exit('{"code":-1,"msg":"一次性最多只能生成100张卡密！"}');
    }
    if ($value=="" || $value<0) {
        exit('{"code":-1,"msg":"月数 输入不规范！"}');
    }
    if (!$num) {
        exit('{"code":-1,"msg":"请确保各项都不为空！"}');
    }
    for ($i = 0; $i < $num; $i++) {
        $km=random(15);
        $DB->query("insert into `yixi_xsklist` (`proid`,`km`,`value`,`addtime`) values ('".$proid."','".$km."','".$value."','".$date."')");
    }
    exit('{"code":0,"msg":"生成'.$program['name'].'接口续时卡成功！"}');
break;
case 'adddhk':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $type = intval($_POST['type']);
    $power = intval($_POST['power']);
    $proid = intval($_POST['proid']);
    $num = daddslashes($_POST['num']);
    if($type<3){
        if(!$proid){
            exit('{"code":-1,"msg":"请选择您要生成的程序！"}');
        }
        $program = $DB->get_row("select * from yixi_program where id='" . $proid . "' limit 1");
        if (!$program) {
            exit('{"code":-1,"msg":"该程序不存在！"}');
        }
    }
    if($type==3 && $power<3){
        if(!$proid){
            exit('{"code":-1,"msg":"请选择您要生成的程序！"}');
        }
        $program = $DB->get_row("select * from yixi_program where id='" . $proid . "' limit 1");
        if (!$program) {
            exit('{"code":-1,"msg":"该程序不存在！"}');
        }
    }
    if($type==3 && $power==3){
        $proid='';
    }
    if ($num>100) {
        exit('{"code":-1,"msg":"一次性最多只能生成100张卡密！"}');
    }
    if (!$num) {
        exit('{"code":-1,"msg":"请确保各项都不为空！"}');
    }
    for ($i = 0; $i < $num; $i++) {
        $km=random(15);
        $DB->query("insert into `yixi_dhklist` (`type`,`power`,`proid`,`km`,`addtime`) values ('".$type."','".$power."','".$proid."','".$km."','".$date."')");
    }
    exit('{"code":0,"msg":"生成兑换卡成功！"}');
break;
case 'addjk':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $appid=intval($_POST['appid']);
	$proid=intval($_POST['proid']);
    $endtime=daddslashes($_POST['endtime']);
    if(!$proid){
        exit('{"code":-1,"msg":"请选择您要添加的接口！"}');
    }
    $program = $DB->get_row("select * from yixi_program where id='" . $proid . "' limit 1");
    if (!$program) {
        exit('{"code":-1,"msg":"该接口不存在！"}');
    }
	$app = $DB->get_row("select * from yixi_apps where id='" . $appid . "' limit 1");
    if (!$app) {
        exit('{"code":-1,"msg":"该应用不存在！"}');
    }
    if(!$endtime){
        exit('{"code":-1,"msg":"请确保到期时间不为空！"}');
    }
        $sql="insert into `yixi_userjk` (`uid`,`proid`,`appid`,`date`,`active`,`ip`,`endtime`) values ('1','".$proid."','".$appid."','".$date."','y','".$clientip."','".$endtime."')";
        $DB->query($sql);
    exit('{"code":0,"msg":"添加接口成功！"}');
break;
case 'addapp':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $name = daddslashes($_POST['name']);
    $img = daddslashes($_POST['img']);
	$appgg = daddslashes($_POST['appgg']);
    $switchauth = daddslashes($_POST['switchauth']);
    $ipauth = daddslashes($_POST['ipauth']);
    $version = daddslashes($_POST['version']);
    $version_info = daddslashes($_POST['version_info']);
    $active = daddslashes($_POST['active']);
    $sqprice = daddslashes($_POST['sqprice']);
    $sqprice2 = daddslashes($_POST['sqprice2']);
    $sqprice3 = daddslashes($_POST['sqprice3']);
    $sqsprice = daddslashes($_POST['sqsprice']);
    $sqsprice2 = daddslashes($_POST['sqsprice2']);
    $cgprice = daddslashes($_POST['cgprice']);
    if ($name == "" || $img == "") {
        exit('{"code":-1,"msg":"请确保应用名称及应用图标不为空"}');
    }
       if ($sqprice == "" || $sqprice2 == "" || $sqprice3 == "" || $sqsprice == "" || $sqsprice2 == "" || $cgprice == "") {
        exit('{"code":-1,"msg":"请确保价格配置各项都不为空"}');
    }

    $row = $DB->get_row("select * from yixi_apps where name='" . $name . "' AND uid='1' limit 1");
    if ($row) {
        exit('{"code":-1,"msg":"该应用名称已存在！"}');
    }
    $sql = "insert into `yixi_apps` (`uid`,`appkey`,`name`,`img`,`app_gg`,`switch`,`ipauth`,`version`,`version_info`,`active`,`sqprice`,`sqprice2`,`sqprice3`,`sqsprice`,`sqsprice2`,`cgprice`,`total`,`date`) values ('1','".random(16)."','" . $name . "','" . $img . "','" . $appgg . "','" . $switchauth . "','" . $ipauth . "','" . $version . "','" . $version_info . "','" . $active . "','" . $sqprice . "','" . $sqprice2 . "','" . $sqprice3 . "','" . $sqsprice . "','" . $sqsprice2 . "','" . $cgprice . "','0','" . $date . "')";
    if ($DB->query($sql)) {
        exit('{"code":0,"msg":"添加成功！"}');
    } else {
        exit('{"code":-1,"msg":"添加失败！' . $DB->error().'"}');
    }
break;
case 'addappuser':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $appid=intval($_POST['appid']);
	$name=daddslashes($_POST['name']);
    $user=daddslashes($_POST['user']);
    $pwd=daddslashes($_POST['pwd']);
    $qq=daddslashes($_POST['qq']);
        $program = $DB->get_row("select * from yixi_apps where id='" . $appid . "' limit 1");
        if (!$program) {
            exit('{"code":-1,"msg":"该应用不存在！"}');
        }
    if (!$user or !$pwd or !$qq) {
        exit('{"code":-1,"msg":"请确保各项都不为空！"}');
    } else {
        $rows = $DB->get_row("select * from yixi_appuser where user='" . $user . "' limit 1");
        if ($rows) {
            exit('{"code":-1,"msg":"该帐号已存在！"}');
        }
        $qqrow = $DB->get_row("select * from yixi_appuser where qq='" . $qq . "' limit 1");
        if ($qqrow) {
            exit('{"code":-1,"msg":"该QQ账号已存在！"}');
        }
        $sql = "insert into `yixi_appuser` (`upid`,`appid`,name,`user`,`pwd`,`qq`,`reg_time`,`status`) values ('1','" . $appid . "','" . $name . "','" . $user . "','" . $pwd . "','" . $qq . "','" . $date . "','y')";
        if ($DB->query($sql)) {
            exit('{"code":0,"msg":"添加'.$power_name.'成功！"}');
        } else {
            exit('{"code":-1,"msg":"添加'.$power_name.'失败！' . $DB->error().'"}');
        }
    }
break;
case 'addappkm':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $type = daddslashes($_POST['type']);
    $add_amount = intval($_POST['add_amount']);
    $proid = intval($_POST['proid']);
	$km_time = daddslashes($_POST['km_time']);
	$km_zdy = daddslashes($_POST['km_zdy']);
	$km_length = intval($_POST['km_length']);
    $km_num = intval($_POST['km_num']);
	if(($type=='code')&&(!$km_time)){
            exit('{"code":-1,"msg":"请选择单码卡密对应时长！"}');
        }
	if(!$add_amount){
            exit('{"code":-1,"msg":"请输入卡密对应的值！"}');
        }
	if(!$proid){
            exit('{"code":-1,"msg":"请选择您要生成的应用！"}');
        }
	if($km_length>32){
            exit('{"code":-1,"msg":"卡密长度不能大于32位！"}');
        }
	if($km_num>100){
            exit('{"code":-1,"msg":"卡密数量不能大于100张！"}');
        }
     $program = $DB->get_row("select * from yixi_apps where id='" . $proid . "' limit 1");
        if (!$program) {
            exit('{"code":-1,"msg":"该应用不存在！"}');
        }
    for ($i = 0; $i < $km_num; $i++) {
		$km=$km_zdy.random($km_length);
        $DB->query("insert into `yixi_appkm` (`upid`,`type`,`appid`,`kami`,`amount`,`km_time`,`addtime`,`state`) values ('1','".$type."','".$proid."','".$km."','".$add_amount."','".$km_time."','".$date."','y')");
    }
    exit('{"code":0,"msg":"生成卡密成功！"}');
break;
case 'addappfile':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $type = daddslashes($_POST['type']);
    $file_url = daddslashes($_POST['file_url']);
    $proid = intval($_POST['proid']);
	$lanzou_pass = daddslashes($_POST['lanzou_pass']);
	$preg = "/^http(s)?:\\/\\/.+/";
	if(!$type){
            exit('{"code":-1,"msg":"请选择对应云端！"}');
        }
	if(!$file_url){
            exit('{"code":-1,"msg":"请输入外链地址！"}');
        }
	if(!preg_match($preg,$file_url)){
            exit('{"code":-1,"msg":"请正确填写http/https"}');
	    }
	if(!$proid){
            exit('{"code":-1,"msg":"请选择您对应的应用！"}');
        }
     $program = $DB->get_row("select * from yixi_apps where id='" . $proid . "' limit 1");
        if (!$program) {
            exit('{"code":-1,"msg":"该应用不存在！"}');
        }
	$check_file = $DB->get_row("select * from yixi_appfile where file_url='" . $file_url . "' AND uid='".$userrow['uid']."' limit 1");
        if ($check_file) {
            exit('{"code":-1,"msg":"该外链已存在！"}');
        }
     $sql = "insert into `yixi_appfile` (`uid`,`type`,`appid`,`file_url`,`lanzou_pass`,`addtime`,`state`) values ('1','" . $type . "','" . $proid . "','" . $file_url . "','" . $lanzou_pass . "','".$date."','y')";
     if ($DB->query($sql)) {
        exit('{"code":0,"msg":"添加文件成功"}');
     } else {
        exit('{"code":-1,"msg":"添加文件失败！' . $DB->error().'"}');
     }
break;
case 'editAppPromoney':
	if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_POST['id']);
    $sqprice = daddslashes($_POST['sqprice']);
    $sqprice2 = daddslashes($_POST['sqprice2']);
    $sqprice3 = daddslashes($_POST['sqprice3']);
    $sqsprice = daddslashes($_POST['sqsprice']);
    $sqsprice2 = daddslashes($_POST['sqsprice2']);
    $cgprice = daddslashes($_POST['cgprice']);
    $row=$DB->get_row("select * from yixi_apps where id='$id' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"当前程序不存在！"}');
    }
    if ($sqprice == "" || $sqprice2 == "" || $sqprice3 == "" || $sqsprice == "" || $sqsprice2 == "" || $cgprice == "") {
        exit('{"code":-1,"msg":"请确保价格配置各项都不为空"}');
    }
    $sql="update `yixi_apps` set `sqprice` ='{$sqprice}',`sqprice2` ='{$sqprice2}',`sqprice3` ='{$sqprice3}',`sqsprice` ='{$sqsprice}',`sqsprice2` ='{$sqsprice2}',`cgprice` ='{$cgprice}' where `id`='{$id}'";
    if($DB->query($sql)){
        exit('{"code":0,"msg":"保存程序价格成功"}');
    } else {
        exit('{"code":-1,"msg":"保存程序价格失败！' . $DB->error().'"}');
    }
break;
case 'editAppNote':
	if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_POST['id']);
    $appnote = daddslashes($_POST['appnote']);
    $row=$DB->get_row("select * from yixi_apps where id='$id' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"当前程序不存在！"}');
    }
    $sql="update `yixi_apps` set `note` ='{$appnote}' where `id`='{$id}'";
    if($DB->query($sql)){
        exit('{"code":0,"msg":"保存成功"}');
    } else {
        exit('{"code":-1,"msg":"保存失败！' . $DB->error().'"}');
    }
break;
case 'editjkNote':
	if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_POST['id']);
    $jknote = daddslashes($_POST['jknote']);
    $row=$DB->get_row("select * from yixi_userjk where id='$id' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"当前接口不存在！"}');
    }
    $sql="update `yixi_userjk` set `note` ='{$jknote}' where `id`='{$id}'";
    if($DB->query($sql)){
        exit('{"code":0,"msg":"保存成功"}');
    } else {
        exit('{"code":-1,"msg":"保存失败！' . $DB->error().'"}');
    }
break;
case 'editAppVersion':
	if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_POST['id']);
    $version = daddslashes($_POST['version']);
    $row=$DB->get_row("select * from yixi_apps where id='$id' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"当前程序不存在！"}');
    }
    $sql="update `yixi_apps` set `version` ='{$version}' where `id`='{$id}'";
    if($DB->query($sql)){
        exit('{"code":0,"msg":"保存成功"}');
    } else {
        exit('{"code":-1,"msg":"保存失败！' . $DB->error().'"}');
    }
break;
case 'editApp_other':
	if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $mi_state = daddslashes($_POST['mi_state']);
    $mi_type = daddslashes($_POST['mi_type']);
    $mi_sign = daddslashes($_POST['mi_sign']);
    $mi_sign_in = daddslashes($_POST['mi_sign_in']);
    $print_sign = daddslashes($_POST['print_sign']);
	$rc4_key = daddslashes($_POST['rc4_key']);
    $mi_time = intval($_POST['mi_time']);
	$km_change = intval($_POST['km_change']);
    $km_change_num = intval($_POST['km_change_num']);
	$km_change_time = intval($_POST['km_change_time']);
	$longuse_km_change = intval($_POST['longuse_km_change']);
	$single_km_change_num = intval($_POST['single_km_change_num']);
    $row=$DB->get_row("select * from yixi_apps where id='$id' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"当前应用不存在！"}');
    }
    if ($mi_state == "") {
        exit('{"code":-1,"msg":"请确保应用安全配置选项不为空"}');
    }
    $sql="update `yixi_apps` set `mi_state` ='{$mi_state}',`mi_type` ='{$mi_type}',`mi_sign` ='{$mi_sign}',`mi_sign_in` ='{$mi_sign_in}',`print_sign` ='{$print_sign}',`rc4_key` ='{$rc4_key}',`mi_time` ='{$mi_time}',`km_change_num` ='{$km_change_num}',`km_change` ='{$km_change}',`km_change_time` ='{$km_change_time}',`longuse_km_change` ='{$longuse_km_change}',`single_km_change_num` ='{$single_km_change_num}' where `id`='{$id}'";
    if($DB->query($sql)){
        exit('{"code":0,"msg":"保存成功"}');
    } else {
        exit('{"code":-1,"msg":"保存失败！' . $DB->error().'"}');
    }
break;
case 'editApp_update':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $appgg = daddslashes($_POST['appgg']);
    $version = daddslashes($_POST['version']);
    $version_info = daddslashes($_POST['version_info']);
    $app_update_url = daddslashes($_POST['app_update_url']);
    $app_update_show = daddslashes($_POST['app_update_show']);
    $app_update_must = daddslashes($_POST['app_update_must']);
    $type = daddslashes($_POST['type']);
	$lanzou_pass = daddslashes($_POST['lanzou_pass']);
    $row=$DB->get_row("select * from yixi_apps where id='$id' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"当前应用不存在！"}');
    }
    if ($version == "" || $app_update_must == "") {
        exit('{"code":-1,"msg":"请确保应用版本号及强制更新不为空"}');
    }
    $sql="update `yixi_apps` set `app_gg` ='{$appgg}',`version` ='{$version}',`version_info` ='{$version_info}',`app_update_url` ='{$app_update_url}',`app_update_show` ='{$app_update_show}',`app_update_url_type` ='{$type}',`lanzou_pass` ='{$lanzou_pass}',`app_update_must` ='{$app_update_must}' where `id`='{$id}'";
    if($DB->query($sql)){
        exit('{"code":0,"msg":"保存成功"}');
    } else {
        exit('{"code":-1,"msg":"保存失败！' . $DB->error().'"}');
    }
break;
case 'getAppPromoney': //查看应用价格
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $rows=$DB->get_row("select * from yixi_apps where id='$id' limit 1");
    if(!$rows)
        exit('{"code":-1,"msg":"当前程序好像不存在！"}');
    $data = '<div class="card-body">';
    $data .= '<form class="form-horizontal">';
    $data .= '<div class="form-group mb-3"><label for="example-input-normal" style="font-weight: 500">应用卡密销售价格：</label><input type="text" id="sqprice" value="'.$rows['sqprice'].'" placeholder="输入该应用的卡密销售价格" class="form-control" required></div>';
    $data .= '<div class="form-group mb-3"><label for="example-input-normal" style="font-weight: 500">应用代理商销售价格：</label><input type="text" id="sqsprice" value="'.$rows['sqsprice'].'" placeholder="输入该应用的代理商销售价格" class="form-control" required></div>';
    $data .= '<div class="form-group mb-3"><label for="example-input-normal" style="font-weight: 500">应用超管销售价格：</label><input type="text" id="cgprice" value="'.$rows['cgprice'].'" placeholder="输入该应用的超管销售价格" class="form-control" required></div>';
    $data .= '<div class="form-group mb-3"><label for="example-input-normal" style="font-weight: 500">应用代理商商添加卡密价格：</label><input type="text" id="sqprice2" value="'.$rows['sqprice2'].'" placeholder="输入该应用的代理商添加卡密的价格" class="form-control" required><small>价格应低于应用销售价格</small></div>';
    $data .= '<div class="form-group mb-3"><label for="example-input-normal" style="font-weight: 500">应用超管添加卡密价格：</label><input type="text" id="sqprice3" value="'.$rows['sqprice3'].'" placeholder="输入该应用的超管添加卡密的价格" class="form-control" required><small>价格应低于代理商添加卡密价格</small></div>';
    $data .= '<div class="form-group mb-3"><label for="example-input-normal" style="font-weight: 500">应用超管添加代理商价格：</label><input type="text" id="sqsprice2" value="'.$rows['sqsprice2'].'" placeholder="输入该应用的超管添加代理商的价格" class="form-control" required><small>价格应低于代理商销售价格</small></div>';
    $data .= '<input type="submit" id="save" onclick="saveInfo('.$id.')" class="btn btn-block btn-xs btn-outline-success" value="保存">';
    $data .= '</form>';
    $data .= '</div>';
    $result=array("code"=>0,"msg"=>"succ","data"=>$data);
    exit(json_encode($result));
break;
case 'appedit':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_apps WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该应用不存在！"}');
    }
    $name = daddslashes($_POST['name']);
    $img = daddslashes($_POST['img']);
    $appgg = daddslashes($_POST['appgg']);
    $switchauth = daddslashes($_POST['switchauth']);
    $ipauth = daddslashes($_POST['ipauth']);
    $version = daddslashes($_POST['version']);
    $version_info = daddslashes($_POST['version_info']);
    $active = daddslashes($_POST['active']);
    $sqprice = daddslashes($_POST['sqprice']);
    $sqprice2 = daddslashes($_POST['sqprice2']);
    $sqprice3 = daddslashes($_POST['sqprice3']);
    $sqsprice = daddslashes($_POST['sqsprice']);
    $sqsprice2 = daddslashes($_POST['sqsprice2']);
    $cgprice = daddslashes($_POST['cgprice']);
    if ($name == "" || $img == "") {
        exit('{"code":-1,"msg":"请确保应用名称及应用图标不为空"}');
    }
    if ($sqprice == "" || $sqprice2 == "" || $sqprice3 == "" || $sqsprice == "" || $sqsprice2 == "" || $cgprice == "") {
        exit('{"code":-1,"msg":"请确保价格配置各项都不为空"}');
    }
    if ($row['name'] != $name) {
        $rows = $DB->get_row("select * from yixi_apps where name='" . $name . "' AND uid='1' limit 1");
        if ($rows) {
            exit('{"code":-1,"msg":"该应用已存在！"}');
        }
    }
    $sql="update `yixi_apps` set `name` ='{$name}',`img` ='{$img}',`app_gg` ='{$appgg}',`switch` ='{$switchauth}',`ipauth` ='{$ipauth}',`version` ='{$version}',`version_info` ='{$version_info}',`active` ='{$active}',`sqprice` ='{$sqprice}',`sqprice2` ='{$sqprice2}',`sqprice3` ='{$sqprice3}',`sqsprice` ='{$sqsprice}',`sqsprice2` ='{$sqsprice2}',`cgprice` ='{$cgprice}' where `id`='{$id}'";
    if ($DB->query($sql)) {
        exit('{"code":0,"msg":"保存成功！"}');
    } else {
        exit('{"code":-1,"msg":"保存失败！' . $DB->error().'"}');
    }
break;
case 'getAppNote': //查看应用备注
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $rows=$DB->get_row("select * from yixi_apps where id='$id' limit 1");
    if(!$rows)
        exit('{"code":-1,"msg":"当前程序好像不存在！"}');
    $data = '<div class="card-body">';
    $data .= '<form class="form-horizontal">';
    $data .= '<div class="form-group mb-3"><label for="example-input-normal" style="font-weight: 500">应用备注：</label><input type="text" id="appnote" value="'.$rows['note'].'" placeholder="输入该应用备注" class="form-control"></div>';
    $data .= '<input type="submit" id="save" onclick="saveappnote('.$id.')" class="btn btn-block btn-xs btn-outline-success" value="保存">';
    $data .= '</form>';
    $data .= '</div>';
    $result=array("code"=>0,"msg"=>"succ","data"=>$data);
    exit(json_encode($result));
break;
case 'getjkNote': //查看接口备注
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $rows=$DB->get_row("select * from yixi_userjk where id='$id' limit 1");
    if(!$rows)
        exit('{"code":-1,"msg":"当前接口好像不存在！"}');
    $data = '<div class="card-body">';
    $data .= '<form class="form-horizontal">';
    $data .= '<div class="form-group mb-3"><label for="example-input-normal" style="font-weight: 500">接口备注：</label><input type="text" id="jknote" value="'.$rows['note'].'" placeholder="输入该接口备注" class="form-control"></div>';
    $data .= '<input type="submit" id="save" onclick="savejknote('.$id.')" class="btn btn-block btn-xs btn-outline-success" value="保存">';
    $data .= '</form>';
    $data .= '</div>';
    $result=array("code"=>0,"msg"=>"succ","data"=>$data);
    exit(json_encode($result));
break;
case 'getAppVersion': //查看应用版本
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $rows=$DB->get_row("select * from yixi_apps where id='$id' limit 1");
    if(!$rows)
        exit('{"code":-1,"msg":"当前程序好像不存在！"}');
    $data = '<div class="card-body">';
    $data .= '<form class="form-horizontal">';
    $data .= '<div class="form-group mb-3"><label for="example-input-normal" style="font-weight: 500">应用版本：</label><input type="text" id="version" value="'.$rows['version'].'" placeholder="输入该应用版本" class="form-control"></div>';
    $data .= '<input type="submit" id="save" onclick="saveappversion('.$id.')" class="btn btn-block btn-xs btn-outline-success" value="保存">';
    $data .= '</form>';
    $data .= '</div>';
    $result=array("code"=>0,"msg"=>"succ","data"=>$data);
    exit(json_encode($result));
break;
case 'appkeyChange':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_apps WHERE id='{$id}' limit 1"); 
    if(!$row){
        exit('{"code":-1,"msg":"该应用不存在！"}');
        }
    $appkey=random(16);
    $sql="update `yixi_apps` set `APPKEY` ='{$appkey}' where `id`='{$id}'";
    if($DB->query($sql)){
        exit('{"code":0,"msg":"更换APPKEY成功！"}');
    } else {
        exit('{"code":-1,"msg":"更换失败！' . $DB->error().'"}');
    }
break;
case 'getmsg':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $proid=intval($_POST['proid']);
    if(!$proid){
        exit('{"code":-1,"msg":"请选择您要查询的接口！"}');
    }
    $program = $DB->get_row("select * from yixi_program where id='" . $proid . "' limit 1");
    if (!$program) {
        exit('{"code":-1,"msg":"该接口不存在！"}');
    }
    exit('{"code":0,"msg":"'.$program['desc'].'"}');
    
break;
case 'addpay':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $proid=intval($_POST['proid']);
    $name=daddslashes($_POST['name']);
    $qq=daddslashes($_POST['qq']);
    $url=daddslashes($_POST['url']);
    if(!$proid){
        exit('{"code":-1,"msg":"请选择您要认证的程序！"}');
    }
    $program = $DB->get_row("select * from yixi_program where id='" . $proid . "' limit 1");
    if (!$program) {
        exit('{"code":-1,"msg":"该程序不存在！"}');
    }
    if(!$name or !$qq or !$url){
        exit('{"code":-1,"msg":"网站名字.QQ.域名不能为空！"}');
    }
    $row=$DB->get_row("SELECT * FROM yixi_paysite WHERE proid='$proid' and url='$url' limit 1");
    if($program && $row){
        exit('{"code":-1,"msg":"该程序中已存在该易支付认证域名！"}');
    }
    if($DB->query("insert into `yixi_paysite` (`uid`,`proid`,`name`,`qq`,`url`,`date`,`active`) values ('1','".$proid."','".$name."','".$qq."','".$url."','".$date."','1')")){
        exit('{"code":0,"msg":"添加易支付认证成功！"}');
    }else{
        exit('{"code":-1,"msg":"添加易支付认证失败！'.$DB->error().'"}');
    }
break;
case 'addver':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $title=daddslashes($_POST['title']);
    $url=daddslashes($_POST['url']);
    $colour=daddslashes($_POST['colour']);
    $daili=intval($_POST['daili']);
    $icon=daddslashes($_POST['icon']);
    $last=daddslashes($_POST['last']);
    if(!$title or !$url){
        exit('{"code":-1,"msg":"广告标题.跳转地址.不能为空！"}');
    }
    $row=$DB->get_row("SELECT * FROM yixi_adver WHERE title='$title' and url='$url' limit 1");
    if($row){
        exit('{"code":-1,"msg":"该用户广告不存在！"}');
    }
    if($DB->query("insert into `yixi_adver` (`icon`, `title`,`url`,`colour`,`date`,`daili`,`last`,`active`,`see`) values ('".$icon."','".$title."','".$url."','".$colour."','".$date."','".$daili."','".$last."','1','1')")){
        exit('{"code":0,"msg":"添加用户广告成功！"}');
    }else{
        exit('{"code":-1,"msg":"添加用户广告失败！'.$DB->error().'"}');
    }
break;
case 'addblack':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $level=intval($_POST['level']);
    $qq=daddslashes($_POST['qq']);
    $note=daddslashes($_POST['note']);
    if(!$level){
        exit('{"code":-1,"msg":"请选择等级！"}');
    }
    if(!$qq or !$note){
        exit('{"code":-1,"msg":"QQ.原因不能为空！"}');
    }
    $row=$DB->get_row("SELECT * FROM yixi_blacklist WHERE qq='$qq' limit 1");
    if($row){
        exit('{"code":-1,"msg":"平台已拉黑该QQ！"}');
    }
    if($DB->query("insert into `yixi_blacklist` (`level`,`qq`,`note`,`date`) values ('".$level."','".$qq."','".$note."','".$date."')")){
        exit('{"code":0,"msg":"添加黑名单成功！"}');
    }else{
        exit('{"code":-1,"msg":"添加黑名单失败！'.$DB->error().'"}');
    }
break;
case 'adduser':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $power=intval($_POST['power']);
    $user=daddslashes($_POST['user']);
    $pwd=daddslashes($_POST['pwd']);
    $rmb=daddslashes($_POST['rmb']);
    $kami=intval($_POST['kami']);
    $qq=daddslashes($_POST['qq']);
    if ($power == 4) {
        $power_name = '星耀会员';
	} else if ($power == 3) {
        $power_name = '钻石会员';
    } else if ($power == 2) {
        $power_name = '黄金会员';
    } else if ($power == 1) {
        $power_name = '白银会员';
    } else {
        $power_name = '普通用户';
    }
    if (!$user or !$pwd or !$qq) {
        exit('{"code":-1,"msg":"请确保各项都不为空！"}');
    } else if ($rmb=="" || $rmb<0) {
        exit('{"code":-1,"msg":"余额设置不规范！"}');
    } else {
        $rows = $DB->get_row("select * from yixi_user where user='" . $user . "' limit 1");
        if ($rows) {
            exit('{"code":-1,"msg":"该用户名已存在！"}');
        }
        $qqrow = $DB->get_row("select * from yixi_user where qq='" . $qq . "' limit 1");
        if ($qqrow) {
            exit('{"code":-1,"msg":"该QQ账号已存在！"}');
        }
        $sql = "insert into `yixi_user` (`upuid`,`power`,`kami`,`user`,`pwd`,`rmb`,`qq`,`invitecode`,`addtime`,`status`) values ('1','" . $power . "','" . $kami . "','" . $user . "','" . $pwd . "','" . $rmb . "','" . $qq . "','" . random(8) . "','" . $date . "','1')";
        if ($DB->query($sql)) {
            exit('{"code":0,"msg":"添加'.$power_name.'成功！"}');
        } else {
            exit('{"code":-1,"msg":"添加'.$power_name.'失败！' . $DB->error().'"}');
        }
    }
break;
case 'addpro':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $name = daddslashes($_POST['name']);
    $img = daddslashes($_POST['img']);
    $desc = daddslashes($_POST['desc']);
    $api_path = daddslashes($_POST['api_path']);
    $api_url = daddslashes($_POST['api_url']);
    $api_return = daddslashes($_POST['api_return']);
    $switchauth = daddslashes($_POST['switchauth']);
    $visible = daddslashes($_POST['visible']);
    $api_request = daddslashes($_POST['api_request']);
    $version = daddslashes($_POST['version']);
    $version_info = daddslashes($_POST['version_info']);
    $active = daddslashes($_POST['active']);
    $ptprice = daddslashes($_POST['ptprice']);
    $byprice = daddslashes($_POST['byprice']);
    $hjprice = daddslashes($_POST['hjprice']);
    $zsprice = daddslashes($_POST['zsprice']);
    if ($name == "" || $img == "" || $desc == "" || $api_return == "" || $api_request == "") {
        exit('{"code":-1,"msg":"请确保各项都不为空"}');
    }
    if ($ptprice == "" || $byprice == "" || $hjprice == "" || $zsprice == "") {
        exit('{"code":-1,"msg":"请确保价格配置各项都不为空"}');
    }
    if ($version == "" || $version_info == "") {
        exit('{"code":-1,"msg":请确保接口版本信息不为空"}');
    }
    $row = $DB->get_row("select * from yixi_program where name='" . $name . "' limit 1");
    if ($row) {
        exit('{"code":-1,"msg":"该接口名称已存在！"}');
    }
    $sql = "insert into `yixi_program` (`name`,`img`,`desc`,`api_path`,`api_url`,`api_return`,`switch`,`visible`,`api_request`,`version`,`version_info`,`active`,`ptprice`,`byprice`,`hjprice`,`zsprice`,`date`) values ('" . $name . "','" . $img . "','" . $desc . "','" . $api_path . "','" . $api_url . "','" . $api_return . "','" . $switchauth . "','" . $visible . "','" . $api_request . "','" . $version . "','" . $version_info . "','" . $active . "','" . $ptprice . "','" . $byprice . "','" . $hjprice . "','" . $zsprice . "','" . $date . "')";
    if ($DB->query($sql)) {
        mkdir('../api/api/'.$api_path,0777,true);
        mkdir('../template/'.$conf['template'].'/doc/'.$api_path,0777,true);
        file_put_contents('../api/api/'.$api_path.'/index.php','');
        file_put_contents('../template/'.$conf['template'].'/doc/'.$api_path.'/demo.html','暂未添加示例');
        file_put_contents('../template/'.$conf['template'].'/doc/'.$api_path.'/error.html','<tr><td>123</td><td>456</td><td>789</td></tr>');
        file_put_contents('../template/'.$conf['template'].'/doc/'.$api_path.'/example.html','暂未添加示例代码');     
        file_put_contents('../template/'.$conf['template'].'/doc/'.$api_path.'/request.html','<tr><td>暂</td><td>未</td><td>添</td><td>加</td></tr>');
        file_put_contents('../template/'.$conf['template'].'/doc/'.$api_path.'/return.html','<tr><td>未</td><td>添</td><td>加</td></tr>');
        exit('{"code":0,"msg":"添加成功！"}');
    } else {
        exit('{"code":-1,"msg":"添加失败！' . $DB->error().'"}');
    }
break;
case 'addshop':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $type = intval($_POST['type']);
    $name = daddslashes($_POST['name']);
    $money = daddslashes($_POST['money']);
    $image = daddslashes($_POST['image']);
    $updatelog = daddslashes($_POST['updatelog']);
    $filedata = daddslashes($_POST['filedata']);
    $proid = daddslashes($_POST['proid']);
    $system_name = daddslashes($_POST['system_name']);
    $recommend = daddslashes($_POST['recommend']);
    if($money<0 || $money>1000){
        exit('{"code":-1,"msg":"最多不可超过1000和低于0！"}');
    }
    if(!is_numeric($money) || !preg_match('/^[0-9.]+$/', $money)) {
        exit('{"code":-1,"msg":"价格输入不规范！"}');
    }
    if (!$name or !$image or !$updatelog or !$filedata or !$recommend) {
        exit('{"code":-1,"msg":"请确保各项都不为空"}');
    }
    if(!$proid){
        exit('{"code":-1,"msg":"请选择商品的客户端！"}');
    }
    if ($proid == 'other') {
        if (!$system_name) {
            exit('{"code":-1,"msg":"请确保类型名称不为空"}');
        }
    }
    $row = $DB->get_row("select * from yixi_shop where name='" . $name . "' limit 1");
    if ($row) {
        exit('{"code":-1,"msg":"该商品已存在！"}');
    }
    $sql = "insert into `yixi_shop` (`uid`,`type`,`name`,`money`,`tcbl`,`image`,`updatelog`,`filedata`,`proid`,`system_name`,`recommend`,`version`,`active`,`rzdate`,`sjdate`) values ('1','" . $type . "','" . $name . "','" . $money . "','" . $conf['shop_tcbl'] . "','" . $image . "','" . $updatelog . "','" . $filedata . "','" . $proid . "','" . $system_name . "','" . $recommend . "','1.00','1','" . $date . "','" . $date . "')";
    if ($DB->query($sql)) {
        exit('{"code":0,"msg":"添加成功！"}');
    } else {
        exit('{"code":-1,"msg":"添加失败！' . $DB->error().'"}');
    }
break;
case 'addmsg':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $title=daddslashes($_POST['title']);
    $type=intval($_POST['type']);
    $content=daddslashes($_POST['content']);
    if($title==NULL or $content==NULL){
        exit('{"code":-1,"msg":"保存错误,请确保每项都不为空！"}');
    } else {
        $rows=$DB->get_row("select * from yixi_message where type='$type' and title='$title' limit 1");
        if($rows){
            exit('{"code":-1,"msg":"通知标题已存在！"}');
        }
        $sql="insert into `yixi_message` (`type`,`title`,`content`,`addtime`,`active`) values ('".$type."','".$title."','".$content."','".$date."','1')";
        if($DB->query($sql)){
            exit('{"code":0,"msg":"发布新通知成功！"}');
        } else {
            exit('{"code":-1,"msg":"发布新通知失败！' . $DB->error().'"}');
        }
    }
break;
case 'uploadproimg':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    if($_POST['do']=='upload'){
        $filename = $_FILES['file']['name'];
        $ext = substr($filename, strripos($filename, '.') + 1);
        $arr = array('png', 'jpg', 'gif', 'jpeg', 'webp', 'bmp');
        if (!in_array($ext , $arr)) {
            exit('{"code":-1,"msg":"只支持上传图片文件"}');
        }
		$image = check_img($_FILES['file']['tmp_name']);
        if($image !== 'true'){
	    exit('{"code":-1,"msg":"上传失败，图片可能含有非法数据"}');
        }else{
     	$type = $_POST['type'];
        $filename = $type.'_'.md5_file($_FILES['file']['tmp_name']).'.png';
        $fileurl = 'assets/img/Program/'.$filename;
        if(copy($_FILES['file']['tmp_name'], ROOT.'assets/img/Program/'.$filename)){
            exit('{"code":0,"msg":"succ","url":"'.$fileurl.'"}');
        }else{
            exit('{"code":-1,"msg":"上传失败，请确保有本地写入权限"}');
        }	
        }
    }
    exit('{"code":-1,"msg":"null"}');
break;
case 'uploadappimg':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    if($_POST['do']=='upload'){
        $filename = $_FILES['file']['name'];
        $ext = substr($filename, strripos($filename, '.') + 1);
        $arr = array('png', 'jpg', 'gif', 'jpeg', 'webp', 'bmp');
        if (!in_array($ext , $arr)) {
            exit('{"code":-1,"msg":"只支持上传图片文件"}');
        }
		$image = check_img($_FILES['file']['tmp_name']);
        if($image !== 'true'){
	    exit('{"code":-1,"msg":"上传失败，图片可能含有非法数据"}');
        }else{
     	$type = $_POST['type'];
        $filename = $type.'_'.md5_file($_FILES['file']['tmp_name']).'.png';
        $fileurl = 'assets/img/Program/'.$filename;
        if(copy($_FILES['file']['tmp_name'], ROOT.'assets/img/Program/'.$filename)){
            exit('{"code":0,"msg":"succ","url":"'.$fileurl.'"}');
        }else{
            exit('{"code":-1,"msg":"上传失败，请确保有本地写入权限"}');
        }	
        }
    }
    exit('{"code":-1,"msg":"null"}');
break;
case 'adveredit':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_adver WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该广告记录不存在！"}');
    }
    $title=daddslashes($_POST['title']);
    $url=daddslashes($_POST['url']);
    $colour=daddslashes($_POST['colour']);
    $daili=intval($_POST['daili']);
    $icon=daddslashes($_POST['icon']);
    $top=intval($_POST['top']);
    $active=intval($_POST['active']);
    $last=daddslashes($_POST['last']);
    if(!$title or !$url){
        exit('{"code":-1,"msg":"广告标题.跳转地址.不能为空！"}');
    }
    if ($row['title'] != $title && $row['url'] != $url) {
        $rows=$DB->get_row("SELECT * FROM yixi_adver WHERE title='$title' and url='$url' limit 1");
        if($rows){
            exit('{"code":-1,"msg":"该用户广告不存在！"}');
        }
    }
    if($DB->query("UPDATE yixi_adver set top='{$top}',icon='{$icon}',title='{$title}',url='{$url}',colour='{$colour}',daili='{$daili}',last='{$last}',active='{$active}' WHERE id='{$id}'")){
        exit('{"code":0,"msg":"保存用户广告成功！"}');
    }else{
        exit('{"code":-1,"msg":"保存用户广告失败！'.$DB->error().'"}');
    }
break;
case 'payedit':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_paysite WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该易支付认证记录不存在！"}');
    }
    $proid=intval($_POST['proid']);
    $name=daddslashes($_POST['name']);
    $qq=daddslashes($_POST['qq']);
    $url=daddslashes($_POST['url']);
    $active=intval($_POST['active']);
    if(!$proid){
        exit('{"code":-1,"msg":"请选择您要认证的程序！"}');
    }
    $program = $DB->get_row("select * from yixi_program where id='" . $proid . "' limit 1");
    if (!$program) {
        exit('{"code":-1,"msg":"该程序不存在！"}');
    }
    if(!$name or !$qq or !$url){
        exit('{"code":-1,"msg":"请确保各项都不为空！"}');
    }
    if ($row['proid'] != $proid && $row['url'] != $url) {
        $rows=$DB->get_row("SELECT * FROM yixi_paysite WHERE proid='$proid' and url='$url' limit 1");
        if($program && $rows){
            exit('{"code":-1,"msg":"该程序中已存在该易支付认证域名！"}');
        }
    }
    $sql="update `yixi_paysite` set `proid` ='{$proid}',`name` ='{$name}',`qq` ='{$qq}',`url` ='{$url}',`active` ='{$active}' where `id`='{$id}'";
    if($DB->query($sql)){
        exit('{"code":0,"msg":"保存成功！"}');
    } else {
        exit('{"code":-1,"msg":"保存失败！' . $DB->error().'"}');
    }
break;
case 'useredit':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $uid=intval($_GET['uid']);
    $row=$DB->get_row("SELECT * FROM yixi_user WHERE uid='{$uid}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该用户记录不存在！"}');
    }
    $power=intval($_POST['power']);
    $proid=intval($_POST['proid']);
    $user=daddslashes($_POST['user']);
    $rmb=daddslashes($_POST['rmb']);
    $kami=intval($_POST['kami']);
    $qq=daddslashes($_POST['qq']);
    if (!empty(daddslashes($_POST['pwd']))) {
        $sql = ",pwd='" . daddslashes($_POST['pwd']) . "'";
    }
    $status=daddslashes($_POST['status']);
    if (!$user or !$qq) {
        exit('{"code":-1,"msg":"请确保各项都不为空！"}');
    } else if ($rmb=="" || $rmb<0) {
        exit('{"code":-1,"msg":"余额设置不规范！"}');
    } else {
        if ($row['user'] != $user) {
            $rows = $DB->get_row("select * from yixi_user where user='" . $user . "' limit 1");
            if ($rows) {
                exit('{"code":-1,"msg":"该用户名已存在！"}');
            }
        }
        if ($row['qq'] != $qq) {
            $qqrow = $DB->get_row("select * from yixi_user where qq='" . $qq . "' limit 1");
            if ($qqrow) {
                exit('{"code":-1,"msg":"该QQ账号已存在！"}');
            }
        }
        $sql="update `yixi_user` set `user` ='{$user}',`power` ='{$power}',`kami` ='{$kami}',`rmb` ='{$rmb}',`qq` ='{$qq}'".$sql.",`status` ='{$status}' where `uid`='{$uid}'";
        if ($DB->query($sql)) {
            exit('{"code":0,"msg":"保存成功！"}');
        } else {
            exit('{"code":-1,"msg":"保存失败！' . $DB->error().'"}');
        }
    }
break;
case 'proedit':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_program WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该接口记录不存在！"}');
    }
    $name = daddslashes($_POST['name']);
    $img = daddslashes($_POST['img']);
    $desc = daddslashes($_POST['desc']);
    $api_path = daddslashes($_POST['api_path']);
    $api_url = daddslashes($_POST['api_url']);
    $api_return = daddslashes($_POST['api_return']);
    $switchauth = daddslashes($_POST['switchauth']);
    $visible = daddslashes($_POST['visible']);
    $api_request = daddslashes($_POST['api_request']);
    $version = daddslashes($_POST['version']);
    $version_info = daddslashes($_POST['version_info']);
    $active = daddslashes($_POST['active']);
    $ptprice = daddslashes($_POST['ptprice']);
    $byprice = daddslashes($_POST['byprice']);
    $hjprice = daddslashes($_POST['hjprice']);
    $zsprice = daddslashes($_POST['zsprice']);
    if ($name == "" || $img == "" || $desc == "" || $api_return == "" || $api_request == "") {
        exit('{"code":-1,"msg":"请确保各项都不为空"}');
    }
    if ($ptprice == "" || $byprice == "" || $hjprice == "" || $zsprice == "") {
        exit('{"code":-1,"msg":"请确保价格配置各项都不为空"}');
    }
    if ($version == "" || $version_info == "") {
        exit('{"code":-1,"msg":请确保接口版本信息不为空"}');
    }
    $sql="update `yixi_program` set `name` ='{$name}',`img` ='{$img}',`desc` ='{$desc}',`api_path` ='{$api_path}',`api_url` ='{$api_url}',`api_return` ='{$api_return}',`api_request` ='{$api_request}',`switch` ='{$switchauth}',`visible` ='{$visible}',`version` ='{$version}',`version_info` ='{$version_info}',`active` ='{$active}',`ptprice` ='{$ptprice}',`byprice` ='{$byprice}',`hjprice` ='{$hjprice}',`zsprice` ='{$zsprice}' where `id`='{$id}'";
    if ($DB->query($sql)) {
        exit('{"code":0,"msg":"保存成功！"}');
    } else {
        exit('{"code":-1,"msg":"保存失败！' . $DB->error().'"}');
    }
break;
case 'getPromoney': //查看程序价格
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $rows=$DB->get_row("select * from yixi_program where id='$id' limit 1");
    if(!$rows)
        exit('{"code":-1,"msg":"当前程序好像不存在！"}');
    $data = '<div class="card-body">';
    $data .= '<form class="form-horizontal">';
    $data .= '<div class="form-group mb-3"><label for="example-input-normal" style="font-weight: 500">普通用户销售价格：</label><input type="text" id="ptprice" value="'.$rows['ptprice'].'" placeholder="输入该程序的普通用户销售价格" class="form-control" required></div>';
    $data .= '<div class="form-group mb-3"><label for="example-input-normal" style="font-weight: 500">白银会员销售价格：</label><input type="text" id="byprice" value="'.$rows['byprice'].'" placeholder="输入该程序的白银会员销售价格" class="form-control" required><small>价格应低于普通会员销售价格</small></div>';
    $data .= '<div class="form-group mb-3"><label for="example-input-normal" style="font-weight: 500">黄金会员销售价格：</label><input type="text" id="hjprice" value="'.$rows['hjprice'].'" placeholder="输入该程序的黄金会员销售价格" class="form-control" required><small>价格应低于白银会员销售价格</small></div>';
    $data .= '<div class="form-group mb-3"><label for="example-input-normal" style="font-weight: 500">钻石会员销售价格：</label><input type="text" id="zsprice" value="'.$rows['zsprice'].'" placeholder="输入该程序的钻石会员销售价格" class="form-control" required><small>价格应低于黄金会员销售价格</small></div>';
    $data .= '<input type="submit" id="save" onclick="saveInfo('.$id.')" class="btn btn-block btn-xs btn-outline-success" value="保存">';
    $data .= '</form>';
    $data .= '</div>';
    $result=array("code"=>0,"msg"=>"succ","data"=>$data);
    exit(json_encode($result));
break;
case 'shopedit':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_shop WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该商品记录不存在！"}');
    }
    $type = intval($_POST['type']);
    $name = daddslashes($_POST['name']);
    $money = daddslashes($_POST['money']);
    $tcbl = daddslashes($_POST['tcbl']);
    $image = daddslashes($_POST['image']);
    $updatelog = daddslashes($_POST['updatelog']);
    $filedata = daddslashes($_POST['filedata']);
    $proid = daddslashes($_POST['proid']);
    $system_name = daddslashes($_POST['system_name']);
    $recommend = daddslashes($_POST['recommend']);
    $version = daddslashes($_POST['version']);
    $active = intval($_POST['active']);
    if ($row['active'] != 1 && $active == 1) {
        $sql = ",rzdate='" . $date . "'";
    } else {
        $sql = ",rzdate=''";
    }
    if (!$name or !$image or !$updatelog or !$filedata or !$recommend or !$version) {
        exit('{"code":-1,"msg":"请确保各项都不为空"}');
    }
    if($money<0 || $money>1000){
        exit('{"code":-1,"msg":"最多不可超过1000和低于0！"}');
    }
    if(!is_numeric($money) || !preg_match('/^[0-9.]+$/', $money)) {
        exit('{"code":-1,"msg":"价格输入不规范！"}');
    }
    if (!$tcbl || $tcbl>100 || $tcbl<0 || !is_numeric($tcbl) || !preg_match('/^[0-9.]+$/', $tcbl)) {
        exit('{"code":-1,"msg":"提成比例设置不规范！"}');
    }
    if ($proid == 'other') {
        if (!$system_name) {
            exit('{"code":-1,"msg":"请确保类型名称不为空"}');
        }
    }
    if ($row['name'] != $name) {
        $rows = $DB->get_row("select * from yixi_shop where name='" . $name . "' limit 1");
        if ($program && $rows) {
            exit('{"code":-1,"msg":"该商品已存在！"}');
        }
    }
    $sql="update `yixi_shop` set `type` ='{$type}',`name` ='{$name}',`money` ='{$money}',`tcbl` ='{$tcbl}',`image` ='{$image}',`updatelog` ='{$updatelog}',`filedata` ='{$filedata}',`proid` ='{$proid}',`system_name` ='{$system_name}',`recommend` ='{$recommend}',`version` ='{$version}',`active` ='{$active}'".$sql." where `id`='{$id}'";
    if ($DB->query($sql)) {
        exit('{"code":0,"msg":"保存成功！"}');
    } else {
        exit('{"code":-1,"msg":"保存失败！' . $DB->error().'"}');
    }
break;
case 'msgedit':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_message WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该通知记录不存在！"}');
    }
    $title=daddslashes($_POST['title']);
    $type=intval($_POST['type']);
    $proid=intval($_POST['proid']);
    $content=daddslashes($_POST['content']);
    $active = intval($_POST['active']);
    if($type>=6){
        if(!$proid){
            exit('{"code":-1,"msg":"请选择程序！"}');
        }
        $program = $DB->get_row("select * from yixi_program where id='" . $proid . "' limit 1");
        if (!$program) {
            exit('{"code":-1,"msg":"该程序不存在！"}');
        }
    }
    if($title==NULL or $content==NULL){
        exit('{"code":-1,"msg":"保存错误,请确保每项都不为空！"}');
    } else {
        if ($row['type'] != $type && $row['proid'] != $proid && $row['title'] != $title) {
            $rows=$DB->get_row("select * from yixi_message where type='$type' and proid='$proid' and title='$title' limit 1");
            if($rows){
                exit('{"code":-1,"msg":"通知标题已存在！"}');
            }
        }
        $sql="update `yixi_message` set `type` ='{$type}',`proid` ='{$proid}',`title` ='{$title}',`content` ='{$content}',`active` ='{$active}' where `id`='{$id}'";
        if($DB->query($sql)){
            exit('{"code":0,"msg":"保存通知成功！"}');
        } else {
            exit('{"code":-1,"msg":"保存通知失败！' . $DB->error().'"}');
        }
    }
break;
case 'blackedit':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_blacklist WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该黑名单记录不存在！"}');
    }
    $level=intval($_POST['level']);
    $qq=daddslashes($_POST['qq']);
    $note=daddslashes($_POST['note']);
    if(!$level){
        exit('{"code":-1,"msg":"请选择等级！"}');
    }
    if(!$qq or !$note){
        exit('{"code":-1,"msg":"QQ.原因不能为空！"}');
    }
    if ($row['qq'] != $qq) {
        $rows=$DB->get_row("SELECT * FROM yixi_blacklist WHERE qq='$qq' limit 1");
        if($rows){
            exit('{"code":-1,"msg":"平台已拉黑该QQ！"}');
        }
    }
    $sql="update `yixi_blacklist` set `level` ='{$level}',`qq` ='{$qq}',`note` ='{$note}' where `id`='{$id}'";
    if ($DB->query($sql)) {
        exit('{"code":0,"msg":"保存黑名单成功！"}');
    }else{
        exit('{"code":-1,"msg":"保存黑名单失败！'.$DB->error().'"}');
    }
break;
case 'jkdel':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_userjk WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该接口记录不存在！"}');
    }
    $sql="DELETE FROM yixi_userjk WHERE id='$id' limit 1";
    if($DB->query($sql)){
        exit('{"code":0,"msg":"删除成功！"}');
    } else {
        exit('{"code":-1,"msg":"删除失败！' . $DB->error().'"}');
    }
break;
case 'paydel':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_paysite WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该易支付认证记录不存在！"}');
    }
    $sql="DELETE FROM yixi_paysite WHERE id='$id' limit 1";
    if($DB->query($sql)){
        exit('{"code":0,"msg":"删除成功！"}');
    } else {
        exit('{"code":-1,"msg":"删除失败！' . $DB->error().'"}');
    }
break;
case 'msgdel':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_message WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该平台通知记录不存在！"}');
    }
    $sql="DELETE FROM yixi_message WHERE id='$id' limit 1";
    if($DB->query($sql)){
        exit('{"code":0,"msg":"删除成功！"}');
    } else {
        exit('{"code":-1,"msg":"删除失败！' . $DB->error().'"}');
    }
break;
case 'blockdel':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_block WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该盗版记录不存在！"}');
    }
    $sql="DELETE FROM yixi_block WHERE id='$id' limit 1";
    if($DB->query($sql)){
        exit('{"code":0,"msg":"删除成功！"}');
    } else {
        exit('{"code":-1,"msg":"删除失败！' . $DB->error().'"}');
    }
break;
case 'prodel':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_program WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该程序记录不存在！"}');
    }
    $sql="DELETE FROM yixi_program WHERE id='$id' limit 1";
	$sql2="DELETE FROM yixi_userjk WHERE proid='$id'";
    if($DB->query($sql)){
		if($DB->query($sql2)){
	      exit('{"code":0,"msg":"删除成功！"}');
		}else{
		  exit('{"code":-1,"msg":"旗下接口删除失败！' . $DB->error().'"}');
		}
    } else {
        exit('{"code":-1,"msg":"删除失败！' . $DB->error().'"}');
    }
break;
case 'shopdel':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_shop WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该商品记录不存在！"}');
    }
    $sql="DELETE FROM yixi_shop WHERE id='$id' limit 1";
    if($DB->query($sql)){
        exit('{"code":0,"msg":"删除成功！"}');
    } else {
        exit('{"code":-1,"msg":"删除失败！' . $DB->error().'"}');
    }
break;
case 'appdel':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_apps WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该应用不存在！"}');
    }
    $sql="DELETE FROM yixi_apps WHERE id='$id' limit 1";
    if($DB->query($sql)){
		$DB->query("DELETE FROM yixi_appuser WHERE appid='{$id}'");
		$DB->query("DELETE FROM yixi_appkm WHERE appid='{$id}'");
		$DB->query("DELETE FROM yixi_userjk WHERE appid='{$id}'");
        exit('{"code":0,"msg":"删除成功！"}');
    } else {
        exit('{"code":-1,"msg":"删除失败！' . $DB->error().'"}');
    }
break;
case 'adverdel':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_adver WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该广告记录不存在！"}');
    }
    $sql="DELETE FROM yixi_adver WHERE id='$id' limit 1";
    if($DB->query($sql)){
        exit('{"code":0,"msg":"删除成功！"}');
    } else {
        exit('{"code":-1,"msg":"删除失败！' . $DB->error().'"}');
    }
break;
case 'userdel':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $uid=intval($_GET['uid']);
    $row=$DB->get_row("SELECT * FROM yixi_user WHERE uid='{$uid}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该用户记录不存在！"}');
    }
    $sql="DELETE FROM yixi_user WHERE uid='$uid' limit 1";
    if($DB->query($sql)){
        exit('{"code":0,"msg":"删除成功！"}');
    } else {
        exit('{"code":-1,"msg":"删除失败！' . $DB->error().'"}');
    }
break;
case 'appuserdel':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $uid=intval($_GET['uid']);
    $row=$DB->get_row("SELECT * FROM yixi_appuser WHERE uid='{$uid}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该用户记录不存在！"}');
    }
    $sql="DELETE FROM yixi_appuser WHERE uid='$uid' limit 1";
    if($DB->query($sql)){
        exit('{"code":0,"msg":"删除成功！"}');
    } else {
        exit('{"code":-1,"msg":"删除失败！' . $DB->error().'"}');
    }
break;
case 'jkkdel':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_jkklist WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"这张加款卡不存在！"}');
    }
    $sql="DELETE FROM yixi_jkklist WHERE id='$id' limit 1";
    if($DB->query($sql)){
        exit('{"code":0,"msg":"删除成功！"}');
    } else {
        exit('{"code":-1,"msg":"删除失败！' . $DB->error().'"}');
    }
break;
case 'hmdel':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_hmlist WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该后门路径不存在！"}');
    }
    $sql="DELETE FROM yixi_hmlist WHERE id='$id' limit 1";
    if($DB->query($sql)){
        exit('{"code":0,"msg":"删除成功！"}');
    } else {
        exit('{"code":-1,"msg":"删除失败！' . $DB->error().'"}');
    }
break;
case 'dhkdel':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_dhklist WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"这张兑换卡不存在！"}');
    }
    $sql="DELETE FROM yixi_dhklist WHERE id='$id' limit 1";
    if($DB->query($sql)){
        exit('{"code":0,"msg":"删除成功！"}');
    } else {
        exit('{"code":-1,"msg":"删除失败！' . $DB->error().'"}');
    }
break;
case 'guessdel':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_guess WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"这条竞猜记录不存在！"}');
    }
    $sql="DELETE FROM yixi_guess WHERE id='$id' limit 1";
    if($DB->query($sql)){
        exit('{"code":0,"msg":"删除成功！"}');
    } else {
        exit('{"code":-1,"msg":"删除失败！' . $DB->error().'"}');
    }
break;
case 'jkkqk':
    if($DB->query("DELETE FROM yixi_jkklist WHERE 1")){
        exit('{"code":0,"msg":"清空成功！"}');
    }else{
        exit('{"code":-1,"msg":"清空失败！' . $DB->error().'"}');
    }
break;
case 'jkkqk1':
    if($DB->query("DELETE FROM yixi_jkklist WHERE status=1")){
        exit('{"code":0,"msg":"清空成功！"}');
    }else{
        exit('{"code":-1,"msg":"清空失败！' . $DB->error().'"}');
    }
break;
case 'jkkqk2':
    if($DB->query("DELETE FROM yixi_jkklist WHERE status=0")){
        exit('{"code":0,"msg":"清空成功！"}');
    }else{
        exit('{"code":-1,"msg":"清空失败！' . $DB->error().'"}');
    }
break;
case 'xskqk':
    if($DB->query("DELETE FROM yixi_xsklist WHERE 1")){
        exit('{"code":0,"msg":"清空成功！"}');
    }else{
        exit('{"code":-1,"msg":"清空失败！' . $DB->error().'"}');
    }
break;
case 'xskqk1':
    if($DB->query("DELETE FROM yixi_xsklist WHERE status=1")){
        exit('{"code":0,"msg":"清空成功！"}');
    }else{
        exit('{"code":-1,"msg":"清空失败！' . $DB->error().'"}');
    }
break;
case 'xskqk2':
    if($DB->query("DELETE FROM yixi_xsklist WHERE status=0")){
        exit('{"code":0,"msg":"清空成功！"}');
    }else{
        exit('{"code":-1,"msg":"清空失败！' . $DB->error().'"}');
    }
break;
case 'dhkqk':
    if($DB->query("DELETE FROM yixi_dhklist WHERE 1")){
        exit('{"code":0,"msg":"清空成功！"}');
    }else{
        exit('{"code":-1,"msg":"清空失败！' . $DB->error().'"}');
    }
break;
case 'dhkqk1':
    if($DB->query("DELETE FROM yixi_dhklist WHERE status=1")){
        exit('{"code":0,"msg":"清空成功！"}');
    }else{
        exit('{"code":-1,"msg":"清空失败！' . $DB->error().'"}');
    }
break;
case 'dhkqk2':
    if($DB->query("DELETE FROM yixi_dhklist WHERE status=0")){
        exit('{"code":0,"msg":"清空成功！"}');
    }else{
        exit('{"code":-1,"msg":"清空失败！' . $DB->error().'"}');
    }
break;
case 'user_czpass':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $uid=intval($_GET['uid']);
    $row=$DB->get_row("SELECT * FROM yixi_user WHERE uid='{$uid}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该用户记录不存在！"}');
    }
    if($row['pwd']=='123456'){
        exit('{"code":-1,"msg":"该用户的密码已是初始密码！"}');
    }
    $sql="update `yixi_user` set `pwd` ='123456' where `uid`='{$uid}'";
    if($DB->query($sql)){
        exit('{"code":0,"msg":"已将该用户的密码重置为123456[初始密码]"}');
    } else {
        exit('{"code":-1,"msg":"重置密码失败！' . $DB->error().'"}');
    }
break;
case 'appuser_czpass':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $uid=intval($_GET['uid']);
    $row=$DB->get_row("SELECT * FROM yixi_appuser WHERE uid='{$uid}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该用户记录不存在！"}');
    }
    if($row['pwd']=='123456'){
        exit('{"code":-1,"msg":"该用户的密码已是初始密码！"}');
    }
    $sql="update `yixi_appuser` set `pwd` ='123456' where `uid`='{$uid}'";
    if($DB->query($sql)){
        exit('{"code":0,"msg":"已将该用户的密码重置为123456[初始密码]"}');
    } else {
        exit('{"code":-1,"msg":"重置密码失败！' . $DB->error().'"}');
    }
break;
case 'adver_see':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $sql="update `yixi_adver` set `see` ='1' where `see`='0'";
    if($DB->query($sql)){
        exit('{"code":0,"msg":"审核所有成功广告！"}');
    } else {
        exit('{"code":-1,"msg":"审核广告失败！' . $DB->error().'"}');
    }
break;
case 'adver_sees':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_adver WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该广告记录不存在！"}');
    }
    if($row['see']==1){
        exit('{"code":-1,"msg":"该广告记录已通过审核！"}');
    }
    $sql="update `yixi_adver` set `see` ='1' where `id`='{$id}'";
    if($DB->query($sql)){
        exit('{"code":0,"msg":"操作成功！"}');
    } else {
        exit('{"code":-1,"msg":"操作失败！' . $DB->error().'"}');
    }
break;
case 'adver_seess':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_adver WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"这条广告记录不存在！"}');
    }
    if($row['see']==1){
        exit('{"code":-1,"msg":"该广告记录已通过审核！"}');
    }
    $sql="DELETE FROM yixi_adver WHERE id='$id' limit 1";
    if($DB->query($sql)){
        if($row['money']>'0.00'){
        $DB->query("update yixi_user set rmb=rmb+{$rows['money']} where uid='{$rows['uid']}'");
        addPointRecord($rows['uid'], $rows['money'], '退回', '广告审核不通过被支付费用退回到不可提现余额'.$rows['money'].'元，请检查网站是否违规');
        }
        exit('{"code":0,"msg":"操作成功！"}');
    } else {
        exit('{"code":-1,"msg":"操作失败！' . $DB->error().'"}');
    }
break;
case 'guess_jk':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $xjqh = $conf['guess_stage'];
    $jchm=jc_hm();
    $DB->query("update yixi_guess set hm='$jchm' where jcqs='{$xjqh}'");
    $sqqh = $xjqh + 1;
    saveSetting('guess_stage',$sqqh);
    $ad=$CACHE->clear();
    if($ad)exit('{"code":0,"msg":"更新数据成功"}');
    else exit('{"code":-1,"msg":"更新数据失败['.$DB->error().']"}');
break;
case 'jk_active':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_userjk WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该接口不存在！"}');
    }
    $active = $row['active'] == y ? n : y;
    $DB->query("update yixi_userjk set active='$active' where id='{$id}'");
    exit('{"code":0,"msg":"succ"}');
break;
case 'pay_active':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_paysite WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该易支付认证记录不存在！"}');
    }
    $active = $row['active'] == 1 ? 0 : 1;
    $DB->query("update yixi_paysite set active='$active' where id='{$id}'");
    exit('{"code":0,"msg":"succ"}');
break;
case 'msg_active':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_message WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该平台通知记录不存在！"}');
    }
    $active = $row['active'] == 1 ? 0 : 1;
    $DB->query("update yixi_message set active='$active' where id='{$id}'");
    exit('{"code":0,"msg":"succ"}');
break;
case 'user_active':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $uid=intval($_GET['uid']);
    $row=$DB->get_row("SELECT * FROM yixi_user WHERE uid='{$uid}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该用户记录不存在！"}');
    }
    $active = $row['active'] == 1 ? 0 : 1;
    $DB->query("update yixi_user set active='$active' where uid='{$uid}'");
    exit('{"code":0,"msg":"succ"}');
break;
case 'user_status':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $uid=intval($_GET['uid']);
    $row=$DB->get_row("SELECT * FROM yixi_user WHERE uid='{$uid}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该用户记录不存在！"}');
    }
    $status = $row['status'] == 1 ? 0 : 1;
    $DB->query("update yixi_user set status='$status' where uid='{$uid}'");
    exit('{"code":0,"msg":"succ"}');
break;
case 'shop_active':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_shop WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该商品记录不存在！"}');
    }
    $active = $row['active'] == 1 ? 0 : 1;
    if($active==1){
        $rzdate=$date;
    }
    $DB->query("update yixi_shop set active='{$active}',rzdate='{$rzdate}' where id='{$id}'");
    exit('{"code":0,"msg":"succ"}');
break;
case 'pro_switch':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_program WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该程序记录不存在！"}');
    }
    $switch = $row['switch'] == y ? n : y;
    $DB->query("update yixi_program set switch='$switch' where id='{$id}'");
    exit('{"code":0,"msg":"succ"}');
break;
case 'pro_visible':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_program WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该程序记录不存在！"}');
    }
    $visible = $row['visible'] == y ? n : y;
    $DB->query("update yixi_program set visible='$visible' where id='{$id}'");
    exit('{"code":0,"msg":"succ"}');
break;
case 'pro_longfree':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_program WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该程序记录不存在！"}');
    }
    $longfree = $row['longfree'] == y ? n : y;
    $DB->query("update yixi_program set longfree='$longfree' where id='{$id}'");
    exit('{"code":0,"msg":"succ"}');
break;
case 'pro_active':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_program WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该程序记录不存在！"}');
    }
    $active = $row['active'] == y ? n : y;
    $DB->query("update yixi_program set active='$active' where id='{$id}'");
    exit('{"code":0,"msg":"succ"}');
break;
case 'adver_top':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_adver WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该广告记录不存在！"}');
    }
    $top = $row['top'] == 1 ? 0 : 1;
    $DB->query("update yixi_adver set top='$top' where id='{$id}'");
    exit('{"code":0,"msg":"succ"}');
break;
case 'adver_active':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_adver WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该广告记录不存在！"}');
    }
    $active = $row['active'] == 1 ? 0 : 1;
    $DB->query("update yixi_adver set active='$active' where id='{$id}'");
    exit('{"code":0,"msg":"succ"}');
break;
case 'user_recharge':
    $uid=intval($_POST['uid']);
    $actdo=intval($_POST['actdo']);
    $rmb=floatval($_POST['rmb']);
    $row=$DB->get_row("select * from yixi_user where uid='$uid' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"当前用户不存在！"}');
    }
    if ($rmb=="" || $rmb<0) {
        exit('{"code":-1,"msg":"金额输入不规范！"}');
    }
    if($actdo==1 && $rmb>$row['rmb']){
        $rmb=$row['rmb'];
    }
    if($actdo==0){
        $DB->query("update yixi_user set rmb=rmb+{$rmb} where uid='{$uid}'");
        addPointRecord($uid, $rmb, '加款', '后台加款'.$rmb.'元');
    }else{
        $DB->query("update yixi_user set rmb=rmb-{$rmb} where uid='{$uid}'");
        addPointRecord($uid, $rmb, '扣除', '后台扣款'.$rmb.'元');
    }
    exit('{"code":0,"msg":"成功"}');
break;
case 'editPromoney':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_POST['id']);
    $ptprice = daddslashes($_POST['ptprice']);
    $byprice = daddslashes($_POST['byprice']);
    $hjprice = daddslashes($_POST['hjprice']);
    $zsprice = daddslashes($_POST['zsprice']);
    $row=$DB->get_row("select * from yixi_program where id='$id' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"当前程序不存在！"}');
    }
    if ($ptprice == "" || $byprice == "" || $hjprice == "" || $zsprice == "") {
        exit('{"code":-1,"msg":"请确保价格配置各项都不为空"}');
    }
    $sql="update `yixi_program` set `ptprice` ='{$ptprice}',`byprice` ='{$byprice}',`hjprice` ='{$hjprice}',`zsprice` ='{$zsprice}' where `id`='{$id}'";
    if($DB->query($sql)){
        exit('{"code":0,"msg":"保存程序价格成功"}');
    } else {
        exit('{"code":-1,"msg":"保存程序价格失败！' . $DB->error().'"}');
    }
break;
case 'app_active':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_apps WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该应用不存在！"}');
    }
    $active = $row['active'] == 'y' ? 'n' : 'y';
    $DB->query("update yixi_apps set active='$active' where id='{$id}'");
    exit('{"code":0,"msg":"succ"}');
break;
case 'app_switch':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_apps WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该应用不存在！"}');
    }
    $switch = $row['switch'] == 'y' ? 'n' : 'y';
    $DB->query("update yixi_apps set switch='$switch' where id='{$id}'");
    exit('{"code":0,"msg":"succ"}');
break;
case 'app_ipauth':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_apps WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该应用不存在！"}');
    }
    $ipauth = $row['ipauth'] == 'y' ? 'n' : 'y';
    $DB->query("update yixi_apps set ipauth='$ipauth' where id='{$id}'");
    exit('{"code":0,"msg":"succ"}');
break;
case 'app_login_check':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_apps WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该应用不存在！"}');
    }
    $logon_check = $row['logon_check_in'] == 'y' ? 'n' : 'y';
    $DB->query("update yixi_apps set logon_check_in='$logon_check' where id='{$id}'");
    exit('{"code":0,"msg":"succ"}');
break;
case 'app_kmactive':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_appkm WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该卡密不存在！"}');
    }
    $state = $row['state'] == 'y' ? 'n' : 'y';
    $DB->query("update yixi_appkm set state='$state' where id='{$id}'");
    exit('{"code":0,"msg":"succ"}');
break;
case 'app_kmqk':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    if($DB->query("DELETE FROM yixi_appkm")){
        exit('{"code":0,"msg":"清空成功！"}');
    }else{
        exit('{"code":-1,"msg":"清空失败！' . $DB->error().'"}');
    }
break;
case 'app_kmqkme':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    if($DB->query("DELETE FROM yixi_appkm WHERE upid='1'")){
        exit('{"code":0,"msg":"清空成功！"}');
    }else{
        exit('{"code":-1,"msg":"清空失败！' . $DB->error().'"}');
    }
break;
case 'app_kmqk1':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    if($DB->query("DELETE FROM yixi_appkm WHERE km_use='y'")){
        exit('{"code":0,"msg":"清空成功！"}');
    }else{
        exit('{"code":-1,"msg":"清空失败！' . $DB->error().'"}');
    }
break;
case 'app_kmqkme1':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    if($DB->query("DELETE FROM yixi_appkm WHERE km_use='y' AND upid='1'")){
        exit('{"code":0,"msg":"清空成功！"}');
    }else{
        exit('{"code":-1,"msg":"清空失败！' . $DB->error().'"}');
    }
break;
case 'app_kmqk2':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    if($DB->query("DELETE FROM yixi_appkm WHERE km_use='n'")){
        exit('{"code":0,"msg":"清空成功！"}');
    }else{
        exit('{"code":-1,"msg":"清空失败！' . $DB->error().'"}');
    }
break;
case 'app_kmqkme2':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    if($DB->query("DELETE FROM yixi_appkm WHERE km_use='n' AND upid='1'")){
        exit('{"code":0,"msg":"清空成功！"}');
    }else{
        exit('{"code":-1,"msg":"清空失败！' . $DB->error().'"}');
    }
break;
case 'app_kmqk3':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    if($DB->query("DELETE FROM yixi_appkm WHERE `end_time`< '".time()."' or `amount`< '1'")){
        exit('{"code":0,"msg":"清空成功！"}');
    }else{
        exit('{"code":-1,"msg":"清空失败！' . $DB->error().'"}');
    }
break;
case 'app_kmqkme3':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    if($DB->query("DELETE FROM yixi_appkm WHERE `end_time`< '".time()."' AND upid='1' or `amount`< '1' AND upid='1'")){
        exit('{"code":0,"msg":"清空成功！"}');
    }else{
        exit('{"code":-1,"msg":"清空失败！' . $DB->error().'"}');
    }
break;
case 'app_fileactive':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_appfile WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该文件不存在！"}');
    }
    $state = $row['state'] == 'y' ? 'n' : 'y';
    $DB->query("update yixi_appfile set state='$state' where id='{$id}'");
    exit('{"code":0,"msg":"succ"}');
break;
case 'appuser_active':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $uid=intval($_GET['uid']);
    $row=$DB->get_row("SELECT * FROM yixi_appuser WHERE uid='{$uid}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该用户记录不存在！"}');
    }
    $status = $row['status'] == y ? n : y;
    $DB->query("update yixi_appuser set status='$status' where uid='{$uid}'");
    exit('{"code":0,"msg":"succ"}');
break;
case 'appkm_del':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_appkm WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该卡密不存在！"}');
    }
    $sql="DELETE FROM yixi_appkm WHERE id='$id' limit 1";
    if($DB->query($sql)){
        exit('{"code":0,"msg":"删除成功！"}');
    } else {
        exit('{"code":-1,"msg":"删除失败！' . $DB->error().'"}');
    }
break;
case 'appfile_del':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("SELECT * FROM yixi_appfile WHERE id='{$id}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该文件不存在！"}');
    }
    $sql="DELETE FROM yixi_appfile WHERE id='$id' limit 1";
    if($DB->query($sql)){
        exit('{"code":0,"msg":"删除成功！"}');
    } else {
        exit('{"code":-1,"msg":"删除失败！' . $DB->error().'"}');
    }
break;
case 'AppUser_recharge':
	if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $uid=intval($_POST['uid']);
    $actdo=intval($_POST['actdo']);
    $rmb=floatval($_POST['rmb']);
    $row=$DB->get_row("select * from yixi_appuser where uid='$uid' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"当前用户不存在！"}');
    }
    if ($rmb=="" || $rmb<0) {
        exit('{"code":-1,"msg":"金额输入不规范！"}');
    }
    if($actdo==1 && $rmb>$row['rmb']){
        $rmb=$row['rmb'];
    }
    if($actdo==0){
        $DB->query("update yixi_appuser set rmb=rmb+{$rmb} where uid='{$uid}'");
    }else{
        $DB->query("update yixi_appuser set rmb=rmb-{$rmb} where uid='{$uid}'");
    }
    exit('{"code":0,"msg":"成功"}');
break;
case 'AppUser_fencharge':
	if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $uid=intval($_POST['uid']);
    $actdo=intval($_POST['actdo']);
    $fen=intval($_POST['fen']);
    $row=$DB->get_row("select * from yixi_appuser where uid='$uid' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"当前用户不存在！"}');
    }
    if ($fen=="" || $fen<0) {
        exit('{"code":-1,"msg":"积分输入不规范！"}');
    }
    if($actdo==1 && $fen>$row['fen']){
        $fen=$row['fen'];
    }
    if($actdo==0){
        $DB->query("update yixi_appuser set fen=fen+{$fen} where uid='{$uid}'");
    }else{
        $DB->query("update yixi_appuser set fen=fen-{$fen} where uid='{$uid}'");
    }
    exit('{"code":0,"msg":"成功"}');
break;
case 'Appuseredit':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $uid=intval($_GET['uid']);
    $row=$DB->get_row("SELECT * FROM yixi_appuser WHERE uid='{$uid}' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"该用户记录不存在！"}');
    }
    if (!empty(daddslashes($_POST['pwd']))) {
        $sql = "`pwd`='".daddslashes($_POST['pwd'])."',";
    }
	$name=daddslashes($_POST['name']);
	$qq=intval($_POST['qq']);
    $status=daddslashes($_POST['status']);
    $sql="update `yixi_appuser` set {$sql}`name` ='{$name}',`qq` ='{$qq}',`status` ='{$status}' where `uid`='{$uid}'";
    if ($DB->query($sql)) {
        exit('{"code":0,"msg":"编辑成功！"}');
    } else {
        exit('{"code":-1,"msg":"编辑失败！' . $DB->error().'"}');
    }
break;
case 'appkm_change':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $aid=$_POST['aid'];
    $checkbox=$_POST['checkbox'];
    $i=0;
    foreach($checkbox as $id){
        if($aid==1){
            $DB->query("update yixi_appkm set state='n' where id='$id' limit 1");
            $i++;
        }elseif($aid==2){
            $DB->query("update yixi_appkm set state='y' where id='$id' limit 1");
            $i++;
        }elseif($aid==3){
            $DB->query("DELETE FROM yixi_appkm WHERE id='$id' limit 1");
                $i++;
        }elseif($aid==4){
			$res=$DB->get_row("SELECT * FROM yixi_appkm WHERE id='$id' limit 1");
			$data[]=$res['kami'];
            $i++;
        }
    }
	if(($aid==4)or($aid==5)){
	if($aid==5){
	$i=$DB->count("SELECT count(*) from yixi_appkm WHERE 1");
	$rs=$DB->query("SELECT * FROM yixi_appkm WHERE 1 order by id desc");
	while($res = $DB->fetch($rs))
    {
		$data[]=$res['kami'];
	} 
    }
		 $cnum = count($data);
          for($i=0;$i<$cnum;$i++){ 
          $output .= $data[$i];
          if ($cnum - 1 > $i) {
          $output .= '&#10;';
        }}
	$date = '<div class="card-body">';
    $date .= '<form class="form-horizontal">';
    $date .= '<div class="form-group mb-3">';
    $date .= '<label for="example-input-normal" style="font-weight: 500">卡密内容：</label>';
    $date .= '<textarea name="km_info" id="km_info" class="form-control" style="height:100px;" lay-verType="tips">';
	$date .= $output.'</textarea></div>';
	$date .= '<span class="btn btn-block btn-xs btn-outline-success" data-clipboard-text="'.$output.'" data-clipboard-action="copy" data-clipboard-target="#btn_code" id="btn_code">复制</span>';
	$date .= '</form>';
    $date .= '</div>';
    $result=array("code"=>1,"msg"=>"成功导出".$i."个卡密","data"=>$date);
    exit(json_encode($result));
	}
    exit('{"code":0,"msg":"成功操作'.$i.'个卡密"}');
break;
case 'appdhk_change':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $aid=$_POST['aid'];
    $checkbox=$_POST['checkbox'];
    $i=0;
    foreach($checkbox as $id){
        if($aid==1){
            $DB->query("DELETE FROM yixi_dhklist WHERE id='$id' limit 1");
            $i++;
        }elseif($aid==2){
			$res=$DB->get_row("SELECT * FROM yixi_dhklist WHERE id='$id' limit 1");
			$data[]=$res['km'];
            $i++;
        }
    }
	if(($aid==2)or($aid==3)){
	if($aid==3){
	$i=$DB->count("SELECT count(*) from yixi_dhklist WHERE 1");
	$rs=$DB->query("SELECT * FROM yixi_dhklist WHERE 1 order by id desc");
	while($res = $DB->fetch($rs))
    {
		$data[]=$res['km'];
	} 
    }
		 $cnum = count($data);
          for($i=0;$i<$cnum;$i++){ 
          $output .= $data[$i];
          if ($cnum - 1 > $i) {
          $output .= '&#10;';
        }}
	$date = '<div class="card-body">';
    $date .= '<form class="form-horizontal">';
    $date .= '<div class="form-group mb-3">';
    $date .= '<label for="example-input-normal" style="font-weight: 500">卡密内容：</label>';
    $date .= '<textarea name="km_info" id="km_info" class="form-control" style="height:100px;" lay-verType="tips">';
	$date .= $output.'</textarea></div>';
	$date .= '<span class="btn btn-block btn-xs btn-outline-success" data-clipboard-text="'.$output.'" data-clipboard-action="copy" data-clipboard-target="#btn_code" id="btn_code">复制</span>';
	$date .= '</form>';
    $date .= '</div>';
    $result=array("code"=>1,"msg"=>"成功导出".$i."个卡密","data"=>$date);
    exit(json_encode($result));
	}
    exit('{"code":0,"msg":"成功操作'.$i.'个卡密"}');
break;
case 'shop_tcbl':
    $id=intval($_POST['id']);
    $tcbl=daddslashes($_POST['tcbl']);
    $row=$DB->get_row("select * from yixi_shop where id='$id' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"当前商品不存在！"}');
    }
    if ($tcbl=="" || $tcbl>100 || $tcbl<0) {
        exit('{"code":-1,"msg":"价格输入不规范！"}');
    }
    $sql="update `yixi_shop` set `tcbl` ='{$tcbl}' where `id`='{$id}'";
    if($DB->query($sql)){
        exit('{"code":0,"msg":"保存提成比例成功"}');
    } else {
        exit('{"code":-1,"msg":"保存提成比例失败！' . $DB->error().'"}');
    }
break;
case 'shop_money':
    $id=intval($_POST['id']);
    $money=floatval($_POST['money']);
    $row=$DB->get_row("select * from yixi_shop where id='$id' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"当前商品不存在！"}');
    }
    if ($money=="" || $money<0) {
        exit('{"code":-1,"msg":"价格输入不规范！"}');
    }
    $sql="update `yixi_shop` set `money` ='{$money}' where `id`='{$id}'";
    if($DB->query($sql)){
        exit('{"code":0,"msg":"保存商品价格成功"}');
    } else {
        exit('{"code":-1,"msg":"保存商品价格失败！' . $DB->error().'"}');
    }
break;
case 'workorder_reply':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $rows=$DB->get_row("select * from yixi_workorder where id='$id' limit 1");
    if (!$rows) {
        exit('{"code":-1,"msg":"当前工单记录不存在！"}');
    } else if ($rows['status']==2) {
        exit('{"code":-1,"msg":"当前工单已结单！"}');
    }
    $content=str_replace(array('*','^','|'),'',trim(strip_tags(daddslashes($_POST['content']))));
    if (empty($content)) {
        exit('{"code":-1,"msg":"补充信息不能为空！"}');
    } else {
        $content = $rows['content'].'*1^'.$date.'^'.$content;
        if($DB->query("update yixi_workorder set content='$content',status=1 where id='{$id}'")){
            if($_POST['email']==1){
                $row=$DB->get_row("select * from yixi_workorder where id='$id' limit 1");
                $siterow = $DB->get_row("select uid,user,qq from yixi_user where uid='{$row['uid']}' limit 1");
                $mail_name = $siterow['qq'].'@qq.com';
                $sub = $conf['sitename'].'售后支持工单待反馈提醒';
                $content=explode('*',$row['content']);
                $content=mb_substr($content[0], 0, 16, 'utf-8');
                $text = '尊敬的'.$siterow['user'].'：<br/>您于'.$row['addtime'].'提交的售后支持工单(ID:'.$id.') 需要您进一步提供相关信息。请登录网站后台“我的工单”查看详情并回复。若3天内您仍未回复此工单，我们会做完成工单处理。<a href="'.$authurl.'user/workorder.php?my=view&id='.$id.'" target="_blank">点此查看</a><br/><a href="'.$authurl.'user/workorder.php?my=view&id='.$id.'" target="_blank">工单标题：'.$content.'</a><br/>----------------<br/>'.$conf['sitename'];
                $msg = youfas($sub,$text);
                if(checkEmail($mail_name)){
                    send_mail($mail_name,$sub,$msg);
                }
            }
            exit('{"code":0,"msg":"回复工单成功"}');
        }else{
            exit('{"code":-1,"msg":"回复工单失败！' . $DB->error().'"}');
        }
    }
break;
case 'workorder_complete':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $rows=$DB->get_row("select * from yixi_workorder where id='$id' limit 1");
    if (!$rows) {
        exit('{"code":-1,"msg":"当前工单记录不存在！"}');
    } else if ($rows['status']==2) {
        exit('{"code":-1,"msg":"当前工单已结单！"}');
    }
    if($DB->query("update yixi_workorder set status=2 where id='{$id}'")){
        exit('{"code":0,"msg":"完结工单成功"}');
    }else{
        exit('{"code":-1,"msg":"完结工单失败！' . $DB->error().'"}');
    }
break;
case 'workorder_change':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $aid=$_POST['aid'];
    $checkbox=$_POST['checkbox'];
    $i=0;
    foreach($checkbox as $id){
        if($aid==1){
            $DB->query("update yixi_workorder set status=0 where id='$id' limit 1");
            $i++;
        }elseif($aid==2){
            $DB->query("update yixi_workorder set status=2 where id='$id' limit 1");
            $i++;
        }elseif($aid==3){
            $rows=$DB->get_row("select * from yixi_workorder where id='$id' limit 1");
            $content=str_replace(array('*','^','|'),'',trim(strip_tags(daddslashes($_POST['content']))));
            if($rows && $rows['status']<2 && !empty($content)){
                $content = addslashes($rows['content']).'*1^'.$date.'^'.$content;
                $DB->query("update yixi_workorder set content='$content',status=1 where id='$id' limit 1");
                $i++;
            }
        }elseif($aid==4){
            $DB->query("DELETE FROM yixi_workorder WHERE id='$id' limit 1");
            $i++;
        }
    }
    exit('{"code":0,"msg":"成功改变'.$i.'个工单"}');
break;
case 'delworkorder':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $sql="DELETE FROM yixi_workorder WHERE id='$id' limit 1";
    if($DB->query($sql)){
        exit('{"code":0,"msg":"删除成功！"}');
    }else{
        exit('{"code":-1,"msg":"删除失败！'.$DB->error().'"}');
    }
break;
case 'set':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    foreach($_POST as $k=>$v){
        saveSetting($k, $v);
    }
    $ad=$CACHE->clear();
    if($ad)exit('{"code":0,"msg":"succ"}');
    else exit('{"code":-1,"msg":"保存设置失败['.$DB->error().']"}');
break;
case 'getMessage': //查看平台通知
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $row=$DB->get_row("select * from yixi_message where id='$id' limit 1");
    if(!$row){
        exit('{"code":-1,"msg":"当前通知不存在！"}');
    }
    $result=array("code"=>0,"msg"=>"succ","title"=>$row['title'],"type"=>$row['type'],"content"=>$row['content'],"date"=>$row['addtime']);
    exit(json_encode($result));
break;
case 'iptype':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $result = [['name'=>'0_X_FORWARDED_FOR', 'ip'=>real_ip(0), 'city'=>get_ip_city(real_ip(0))],['name'=>'1_X_REAL_IP', 'ip'=>real_ip(1), 'city'=>get_ip_city(real_ip(1))],['name'=>'2_REMOTE_ADDR', 'ip'=>real_ip(2), 'city'=>get_ip_city(real_ip(2))]];
    exit(json_encode($result));
break;
case 'getTixian': //查看提现信息
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_GET['id']);
    $rows=$DB->get_row("select * from yixi_tixian where id='$id' limit 1");
    if(!$rows)
        exit('{"code":-1,"msg":"当前提现记录不存在！"}');
    $data = '<div class="card-body">';
    $data .= '<form class="form-horizontal">';
    $data .= '<div class="form-group mb-3"><label for="example-input-normal" style="font-weight: 500">提现方式：</label><select class="form-control" id="type" default="'.$rows['type'].'"><option value="0">支付宝</option><option value="1">微信</option><option value="2">QQ钱包</option></select></div>';
    $data .= '<div class="form-group mb-3"><label for="example-input-normal" style="font-weight: 500">提现姓名：</label><input type="text" id="name" value="'.$rows['name'].'" class="form-control" required/></div>';
    $data .= '<div class="form-group mb-3"><label for="example-input-normal" style="font-weight: 500">提现账号：</label><input type="text" id="account" value="'.$rows['account'].'" class="form-control" required/></div>';
    $data .= '<div class="form-group mb-3"><label for="example-input-normal" style="font-weight: 500">提现备注：</label><input type="text" id="remarks" value="'.$rows['remarks'].'" class="form-control" required/></div>';
    $data .= '<input type="submit" id="save" onclick="saveInfo('.$id.')" class="btn btn-block btn-xs btn-outline-success" value="保存">';
    $data .= '</form>';
    $data .= '</div>';
    $result=array("code"=>0,"msg"=>"succ","data"=>$data);
    exit(json_encode($result));
break;
case 'editTixian': //保存提现信息
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_POST['id']);
    $type=trim(daddslashes($_POST['type']));
    $name=trim(daddslashes($_POST['name']));
    $account=trim(daddslashes($_POST['account']));
    $remarks=trim(daddslashes($_POST['remarks']));
    $sds=$DB->query("update `yixi_tixian` set `type`='$type',`account`='$account',`name`='$name',`remarks`='$remarks' where `id`='$id'");
    if($sds)
        exit('{"code":0,"msg":"保存记录成功！"}');
    else
        exit('{"code":-1,"msg":"保存记录失败！'.$DB->error().'"}');
break;
case 'opTixian': //操作提现
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id=intval($_POST['id']);
    $op=$_POST['op'];
    if($op == 'delete'){
        $sql="DELETE FROM yixi_tixian WHERE id='$id'";
        if($DB->query($sql))
            exit('{"code":0,"msg":"删除成功！"}');
        else
            exit('{"code":-1,"msg":"删除失败！'.$DB->error().'"}');
    }elseif($op == 'complete'){
        if($DB->query("update yixi_tixian set status=1,endtime='{$date}' where id='$id'"))
            exit('{"code":0,"msg":"已变更为已提现状态"}');
        else
            exit('{"code":-1,"msg":"变更失败！'.$DB->error().'"}');
    }elseif($op == 'reset'){
        if($DB->query("update yixi_tixian set status=0 where id='$id'"))
            exit('{"code":0,"msg":"已变更为未提现状态"}');
        else
            exit('{"code":-1,"msg":"变更失败！'.$DB->error().'"}');
    }elseif($op == 'back'){
        $rows=$DB->get_row("select * from yixi_tixian where id='$id' limit 1");
        $DB->query("update yixi_user set rmb_tc=rmb_tc+{$rows['money']} where uid='{$rows['uid']}'");
        addPointRecord($rows['uid'], $rows['money'], '退回', '提现被退回到分站余额'.$rows['money'].'元，请检查提现方式是否正确');
        if($DB->query("update yixi_tixian set status=2 where id='$id'"))
            exit('{"code":0,"msg":"已成功退回到分站余额"}');
        else
            exit('{"code":-1,"msg":"退回失败！'.$DB->error().'"}');
    }
break;
case 'defend':
    $defendid=$_POST['defendid'];
    $file="<?php\\r\\n//防CC模块设置\\r\\ndefine('CC_Defender', ".$defendid.");\\r\\n?>";
    file_put_contents(SYSTEM_ROOT.'base.php',$file);
    $result=array("code"=>0,"msg"=>"保存成功！");
    exit(json_encode($result));
break;
case 'cleanbom':
    $filename=ROOT.'config.php';
    $contents=file_get_contents($filename);
    $charset[1]=substr($contents,0,1);
    $charset[2]=substr($contents,1,1);
    $charset[3]=substr($contents,2,1);
    if (ord($charset[1])==239 && ord($charset[2])==187) {
        $rest=substr($contents,3);
        file_put_contents($filename,$rest);
        $result=array("code"=>0,"msg"=>"找到BOM并已自动去除！");
    }else {
        $result=array("code"=>-1,"msg"=>"没有找到BOM！");
    }
    exit(json_encode($result));
break;
case 'site_endtime':
    $id=intval($_POST['id']);
    $num=intval($_POST['num']);
    $row=$DB->get_row("select * from yixi_site where id='$id' limit 1");
    if($row['endtime']>date("Y-m-d")) {
if($conf['auth_time_type']==2){
    $endtime=date("Y-m-d", strtotime("+{$num} years", strtotime($row['endtime'])));
}elseif($conf['auth_time_type']==1){
    $endtime=date("Y-m-d", strtotime("+{$num} months", strtotime($row['endtime'])));
}else{
    $endtime=date("Y-m-d", strtotime("+{$num} days", strtotime($row['endtime'])));
}
}else{
if($conf['auth_time_type']==2){
    $endtime=date("Y-m-d", strtotime("+{$num} years"));
}elseif($conf['auth_time_type']==1){
    $endtime=date("Y-m-d", strtotime("+{$num} months"));
}else{
    $endtime=date("Y-m-d", strtotime("+{$num} days"));
}
}
    $sql="update yixi_site set endtime='$endtime' where id='{$id}'";
    if($DB->query($sql)){
        exit('{"code":0,"msg":"续时成功"}');
    }else{
        exit('{"code":-1,"msg":"续时失败！' . $DB->error().'"}');
    }
break;
case 'mailtest':
    $mail_name=($conf['mail_recv'] ? $conf['mail_recv'] : $conf['mail_name']);
    $sub = '邮件发送测试。';
    if (!empty($mail_name)) {
        $text = '这是一封测试邮件！<br/><br/>来自：'.$siteurl;
        $msg = youfas($sub,$text);
        $result=send_mail($mail_name,$msg);
        if ($result==1) {
            exit('{"code":0,"msg":"邮件发送成功！"}');
        } else {
            exit('{"code":-1,"msg":"邮件发送失败！"}');
        }
    } else {
        exit('{"code":-1,"msg":"您还未设置邮箱！"}');
    }
break;
case 'optim':
    $rs=$DB->query("SHOW TABLES FROM `".$dbconfig['dbname'].'`');
    while ($row = $DB->fetch($rs)) {
        $DB->query('OPTIMIZE TABLE  `'.$dbconfig['dbname'].'`.`'.$row[0].'`');
    }
    exit('{"code":0,"msg":"已成功优化所有数据表"}');
break;
case 'repair':
    $rs=$DB->query("SHOW TABLES FROM `".$dbconfig['dbname'].'`');
    while ($row = $DB->fetch($rs)) {
        $DB->query('REPAIR TABLE  `'.$dbconfig['dbname'].'`.`'.$row[0].'`');
    }
    exit('{"code":0,"msg":"已成功修复所有数据表"}');
break;
case 'add_key':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $money = $conf['add_key_rmb'];
    $key = random(32);
    saveSetting('api_key',$key);
    $ad=$CACHE->clear();
    if($ad)exit('{"code":0,"msg":"APIKEY生成成功"}');
    else exit('{"code":-1,"msg":"APIKEY生成失败['.$DB->error().']"}');
break;
case 'api_key':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    if(isset($_SESSION['api_key']) && $_SESSION['api_key']>TIMESTAMP-600){
        exit('{"code":-1,"msg":"请勿频繁重置！"}');
    }
    $key = random(32);
    saveSetting('api_key',$key);
    $ad=$CACHE->clear();
    if($ad){
        $_SESSION['api_key']=TIMESTAMP;
        exit('{"code":0,"msg":"APIKEY重置成功"}');
    }else{
        exit('{"code":-1,"msg":"APIKEY重置失败['.$DB->error().']"}');
    }
break;
case 'api_ip':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $data=trim($_POST['data']);
    saveSetting('api_iplist',$data);
    $ad=$CACHE->clear();
    if($ad)exit('{"code":0,"msg":"设置成功"}');
    else exit('{"code":-1,"msg":"设置失败['.$DB->error().']"}');
break;
case 'api_iplist':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $result=array("code"=>0,"msg"=>"succ","data"=>$conf['api_iplist']);
    exit(json_encode($result));
break;
case 'get_apijk':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $proid=intval($_POST['proid']);
    if(!$proid){
        exit('{"code":-1,"msg":"请选择您要生成的程序！"}');
    }
    $program = $DB->get_row("select * from yixi_program where id='" . $proid . "' limit 1");
    if (!$program) {
        exit('{"code":-1,"msg":"该程序不存在！"}');
    }
    $result=array("code"=>0,"msg"=>"succ","api_jk"=>$authurl.'api/cloud_api.php?act=cloud_auth&proid='.$proid.'&name=授权站点名称&qq=授权QQ&url=授权域名&ip=服务器ip&key='.$conf['api_key']);
    exit(json_encode($result));
break;
case 'get_user_apijk':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $proid=intval($_POST['proid']);
    if(!$proid){
        exit('{"code":-1,"msg":"请选择您要生成的程序！"}');
    }
    $program = $DB->get_row("select * from yixi_program where id='" . $proid . "' limit 1");
    if (!$program) {
        exit('{"code":-1,"msg":"该程序不存在！"}');
    }
    $result=array("code"=>0,"msg"=>"succ","apisqs_jk"=>$authurl.'api/cloud_api.php?act=cloud_user&proid='.$proid.'&power=1&user=登录用户名&pwd=登录密码&qq=联系QQ&email=绑定邮箱&ip=服务器ip&key='.$conf['api_key'],"apicg_jk"=>$authurl.'api/cloud_api.php?act=cloud_user&proid='.$proid.'&power=2&user=登录用户名&user=登录密码&qq=联系QQ&email=绑定邮箱&ip=服务器ip&key='.$conf['api_key']);
    exit(json_encode($result));
break;
case 'transfer':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    $id = intval($_POST['id']);
    if(!$conf['user_daifu'])exit(json_encode(array('code'=>0,'msg'=>'请先在用户设置开启代付接口')));
    if(!$conf['transfer_id'] || !$conf['transfer_key'] || !$conf['transfer_check'] || !$_SESSION["transfer_pass"])exit(json_encode(array('code'=>0,'msg'=>'请先配置好自动转账接口信息')));
    $res = $DB->get_row("SELECT * FROM yixi_tixian WHERE id='$id' AND status=0");
    if (!$res) exit(json_encode(array('code'=>0,'msg'=>'记录不存在或状态不是待处理！')));
    if ($res['type'].'' == '1') {
        $type = '3';
    }elseif ($res['type'].'' == '0') {
        $type = '1';
    }else{
        $type = $res['type'];
    }
    $param = ['api_id'=>trim($conf['transfer_id']),'money'=>$res['realmoney'],'payee_type'=>$type,'payee_account'=>$res['account'],'payee_name'=>$res['name'],'realname'=>$conf['transfer_check'],'timestamp'=>TIMESTAMP,'pay_pass'=>$_SESSION["transfer_pass"],];
    $param['sign'] = getSign($param, trim($conf['transfer_key']));
    $data = get_curl('https://api.fcypay.com/transfer', $param);
    $json = json_decode($data,true);
    if (isset($json['code']) && $json['code']) {
        if(!$DB->query("update yixi_tixian set status=1,endtime=NOW() where id='$id'")) {
            exit(json_encode(array('code'=>0,'msg'=>'汇款成功!但是结算记录状态改变失败！')));
        }
        exit(json_encode(array('code'=>1,'msg'=>'汇款成功')));
    }else{
        exit(json_encode(array('code'=>0,'msg'=>isset($json['msg'])?$json['msg']:'对接平台未知错误')));
    }
break;
case 'transfer_config':
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    if(!$conf['user_daifu'])exit(json_encode(array('code'=>0,'msg'=>'请先在分站设置开启代付接口')));
    if (!$_POST['id'] || !$_POST['key'] || !$_POST['pass']) exit(json_encode(['code'=>0,'msg'=>'请填写完整']));
    if ($_POST['check'] !== 'NO_CHECK' && $_POST['check'] !== 'FORCE_CHECK') exit(json_encode(['code'=>0,'msg'=>'验证选项错误']));
    saveSetting('transfer_id',$_POST['id']);
    saveSetting('transfer_key',$_POST['key']);
    saveSetting('transfer_check',$_POST['check']);
    $CACHE->clear();
    $_SESSION["transfer_pass"] = md5($_POST['pass']);
    $_SESSION["transfer"] = true;
    exit(json_encode(['code'=>1,'msg'=>'保存成功']));
break;
default:
    if($islogin!=1)exit('{"code":-1,"msg":"未登录！"}');
    exit('{"code":-4,"msg":"No Act"}');
break;
}