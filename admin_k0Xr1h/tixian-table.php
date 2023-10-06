<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
?>
<?php
function display_zt($zt){
    if($zt==2)
        return '<a class="layui-btn layui-btn-xs" style="background-color: rgba(14,0,101,0.6)"><font color="white">已退款</font></a>';
    elseif($zt==1)
        return '<a class="layui-btn layui-btn-xs" style="background-color: #00cb5b"><font color="white">已完成</font></a>';
    else
        return '<a class="layui-btn layui-btn-xs" style="background-color: #5c3dff"><font color="white">待处理</font></a>';
}
function display_type($type){
    if($type==1)
        return '<font color=#E65100>微信</font>';
    elseif($type==2)
        return '<font color=#43A047>QQ钱包</font>';
    else
        return '<font color=#BA68C8>支付宝</font>';
}

if(isset($_GET['types'])){
    $type = intval($_GET['types']);
    $sql=" type='$type'";
    if($type==1){
        $typename = '微信';
    }elseif($type==2){
        $typename = 'QQ钱包';
    }else{
        $typename = '支付宝';
    }
	$numrows=$DB->count("SELECT count(*) from yixi_tixian WHERE{$sql}");
	$con='提现方式 '.$typename.' 的共有 <b>'.$numrows.'</b> 条提现记录';
    $link='&type='.$type;
}elseif(isset($_GET['kw'])){
	$kw = daddslashes($_GET['kw']);
	if($_GET['type']==1)
		$sql=($_GET['method']==1)?" `account` LIKE '%{$kw}%'":" `account`='{$kw}'";
	elseif($_GET['type']==2)
		$sql=($_GET['method']==1)?" `name` LIKE '%{$kw}%'":" `name`='{$kw}'";
	elseif($_GET['type']==3)
		$sql=($_GET['method']==1)?" `remarks` LIKE '%{$kw}%'":" `remarks`='{$kw}'";
	else{
		$sql=($_GET['method']==1)?" `{$column}` LIKE '%{$kw}%'":" `{$column}`='{$kw}'";
	}
	$numrows=$DB->count("SELECT count(*) from yixi_tixian WHERE{$sql}");
	$con='包含 '.$kw.' 的共有 <b>'.$numrows.'</b> 条提现记录';
	$link='&kw='.$_GET['kw'];
}else{
	$numrows=$DB->count("SELECT count(*) from yixi_tixian WHERE 1");
	$sql=" 1";
	$con='平台共有 <b>'.$numrows.'</b> 个提现记录';
}
?>
     <div style="white-space:nowrap;overflow-x: auto;">
        <table class="layui-table layuiadmin-page-table">
          <thead><tr><th><div class="custom-control custom-checkbox"><input type="checkbox" id="checkbox" value="" class="custom-control-input"><label class="custom-control-label" for="checkbox"></label></div></th><th>提现ID</th><th>用户UID</th><th>用户头像</th><th>提现金额</th><th>实际到账</th><th>提现方式</th><th>收款人姓名</th><th>提现账号</th><th>提现备注</th><th>收款图</th><th>申请时间</th><th>处理时间</th><th>状态</th><th>信息</th><th>操作</th></tr></thead>
          <tbody>
<?php
$pagesize=30;
$pages=ceil($numrows/$pagesize);
$page=isset($_GET['page'])?intval($_GET['page']):1;
$offset=$pagesize*($page - 1);

$rs=$DB->query("SELECT * FROM yixi_tixian WHERE{$sql} ORDER BY id DESC limit $offset,$pagesize");
while($res = $DB->fetch($rs))
{
$user = $DB->get_row("select * from yixi_user where uid='" . $res['uid'] . "' limit 1");
echo '<tr data-id="'.$res['id'].'"><td width="10" style="vertical-align:middle;"><div class="custom-control custom-checkbox"><input type="checkbox" id="'.$res['id'].'" value="" name="data" class="custom-control-input"/><label class="custom-control-label" for="'.$res['id'].'"></label></div></td><td><b>'.$res['id'].'</b></td><td>'.$res['uid'].'</td><td><img src="http://q4.qlogo.cn/headimg_dl?dst_uin='.$user['qq'].'&spec=640" style="height: 50px;width: 50px" class="img-rounded img-circle img-thumbnail"></td><td style="color:#4E342E">'.$res['money'].'</td><td style="color:#FB8C00">'.$res['realmoney'].'</td><td>'.display_type($res['type']).'</td><td><span onclick="inputInfo('.$res['id'].')" title="修改信息">'.$res['name'].'</span></td><td><span onclick="inputInfo('.$res['id'].')" title="修改信息">'.$res['account'].'</span></td><td><span onclick="inputInfo('.$res['id'].')" title="修改信息">'.$res['remarks'].'</span></td><td><a href="javascript:skimg('.$res['uid'].')">点击查看</a></td><td>'.$res['addtime'].'</td><td>'.($res['status']>=1?$res['endtime']:'提现未处理，请赶快处理').'</td><td class="status">'.display_zt($res['status']).'</td><td class="transfer_info"></td><td class="op">'.($res['status']==0?'<a href="javascript:operation('.$res['id'].',\'complete\')" class="layui-btn layui-btn-xs btn-success">完成</a>'.($conf['user_daifu']>0?'<a href="javascript:transfer('.$res['id'].')" class="layui-btn layui-btn-xs btn-primary transfer_do">转账</a>':null).'<a href="javascript:back('.$res['id'].',\''.$res['money'].'\')" class="layui-btn layui-btn-xs btn-info">退回</a>':'<a href="javascript:operation('.$res['id'].',\'reset\')" class="layui-btn layui-btn-xs btn-info">撤销</a>').'<a href="./record.php?uid='.$res['uid'].'" class="layui-btn layui-btn-xs btn-warning">明细</a><a href="javascript:delItem('.$res['id'].')" class="layui-btn layui-btn-xs btn-danger">删除</a></td></tr>';
}
?>
          </tbody>
        </table>
      </div>
