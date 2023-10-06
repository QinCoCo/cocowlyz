<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
$title='生成卡密';
include_once './header.php';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                生成卡密
            </div>
            <div class="card-body">
                <form class="form-horizontal layui-form">
				         <div class="form-group mb-3">
                            <label for="example-input-normal" style="font-weight: 500">归属应用：</label>
                            <select name="proid" class="form-control" lay-search lay-filter="proid">
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
                          <div class="form-row">
							<div class="form-group col-md-2">
								<label for="example-input-normal" style="font-weight: 500">卡密类型</label>
								<select name="type" class="form-control" lay-search lay-filter="type"><option value="code">单码卡密</option><option value="single">次数卡密</option><option value="vip">会员兑换卡</option><option value="fen">积分兑换卡</option><option value="svipcard">至尊卡</option></select>
							</div>
							<div class="form-group col-md-10">
								<label for="example-input-normal" style="font-weight: 500" id="amount_name">单码卡密</label>
								<div class="input-group">
									<input type="number" id="add_amount" name="add_amount" class="form-control" value='1' placeholder="单码卡密可用时长">
									<div class="input-group-prepend" id="frame_set1" style="display:none">
										<span class="input-group-text" id="amount_a">天</span>
									</div>
									<div class="input-group-prepend" id="frame_set2" style="display:inherit">
										<select name="km_time" class="form-control" lay-search lay-filter="km_time"><option value="hour">小时卡</option><option value="day">天卡</option><option value="week">周卡</option><option value="month">月卡</option><option value="season">季卡</option><option value="year">年卡</option><option value="longuse">永久卡</option><option value="vipcard">贵宾卡</option></select>
									</div>
								</div>
							</div>
						</div>
                       <div class="form-group mb-3">
								<label for="example-input-normal" style="font-weight: 500" id="amount_name">卡密长度</label>
								<div class="input-group">
									<input type="number" class="form-control" name="km_length" lay-verType="tips" lay-verify="required" value="10">
									<div class="input-group-prepend">
										<span class="input-group-text" id="amount_a">位</span>
									</div>
								</div>
							  </div>
						 <div class="form-group mb-3">
                            <label for="example-input-normal" style="font-weight: 500">自定义前缀：</label>
                            <input type="text" class="form-control" name="km_zdy" placeholder="如:ceshi521_(不自定义可不填)">
                        </div>
                    <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">生成数量：</label>
                        <input type="number" class="form-control" name="km_num" lay-verType="tips" lay-verify="required" value="1">
                    </div>
                    <button type="submit" class="btn btn-block btn-xs btn-outline-success" lay-submit lay-filter="submit_addappkm">生 成</button>
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
    form.on('submit(submit_addappkm)', function (data) {
        layer.alert('是否要执行当前操作？', {
            icon: 3, btn: ['确定', '取消'], btn1: function (layero, index) {
                addappkm();
            }
        });
        return false;
    });
    form.on('select(type)', function(data){ 
        if(data.value == 'code'){
			      $("#frame_set1").css("display","none");
				  $("#frame_set2").css("display","inherit");
				  document.getElementById('amount_name').innerHTML="&nbsp;单码卡密";
				  document.getElementById('add_amount').value="1";
				  document.getElementById('add_amount').setAttribute("placeholder","单码卡密可用时长");
				  document.getElementById('amount_a').innerHTML="天";
        }
	    if(data.value == 'single'){
			      $("#frame_set1").css("display","inherit");
				  $("#frame_set2").css("display","none");
                  document.getElementById('amount_name').innerHTML="&nbsp;卡密次数 *";
				  document.getElementById('add_amount').value="1";
				  document.getElementById('add_amount').setAttribute("placeholder","卡密可用次数");
				  document.getElementById('amount_a').innerHTML="次";
		}
	    if(data.value == 'vip'){
			      $("#frame_set1").css("display","inherit");
				  $("#frame_set2").css("display","none");
                  document.getElementById('amount_name').innerHTML="&nbsp;会员天数 *";
				  document.getElementById('add_amount').value="1";
				  document.getElementById('add_amount').setAttribute("placeholder","会员天数，永久卡9个9");
				  document.getElementById('amount_a').innerHTML="天";
		}
		if(data.value == 'fen'){
			      $("#frame_set1").css("display","inherit");
				  $("#frame_set2").css("display","none");
                  document.getElementById('amount_name').innerHTML="&nbsp;积分数 *";
				  document.getElementById('add_amount').value="1";
				  document.getElementById('add_amount').setAttribute("placeholder","积分数");
				  document.getElementById('amount_a').innerHTML="积分";
        }
		if(data.value == 'svipcard'){
			      $("#frame_set1").css("display","inherit");
				  $("#frame_set2").css("display","none");
                  document.getElementById('amount_name').innerHTML="&nbsp;至尊卡 ";
				  document.getElementById('add_amount').value="999999999";
				  document.getElementById('amount_a').innerHTML="";
        }
    });
});
function addappkm() {
    var type = $("select[name='type']").val();
	var add_amount = $("input[name='add_amount']").val();
    var proid = $("select[name='proid']").val();
	var km_time = $("select[name='km_time']").val();
	var km_zdy = $("input[name='km_zdy']").val();
	var km_length = $("input[name='km_length']").val();
    var km_num = $("input[name='km_num']").val();
    var ii = layer.msg('正在生成中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type: "POST",
        url: "ajax.php?act=addappkm",
        data : {type:type,add_amount:add_amount,proid:proid,km_zdy:km_zdy,km_time:km_time,km_length:km_length,km_num:km_num},
        dataType: "json",
        success: function(data) {
            layer.close(ii);
            if (data.code == 0) {
                layer.msg(data.msg, {
                    icon: 6,
                    end: function (layero, index) {
                        window.location.href = 'appkmlist.php';
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