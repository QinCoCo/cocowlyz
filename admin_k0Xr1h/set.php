<?php
/**
 * 系统设置
**/
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
$mod=isset($_GET['mod'])?$_GET['mod']:sysmsg("参数错误",2,'./',true);
if($mod=='account_n' && $_POST['do']=='submit'){
    $user=$_POST['user'];
    $oldpwd=$_POST['oldpwd'];
    $newpwd=$_POST['newpwd'];
    $newpwd2=$_POST['newpwd2'];
    if($user==null)sysmsg('用户名不能为空！',2,'./set.php?mod=account',true);
    saveSetting('admin_user',$user);
    if(!empty($newpwd) && !empty($newpwd2)){
        if($oldpwd!=$conf['admin_pwd'])sysmsg('旧密码不正确！',2,'./set.php?mod=account',true);
        if($newpwd!=$newpwd2)sysmsg('两次输入的密码不一致！',2,'./set.php?mod=account',true);
        saveSetting('admin_pwd',$newpwd);
    }
    $ad=$CACHE->clear();
    if($ad)sysmsg('修改成功！请重新登录',1,'./set.php?mod=account',true);
    else sysmsg('修改失败！<br/>'.$DB->error(),2,'./set.php?mod=account',true);
}elseif($mod=='captcha_n' && $_POST['do']=='submit'){
    $captcha_open=$_POST['captcha_open'];
    $captcha_id=$_POST['captcha_id'];
    $captcha_key=$_POST['captcha_key'];
    $captcha_open_buy=$_POST['captcha_open_buy'];
    $captcha_open_adminlogin=$_POST['captcha_open_adminlogin'];
    $captcha_open_reg=$_POST['captcha_open_reg'];
    $captcha_open_login=$_POST['captcha_open_login'];
    saveSetting('captcha_open',$captcha_open);
    saveSetting('captcha_id',$captcha_id);
    saveSetting('captcha_key',$captcha_key);
    saveSetting('captcha_open_buy',$captcha_open_buy);
    saveSetting('captcha_open_adminlogin',$captcha_open_adminlogin);
    saveSetting('captcha_open_reg',$captcha_open_reg);
    saveSetting('captcha_open_login',$captcha_open_login);
    $ad=$CACHE->clear();
    if($ad)sysmsg('修改成功！',1,'./set.php?mod=captcha',true);
    else sysmsg('修改失败！<br/>'.$DB->error(),2,'./set.php?mod=captcha',true);
}
?>
<?php
if($mod=='site'){
$title='网站信息配置';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                网站信息配置
            </div>
            <div class="card-body">
                <form class="form-horizontal layui-form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">网站名称：</label>
                        <input type="text" name="sitename" value="<?php echo $conf['sitename']; ?>" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">标题栏后辍：</label>
                        <input type="text" name="title" value="<?php echo $conf['title']; ?>" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">关键词：</label>
                        <input type="text" name="keywords" value="<?php echo $conf['keywords']; ?>" class="form-control"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">网站描述：</label>
                        <input type="text" name="description" value="<?php echo $conf['description']; ?>" class="form-control"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">网站关于：</label>
                        <textarea class="form-control" name="orgname" value='关于'><?php echo $conf['orgname'];?></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">备案号：</label>
                        <textarea class="form-control" name="icp" value='备案号'><?php echo $conf['icp'];?></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">客服Q Q：</label>
                        <input type="text" name="kfqq" value="<?php echo $conf['kfqq']; ?>" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">官方QQ群号：</label>
                        <input type="text" name="qunhao" value="<?php echo $conf['qunhao']; ?>" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">QQ群链接：</label>
                        <input type="text" name="Communication" value="<?php echo $conf['Communication']; ?>" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">云端API服务：</label>
                        <select class="form-control" name="cloud_api_open"><option value="0">关闭</option><option value="1">开启</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">提交工单后给站长发信：</label>
                        <select class="form-control" name="workorder_mail"><option value="1">开启</option><option value="0">关闭</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">手机QQ打开网站跳转其他浏览器：</label>
                        <select class="form-control" name="qqjump"><option value="0">关闭</option><option value="1">开启</option></select>
                        <small>此功能没有任何防红效果，理论上直接在QQ发域名推广都会拦截，建议生成防红链接进行访问</small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">授权检测KEY：</label>
                        <input type="text" name="auth_key" value="<?php echo $conf['auth_key']; ?>" class="form-control" placeholder="比如：yixi_key" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_set">保存内容</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
include_once './bottom.php';
?>
<?php
}elseif($mod=='login'){
$title='网站登录配置';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                网站登录配置
            </div>
            <div class="card-body">
                <form class="form-horizontal layui-form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">管理员登录方式：</label>
                        <select class="form-control" name="admin_send_type"><option value="0">图片验证码登录</option><option value="1">邮箱验证码登录</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">管理员QQ一键登录：</label>
                        <select class="form-control" name="admin_qqlogin_open" default="<?php echo $conf['admin_qqlogin_open'];?>"><option value="1">开启</option><option value="0">关闭</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">管理员QQ扫码登录：</label>
                        <select class="form-control" name="admin_qqloginsm_open" default="<?php echo $conf['admin_qqloginsm_open'];?>"><option value="1">开启</option><option value="0">关闭</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">管理员异地登录检测：</label>
                        <select class="form-control" name="admin_remote_login_open" default="<?php echo $conf['admin_remote_login_open'];?>"><option value="1">开启</option><option value="0">关闭</option></select>
                    </div>
                    <div id="set_from" style="<?php echo $conf['admin_remote_login_open']==0?'display:none;':null; ?>">
                        <div class="form-group mb-3">
                            <label for="example-input-normal" style="font-weight: 500">常用登录地点：</label>
                            <input type="text" name="citylist" value="<?php echo $conf['citylist']; ?>" placeholder="多个登录地点，请用,隔开" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">管理员登录邮箱提醒：</label>
                        <select class="form-control" name="admin_login_open" default="<?php echo $conf['admin_login_open'];?>"><option value="1">开启</option><option value="0">关闭</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">用户登录方式：</label>
                        <select class="form-control" name="user_send_type" default="<?php echo $conf['user_send_type']; ?>"><option value="0">图片验证码登录</option><option value="1">邮箱验证码登录</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">用户QQ一键登录：</label>
                        <select class="form-control" name="user_qqlogin_open" default="<?php echo $conf['user_qqlogin_open'];?>"><option value="1">开启</option><option value="0">关闭</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">用户QQ扫码登录：</label>
                        <select class="form-control" name="user_qqloginsm_open" default="<?php echo $conf['user_qqloginsm_open'];?>"><option value="1">开启</option><option value="0">关闭</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">用户异地登录检测：</label>
                        <select class="form-control" name="user_remote_login_open" default="<?php echo $conf['user_remote_login_open'];?>"><option value="1">开启</option><option value="0">关闭</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">用户登录邮箱提醒：</label>
                        <select class="form-control" name="user_login_open" default="<?php echo $conf['user_login_open'];?>"><option value="1">开启</option><option value="0">关闭</option></select>
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_set">保存内容</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
include_once './bottom.php';
?>
<script type="text/javascript">
$("select[name='admin_remote_login_open']").change(function(){
    if($(this).val() > 0){
        $("#set_from").show();
    }else{
        $("#set_from").hide();
    }
});
</script>
<?php
}elseif($mod=='dwz'){
$title='防洪接口配置';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                防洪接口配置
            </div>
            <div class="card-body">
                <form onsubmit="return saveSetting(this)" method="post" class="form-horizontal layui-form" role="form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">防洪接口地址：<span class="badge badge-success-lighten" onclick="checkurl()">检测地址</span></label>
                        <input type="text" name="fanghong_url" value="<?php echo $conf['fanghong_url'];?>" class="form-control" placeholder="不填写则关闭防红链接生成" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_set">保存内容</button>
                </form>
            </div>
            <div class="card-footer">
                <span class="layui-icon layui-icon-tips"></span> 一般防红接口地址为 http://防红网站域名/dwz.php?longurl= 具体可以咨询相应站长<br/><b>没有或不知道的请不要填写！否则会导致推广页面链接无法生成！</b>
            </div>
        </div>
    </div>
</div>
<?php
include_once './bottom.php';
?>
<?php
}elseif($mod=='recharge'){
$title='用户充值配置';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                用户充值配置
            </div>
            <div class="card-body">
                <form onsubmit="return saveSetting(this)" method="post" class="form-horizontal layui-form" role="form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">余额充值开关：</label>
                        <select class="form-control" name="recharge_open" default="<?php echo $conf['recharge_open']; ?>"><option value="1">开启</option><option value="0">关闭</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">最小充值金额：</label>
                        <input type="text" name="recharge_min" value="<?php echo $conf['recharge_min']; ?>" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">最大充值金额：</label>
                        <input type="text" name="recharge_max" value="<?php echo $conf['recharge_max']; ?>" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                       <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">卡密额度单价：</label>
                        <input type="text" name="kmcode_rmb" value="<?php echo $conf['kmcode_rmb']; ?>" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                       <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">代理额度单价：</label>
                        <input type="text" name="dailicode_rmb" value="<?php echo $conf['dailicode_rmb']; ?>" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
					<div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">扭蛋币单价：</label>
                        <input type="text" name="niudan_rmb" value="<?php echo $conf['niudan_rmb']; ?>" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">加款卡充值开关：</label>
                        <select class="form-control" name="recharge_jiakuan" default="<?php echo $conf['recharge_jiakuan']; ?>"><option value="1">开启</option><option value="0">关闭</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">充值返利开关：</label>
                        <select class="form-control" name="recharge_rebate" default="<?php echo $conf['recharge_rebate'];?>"><option value="1">开启</option><option value="0">关闭</option></select>
                    </div>
                    <div id="set_from" style="<?php echo $conf['recharge_rebate']==0?'display:none;':null; ?>">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">充值返利规则：</label>
                        <input type="text" name="recharge_rebate_rule" value="<?php echo $conf['recharge_rebate_rule'];?>" class="form-control"/>
                        <small>例如满10元返2%等可以这样填：10|2,20|3,50|4,100|5 多个用英文逗号,隔开</small>
                    </div>
					</div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_set">保存内容</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
include_once './bottom.php';
?>
<script type="text/javascript">
$("select[name='recharge_rebate']").change(function(){
    if($(this).val() > 0){
        $("#set_from").show();
    }else{
        $("#set_from").hide();
    }
});
</script>
<?php
}elseif($mod=='carousel'){
$title='平台轮播配置';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                平台轮播配置
            </div>
            <div class="card-body">
                <form onsubmit="return saveSetting(this)" method="post" class="form-horizontal layui-form" role="form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">用户后台轮播：</label>
                        <select class="form-control" name="user_carousel_open" default="<?php echo $conf['user_carousel_open']; ?>"><option value="1">开启</option><option value="0">关闭</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">轮播图片链接：</label>
                        <input type="text" name="user_carousel" value="<?php echo $conf['user_carousel']; ?>" class="form-control" lay-verType="tips" lay-verify="required"/>
                        <small>图片外链可使用图床 <a href="https://cloud.0v7.cn/" style="color: cornflowerblue;">点击进入图床(可做图片外链)</a><br><font color="darkmagenta">多张图片可用英文逗号(,)分割!如[图片链接1,图片链接2,图片链接3]</font></small>
                    </div>
                    <div class="form-group mb-3" id="iamge_le" style="display: none;">
                        <div class="image"></div>
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_set">保存内容</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
include_once './bottom.php';
?>
<script type="text/javascript">
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
</script>
<?php
}elseif($mod=='guess'){
$title='竞猜玩法配置';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                竞猜玩法配置
            </div>
            <div class="card-body">
                <form onsubmit="return saveSetting(this)" method="post" class="form-horizontal layui-form" role="form">
                <div class="layui-elem-quote">本玩法需要监控，推荐一小时或者十分钟执行<hr><span class="btn btn-success" onclick="guessjk();">点击可快速执行监控</span></div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">竞猜玩法开关：</label>
                        <select class="form-control" name="guess_open" default="<?php echo $conf['guess_open']; ?>"><option value="1">开启</option><option value="0">关闭</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">最小下注金额：</label>
                        <input type="text" name="guess_xz_min" value="<?php echo $conf['guess_xz_min']; ?>" placeholder="竞猜下注最小可下注金额" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">最大下注金额：</label>
                        <input type="text" name="guess_xz_max" value="<?php echo $conf['guess_xz_max']; ?>" placeholder="竞猜下注最大可下注金额" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">竞猜获奖倍数：</label>
                        <input type="text" name="guess_multiple" value="<?php echo $conf['guess_multiple']; ?>" class="form-control" placeholder="例如下注一元，倍数是2，返利就是2元" lay-verType="tips" lay-verify="required">
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_set">保存内容</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
include_once './bottom.php';
?>
<?php
}elseif($mod=='setuser'){
$title='个人信息配置';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                个人信息配置
            </div>
            <div class="card-body">
                <form onsubmit="return saveSetting(this)" method="post" class="form-horizontal layui-form" role="form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">我的Q Q：</label>
                        <input type="text" name="admin_qq" value="<?php echo $conf['admin_qq']; ?>" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">我的邮箱：</label>
                        <input type="text" name="email" value="<?php echo $conf['email']; ?>" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">我的手机号：</label>
                        <input type="text" name="phone" value="<?php echo $conf['phone']; ?>" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">我的个性签名：</label>
                        <textarea class="form-control" name="gxqm" rows="5" placeholder="填写个性签名"><?php echo $conf['gxqm']?></textarea>
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_set">保存内容</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
include_once './bottom.php';
?>
<?php
}elseif($mod=='qiandao'){
$title='每日签到配置';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                签到模板配置
            </div>
            <div class="card-body">
                <form onsubmit="return saveSetting(this)" method="post" class="form-horizontal layui-form" role="form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">每日签到开关：</label>
                        <select class="form-control" name="qiandao_open" default="<?php echo $conf['qiandao_open']; ?>"><option value="0">关闭</option><option value="1">开启</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">最小签到金额：</label>
                        <input type="text" name="qiandao_min" value="<?php echo $conf['qiandao_min']; ?>" placeholder="乱填后果自负" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">最大签到金额：</label>
                        <input type="text" name="qiandao_max" value="<?php echo $conf['qiandao_max']; ?>" placeholder="乱填后果自负" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                  <div class="form-group mb-3">
                    <label for="example-input-normal" style="font-weight: 500">普通用户最小签到卡密额度：</label>
                        <input type="text" name="qiandaokm_ptmin" value="<?php echo $conf['qiandaokm_ptmin']; ?>" placeholder="乱填后果自负" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">普通用户最大签到卡密额度：</label>
                        <input type="text" name="qiandaokm_ptmax" value="<?php echo $conf['qiandaokm_ptmax']; ?>" placeholder="乱填后果自负" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                                   <label for="example-input-normal" style="font-weight: 500">白银用户最小签到卡密额度：</label>
                        <input type="text" name="qiandaokm_bymin" value="<?php echo $conf['qiandaokm_bymin']; ?>" placeholder="乱填后果自负" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">白银用户最大签到卡密额度：</label>
                        <input type="text" name="qiandaokm_bymax" value="<?php echo $conf['qiandaokm_bymax']; ?>" placeholder="乱填后果自负" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                                   <label for="example-input-normal" style="font-weight: 500">黄金用户最小签到卡密额度：</label>
                        <input type="text" name="qiandaokm_hjmin" value="<?php echo $conf['qiandaokm_hjmin']; ?>" placeholder="乱填后果自负" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">黄金用户最大签到卡密额度：</label>
                        <input type="text" name="qiandaokm_hjmax" value="<?php echo $conf['qiandaokm_hjmax']; ?>" placeholder="乱填后果自负" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                                   <label for="example-input-normal" style="font-weight: 500">钻石用户最小签到卡密额度：</label>
                        <input type="text" name="qiandaokm_zsmin" value="<?php echo $conf['qiandaokm_zsmin']; ?>" placeholder="乱填后果自负" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">钻石用户最大签到卡密额度：</label>
                        <input type="text" name="qiandaokm_zsmax" value="<?php echo $conf['qiandaokm_zsmax']; ?>" placeholder="乱填后果自负" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                                   <label for="example-input-normal" style="font-weight: 500">星耀用户最小签到卡密额度：</label>
                        <input type="text" name="qiandaokm_xymin" value="<?php echo $conf['qiandaokm_xymin']; ?>" placeholder="乱填后果自负" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">星耀用户最大签到卡密额度：</label>
                        <input type="text" name="qiandaokm_xymax" value="<?php echo $conf['qiandaokm_xymax']; ?>" placeholder="乱填后果自负" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                                   <label for="example-input-normal" style="font-weight: 500">荣耀用户最小签到卡密额度：</label>
                        <input type="text" name="qiandaokm_rymin" value="<?php echo $conf['qiandaokm_rymin']; ?>" placeholder="乱填后果自负" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">荣耀用户最大签到卡密额度：</label>
                        <input type="text" name="qiandaokm_rymax" value="<?php echo $conf['qiandaokm_rymax']; ?>" placeholder="乱填后果自负" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">每日签到费用：</label>
                        <input type="text" name="qiandao_money" value="<?php echo $conf['qiandao_money']; ?>" placeholder="乱填后果自负" class="form-control"/>
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_set">保存内容</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
include_once './bottom.php';
?>
<?php
}elseif($mod=='defend'){
$title='防CC模板配置';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                防CC模板配置
            </div>
            <div class="card-body">
                <form onsubmit="return defend()" method="post" class="form-horizontal layui-form" role="form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">CC防护等级：</label>
                        <select class="form-control" name="defendid" default="<?php echo CC_Defender;?>"><option value="0">关闭</option><option value="1">低(推荐)</option><option value="2">中</option><option value="3">高</option></select>
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_set">保存内容</button>
                </form>
            </div>
            <div class="card-footer">
                <span class="layui-icon layui-icon-tips"></span> CC防护说明<br/>
                高：全局使用防CC，会影响网站APP和对接软件的正常使用<br/>
                中：会影响搜索引擎的收录，建议仅在正在受到CC攻击且防御不佳时开启<br/>
                低：用户首次访问进行验证（推荐）<br/>
            </div>
        </div>
    </div>
</div>
<?php
include_once './bottom.php';
?>
<?php
}elseif($mod=='invite'){
$title='邀请返利配置';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                邀请返利配置
            </div>
            <div class="card-body">
                <form onsubmit="return saveSetting(this)" method="post" class="form-horizontal layui-form" role="form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">邀请用户开关：</label>
                        <select class="form-control" name="invite_rebate_open" default="<?php echo $conf['invite_rebate_open']; ?>"><option value="0">关闭</option><option value="1">开启</option></select>
                    </div>
				    <div class="form-group mb-3">
                    <label for="example-input-normal" style="font-weight: 500">邀请时间间隔：</label>
                            <select name="invite_time" class="form-control" default="<?php echo $conf['invite_time']; ?>"><option value="week">一星期</option><option value="month">一个月</option><option value="season">一个季</option><option value="year">一年</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">邀请注册奖励：</label>
                        <input type="text" name="invite_money" value="<?php echo $conf['invite_money']; ?>" placeholder="乱填后果自负" class="form-control" lay-verType="tips" lay-verify="required"/>
                        <small>邀请好友注册，赠送金额</small>
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_set">保存内容</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
include_once './bottom.php';
?>
<?php
}elseif($mod=='chat'){
$title='平台聊天室配置';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                平台聊天室配置
            </div>
            <div class="card-body">
                <form onsubmit="return saveSetting(this)" method="post" class="form-horizontal layui-form" role="form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">平台聊天室开关：</label>
                        <select class="form-control" name="chat_open" default="<?php echo $conf['chat_open']; ?>"><option value="0">关闭</option><option value="1">开启</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">平台聊天室UI：</label>
                        <select class="form-control" name="chat_template" default="<?php echo $conf['chat_template']; ?>"><option value="1">轻聊天气泡UI</option><option value="2">响应式聊天UI</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">发言敏感词检测：</label>
                        <select class="form-control" name="chat_sensitive_open" default="<?php echo $conf['chat_sensitive_open']; ?>"><option value="0">关闭</option><option value="1">开启</option></select>
                    </div>
                    <div id="set_from" style="<?php echo $conf['chat_sensitive_open']==0?'display:none;':null; ?>">
                        <div class="form-group mb-3">
                            <label for="example-input-normal" style="font-weight: 500">聊天发言敏感词：</label>
                            <textarea class="form-control" name="chat_sensitive" rows="5" placeholder="多个违规词之间，请用,隔开"><?php echo $conf['chat_sensitive']; ?></textarea>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">发言频率限制：</label>
                        <select class="form-control" name="chat_limit_open" default="<?php echo $conf['chat_limit_open']; ?>"><option value="0">关闭</option><option value="1">开启</option></select>
                    </div>
                    <div id="set_from1" style="<?php echo $conf['chat_limit_open']==0?'display:none;':null; ?>">
                        <div class="form-group mb-3">
                            <label for="example-input-normal" style="font-weight: 500">发言时间周期(秒)：</label>
                            <input type="text" name="chat_timelimit" value="<?php echo $conf['chat_timelimit']; ?>" class="form-control"/>
                        </div>
                        <div class="form-group mb-3">
                            <label for="example-input-normal" style="font-weight: 500">周期发言限制次数：</label>
                            <div class="col-sm-10"><input type="text" name="chat_iplimit" value="<?php echo $conf['chat_iplimit']; ?>" class="form-control"/>
                            <small>相同IP在1个时间周期内限制发言的次数，站长发言不限制</small>
                        </div>
                    </div>
					</div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_set">保存内容</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
include_once './bottom.php';
?>
<script type="text/javascript">
$("select[name='chat_sensitive_open']").change(function(){
    if($(this).val() > 0){
        $("#set_from").show();
    }else{
        $("#set_from").hide();
    }
});
$("select[name='chat_limit_open']").change(function(){
    if($(this).val() > 0){
        $("#set_from1").show();
    }else{
        $("#set_from1").hide();
    }
});
</script>
<?php
}elseif($mod=='auth'){
$title='授权认证配置';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                授权与认证配置
            </div>
            <div class="card-body">
                <form onsubmit="return saveSetting(this)" method="post" class="form-horizontal layui-form" role="form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">授权到期时间计算方式：</label>
                        <select class="form-control" name="auth_time_type" default="<?php echo $conf['auth_time_type']; ?>"><option value="0">按天数计算</option><option value="1">按月数计算</option><option value="2">按年数计算</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">默认授权时间数量：</label>
                        <input type="text" name="auth_time" value="<?php echo $conf['auth_time']; ?>" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">更换授权次数上限：</label>
                        <input type="text" name="auth_number" value="<?php echo $conf['auth_number']; ?>" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">更换授权次数上限封禁：</label>
                        <select class="form-control" name="auth_number_open" default="<?php echo $conf['auth_number_open']; ?>"><option value="0">关闭</option><option value="1">开启</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">更换认证次数上限：</label>
                        <input type="text" name="pay_number" value="<?php echo $conf['pay_number']; ?>" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">更换认证次数上限封禁：</label>
                        <select class="form-control" name="pay_number_open" default="<?php echo $conf['pay_number_open']; ?>"><option value="0">关闭</option><option value="1">开启</option></select>
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_set">保存内容</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
include_once './bottom.php';
?>
<?php
}elseif($mod=='adver'){
$title='广告系统配置';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                广告系统配置
            </div>
            <div class="card-body">
                <form onsubmit="return saveSetting(this)" method="post" class="form-horizontal layui-form" role="form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">广告系统开关：</label>
                        <select class="form-control" name="adver_open" default="<?php echo $conf['adver_open']; ?>"><option value="0">关闭</option><option value="1">开启</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">广告到期时间计算方式：</label>
                        <select class="form-control" name="adver_time_type" default="<?php echo $conf['adver_time_type']; ?>"><option value="0">按天数计算</option><option value="1">按月数计算</option><option value="2">按年数计算</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">默认广告位到期时间数量：</label>
                        <input type="text" name="adver_time" value="<?php echo $conf['adver_time']; ?>" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">在线购买广告位开关：</label>
                        <select class="form-control" name="adver_buy_open" default="<?php echo $conf['adver_buy_open']; ?>"><option value="0">关闭</option><option value="1">开启</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">广告位销售价格：</label>
                        <input type="text" name="adver_money" value="<?php echo $conf['adver_money']; ?>" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">用户购买广告位后是否审核：</label>
                        <select class="form-control" name="adver_sh" default="<?php echo $conf['adver_sh']; ?>"><option value="0">需要审核</option><option value="1">无需审核</option></select>
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_set">保存内容</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
include_once './bottom.php';
?>
<?php
}elseif($mod=='temple'){
$title='网站模块配置';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                网站模块配置
            </div>
            <div class="card-body">
                <form onsubmit="return saveSetting(this)" method="post" class="form-horizontal layui-form" role="form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">首页查询模板开关：</label>
                        <select class="form-control" name="index_open" default="<?php echo $conf['index_open']; ?>"><option value="1">开启</option><option value="0">关闭</option><option value="2">手机验证码登录</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">在线购买模板开关：</label>
                        <select class="form-control" name="buy_open" default="<?php echo $conf['buy_open'];?>"><option value="1">开启</option><option value="0">关闭</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">卡密注册模板开关：</label>
                        <select class="form-control" name="kmchange_open" default="<?php echo $conf['kmchange_open'];?>"><option value="1">开启</option><option value="0">关闭</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">在线更换模板开关：</label>
                        <select class="form-control" name="change_open" default="<?php echo $conf['change_open'];?>"><option value="1">开启</option><option value="0">关闭</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">源码下载模板开关：</label>
                        <select class="form-control" name="getprogram_open" default="<?php echo $conf['getprogram_open'];?>"><option value="1">开启</option><option value="0">关闭</option></select>
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_set">保存内容</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
include_once './bottom.php';
?>
<?php
}elseif($mod=='Market'){
$title='平台商城配置';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                平台商城配置
            </div>
            <div class="card-body">
                <form onsubmit="return saveSetting(this)" method="post" class="form-horizontal layui-form" role="form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">商城系统开关：</label>
                        <select class="form-control" name="Market_open" default="<?php echo $conf['Market_open']; ?>"><option value="0">关闭</option><option value="1">开启</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">每日推荐开关：</label>
                        <select class="form-control" name="recommend_show_open" default="<?php echo $conf['recommend_show_open']; ?>"><option value="0">关闭</option><option value="1">开启</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">上架商品默认提成百分比：</label>
                        <input type="text" name="shop_tcbl" value="<?php echo $conf['shop_tcbl']; ?>" placeholder="乱填后果自负" class="form-control" lay-verType="tips" lay-verify="required"/>
                        <small>用户添加商品默认的提成百分比</small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">商城系统名称：</label>
                        <input type="text" name="Market_name" value="<?php echo $conf['Market_name']; ?>" placeholder="商城系统的名称" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">商品交易方式：</label>
                        <select class="form-control" name="Market_trade_type" default="<?php echo $conf['Market_trade_type']; ?>"><option value="0">需要认证(管理员审核)</option><option value="1">无需认证(自由交易)</option></select>
                    </div><br/>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">点赞功能开关：</label>
                        <select class="form-control" name="Market_praise_open" default="<?php echo $conf['Market_praise_open']; ?>"><option value="0">关闭</option><option value="1">开启</option></select>
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_set">保存内容</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
include_once './bottom.php';
?>
<?php
}elseif($mod=='user'){
$title='用户相关配置';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                用户相关配置
            </div>
            <div class="card-body">
                <form onsubmit="return saveSetting(this)" method="post" class="form-horizontal layui-form" role="form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">自助余额转换：</label>
                        <select class="form-control" name="user_toMoney_open" default="<?php echo $conf['user_tixian']; ?>"><option value="0">关闭</option><option value="1">开启</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">自助余额转账：</label>
                        <select class="form-control" name="user_zzMoney_open" default="<?php echo $conf['user_skimg']; ?>"><option value="0">关闭</option><option value="1">开启</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">用户注册功能：</label>
                        <select class="form-control" name="user_reg_open" default="<?php echo $conf['user_reg_open']; ?>"><option value="0">关闭</option><option value="1">开启</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">自助升级权限：</label>
                        <select class="form-control" name="user_uppower_open" default="<?php echo $conf['user_uppower_open']; ?>"><option value="0">关闭</option><option value="1">开启</option></select>
                    </div>
					<div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">白银会员价格：</label>
                        <input type="text" name="byprice" value="<?php echo $conf['byprice']; ?>" placeholder="白银会员价格" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
					<div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">黄金会员价格：</label>
                        <input type="text" name="hjprice" value="<?php echo $conf['hjprice']; ?>" placeholder="黄金会员价格" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">钻石会员价格：</label>
                        <input type="text" name="zsprice" value="<?php echo $conf['zsprice']; ?>" placeholder="钻石会员价格" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
					<div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">星耀会员价格：</label>
                        <input type="text" name="xyprice" value="<?php echo $conf['xyprice']; ?>" placeholder="星耀会员价格" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">API密钥生成价格：</label>
                        <input type="text" name="add_key_rmb" value="<?php echo $conf['add_key_rmb']; ?>" placeholder="API密钥生成价格" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">提交工单可上传图片：</label>
                        <select class="form-control" name="workorder_pic" default="<?php echo $conf['workorder_pic']; ?>"><option value="0">关闭</option><option value="1">开启</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">工单可选问题类型：</label>
                        <input type="text" name="workorder_type" value="<?php echo $conf['workorder_type']; ?>" class="form-control" placeholder="多个类型用|隔开"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">用户加款卡生成：</label>
                        <select class="form-control" name="user_jiakuank_open" default="<?php echo $conf['user_jiakuank_open']; ?>"><option value="0">关闭</option><option value="1">开启</option></select>
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_set">保存内容</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
include_once './bottom.php';
?>
<?php
}elseif($mod=='tixianset'){
$title='用户提现配置';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                用户提现配置
            </div>
            <div class="card-body">
                <form onsubmit="return saveSetting(this)" method="post" class="form-group" role="form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">用户提现开关：</label>
                        <select class="form-control" name="user_tixian" default="<?php echo $conf['user_tixian']; ?>"><option value="0">关闭</option><option value="1">开启</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">是否启用二维码：</label>
                        <select class="form-control" name="user_skimg" default="<?php echo $conf['user_skimg']; ?>"><option value="0">关闭</option><option value="1">开启</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">是否启用代付接口提现：</label>
                        <select class="form-control" name="user_daifu" default="<?php echo $conf['user_daifu']; ?>"><option value="0">关闭</option><option value="1">开启</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">提现余额比例：</label>
                        <input type="text" name="tixian_rate" value="<?php echo $conf['tixian_rate']; ?>" placeholder="填写百分数" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">最小提现金额：</label>
                        <input type="text" name="tixian_min" value="<?php echo $conf['tixian_min']; ?>" placeholder="最小提现金额" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">最大提现金额：</label>
                        <input type="text" name="tixian_max" value="<?php echo $conf['tixian_max']; ?>" placeholder="最大提现金额" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_set">保存内容</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
include_once './bottom.php';
?>
<?php
}elseif($mod=='account'){
$title='管理员账号配置';
include_once './header.php';
?>
<style>
.pwd{width:100px;height:20px;line-height:14px;padding-top:2px;}  
.pwd_f{color:#BBBBBB;}  
.pwd_c{background-color:#F3F3F3;border-top:1px solid #D0D0D0;border-bottom:1px solid #D0D0D0;border-left:1px solid #D0D0D0;}
.pwd_Weak_c{background-color:#FF4545;border-top:1px solid #BB2B2B;border-bottom:1px solid #BB2B2B;border-left:1px solid #BB2B2B;}  
.pwd_Medium_c{background-color:#FFD35E;border-top:1px solid #E9AE10;border-bottom:1px solid #E9AE10;border-left:1px solid #E9AE10;}  
.pwd_Strong_c{background-color:#3ABB1C;border-top:1px solid #267A12;border-bottom:1px solid #267A12;border-left:1px solid #267A12;}  
.pwd_c_r{border-right:1px solid #D0D0D0;}  .pwd_Weak_c_r{border-right:1px solid #BB2B2B;}  .pwd_Medium_c_r{border-right:1px solid #E9AE10;}  
.pwd_Strong_c_r{border-right:1px solid #267A12;} 
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                管理员账号配置
            </div>
            <div class="card-body">
                <form action="./set.php?mod=account_n" method="post" class="form-horizontal layui-form" role="form"><input type="hidden" name="do" value="submit"/>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">用户名：</label>
                        <input type="text" name="user" value="<?php echo $conf['admin_user']; ?>" class="form-control" lay-verType="tips" lay-verify="required"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">旧密码：</label>
                        <input type="password" name="oldpwd" class="form-control" placeholder="请输入当前的管理员密码"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">新密码：</label>
                        <input type="password" name="newpwd" id="newpass" onKeyUp="CheckIntensity(this.value)" value="" class="form-control" placeholder="不修改请留空"/>
                    </div>
                    <div class="text-center">
                        <table border="0" cellpadding="0" cellspacing="0">
                            <tr align="center">
                                <td id="pwd_Weak" class="pwd pwd_c"> </td>
                                <td id="pwd_Medium" class="pwd pwd_c pwd_f">无</td>
                                <td id="pwd_Strong" class="pwd pwd_c pwd_c_r"> </td>
                            </tr>
                        </table>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">重输密码：</label>
                        <input type="password" name="newpwd2" class="form-control" placeholder="不修改请留空"/>
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_account">保存内容</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
include_once './bottom.php';
?>
<script type="text/javascript">
function CheckIntensity(pwd) {
    var Mcolor, Wcolor, Scolor, Color_Html;       
    var m = 0;      
    //匹配数字
    if (/\d+/.test(pwd)) {
        debugger;
        m++;
    };
    //匹配字母
    if (/[A-Za-z]+/.test(pwd)) {     
        m++;
    };
    //匹配除数字字母外的特殊符号
    if (/[^0-9a-zA-Z]+/.test(pwd)) {        
        m++;
    };
    if (pwd.length <= 6) { m = 1; }
    if (pwd.length <= 0) { m = 0; }       
    switch (m) {
        case 1:
        Wcolor = "pwd pwd_Weak_c";
        Mcolor = "pwd pwd_c";
        Scolor = "pwd pwd_c pwd_c_r";
        Color_Html = "弱";
        break;
        case 2:
        Wcolor = "pwd pwd_Medium_c";
        Mcolor = "pwd pwd_Medium_c";
        Scolor = "pwd pwd_c pwd_c_r";
        Color_Html = "中";
        break;
        case 3:
        Wcolor = "pwd pwd_Strong_c";
        Mcolor = "pwd pwd_Strong_c";
        Scolor = "pwd pwd_Strong_c pwd_Strong_c_r";
        Color_Html = "强";
        break;
        default:
        Wcolor = "pwd pwd_c";
        Mcolor = "pwd pwd_c pwd_f";
        Scolor = "pwd pwd_c pwd_c_r";
        Color_Html = "无";
        break;
    }
    document.getElementById('pwd_Weak').className = Wcolor;
    document.getElementById('pwd_Medium').className = Mcolor;
    document.getElementById('pwd_Strong').className = Scolor;
    document.getElementById('pwd_Medium').innerHTML = Color_Html;
}
</script>
<?php
}elseif($mod=='pay'){
$title='支付接口配置';
include_once './header.php';
if ($conf['alipay_api']!=3) {
    $display='display:none;';
}
if ($conf['alipay_api']!=1) {
    $display1='display:none;';
}
if ($conf['alipay2_api']!=1) {
    $display2='display:none;';
}
if ($conf['qqpay_api']!=1) {
    $display3='display:none;';
}
if ($conf['wxpay_api']!=1 || $conf['wxpay_api']!=3) {
    //$display4='display:none;';
}
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                支付接口配置
            </div>
            <div class="card-body">
                <form onsubmit="return saveSetting(this)" method="post" class="form-horizontal layui-form">
                    <ul class="nav nav-tabs nav-justified nav-bordered mb-3">
                        <li class="nav-item">
                            <a href="#pay" data-toggle="tab" aria-expanded="false" class="nav-link active">
                                支付接口配置
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#pays" data-toggle="tab" aria-expanded="false" class="nav-link">
                                支付多个接口
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane show active" id="pay">
                            <div class="form-group mb-3">
                                <label for="example-input-normal" style="font-weight: 500">支付宝即时到账：</label>
                                <select class="form-control" name="alipay_api" default="<?php echo $conf['alipay_api'];?>"><option value="0">关闭</option><option value="1">支付宝官方即时到账接口</option><option value="3">支付宝当面付扫码支付</option><option value="2">易支付免签约接口</option><option value="5">码支付免签约接口</option></select>
                                <small id="payapi_06" style="<?php echo $display;?>"><font color="green">*支付宝当面付接口配置请修改other/f2fpay/config.php</font></small>
                            </div>
                            <div id="payapi_01" style="<?php echo $display1;?>">
                                <div class="form-group mb-3">
                                    <label for="example-input-normal" style="font-weight: 500">合作者身份(PID)：</label>
                                    <input type="text" name="alipay_pid" class="form-control" value="<?php echo $conf['alipay_pid'];?>">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="example-input-normal" style="font-weight: 500">收款支付宝账户(ACCOUNT)：</label>
                                    <input type="text" name="alipay_account" class="form-control" value="<?php echo $conf['alipay_account'];?>">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="example-input-normal" style="font-weight: 500">安全校验码(Key)：</label>
                                    <input type="text" name="alipay_key" class="form-control" value="<?php echo $conf['alipay_key'];?>">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="example-input-normal" style="font-weight: 500">支付宝手机网站支付：</label>
                                    <select class="form-control" name="alipay2_api" default="<?php echo $conf['alipay2_api'];?>"><option value="0">关闭</option><option value="1">支付宝手机网站支付接口</option></select>
                                    <small id="payapi_02" style="<?php echo $display2;?>">相关信息与以上支付宝即时到账接口一致，开启前请确保已开通支付宝手机支付，否则会导致手机用户无法支付！</small>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="example-input-normal" style="font-weight: 500">QQ钱包支付接口：</label>
                                <select class="form-control" name="qqpay_api" default="<?php echo $conf['qqpay_api'];?>"><option value="0">关闭</option><option value="1">QQ钱包官方支付接口</option><option value="2">易支付免签约接口</option><option value="5">码支付免签约接口</option></select>
                            </div>
                            <div id="payapi_05" style="<?php echo $display4;?>">
                                <div class="form-group mb-3">
                                    <label for="example-input-normal" style="font-weight: 500">MCH_ID：</label>
                                    <input type="text" name="qqpay_mchid" value="<?php echo $conf['qqpay_mchid']; ?>" class="form-control"/>
                                    <small>QQ钱包商户号</small>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="example-input-normal" style="font-weight: 500">MCH_KEY：</label>
                                    <input type="text" name="qqpay_mchkey" value="<?php echo $conf['qqpay_mchkey']; ?>" class="form-control"/>
                                    <small>QQ钱包商户平台(http://qpay.qq.com/)获取</small>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="example-input-normal" style="font-weight: 500">微信支付接口：</label>
                                <select class="form-control" name="wxpay_api" default="<?php echo $conf['wxpay_api'];?>"><option value="0">关闭</option><option value="1">微信官方扫码+公众号支付接口</option><option value="3">微信官方扫码+H5支付接口</option><option value="2">易支付免签约接口</option><option value="5">码支付免签约接口</option></select>
                            </div>
                            <div id="payapi_04" style="<?php echo $display4;?>">
                                <div class="form-group mb-3">
                                    <label for="example-input-normal" style="font-weight: 500">APPID：</label>
                                    <input type="text" name="wxpay_appid" value="<?php echo $conf['wxpay_appid']; ?>" class="form-control"/>
                                    <small>绑定支付的APPID（必须配置，开户邮件中可查看）</small>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="example-input-normal" style="font-weight: 500">MCHID：</label>
                                    <input type="text" name="wxpay_mchid" value="<?php echo $conf['wxpay_mchid']; ?>" class="form-control"/>
                                    <small>商户号（必须配置，开户邮件中可查看）</small>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="example-input-normal" style="font-weight: 500">KEY：</label>
                                    <input type="text" name="wxpay_key" value="<?php echo $conf['wxpay_key']; ?>" class="form-control"/>
                                    <small>商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置） <br>设置地址：https://pay.weixin.qq.com/index.php/account/api_cert</small>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="example-input-normal" style="font-weight: 500">APPSECRET：</label>
                                    <input type="text" name="wxpay_appsecret" value="<?php echo $conf['wxpay_appsecret']; ?>" class="form-control"/>
                                    <small>公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置）<br>获取地址：https://mp.weixin.qq.com/advanced/advanced?action=dev&t=advanced/dev&token=2005451881&lang=zh_CN</small>
                                </div>
                            </div>
                            <?php if (($conf['alipay_api']==2 || $conf['tenpay_api']==2) || $conf['qqpay_api']==2 || $conf['wxpay_api']==2) {?>
                            <div class="form-group mb-3">
                                <label for="example-input-normal" style="font-weight: 500">易支付接口网站：</label>
                                <input type="text" name="epay_url" class="form-control" value="<?php echo $conf['epay_url'];?>" placeholder="http://www.qq.com/">
                            </div>
                            <div class="form-group mb-3">
                                <label for="example-input-normal" style="font-weight: 500">易支付商户ID：</label>
                                <input type="text" name="epay_pid" class="form-control" value="<?php echo $conf['epay_pid'];?>">
                            </div>
                            <div class="form-group mb-3">
                                <label for="example-input-normal" style="font-weight: 500">易支付商户密钥：</label>
                                <input type="text" name="epay_key" class="form-control" value="<?php echo $conf['epay_key'];?>">
                            </div>
                            <?php }?>
                            <?php if ($conf['alipay_api']==5 || $conf['qqpay_api']==5 || $conf['wxpay_api']==5) {?>
                            <div class="form-group mb-3">
                                <label for="example-input-normal" style="font-weight: 500">码支付ID：</label>
                                <input type="text" name="codepay_id" class="form-control" value="<?php echo $conf['codepay_id'];?>">
                            </div>
                            <div class="form-group mb-3">
                                <label for="example-input-normal" style="font-weight: 500">码支付通信密钥：</label>
                                <input type="text" name="codepay_key" class="form-control" value="<?php echo $conf['codepay_key'];?>">
                                <small><font color="green">codepay.fateqq.com 码支付支付宝和QQ需要挂电脑软件，微信不需要挂软件</font></small>
                            </div>
                            <?php }?>
                        </div>
                        <div class="tab-pane" id="pays">
                            <div class="layui-elem-quote">使用本功能可在一定程度上避免被某支付商圈钱，望大家在选择某支付对接商时多多观察避免产生利润亏损</div>
                            <div class="form-group mb-3">
                                <label for="example-input-normal" style="font-weight: 500">开启多个独立支付：</label>
                                <select class="form-control" name="epays_open" default="<?php echo $conf['epays_open'];?>"><option value="0">关闭</option><option value="1">开启</option></select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="example-input-normal" style="font-weight: 500">QQ钱包接口账号：</label>
                                <input type="text" name="qqpay_pid" value="<?php echo $conf['qqpay_pid'];?>" class="form-control" placeholder="QQ钱包支付账号"/>
                            </div>
                            <div class="form-group mb-3">
                                <label for="example-input-normal" style="font-weight: 500">QQ钱包接口密码：</label>
                                <input type="text" name="qqpay_key" value="<?php echo $conf['qqpay_key'];?>" class="form-control" placeholder="QQ钱包接口密码"/>
                            </div>
                            <div class="form-group mb-3">
                                <label for="example-input-normal" style="font-weight: 500">QQ钱包接口地址：</label>
                                <input type="text" name="qqpay_url" value="<?php echo $conf['qqpay_url'];?>" class="form-control" placeholder="QQ钱包接口地址"/>
                                <small>请填写单独对接QQ钱包的完整易支付接口地址，此QQ钱包配置不完整时将使用默认接口</small>
                            </div>
                            <div class="form-group mb-3">
                                <label for="example-input-normal" style="font-weight: 500">支付宝接口账号：</label>
                                <input type="text" name="alipay_pid" value="<?php echo $conf['alipay_pid'];?>" class="form-control" placeholder="支付宝支付账号"/>
                            </div>
                            <div class="form-group mb-3">
                                <label for="example-input-normal" style="font-weight: 500">支付宝接口密码：</label>
                                <input type="text" name="alipay_key" value="<?php echo $conf['alipay_key'];?>" class="form-control" placeholder="支付宝接口密码"/>
                            </div>
                            <div class="form-group mb-3">
                                <label for="example-input-normal" style="font-weight: 500">支付宝接口地址：</label>
                                <input type="text" name="alipay_url" value="<?php echo $conf['alipay_url'];?>" class="form-control" placeholder="支付宝接口地址"/>
                                <small>请填写单独对接支付宝的完整易支付接口地址，此支付宝配置不完整时将使用默认接口</small>
                            </div>
                            <div class="form-group mb-3">
                                <label for="example-input-normal" style="font-weight: 500">微信支付接口账号：</label>
                                <input type="text" name="wxpay_pid" value="<?php echo $conf['wxpay_pid'];?>" class="form-control" placeholder="微信支付支付账号"/>
                            </div>
                            <div class="form-group mb-3">
                                <label for="example-input-normal" style="font-weight: 500">微信支付接口密码：</label>
                                <input type="text" name="wxpay_key" value="<?php echo $conf['wxpay_key'];?>" class="form-control" placeholder="微信支付接口密码"/>
                            </div>
                            <div class="form-group mb-3">
                                <label for="example-input-normal" style="font-weight: 500">微信支付接口地址：</label>
                                <input type="text" name="wxpay_url" value="<?php echo $conf['wxpay_url'];?>" class="form-control" placeholder="微信支付接口地址"/>
                                <small>请填写单独对接微信支付的完整易支付接口地址，此微信支付配置不完整时将使用默认接口</small>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_set">保存内容</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
include_once './bottom.php';
?>
<script type="text/javascript">
$("select[name='alipay_api']").change(function(){
    if($(this).val() == 1){
        $("#payapi_01").css("display","inherit");
        $("#payapi_06").css("display","none");
    }else if($(this).val() == 3){
        $("#payapi_01").css("display","none");
        $("#payapi_06").css("display","inherit");
    }else{
        $("#payapi_01").css("display","none");
        $("#payapi_06").css("display","none");
    }
});
$("select[name='wxpay_api']").change(function(){
    if($(this).val() == 1 || $(this).val() == 3){
        $("#payapi_04").css("display","inherit");
    }else{
        $("#payapi_04").css("display","none");
    }
});
$("select[name='qqpay_api']").change(function(){
    if($(this).val() == 1){
        $("#payapi_05").css("display","inherit");
    }else{
        $("#payapi_05").css("display","none");
    }
});
$("select[name='alipay2_api']").change(function(){
    if($(this).val() == 1){
        $("#payapi_02").css("display","inherit");
    }else{
        $("#payapi_02").css("display","none");
    }
});
</script>
<?php
}elseif($mod=='template'){
$title='网站模板配置';
include_once './header.php';
$mblist=Template::getList();
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                首页模板配置
            </div>
            <div class="card-body">
    <?php if ($conf['template']) {?>
    <h4>当前使用模板：</h4>
    <div class="row text-center">
        <div class="col-xs-6 col-sm-4">
        <img class="img-responsive img-thumbnail img-rounded" src="/template/<?php echo $conf['template']?>/<?php echo $conf['template']?>.png" onerror="this.src='/assets/img/NoImg.png'">
        </div>
        <div class="col-xs-6 col-sm-4">
        <p>模板名称：<font color="red"><?php echo $conf['template']?></font></p>
        <p>适应版本：<font color="orange">1.0＋</font></p>
        <p>模板作者：<font color="blue">可可</font></p>
        </div>
    </div>
    <hr/>
    <?php }?>
    <h4>更换模板：</h4>
    <div class="layui-row">
    <?php foreach($mblist as $template){?>
    <?php if ($conf['template'] == $template) {?>
    <div class="layui-col-xs6 layui-col-sm4 layui-col-md3 layui-anim layui-anim-upbit" style="box-shadow: 3px 3px 8px 1px whitesmoke;border-radius: 0.5rem;">
        <div class="layui-card" style="box-shadow: none;">
            <div class="layui-card-header layui-elip" align="center" title="<?php echo $template?>模板" onclick="layer.msg('<?php echo $template?>模板')"><?php echo $template?>模板</div>
            <div class="layui-card-body image_body"><img class="img-responsive img-thumbnail img-rounded" src="/template/<?php echo $template?>/<?php echo $template?>.png" onerror="this.src='/assets/img/NoImg.png'" title="点击更换到该模板"/></div>
        </div>
    </div>
    <?php } else {?>
    <a href="javascript:changeTemplate('<?php echo $template?>')">
    <div class="layui-col-xs6 layui-col-sm4 layui-col-md3 layui-anim layui-anim-upbit" style="box-shadow: 3px 3px 8px 1px whitesmoke;border-radius: 0.5rem;">
        <div class="layui-card" style="box-shadow: none;">
            <div class="layui-card-header layui-elip" align="center" title="<?php echo $template?>模板" onclick="layer.msg('<?php echo $template?>模板')"><?php echo $template?>模板</div>
            <div class="layui-card-body image_body"><img class="img-responsive img-thumbnail img-rounded" src="/template/<?php echo $template?>/<?php echo $template?>.png" onerror="this.src='/assets/img/NoImg.png'" title="点击更换到该模板"/></div>
        </div>
    </div>
    </a>
    <?php }?>
    <?php }?>
</div>
    </div>
</div>
</div>
</div>
<?php
include_once './bottom.php';
?>
<?php
}elseif($mod=='captcha'){
$title='验证与IP配置';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                滑块验证码设置
            </div>
            <div class="card-body">
                <form action="./set.php?mod=captcha_n" method="post" class="form-horizontal layui-form" role="form"><input type="hidden" name="do" value="submit"/>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">验证码选择：</label>
                        <select class="form-control" name="captcha_open" default="<?php echo $conf['captcha_open'];?>"><option value="0">关闭</option><option value="1">极限滑动验证码</option><option value="2">顶象滑动验证码</option><option value="3">VAPTCHA手势验证码</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">接口ID：</label>
                        <input type="text" name="captcha_id" value="<?php echo $conf['captcha_id'];?>" class="form-control"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">接口KEY：</label>
                        <input type="text" name="captcha_key" value="<?php echo $conf['captcha_key'];?>" class="form-control"/>
                    </div>
                    <label for="example-input-normal" style="font-weight: 500">开启验证背景：</label>
                    <div class="mb-3">
                        <div class="custom-control custom-checkbox">
                            <input name="captcha_open_buy" type="checkbox" value="1"<?php echo $conf['captcha_open_buy'] == 1 ? ' checked' : '';?> class="custom-control-input" id="captcha_open_buy">
                            <label class="custom-control-label" for="captcha_open_buy">免费购买</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input name="captcha_open_adminlogin" type="checkbox" value="1"<?php echo $conf['captcha_open_adminlogin'] == 1 ? ' checked' : '';?> class="custom-control-input" id="captcha_open_adminlogin">
                            <label class="custom-control-label" for="captcha_open_adminlogin">管理员登录</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input name="captcha_open_reg" type="checkbox" value="1"<?php echo $conf['captcha_open_reg'] == 1 ? ' checked' : '';?> class="custom-control-input" id="captcha_open_reg">
                            <label class="custom-control-label" for="captcha_open_reg">用户注册</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input name="captcha_open_login" type="checkbox" value="1"<?php echo $conf['captcha_open_login'] == 1 ? ' checked' : '';?> class="custom-control-input" id="captcha_open_login">
                            <label class="custom-control-label" for="captcha_open_login">用户登录</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_set">保存内容</button>
                </form>
            </div>
            <div class="card-footer">
                <span class="layui-icon layui-icon-tips"></span>
                极限验证码：<a href="https://www.geetest.com/Register" rel="noreferrer" target="_blank">点击进入</a>（免费版每小时限流，需人工审核）<br/>
                顶象验证码：<a href="https://www.dingxiang-inc.com/business/captcha" rel="noreferrer" target="_blank">点击进入</a>（收费的，可免费试用）<br/>
                VAPTCHA手势验证码：<a href="https://www.vaptcha.com/" rel="noreferrer" target="_blank">点击进入</a> （目前完全免费）选择极限验证码，然后ID和KEY留空保存，即可直接免费使用公共接口(测试中)
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                用户IP地址获取设置
            </div>
            <div class="card-body">
                <form onsubmit="return saveSetting(this)" method="post" class="form-horizontal layui-form" role="form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">用户IP地址获取方式：</label>
                        <select class="form-control" name="ip_type" default="<?php echo $conf['ip_type']?>"><option value="0">0_X_FORWARDED_FOR</option><option value="1">1_X_REAL_IP</option><option value="2">2_REMOTE_ADDR</option></select>
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_set">保存内容</button>
                </form>
            </div>
            <div class="card-footer">
                <span class="layui-icon layui-icon-tips"></span>
                此功能设置用于防止用户伪造IP请求。<br/>
                X_FORWARDED_FOR：之前的获取真实IP方式，极易被伪造IP<br/>
                X_REAL_IP：在网站使用CDN的情况下选择此项，在不使用CDN的情况下也会被伪造<br/>
                REMOTE_ADDR：直接获取真实请求IP，无法被伪造，但可能获取到的是CDN节点IP<br/>
                <b>你可以从中选择一个能显示你真实地址的IP，优先选下方的选项。</b>
            </div>
        </div>
    </div>
</div>
<?php
include_once './bottom.php';
?>
<script type="text/javascript">
$(document).ready(function(){
    $.ajax({
        type : "GET",
        url : "ajax.php?act=iptype",
        dataType : 'json',
        async: true,
        success : function(data) {
            $("select[name='ip_type']").empty();
            var defaultv = $("select[name='ip_type']").attr('default');
            $.each(data, function(k, item){
                $("select[name='ip_type']").append('<option value="'+k+'" '+(defaultv==k?'selected':'')+'>'+ item.name +' - '+ item.ip +' '+ item.city +'</option>');
            })
        }
    });
})
</script>
<?php
}elseif($mod=='gonggao'){
$title='公告排版配置';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                其他公告与排版设置
            </div>
            <div class="card-body">
                <form onsubmit="return saveSetting(this)" method="post" class="form-horizontal layui-form" role="form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">首页弹出公告：</label>
                        <textarea class="form-control" name="index_notice" rows="5" placeholder="不填写则不显示弹出公告"><?php echo $conf['index_notice']?></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">在线购买弹出公告：</label>
                        <textarea class="form-control" name="buy_notice" rows="5" placeholder="不填写则不显示弹出公告"><?php echo $conf['buy_notice']?></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">在线兑换弹出公告：</label>
                        <textarea class="form-control" name="kmchange_notice" rows="5" placeholder="不填写则不显示弹出公告"><?php echo $conf['kmchange_notice']?></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">在线更换弹出公告：</label>
                        <textarea class="form-control" name="change_notice" rows="5" placeholder="不填写则不显示弹出公告"><?php echo $conf['change_notice']?></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">源码下载弹出公告：</label>
                        <textarea class="form-control" name="getprogram_notice" rows="5" placeholder="不填写则不显示弹出公告"><?php echo $conf['getprogram_notice']?></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">用户注册弹出公告：</label>
                        <textarea class="form-control" name="reg_notice" rows="5" placeholder="不填写则不显示弹出公告"><?php echo $conf['reg_notice']?></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">用户后台公告：</label>
                        <textarea class="form-control" name="user_notice" rows="5" placeholder="不填写则不显示弹出公告"><?php echo $conf['user_notice']?></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">首页底部排版：</label>
                        <textarea class="form-control" name="footer" rows="3" placeholder="可填写备案号等"><?php echo $conf['footer']?></textarea>
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_set">保存内容</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
include_once './bottom.php';
?>
<?php
}elseif($mod=='mail'){
$title='发信邮箱配置';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                发信邮箱配置
            </div>
            <div class="card-body">
                <form onsubmit="return saveSetting(this)" method="post" class="form-horizontal layui-form" role="form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">SMTP服务器：</label>
                        <input type="text" name="mail_smtp" value="<?php echo $conf['mail_smtp']; ?>" class="form-control"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">SMTP端口：</label>
                        <input type="text" name="mail_port" value="<?php echo $conf['mail_port']; ?>" class="form-control"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">邮箱账号：</label>
                        <input type="text" name="mail_name" value="<?php echo $conf['mail_name']; ?>" class="form-control"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">邮箱密码(授权码)：</label>
                        <input type="text" name="mail_pwd" value="<?php echo $conf['mail_pwd']; ?>" class="form-control"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">邮箱发信模板：</label>
                        <select class="form-control" name="email_temp" default="<?php echo $conf['email_temp']; ?>"><option value="1">HTML_EMAIL模板1</option><option value="2">HTML_EMAIL模板2</option><option value="3">HTML_EMAIL模板3</option><option value="4">HTML_EMAIL模板4</option><option value="5">纯文本_EMAIL模板</option></select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">收信邮箱：</label>
                        <input type="text" name="mail_recv" value="<?php echo $conf['mail_recv']; ?>" class="form-control" placeholder="不填默认为发信邮箱"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">每日同一邮箱验证码发送次数：</label>
                        <input type="text" name="mail_count" value="<?php echo $conf['mail_count']; ?>" class="form-control"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">每日同一IP邮箱验证码发送次数：</label>
                        <input type="text" name="mail_countday" value="<?php echo $conf['mail_countday']; ?>" class="form-control"/>
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_set">保存内容</button>
                </form>
            </div>
            <div class="card-footer">
                <span class="layui-icon layui-icon-tips"></span>
                <?php if($conf['mail_name']){?>[<span onclick="mailtest();">给 <?php echo $conf['mail_recv']?$conf['mail_recv']:$conf['mail_name']?> 发一封测试邮件</span>]<hr><?php }?>
                使用QQ邮箱，SMTP服务器smtp.qq.com，端口465，密码不是QQ密码也不是邮箱独立密码，是QQ邮箱设置界面生成的<a href="https://service.mail.qq.com/cgi-bin/help?subtype=1&&no=1001256&&id=28"  target="_blank" rel="noreferrer">授权码</a>。
            </div>
        </div>
    </div>
</div>
<?php
include_once './bottom.php';
?>
<?php
}
?>
<script>
layui.use(['form'], function () {
    var form = layui.form;
    form.on('submit(submit_set)', function (data) {
        var ii = layer.msg('正在修改中,请稍后...', {icon: 16, time: 10 * 1000});
        $.post('ajax.php?act=set', data.field, function (data) {
            layer.close(ii);
            if(data.code == 0){
                layer.msg('设置保存成功！', {
                    icon: 6,
                    end: function (layero, index) {
                        window.location.reload();
                    }
                });
            } else {
                layer.msg(data.msg, {icon: 5});
            }
        });
        return false;
    });
});
var items = $("select[default]");
for (i = 0; i < items.length; i++) {
    $(items[i]).val($(items[i]).attr("default")||0);
}
function saveSetting(obj){
    var ii = layer.msg('正在修改中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : 'POST',
        url : 'ajax.php?act=set',
        data : $(obj).serialize(),
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if(data.code == 0){
                layer.msg('设置保存成功！', {
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
}
function changeTemplate(template){
    var ii = layer.msg('正在更换中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : 'POST',
        url : 'ajax.php?act=set',
        data : {template:template},
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if(data.code == 0){
                layer.msg('更换模板成功！', {
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
}
function defend(){
    var defendid = $("input[name='defendid']").val();
    var ii = layer.msg('正在修改中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : 'POST',
        url : 'ajax.php?act=defend',
        data : {defendid:defendid},
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if(data.code == 0){
                layer.msg('防CC模板配置修改', {
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
}
function mailtest(){
    var ii = layer.msg('正在发送中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : 'POST',
        url : 'ajax.php?act=mailtest',
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if(data.code == 0){
                layer.msg(data.msg, {icon: 6});
            }else{
                layer.msg(data.msg, {icon: 5})
            }
        },
        error:function(data){
            layer.msg('服务器错误', {icon: 5});
            return false;
        }
    });
    return false;
}
function guessjk(){
    var ii = layer.msg('正在更新中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : 'POST',
        url : 'ajax.php?act=guess_jk',
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if(data.code == 0){
                layer.msg(data.msg, {icon: 6});
            }else{
                layer.msg(data.msg, {icon: 5})
            }
        },
        error:function(data){
            layer.msg('服务器错误', {icon: 5});
            return false;
        }
    });
    return false;
}
function checkurl(){
    var url = $("input[name='fanghong_url']").val();
    if(url.indexOf('http')>=0 && url.indexOf('=')>=0){
        var ii = layer.msg('正在检测中,请稍后...', {icon: 16, time: 10 * 1000});
        $.ajax({
            type : "POST",
            url : "ajax.php?act=checkdwz",
            data : {url:url},
            dataType : 'json',
            success : function(data) {
                layer.close(ii);
                if(data.code == 1){
                    layer.msg('检测正常', {icon: 6});
                }else if(data.code == 2){
                    layer.msg('链接无法访问或返回内容不是json格式', {icon: 5});
                }else{
                    layer.msg('该链接无法访问', {icon: 5});
                }
            } ,
            error:function(data){
                layer.close(ii);
                layer.msg('目标URL连接超时', {icon: 5});
                return false;
            }
        });
    }else{
        layer.msg('链接地址错误', {icon: 5});
    }
}
</script>