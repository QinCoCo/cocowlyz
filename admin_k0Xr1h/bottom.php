<?php
/**
 * 全局底部调用
 */
?>
    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    2020 - 2022 © All rights reserved - <?php echo $conf['sitename'] ?>
                </div>
                <div class="col-md-6">
                    <div class="text-md-right footer-links d-none d-md-block">
                        <a href="<?php echo $conf['Communication'] ?>" target="_blank">官方交流群</a>
                        <a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $admin_qq ?>&site=qq&menu=yes" target="_blank">联系客服</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
<script src="../assets/js/app.min.js"></script>
<script src="../assets/js/vendor/jquery-jvectormap-1.2.2.min.js"></script>
<script src="../assets/js/vendor/jquery-jvectormap-world-mill-en.js"></script>
<script src="../assets/layui/layui.all.js"></script>
<script src="../assets/js/vue.js"></script>
<script>
$(document).ready(function(){
    $.ajax({
        type : "GET",
        url : "ajax.php?act=count",
        dataType : 'json',
        async: true,
        success : function(data) {
            if(data.code==0){
                if(data.count>0){
                    toastr.info('<a href="tixian.php">API管理平台共有<b>'+data.count+'</b>条提现申请，请赶快处理！</a>', '提现提醒');
                }
                if(data.count2>0){
                    toastr.warning('<a href="workorder.php">API管理平台共有<b>'+data.count2+'</b>个工单还未处理哦！</a>', '工单提醒');
                }
            }
        }
    });
});
function optim(){
    var ii = layer.msg('正在优化中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : 'POST',
        url : 'ajax.php?act=optim',
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
function repair(){
    var ii = layer.msg('正在修复中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : 'POST',
        url : 'ajax.php?act=repair',
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
function cleanbom() {
    var confirmobj = layer.confirm('你确实要清空缓存吗？', {
      icon: 3, btn: ['确定','取消']
    }, function(){
      $.ajax({
        type : 'GET',
        url : 'ajax.php?act=cleanbom',
        dataType : 'json',
        success : function(data) {
            if(data.code == 0){
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
    }, function(){
      layer.close(confirmobj);
    });
}
</script>