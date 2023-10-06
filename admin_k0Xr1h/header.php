<?php
@header('Content-Type: text/html; charset=UTF-8');
$rs=$DB->query("SELECT * FROM yixi_program WHERE 1 order by id desc");
while($res = $DB->fetch($rs))
{
    $select .= '<option value="'.$res['id'].'">'.$res['name'].'</option>';
}
if($conf['admin_qq']){
    $admin_qq=$conf['admin_qq'];
}else{
    $admin_qq=$conf['kfqq'];
}
$gdcount=$DB->count("SELECT count(*) FROM yixi_workorder WHERE status=0");
$txcount=$DB->count("SELECT count(*) FROM yixi_tixian WHERE status=0");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title><?php echo $title;?> [平台站长的后台]</title>
    <meta name="keywords" content="<?= $conf['keywords'] ?>">
    <meta name="description" content="<?= $conf['description'] ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description"/>
    <meta content="Coderthemes" name="author"/>
    <!-- App favicon -->
    <link rel="shortcut icon" href="../assets/favicon.ico" type="image/x-icon"/>
    <!-- third party css -->
    <link href="../assets/css/vendor/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="../assets/layui/css/layui.css"/>
    <!-- third party css end -->
    <!-- App css -->
    <link href="../assets/css/icons.min.css" rel="stylesheet" type="text/css"/>
    <link href="../assets/css/app.min.css" rel="stylesheet" type="text/css"/>
</head>
<style>
    .card-title {font-weight: 300;};
