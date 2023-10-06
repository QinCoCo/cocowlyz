<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
$title='添加接口';
include_once './header.php';
if($conf['auth_time_type']==2){
    $endtime=date('Y-m-d', strtotime('+'.$conf['auth_time'].' years'));
}elseif($conf['auth_time_type']==1){
    $endtime=date('Y-m-d', strtotime('+'.$conf['auth_time'].' months'));
}else{
    $endtime=date('Y-m-d', strtotime('+'.$conf['auth_time'].' days'));
}
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                添加接口
            </div>
            <div class="card-body">
                <form class="form-horizontal layui-form">
                    <div class="form-group mb-3">
                  <label for="example-input-normal" style="font-weight: 500">归属应用：</label>
                  <select name="appid" class="form-control" lay-search lay-filter="appid">
                  <?php
				  $appid=isset($_GET['appid'])?intval($_GET['appid']):NULL;
                  $rs=$DB->query("SELECT * FROM yixi_apps WHERE 1 order by id desc");
                  while($res = $DB->fetch($rs))
                  {
                  echo '<option value="'.$res['id'].'"'.($res['id']==$appid?' selected="selected" ':'').'>'.$res['name'].'</option>';
                  }
                 ?>
                </select>
                   </div>
					<div class="form-group mb-3">
                      <label for="example-input-normal" style="font-weight: 500">归属分类 <span class="badge badge-success-lighten" onclick="getmsg()">接口简介</span></label>
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
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">到期时间：</label>
                        <input type="date" class="form-control" name="endtime" value="<?php echo $endtime;?>" lay-verType="tips" lay-verify="required"/>
                    </div>                 
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_addjk">添 加</button>
                </form>
            </div>
            <div class="card-footer">
                <span class="layui-icon layui-icon-tips"></span> 选择接口分类后可查看接口简介哦！
            </div>
        </div>
    </div>
</div>
<?php
include_once './bottom.php';
?>
<script type="text/javascript">layui.use(['form'], function () {
    var form = layui.form;
    form.on('submit(submit_addjk)', function (data) {
        layer.alert('是否要执行当前操作？', {
            icon: 3, btn: ['确定', '取消'], btn1: function (layero, index) {
                addjk();
            }
        });
        return false;
    });
});
function addjk() {
	var appid = $("select[name='appid']").val();
    var proid = $("select[name='proid']").val();
    var endtime = $("input[name='endtime']").val();
    var ii = layer.msg('正在添加中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type: "POST",
        url: "ajax.php?act=addjk",
        data : {proid:proid,appid:appid,endtime:endtime},
        dataType: "json",
        success: function(data) {
            layer.close(ii);
            if (data.code == 0) {
                layer.msg(data.msg, {
                    icon: 6,
                    end: function (layero, index) {
                        window.location.href = 'jklist.php';;
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
function getmsg() {
    var proid = $("select[name='proid']").val();
    var ii = layer.msg('正在获取中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type: "POST",
        url: "ajax.php?act=getmsg",
        data : {proid:proid},
        dataType: "json",
        success: function(data) {
            layer.close(ii);
            if (data.code == 0) {
                layer.msg(data.msg, {
                    icon: 1,
                    time: 1 * 1000
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