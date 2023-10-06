<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
$title='API对接密钥';
include_once './header.php';
if($conf['cloud_api_open']!=1)showmsg('错误', '平台未开放云端API服务！', 4, './');
if(!$conf['api_key']){
    $form='add_key();';
    $text='暂未生成KEY密钥';
    $texts='生成KEY密钥';
}else{
    $form='api_key();';
    $text=$conf['api_key'];
    $texts='重置KEY密钥';
}
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                API对接密钥
            </div>
            <div class="card-body">
                <form class="form-horizontal layui-form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">KEY密钥：<span class="badge badge-danger-lighten" onclick="ip();">设置IP白名单</span> <span class="badge badge-danger-lighten" onclick="<?php echo $form?>"><?php echo $texts?></span></label>
                        <input type="text" class="form-control" value="<?php echo $text?>" disabled>
                    </div>
                </form>
            </div>
        </div>
    <?php if($conf['api_key']){ ?>
        <div class="card">
            <div class="card-header">
                云端授权API对接
            </div>
            <div class="card-body">
                <form class="form-horizontal layui-form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">请选择对接程序：</label>
                        <select name="proid" class="form-control" lay-search>
                        <?php
                        $rs=$DB->query("SELECT * FROM yixi_program WHERE 1 order by id desc");
                        while($res = $DB->fetch($rs))
                        {
                        echo '<option value="'.$res['id'].'">'.$res['name'].'</option>';
                        }
                        ?>
                        </select>
                   </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">添加授权[将中文修改为输入框变量]：</label>
                        <input type="text" name="api_jk" class="form-control" value="未选择对接程序"/>
                  </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">参数说明：</label>
                        <textarea type="html" class="form-control" rows="6"  style="color: green;" disabled/>提交方式 : GET提交&#10;[proid] : 授权程序识别码&#10;[name] : 授权站点名称&#10;[qq] : 授权QQ&#10;[url] : 授权域名&#10;[ip] : 服务器ip</textarea>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                云端添加代理API对接
            </div>
            <div class="card-body">
                <form class="form-horizontal layui-form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">请选择对接程序：</label>
                        <select name="proids" class="form-control" lay-search>
                        <?php
                        $rs=$DB->query("SELECT * FROM yixi_program WHERE 1 order by id desc");
                        while($res = $DB->fetch($rs))
                        {
                        echo '<option value="'.$res['id'].'">'.$res['name'].'</option>';
                        }
                        ?>
                       </select>
                   </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">添加授权商[将中文修改为输入框变量]：</label>
                        <input type="text" name="apisqs_jk" class="form-control" value="未选择对接程序"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">添加超管[将中文修改为输入框变量]：</label>
                        <input type="text" name="apicg_jk" class="form-control" value="未选择对接程序"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">添加全能管理员[将中文修改为输入框变量]：</label>
                        <input type="text" name="apiqn_jk" class="form-control" value="<?php echo $authurl?>api/cloud_api.php?act=cloud_user&power=3&user=登录用户名&pwd=登录密码&qq=联系QQ&email=绑定邮箱&ip=服务器ip&key=<?php echo $conf['api_key']?>"/>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">参数说明：</label>
                        <textarea type="html" class="form-control" rows="8"  style="color: green;" disabled/>提交方式 : GET提交&#10;[proid] : 授权程序识别码&#10;[power] : 购买权限等级&#10;[user] : 绑定用户名&#10;[pwd] : 绑定密码&#10;[qq] : 联系QQ&#10;[email] : 绑定邮箱&#10;[ip] : 服务器ip</textarea></div>
                    </div>
                </form>
            </div>
        </div>
    <?php }?>
    </div>
</div>
<?php
include_once './bottom.php';
?>
<script type="text/javascript">
$("select[name='proid']").change(function(){
    var proid = $("select[name='proid']").val();
    $.ajax({
        type : 'POST',
        url : 'ajax.php?act=get_apijk',
        data : {proid:proid},
        dataType : 'json',
        success : function(data) {
            if(data.code == 0){
                $("input[name='api_jk']").val(data.api_jk);
            }else{
                layer.msg(data.msg, {icon: 5});
            }
        },
        error:function(data){
            layer.close(ii);
            layer.msg('服务器错误', {icon: 5});
        }
    });
});
$("select[name='proids']").change(function(){
    var proid = $("select[name='proids']").val();
    $.ajax({
        type : 'POST',
        url : 'ajax.php?act=get_user_apijk',
        data : {proid:proid},
        dataType : 'json',
        success : function(data) {
            if(data.code == 0){
                $("input[name='apisqs_jk']").val(data.apisqs_jk);
                $("input[name='apicg_jk']").val(data.apicg_jk);
            }else{
                layer.msg(data.msg, {icon: 5});
            }
        },
        error:function(data){
            layer.close(ii);
            layer.msg('服务器错误', {icon: 5});
        }
    });
});
function ip() {
    layer.open({
        area: ['360px'],
        title: '服务器ip白名单（多个ip,分隔）',
        content: '<div class="form-group"><textarea class="form-control" name="ipcontent" placeholder="示例：113.111.222,222.888.558" rows="3"><?php echo $conf['api_iplist']?></textarea></div>',
        yes: function(){
            var content = $("textarea[name='ipcontent']").val();
            $.ajax({
                type : 'POST',
                url : 'ajax.php?act=api_ip',
                data : {data: content.replace("，",",")},
                dataType : 'json',
                success : function(data) {
                    if(data.code == 0){
                        layer.msg(data.msg, {icon: 6});
                    }else{
                        layer.alert(data.msg, {icon: 5});
                    }
                },
                error:function(data){
                    layer.msg('服务器错误', {icon: 5});
                    return false;
                }
            });
        }
    });
}
function add_key(){//生成KEY
    var ii = layer.msg('正在生成中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : "POST",
        url : "ajax.php?act=add_key",
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if (data.code == 1) {
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
            layer.close(ii);
            layer.msg('服务器错误', {icon: 5});
        }
    });
}
function api_key(){//重置KEY
    var ii = layer.msg('正在重置中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : "POST",
        url : "ajax.php?act=api_key",
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if (data.code == 1) {
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
            layer.close(ii);
            layer.msg('服务器错误', {icon: 5});
        }
    });
}
</script>