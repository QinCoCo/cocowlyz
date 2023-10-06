<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
function display_type($type){
    global $conf;
    $types = explode('|', $conf['workorder_type']);
    if($type==0 || !array_key_exists($type-1,$types))
        return '其它问题';
    else
        return $types[$type-1];
}
function display_status($status){
    if($status==1)
        return '<font color="red">处理中</font>';
    elseif($status==2)
        return '<font color="green">已完成</font>';
    else
        return '<font color="blue">待处理</font>';
}
if(isset($_GET['id'])) {
$id=isset($_GET['id'])?intval($_GET['id']):sysmsg("参数错误",2,'./',true);
$row=$DB->get_row("SELECT * FROM yixi_workorder WHERE id='{$id}' limit 1");
if(!$row)sysmsg("平台不存在该工单记录",2,'./workorder.php',true);
$title='工单详情';
include_once './header.php';
?>
 <style>
.gdan_gout{width:100%;height:auto;background-color:#fff;padding-bottom:1em}
.gdan_txt{height:3em;line-height:3em;text-indent:1em;font-family:"微软雅黑";font-weight:800;}
.gdan_txt>span{position:absolute;right:4em;}
.gdan_zhugan{width:96%;height:auto;padding-top:1em;margin-left:2%;padding-left:.5em;padding-right:1em;margin-bottom:1em;border-top:dashed 1px #a9a9a9}
.gdan_kjia1{width:auto;margin-left:4em;margin-top:-3em}
.gdan_xiaozhi{width:100%;height:1em;color:#a9a9a9;margin-bottom:1em}
.gdan_xiaozhi>span{position:absolute;right:4em;}
.gdan_huifu{width:100%;height:auto;margin-top:1em;border-top:solid #ccc 1px}
.gdan_srk{width:98%;height:8em;margin-left:1%;margin-top:1em;border-color:#6495ed}
.gdan_huifu1{width:6em;height:2.5em;border:none;background-color:#1e90ff;color:#fff;margin:.5em 0 .5em 1%}
.gdan_jied{width:100%;height:3em;line-height:3em;text-align:center;color:#129DDE}
</style>
<?php
$contents = explode('*',$row['content']);
$siterow = $DB->get_row("SELECT uid,qq,user FROM yixi_user WHERE uid='{$row['uid']}' LIMIT 1");
$myimg = $userrow['qq']?'//q2.qlogo.cn/headimg_dl?bs=qq&dst_uin='.$siterow['qq'].'&src_uin='.$siterow['qq'].'&fid='.$siterow['qq'].'&spec=100&url_enc=0&referer=bu_interface&term_type=PC':'../assets/img/user.png';
$kfimg = 'https://imgcache.qq.com/open_proj/proj_qcloud_v2/mc_2014/work-order/css/img/custom-service-avatar.svg';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                工单详情
            </div>
            <div class="gdan_gout">
                <div class="gdan_txt">沟通记录 - <?php echo count($contents)?><span>状态：<?php echo display_status($row['status'])?></span></div>
                <!------------------开始沟通------------------------>
                <div class="gdan_zhugan" style="border: none;">
                    <a href="./userlist.php?type=1&kw=<?php echo $row['uid']?>&method=0" target="_blank"><img src="<?php echo $myimg?>" class="img-circle" width="40"/></a>
                    <div class="gdan_kjia1">
                        <div class="gdan_xiaozhi">问题描述<span><?php echo $row['addtime']?></span></div>
                        <?php echo htmlspecialchars($contents[0])?><br/><br/>
                        用户信息：<?php echo $siterow?'<a href="./userlist.php?type=1&kw='.$row['uid'].'&method=0" target="_blank">'.$siterow['user'].'</a>':$row['account'];?><br/>
                        问题类型：<?php echo display_type($row['type'])?>
                        <?php echo $row['picurl']?'<p>问题图片：[<span onclick="image_msg('.$id.')">点此查看</span>]':null;?>
                    </div>
                </div>
            <?php
            for($i=1;$i<count($contents);$i++){
                $content = explode('^',$contents[$i]);
                if(count($content)==3){
                    echo '<div class="gdan_zhugan">
                    <img src="'.($content[0]==1?$kfimg:$myimg).'" class="img-circle" width="40"/>
                    <div class="gdan_kjia1">
                        <div class="gdan_xiaozhi">'.($content[0]==1?'官方客服':$userrow['user']).'<span>'.$content[1].'</span></div>
                            '.htmlspecialchars($content[2]).'
                        </div>
                    </div>';
                }
            }
            if($row['status']==2){
            ?>
            <div class="gdan_jied">此工单已经结单</div>
            <?php }else{?>
            <div class="gdan_huifu">
                <form onsubmit="return workorder_reply();" method="post" class="layui-form">
                <textarea class="gdan_srk" name="content" placeholder="回复后工单状态自动变为已处理 ,用户将会收到通知哦！" lay-verType="tips" lay-verify="required"></textarea>
                <div class="mb-3">
                    <div class="custom-control custom-checkbox">
                        <input name="email" type="checkbox" value="1" class="custom-control-input" checked="checked" id="email">
                        <label class="custom-control-label" for="email">同时发送提醒邮件到用户邮箱</label>
                    </div>
                </div>
                <input type="submit" name="submit" value="提交回复" class="btn btn-outline-info">
                <span class="btn btn-outline-success" onclick="workorder_complete();">完结工单</span>
                </form>
            </div>
            <?php }?>
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
function image_msg(id) {
    $.getJSON('../ajax.php?act=image_workorder&id=' + id, function (json) {
        layer.photos({photos: json, anim: 5});
    });
}
function workorder_reply() {
    var content = $("textarea[name='content']").val();
    var email = $("input[type='checkbox']:checked").val();
    var ii = layer.msg('正在回复中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type: "POST",
        url: "ajax.php?act=workorder_reply&id=<?php echo $id;?>",
        data : {content:content,email:email},
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
function workorder_complete() {
    var ii = layer.msg('正在完结中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type: "GET",
        url: "ajax.php?act=workorder_complete&id=<?php echo $id;?>",
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
</script>
<?php
}else{
    sysmsg("参数错误",2,'./',true);
}
?>