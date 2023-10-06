<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
$title='加款卡列表';
include_once './header.php';
$numrows=$DB->count("SELECT count(*) from yixi_jkklist WHERE 1");
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                加款卡列表
                <span class="badge badge-primary-lighten" onclick="jkkqk();">清空所有</span>
                <span class="badge badge-warning-lighten" onclick="jkkqk1();">清空已使用</span>
                <span class="badge badge-success-lighten" onclick="jkkqk2();">清空未使用</span>
            </div>
            <div class="card-body">
                <div class="layui-elem-quote">平台共有<?php echo $numrows;?>张加款卡卡密</div>
                <div style="white-space:nowrap;overflow-x: auto;">
                    <table class="layui-table layuiadmin-page-table">
                        <thead><tr><th>ID</th><th>卡密</th><th>面值</th><th>状态</th><th>添加时间</th><th>使用时间</th><th>操作</th></tr></thead>
                        <tbody>
                        <?php
                        $pagesize=30;
                        $pages=ceil($numrows/$pagesize);
                        $page=isset($_GET['page'])?intval($_GET['page']):1;
                        $offset=$pagesize*($page - 1);
                        $rs=$DB->query("SELECT * FROM yixi_jkklist WHERE 1 order by id desc limit $offset,$pagesize");
                        while($res = $DB->fetch($rs))
                        {
                        $status=$res['status']==0?'<font color="green">未使用</font>':'<font color="red">已使用</font>';
                        if ($res['status'] == 1) {
                            $lasttime = $res['lasttime'];
                        } else {
                            $lasttime = '卡密未使用';
                        }
                        echo '<tr><td>'.$res['id'].'</td><td>'.$res['km'].'</td><td>'.$res['money'].'</td><td>'.$status.'</td><td>'.$res['addtime'].'</td><td>'.$lasttime.'</td><td><span class="layui-btn layui-btn-xs btn-danger" onclick="jkkdel('.$res['id'].');">删除</a></td></tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-center">
                <?php
                #分页
                $pageList=new Page($numrows,$pagesize,0,$link);
                echo $pageList->showPage();
                ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include_once './bottom.php';
?>
<script type="text/javascript">
function jkkqk() {
    var confirmobj = layer.confirm('你确定要清空所有加款卡卡密吗？', {
      btn: ['确定','取消']
    }, function(){
      $.ajax({
        type : 'GET',
        url : 'ajax.php?act=jkkqk',
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
function jkkqk1() {
    var confirmobj = layer.confirm('你确定要清空所有已使用的加款卡卡密吗？', {
      btn: ['确定','取消']
    }, function(){
      $.ajax({
        type : 'GET',
        url : 'ajax.php?act=jkkqk1',
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
function jkkqk2() {
    var confirmobj = layer.confirm('你确定要清空所有未使用的加款卡卡密吗？', {
      btn: ['确定','取消']
    }, function(){
      $.ajax({
        type : 'GET',
        url : 'ajax.php?act=jkkqk2',
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
function jkkdel(id) {
    var confirmobj = layer.confirm('你确定要删除这张加款卡卡密吗？', {
      btn: ['确定','取消']
    }, function(){
      $.ajax({
        type : 'GET',
        url : 'ajax.php?act=jkkdel&id='+id,
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