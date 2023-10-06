<?php
/**
 * 平台
**/
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
$title='站长管理中心';
include_once './header.php';
$thtime=date("Y-m-d").' 00:00:00';
$program=$DB->count("SELECT count(*) from yixi_program WHERE 1");
$app=$DB->count("SELECT count(*) from yixi_apps WHERE 1");
$app1=$DB->count("SELECT count(*) from yixi_apps WHERE date>'$thtime'");
$jk=$DB->count("SELECT count(*) from yixi_userjk WHERE 1");
$jk1=$DB->count("SELECT count(*) from yixi_userjk WHERE date>'$thtime'");
$jk2=$DB->count("SELECT count(*) from yixi_userjk WHERE uid=1");
$block=$DB->count("SELECT count(*) from yixi_block WHERE 1");
$qngly=$DB->count("SELECT count(*) from yixi_user WHERE power=3");
$cjgly=$DB->count("SELECT count(*) from yixi_user WHERE power=2");
$sqs=$DB->count("SELECT count(*) from yixi_user WHERE power=1");
$ptyh=$DB->count("SELECT count(*) from yixi_user WHERE power=0");
$user=$DB->count("SELECT count(*) from yixi_user WHERE 1");
$user1=$DB->count("SELECT count(*) from yixi_user WHERE upuid=1");
$tixiancount=$DB->count("SELECT count(*) from yixi_tixian WHERE status=0");
$remotecount=$DB->count("SELECT count(*) from yixi_log WHERE uid=1 AND type='异地登录'");
$sec_msg = sec_check();
?>
<link rel="stylesheet" href="//lib.baomitu.com/toastr.js/latest/css/toastr.min.css">
<div class="row">
    <div class="col-xl-4">
        <div class="card d-block pt-2 pb-1 text-center">
            <img class="card-img-top m-auto" style="height: 68px;width: 68px;margin:auto;display: block;border-radius: 0.3em;box-shadow: 0px 0px 30px #ccc" src="//q4.qlogo.cn/headimg_dl?dst_uin=<?php echo $admin_qq;?>&spec=640" alt="Card image cap">
            <div class="card-body pb-2">
                <h5 class="card-title">平台官方管理员</h5>
                <p class="card-text text-success"><?php echo qqname($admin_qq) ?> [ UID:1 / 平台站长 ]</p>
            </div>
            <ul class="list-group list-group-flush mb-2">
                <li class="list-group-item"><?php echo $remotecount ?>条异常登录记录，账号切莫外借，否则后果自负哦</li>
            </ul>
            <a class="btn btn-outline-danger mb-2" href="jklist.php"><i class="layui-icon layui-icon-list"></i> 接口列表</a>
            <a class="btn btn-outline-warning mb-2" href="paylist.php"><i class="layui-icon layui-icon-list"></i> 认证列表</a>
            <a class="btn btn-outline-primary mb-2" href="userlist.php"><i class="layui-icon layui-icon-list"></i> 用户列表</a>
            <a class="btn btn-outline-success mb-2" href="addpro.php"><i class="layui-icon layui-icon-auz"></i> 添加接口</a>
            <a class="btn btn-outline-dark mb-2" href="addpay.php"><i class="layui-icon layui-icon-add-circle"></i> 添加认证</a>
            <a class="btn btn-outline-warning mb-2" href="adduser.php"><i class="layui-icon layui-icon-username"></i> 添加用户</a>
            <a class="btn btn-outline-info mb-2" href="set.php?mod=setuser"><i class="layui-icon layui-icon-username"></i> 个人信息</a>
            <a class="btn btn-outline-success mb-2" href="workorder.php"><i class="layui-icon layui-icon-survey"></i> 工单管理</a>
            <a class="btn btn-outline-secondary mb-2" href="login.php?logout"><i class="layui-icon layui-icon-logout"></i> 退出登陆</a>
        </div> <!-- end card-->
    </div>
    <div class="col-xl-8">
        <div class="row">
            <div class="col-xl-12">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6" style="width: 50%;" onclick="layer.alert('QQ互联绑定可用作账号登陆！',{icon:3,title:'QQ互联绑定',btn:['确定','重新绑定'],btn2:function(layero,index) { window.open('binding.php') }})">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-right" style="width: 30px;height: 30px">
                                    <i class="layui-icon layui-icon-login-qq widget-icon" style="background-color: #494951;color: white;"></i>
                                </div>
                                <h5 class="text-muted font-weight-normal mt-0">QQ互联</h5>
                                <h5 class="mt-2 mb-0" style="font-weight: 300"><?php echo ($conf['access_token'] ? $conf['nickname'] : '点击绑定') ?></h5>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6" style="width: 50%">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-right" style="width: 30px;height: 30px">
                                    <i class="layui-icon layui-icon-app widget-icon" style="background-color: #02cbe4;color: white"></i>
                                </div>
                                <h5 class="text-muted font-weight-normal mt-0">程序总数</h5>
                                <h5 class="mt-2 mb-0" style="font-weight: 300"><?php echo $program ?>个</h5>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->
                </div>
            </div>
        </div> <!-- end row -->
        <div class="row">
            <div class="col-xl-6">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6" style="width: 50%;">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-right" style="width: 30px;height: 30px">
                                    <i class="layui-icon layui-icon-list widget-icon" style="background-color: #ff0a24;color: white;"></i>
                                </div>
                                <h5 class="text-muted font-weight-normal mt-0">应用总数 <a href="applist.php" class="badge badge-danger-lighten">管理</a></h5>
                                <h5 class="mt-2 mb-0" style="font-weight: 300"><?php echo $app ?>个</h5>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6" style="width: 50%">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-right" style="width: 30px;height: 30px">
                                    <i class="layui-icon layui-icon-list widget-icon" style="background-color: #ff824d;color: white"></i>
                                </div>
                                <h5 class="text-muted font-weight-normal mt-0">今日新增</h5>
                                <h5 class="mt-2 mb-0" style="font-weight: 300"><?php echo $app1 ?>个</h5>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->
                </div>
            </div>
            <div class="col-xl-6">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6" style="width: 50%">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-right" style="width: 30px;height: 30px">
                                    <i class="layui-icon layui-icon-list widget-icon" style="background-color: #98ff3b;color: white"></i>
                                </div>
                                <h5 class="text-muted font-weight-normal mt-0">接口总数 <a href="jklist.php" class="badge badge-danger-lighten">管理</a></h5>
                                <h5 class="mt-2 mb-0" style="font-weight: 300"><?php echo $jk?>个</h5>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6" style="width: 50%;">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-right" style="width: 30px;height: 30px">
                                    <i class="layui-icon layui-icon-list widget-icon" style="background-color: #8ed9ff;color: white;"></i>
                                </div>
                                <h5 class="text-muted font-weight-normal mt-0">今日新增 </h5>
                                <h5 class="mt-2 mb-0" style="font-weight: 300"><?php echo $jk1?>个</h5>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->
                </div>
            </div>
            <div class="col-xl-6">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6" style="width: 50%;">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-right" style="width: 30px;height: 30px">
                                    <i class="layui-icon layui-icon-list widget-icon" style="background-color: #ff6ba8;color: white;"></i>
                                </div>
                                <h5 class="text-muted font-weight-normal mt-0">我的接口 <a href="jklist.php?uid=1" class="badge badge-danger-lighten">管理</a></h5>
                                <h5 class="mt-2 mb-0" style="font-weight: 300"><?php echo $jk2?>个</h5>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6" style="width: 50%;">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-right" style="width: 30px;height: 30px">
                                    <i class="layui-icon layui-icon-list widget-icon" style="background-color: #FF7043;color: white;"></i>
                                </div>
                                <h5 class="text-muted font-weight-normal mt-0">用户总数 </h5>
                                <h5 class="mt-2 mb-0" style="font-weight: 300"><?php echo $user?>人</h5>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->
                </div>
            </div>
            <div class="col-xl-6">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6" style="width: 50%;">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-right" style="width: 30px;height: 30px">
                                    <i class="layui-icon layui-icon-list widget-icon" style="background-color: #FF9800;color: white;"></i>
                                </div>
                                <h5 class="text-muted font-weight-normal mt-0">普通用户 </h5>
                                <h5 class="mt-2 mb-0" style="font-weight: 300"><?php echo $ptyh?>人</h5>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6" style="width: 50%">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-right" style="width: 30px;height: 30px">
                                    <i class="layui-icon layui-icon-user widget-icon" style="background-color: #40C4FF;color: white"></i>
                                </div>
                                <h5 class="text-muted font-weight-normal mt-0">白银会员</h5>
                                <h5 class="mt-2 mb-0" style="font-weight: 300"><?php echo $sqs?>人</h5>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->
                </div>
            </div>
            <div class="col-xl-6">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6" style="width: 50%;">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-right" style="width: 30px;height: 30px">
                                    <i class="layui-icon layui-icon-list widget-icon" style="background-color: #1DE9B6;color: white;"></i>
                                </div>
                                <h5 class="text-muted font-weight-normal mt-0">黄金会员</h5>
                                <h5 class="mt-2 mb-0" style="font-weight: 300"><?php echo $cjgly?>人</h5>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6" style="width: 50%">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-right" style="width: 30px;height: 30px">
                                    <i class="layui-icon layui-icon-user widget-icon" style="background-color: #a1ccff;color: white"></i>
                                </div>
                                <h5 class="text-muted font-weight-normal mt-0">钻石会员</h5>
                                <h5 class="mt-2 mb-0" style="font-weight: 300"><?php echo $qngly?>人</h5>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->
                </div>
            </div>
            <div class="col-xl-6">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6" style="width: 50%;">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-right" style="width: 30px;height: 30px">
                                    <i class="layui-icon layui-icon-user widget-icon" style="background-color: #9655d0;color: white;"></i>
                                </div>
                                <h5 class="text-muted font-weight-normal mt-0">我的下级 <a href="userlist.php?uid=1" class="badge badge-danger-lighten">管理</a></h5>
                                <h5 class="mt-2 mb-0" style="font-weight: 300"><?php echo $user1?>人</h5>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6" style="width: 50%">
                        <div class="card widget-flat">
                            <div class="card-body">
                                <div class="float-right" style="width: 30px;height: 30px">
                                    <i class="layui-icon layui-icon-rmb widget-icon" style="background-color: #43A047;color: white"></i>
                                </div>
                                <h5 class="text-muted font-weight-normal mt-0">待处理提现 <a href="tixian.php" class="badge badge-danger-lighten">处理</a></h5>
                                <h5 class="mt-2 mb-0" style="font-weight: 300"><?php echo $tixiancount?>笔</h5>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        安全中心
                    </div>
                    <div class="card-body">
                    <?php
                    foreach($sec_msg as $row){
                        echo $row;
                    }
                    if(count($sec_msg)==0)echo '<li class="list-group-item"><span class="btn-sm btn-success">正常</span>&nbsp;暂未发现网站安全问题</li>';
                    ?>
                    </div>
                </div>
            </div>
        </div> <!-- end row -->
    </div>
</div>
<?php
include_once './bottom.php';
?>
<script src="//lib.baomitu.com/toastr.js/latest/toastr.min.js"></script>