<div class="text-center">
<?php
#分页
$pageList=new Page($numrows,$pagesize,1,$link);
echo $pageList->showPage();
?>
</div>
    <script type="text/html" id="config">
        <div class="card-body">
            <form action="" id="editform" method="post" class="form-horizontal" role="form">
                <div class="layui-elem-quote">平台地址：<a href="http://www.fcypay.com" target="_blank" rel="noreferrer">www.fcypay.com</a><br>安全起见 每次重启打开浏览器都需重新设置支付密码</div>
                <div class="form-group mb-3">
                    <label for="example-input-normal" style="font-weight: 500">Api_Id：</label>
                    <input type="text" name="id" value="<?php echo (isset($conf['transfer_id'])?$conf['transfer_id']:'');?>" class="form-control" placeholder="对接ID"/>
                </div>
                <div class="form-group mb-3">
                    <label for="example-input-normal" style="font-weight: 500">Api_Key：</label>
                    <input type="text" name="key" value="<?php echo (isset($conf['transfer_key'])?$conf['transfer_key']:'');?>" class="form-control" placeholder="对接Key"/>
                </div>
                <div class="form-group mb-3">
                    <label for="example-input-normal" style="font-weight: 500">支付密码：</label>
                    <input type="text" name="pass" value="" class="form-control" placeholder="对接支付密码"/>
                </div>
                <div class="form-group mb-3">
                    <label for="example-input-normal" style="font-weight: 500">汇款选项：</label>
                    <select name="check" class="form-control"><option value="FORCE_CHECK" <?php echo isset($conf['transfer_check']) && $conf['transfer_check'] == 'FORCE_CHECK'?'selected="selected"':'';?> >验证用户账号与姓名实名一致</option><option value="NO_CHECK" <?php echo isset($conf['transfer_check']) && $conf['transfer_check'] == 'NO_CHECK'?'selected="selected"':'';?> >不验证真实姓名</option></select>
                </div>
                <span class="btn btn-block btn-xs btn-outline-success" onclick="configEdit()">修 改</span>
            </form>
        </div>
    </script>
<script type="text/javascript">
$("#blocktitle").html('<?php echo $con?>');
var transfer = function(id){
    var transfer = '<?php echo (isset($_SESSION['transfer'])?$_SESSION['transfer']:'')?>';
    if (!transfer) {
        layer.msg('请先配置信息', {icon: 5});
        return false;
    }
    var load= layer.load();
    $.ajax({
        type: "POST",
        url: "./ajax.php?act=transfer",
        data: {"id":id},
        dataType: "json",
        success: function(res){
            layer.close(load);
            layer.msg(res.msg,{icon: 6},function(index){
                layer.close(index);
                if (res.code) listTable();
            });
        },error: function(){
            layer.close(load);
            layer.msg('服务器连接失败', {icon: 5});
        }
    });
}
var config = function(){
    var html = $("#config").html()
    layer.open({
        type:1,
        title:"自动转账信息配置",
        content:html
    })
}
var configEdit = function () {
    $.ajax({
        type: "POST",
        url: "./ajax.php?act=transfer_config",
        data: $("#editform").serialize(),
        dataType: "json",
        success: function(res){
            layer.alert(res.msg,function(index){
                if (res.code) {
                    location.reload();
                }else{
                    layer.close(index);
                }
            });
        }
    });
}
var pl_config = function () {
    var transfer = '<?php echo (isset($_SESSION['transfer'])?$_SESSION['transfer']:'')?>';
    if (!transfer) {
        layer.msg('请先配置信息', {icon: 5});
        return false;
    }
    if(layer.confirm('确认批量转账？不可取消')){
        var id='';
        var arrChk=$("input[name='data']:checked"); 
        if(arrChk.length<=0){
            layer.msg('请先勾选数据', {icon: 5});
            return false;
        }
        $(arrChk).each(function(){ 
            var id = this.id;
            $("table").find('tr[data-id="'+id+'"]').find('.transfer_info').html('<font color="red">转账中....</font>');
        });
        $(arrChk).each(function(){ 
            var id = this.id;
            $.ajax({
                type: "POST",
                url: "./ajax.php?act=transfer",
                data: {"id":id},
                dataType: "json",
                async: true,
                success: function(res){
                    layer.close(load);
                    if (res.code) {
                        $("table").find('tr[data-id="'+id+'"]').find('.status').html("<font color='green'>已完成</font>");
                        $("table").find('tr[data-id="'+id+'"]').find('.transfer_do').hide();
                        var html = '<font color="green">'+res.msg+'</font>';
                    }else{
                        var html = '<font color="red">'+res.msg+'</font>';
                    }
                    $("table").find('tr[data-id="'+id+'"]').find('.transfer_info').html(html);
                },error: function(){
                    layer.close(load);
                    layer.msg('服务器连接失败', {icon: 5});
                }
            });
        });
        return false;
    }else{
        return false;
    }
    var load= layer.load();
}
$(document).ready(function(){
    var checkboxes = document.getElementsByName('data');
    $("#checkbox").click(function() {
        for (var i = 0; i < checkboxes.length; i++) {
            var checkbox = checkboxes[i];
            if (!$(this).get(0).checked) {
                checkbox.checked = false;
            } else {
                checkbox.checked = true;
            }
        }
    });
})
</script>