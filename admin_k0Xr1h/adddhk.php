<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
$title='生成兑换卡';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                生成兑换卡
            </div>
            <div class="card-body">
                <form class="form-horizontal layui-form">
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">兑换卡类别：</label>
                        <select name="type" class="form-control" lay-search lay-filter="type"><option value="1">接口兑换卡</option><option value="2">认证兑换卡</option><option value="3">权限兑换卡</option><option value="4">网站邀请码</option></select>
                    </div>
                    <div id="frame_set1" style="display:none">
                        <div class="form-group mb-3">
                            <label for="example-input-normal" style="font-weight: 500">权限卡权限：</label>
                            <select name="power" class="form-control" lay-search lay-filter="power"><option value="1">白银会员</option><option value="2">黄金会员</option><option value="3">钻石会员</option><option value="4">星耀会员</option></select>
                        </div>
                    </div>
                    <div id="frame_set2" style="display:none">
                        <div class="form-group mb-3">
                            <label for="example-input-normal" style="font-weight: 500">归属程序：</label>
                            <select name="proid" class="form-control" lay-search lay-filter="proid">
                            <?php
                            $rs=$DB->query("SELECT * FROM yixi_program WHERE 1 order by id desc");
                            while($res = $DB->fetch($rs))
                            {
                            echo '<option value="'.$res['id'].'">'.$res['name'].'</option>';
                            }
                            ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">生成数量：</label>
                        <input type="number" class="form-control" name="num" lay-verType="tips" lay-verify="required">
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_adddhk">生 成</button>
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
    form.on('submit(submit_adddhk)', function (data) {
        layer.alert('是否要执行当前操作？', {
            icon: 3, btn: ['确定', '取消'], btn1: function (layero, index) {
                adddhk();
            }
        });
        return false;
    });
    form.on('select(type)', function(data){ 
        if(data.value == '3'){
            $("#frame_set1").show();
            form.on('select(power)', function(data){ 
                if(data.value == '3'){
                    $("#frame_set2").hide();
                }else{
                    $("#frame_set2").show();
                }
            });
		}
		if(data.value == '4'){
            $("#frame_set1").hide();
			$("#frame_set2").hide();
        }else{
            $("#frame_set1").hide();
            $("#frame_set2").show();
        }
    });
});
function adddhk() {
    var type = $("select[name='type']").val();
    var power = $("select[name='power']").val();
    var proid = $("select[name='proid']").val();
    var num = $("input[name='num']").val();
    var ii = layer.msg('正在生成中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type: "POST",
        url: "ajax.php?act=adddhk",
        data : {type:type,power:power,proid:proid,num:num},
        dataType: "json",
        success: function(data) {
            layer.close(ii);
            if (data.code == 0) {
                layer.msg(data.msg, {
                    icon: 6,
                    end: function (layero, index) {
                        window.location.href = 'dhklist.php';
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