</style>
<body>
<script>
function toggleFullScreen(){
    if (!document.fullscreenElement &&!document.mozFullScreenElement && !document.webkitFullscreenElement) {
        if (document.documentElement.requestFullscreen) {
            document.documentElement.requestFullscreen();
        } else if (document.documentElement.mozRequestFullScreen) {
            document.documentElement.mozRequestFullScreen();
        } else if (document.documentElement.webkitRequestFullscreen) {
            document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
        }
    } else {
        if (document.cancelFullScreen) {
            document.cancelFullScreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.webkitCancelFullScreen) {
            document.webkitCancelFullScreen();
        }
    }
};
</script>
<!-- Begin page -->
<div class="wrapper">
    <div class="left-side-menu" style="z-index: 10">
        <div class="slimscroll-menu" id="left-side-menu-container">
            <!-- LOGO -->
            <!--- Sidemenu -->
            <ul class="metismenu side-nav">
                <li class="side-nav-title side-nav-item" style="font-size: 2em;color: white;font-weight: 300">管理中心
                </li>
                <li class="side-nav-item">
                    <a href="index.php" class="side-nav-link">
                        <i class="layui-icon layui-icon-home"></i>
                        <span> 用户首页 </span>
                    </a>
                </li>
                <li class="side-nav-item">
                    <a href="javascript: void(0);" class="side-nav-link">
                        <i class="layui-icon layui-icon-table"></i>
                        <span> 明细管理 </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="side-nav-second-level" aria-expanded="false">
                        <li><a href="record.php">收支明细</a></li>
                        <li><a href="paylogs.php">支付记录</a><li>
                        <li><a href="codelogs.php">发信日志</a><li>
                    </ul>
                </li>
                <li class="side-nav-item">
                    <a href="javascript: void(0);" class="side-nav-link">
                        <i class="layui-icon layui-icon-rmb"></i>
                        <span> 提现管理  <?php echo ($txcount == 0 ? '' : '<span class="layui-badge-dot"></span>') ?></span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="side-nav-second-level" aria-expanded="false">
                        <li><a href="tixian.php">余额提现 <?php echo ($txcount == 0 ? '' : '<span class="layui-badge layui-bg-red">' . $txcount . '</span>') ?></a><li>
                        <li><a href="set.php?mod=tixianset">提现配置</a><li>
                    </ul>
                </li>
                <li class="side-nav-item">
                    <a href="javascript: void(0);" class="side-nav-link">
                        <i class="layui-icon layui-icon-user"></i>
                        <span> 用户管理 </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="side-nav-second-level" aria-expanded="false">
                        <li><a href="userlist.php">用户列表</a></li>
                        <li><a href="adduser.php">添加用户</a><li>
                        <li><a href="set.php?mod=user">用户配置</a><li>
                        <li><a href="set.php?mod=recharge">充值配置</a><li>
                    </ul>
                </li>
                <li class="side-nav-item">
                    <a href="javascript: void(0);" class="side-nav-link">
                        <i class="layui-icon layui-icon-notice"></i>
                        <span> 通知管理 </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="side-nav-second-level" aria-expanded="false">
                        <li><a href="msglist.php">通知列表</a><li>
                        <li><a href="addmsg.php">添加通知</a></li>
                    </ul>
                </li>
                <li class="side-nav-item">
                    <a href="javascript: void(0);" class="side-nav-link">
                        <i class="layui-icon layui-icon-form"></i>
                        <span> 云黑管理 </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="side-nav-second-level" aria-expanded="false">
                        <li><a href="blacklist.php">云黑列表</a></li>
                        <li><a href="addblack.php">添加云黑</a><li>
                    </ul>
                </li>
                <li class="side-nav-item">
                    <a href="javascript: void(0);" class="side-nav-link">
                        <i class="layui-icon layui-icon-speaker"></i>
                        <span> 广告管理 </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="side-nav-second-level" aria-expanded="false">
                        <li><a href="adverlist.php">广告列表</a><li>
                        <li><a href="advershlist.php">广告审核</a><li>
                        <li><a href="adver.php">添加广告</a></li>
                        <li><a href="set.php?mod=adver">广告配置</a><li>
                        <li><a href="set.php?mod=carousel">轮播配置</a></li>
                    </ul>
                </li>
                <li class="side-nav-item">
                    <a href="javascript: void(0);" class="side-nav-link">
                        <i class="layui-icon layui-icon-cart"></i>
                        <span> 商品管理 </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="side-nav-second-level" aria-expanded="false">
                        <li><a href="shoplist.php">商品列表</a></li>
                        <li><a href="addshop.php">添加商品</a><li>
                        <li><a href="shoprank.php">销量排行</a><li>
                    </ul>
                </li>
                <li class="side-nav-item">
                    <a href="javascript: void(0);" class="side-nav-link">
                        <i class="layui-icon layui-icon-form"></i>
                        <span> 接口管理 </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="side-nav-second-level" aria-expanded="false">
                        <li><a href="addpro.php">添加程序</a><li>
                        <li><a href="prolist.php">程序列表</a></li>
						<li><a href="addjk.php">添加接口</a><li>
						<li><a href="jklist.php">接口列表</a></li>
                    </ul>
                </li>
                <li class="side-nav-item">
                    <a href="javascript: void(0);" class="side-nav-link">
                        <i class="layui-icon layui-icon-app"></i>
                        <span> 应用管理 </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="side-nav-second-level" aria-expanded="false">
                        <li><a href="addapp.php">添加应用</a></li>
                        <li><a href="applist.php">应用列表</a></li>
						<li><a href="addappfile.php">添加文件</a><li>
						<li><a href="appfilelist.php">文件列表</a><li>
                    </ul>
                </li>
               <!--
		       <li class="side-nav-item">
                    <a href="javascript: void(0);" class="side-nav-link">
                        <i class="layui-icon layui-icon-flag"></i>
                        <span> 旗下管理 </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="side-nav-second-level" aria-expanded="false">
                        <li><a href="#"></a></li>
                    </ul>
                </li>
				-->
                <li class="side-nav-item">
                    <a href="javascript: void(0);" class="side-nav-link">
                        <i class="layui-icon layui-icon-auz"></i>
                        <span> 认证管理 </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="side-nav-second-level" aria-expanded="false">
                        <li><a href="paylist.php">认证列表</a></li>
                        <li><a href="addpay.php">添加认证</a><li>
                    </ul>
                </li>
                <li class="side-nav-item">
                    <a href="javascript: void(0);" class="side-nav-link">
                        <i class="layui-icon layui-icon-layouts"></i>
                        <span> 卡密管理 </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="side-nav-second-level" aria-expanded="false">
                        <li><a href="jkklist.php">加款卡列表</a></li>
                        <li><a href="addjkk.php">生成加款卡</a><li>
                        <li><a href="dhklist.php">兑换卡列表</a></li>
                        <li><a href="adddhk.php">生成兑换卡</a><li>
						<li><a href="addappkm.php">生成卡密</a><li>
                        <li><a href="appkmlist.php">卡密列表</a></li>
                    </ul>
                </li>
                <li class="side-nav-item">
                    <a href="javascript: void(0);" class="side-nav-link">
                        <i class="layui-icon layui-icon-set"></i>
                        <span> 系统设置 </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="side-nav-second-level" aria-expanded="false">
                        <li><a href="set.php?mod=account">账号信息配置</a></li>
                        <li><a href="set.php?mod=setuser">个人信息配置</a></li>
                        <li><a href="set.php?mod=site">网站信息配置</a></li>
                        <li><a href="set.php?mod=gonggao">网站公告配置</a></li>
                        <li><a href="set.php?mod=mail">发信邮箱配置</a><li>
                        <li><a href="set.php?mod=pay">支付接口配置</a><li>
                        <li><a href="set.php?mod=template">网站模板配置</a><li>
                        <li><a href="set.php?mod=temple">网站模块配置</a><li>
                        <li><a href="set.php?mod=login">网站登录配置</a><li>
                        <li><a href="set.php?mod=captcha">验证与IP配置</a><li>
                        <li><a href="set.php?mod=auth">授权认证配置</a></li>
                        <li><a href="set.php?mod=defend">防CC模板配置</a><li>
                    </ul>
                </li>
                <li class="side-nav-item">
                    <a href="javascript: void(0);" class="side-nav-link">
                        <i class="layui-icon layui-icon-component"></i>
                        <span> 其他组件 </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="side-nav-second-level" aria-expanded="false">
                        <li><a href="set.php?mod=qiandao">每日签到配置</a></li>
                        <li><a href="set.php?mod=chat">平台聊天配置</a></li>
                        <li><a href="set.php?mod=Market">商城系统配置</a><li>
                        <li><a href="set.php?mod=invite">邀请返利配置</a><li>
                        <li><a href="set.php?mod=dwz">防洪接口配置</a><li>
                        <li><a href="set.php?mod=guess">用户竞猜配置</a><li>
                        <?php if($conf['guess_open']==1){?><li><a href="guesslist.php">用户竞猜列表</a><li><?php }?>
                    </ul>
                </li>
                <?php if($conf['cloud_api_open']==1){?>
                <li class="side-nav-item">
                    <a href="key.php" class="side-nav-link">
                        <i class="layui-icon layui-icon-link"></i>
                        <span> 云端对接 </span>
                    </a>
                </li>
                <?php }?>
                <li class="side-nav-item">
                    <a href="guesslist.php" class="side-nav-link">
                        <i class="layui-icon layui-icon-gift"></i>
                        <span> 竞猜记录 </span>
                    </a>
                </li>
                <li class="side-nav-item">
                    <a href="workorder.php" class="side-nav-link">
                        <i class="layui-icon layui-icon-survey"></i>
                        <span> 工单列表 <?php echo ($gdcount == 0 ? '' : '<span class="layui-badge layui-bg-red">' . $gdcount . '</span>') ?></span>
                    </a>
                </li>
                <li class="side-nav-item">
                    <a href="chat.php" class="side-nav-link">
                        <i class="layui-icon layui-icon-dialogue"></i>
                        <span> 聊天交友 </span>
                    </a>
                </li>
                <li class="side-nav-item">
                    <a href="login.php?logout" class="side-nav-link">
                        <i class="layui-icon layui-icon-logout"></i>
                        <span> 退出登陆 </span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- Sidebar -left -->
    </div>
    <!-- Left Sidebar End -->
    <div class="content-page">
        <div class="content">
            <!-- Topbar Start -->
            <div class="navbar-custom">
                <ul class="list-unstyled topbar-right-menu float-right mb-0">
                    <li class="dropdown notification-list">
                        <a href="javascript:toggleFullScreen();" class="nav-link">
                            <i class="layui-icon layui-icon-screen-full noti-icon"></i>
                        </a>
                    </li>
                    <li class="dropdown notification-list">
                        <a class="nav-link dropdown-toggle nav-user arrow-none mr-0" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                            <span class="account-user-avatar">
                                <img src="//q4.qlogo.cn/headimg_dl?dst_uin=<?php echo $admin_qq;?>&spec=640" alt="user-image" class="rounded-circle bg-warning" style="border: solid 2px #ccc">
                            </span>
                            <span>
                                <span class="account-user-name">用户名：<?php echo $conf['admin_user']?></span>
                                <span class="account-position">权限：平台站长</span>
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-animated topbar-dropdown-menu profile-dropdown">
                            <!-- item-->
                            <div class=" dropdown-header noti-title">
                                <h6 class="text-overflow m-0">快捷导航</h6>
                            </div>
                            <!-- item-->
                            <a href="../" target="_blank" class="dropdown-item notify-item">
                                <i class="layui-icon layui-icon-home mr-1"></i>
                                <span>前往首页</span>
                            </a>
                            <a href="set.php?mod=site" class="dropdown-item notify-item">
                                <i class="layui-icon layui-icon-set mr-1"></i>
                                <span>网站信息</span>
                            </a>
                            <a href="set.php?mod=setuser" class="dropdown-item notify-item">
                                <i class="layui-icon layui-icon-user mr-1"></i>
                                <span>个人信息</span>
                            </a>
                            <a href="javascript:cleanbom()" class="dropdown-item notify-item">
                                <i class="layui-icon layui-icon-fonts-clear mr-1"></i>
                                <span>清除头部</span>
                            </a>
                            <a href="javascript:optim()" class="dropdown-item notify-item">
                                <i class="layui-icon layui-icon-console mr-1"></i>
                                <span>优化数据库</span>
                            </a>
                            <a href="javascript:repair()" class="dropdown-item notify-item">
                                <i class="layui-icon layui-icon-util mr-1"></i>
                                <span>修复数据库</span>
                            </a>
                            <!-- item-->
                            <a href="login.php?logout" class="dropdown-item notify-item">
                                <i class="layui-icon layui-icon-logout mr-1"></i>
                                <span>注销账号</span>
                            </a>
                        </div>
                    </li>
                </ul>
                <button class="button-menu-mobile open-left disable-btn">
                    <i class="layui-icon layui-icon-spread-left"></i>
                </button>
                <div class="app-search">
                    <form>
                        <div class="input-group">
                            <input type="text" class="form-control" id="dw" placeholder="请输入需要搜索的内容">
                            <span class="layui-icon layui-icon-search"></span>
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button" onclick="window.open('https://www.baidu.com/s?ie=UTF-8&wd='+$('#dw').val())">百度一下</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- end Topbar -->
            <!-- start page title -->
            <div class="row" style="width: 98%;margin: auto">
                <div class="col-12">
                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="index.php">首页</a></li>
                                        <li class="breadcrumb-item active"><?php echo $title ?></li>
                                    </ol>
                                </div>
                                <h4 class="page-title" style="font-weight: 400"><?php echo $title ?></h4>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->
                </div>
            </div>
            <!-- end page title -->
            <style>
                .note-popover {
                    display: none
                }
                .note-toolbar {
                    z-index: 9 !important;
                }
                .panel-heading {
                    border-bottom: 1px solid #ccc;
                }
            </style>