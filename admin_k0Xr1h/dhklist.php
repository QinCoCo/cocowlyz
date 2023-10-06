<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
$title='兑换卡列表';
include_once './header.php';
$numrows=$DB->count("SELECT count(*) from yixi_dhklist WHERE 1");
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                兑换卡列表
                <span class="badge badge-primary-lighten" onclick="dhkqk();">清空所有</span>
                <span class="badge badge-warning-lighten" onclick="dhkqk1();">清空已使用</span>
                <span class="badge badge-success-lighten" onclick="dhkqk2();">清空未使用</span>
            </div>
            <div class="card-body">
                <div class="layui-elem-quote">平台共有<?php echo $numrows;?>张兑换卡卡密</div>
				<form name="form1" id="form1">
                <div style="white-space:nowrap;overflow-x: auto;">
                    <table class="layui-table layuiadmin-page-table">
                        <thead><tr><th><div class="custom-control custom-checkbox"><input name="chkAll1" type="checkbox" id="chkAll1" onclick="selectAll(this);" value="checkbox" class="custom-control-input"><label class="custom-control-label" for="chkAll1"></label></div></th><th>ID</th><th>类型</th><th>卡密</th><th>详情</th><th>状态</th><th>添加时间</th><th>使用时间</th><th>操作</th></tr></thead>
                        <tbody>
                        <?php
                        $pagesize=30;
                        $pages=ceil($numrows/$pagesize);
                        $page=isset($_GET['page'])?intval($_GET['page']):1;
                        $offset=$pagesize*($page - 1);
                        $rs=$DB->query("SELECT * FROM yixi_dhklist WHERE 1 order by id desc limit $offset,$pagesize");
                        while($res = $DB->fetch($rs))
                        {
                        $program = $DB->get_row("select * from yixi_program where id='" . $res['proid'] . "' limit 1");
                        $status=$res['status']==0?'<font color="green">未使用</font>':'<font color="red">已使用</font>';
                        if ($res['status'] == 1) {
                            $lasttime = $res['lasttime'];
                        } else {
                            $lasttime = '卡密未使用';
                        }
                        if ($res['type'] == 1) {
                            $type_name = '授权兑换卡';
                            $xq = '可兑换'.$program['name'].'域名授权';
                        } else if ($res['type'] == 2) {
                            $type_name = '认证兑换卡';
                            $xq = '可兑换'.$program['name'].'易支付域名认证';
                        } else if ($res['type'] == 3) {
                            $type_name = '权限兑换卡';
                            if ($res['power'] == 3) {
                                $xq = '可兑换全能管理员权限';
                            } else if ($res['power'] == 2) {
                                $xq = '可兑换程序：'.$program['name'].'的超级管理员权限';
                            } else if ($res['power'] == 1) {
                                $xq = '可兑换程序：'.$program['name'].'的授权商权限';
                            }
						} else if ($res['type'] == 4) {
                            $type_name = '邀请码';
                            $xq = '可用于用户站点注册';
                        } else {
                            $type_name = '未知类型';
                            $xq = '无法兑换任何东西';
                        }
                        echo '<tr><td><div class="custom-control custom-checkbox"><input type="checkbox" name="checkbox[]" id="workorder'.$res['id'].'" value="'.$res['id'].'" class="custom-control-input"><label class="custom-control-label" for="workorder'.$res['id'].'"></label></div></td><td>'.$res['id'].'</td><td>'.$type_name.'</td><td>'.$res['km'].'</td><td>'.$xq.'</td><td>'.$status.'</td><td>'.$res['addtime'].'</td><td>'.$lasttime.'</td><td><span class="layui-btn layui-btn-xs btn-danger" onclick="dhkdel('.$res['id'].');">删除</a></td></tr>';
                        }
                        ?>
                        </tbody>
        </table>
		<div class="form-group mb-3">
        <input type="hidden" name="content"/>
        <label for="example-input-normal" style="font-weight: 500">操作：
        <select class="form-control" style="display: inline;width: auto;" name="aid"><option selected>批量操作</option><option value="1">&gt;删除选中</option><option value="2">&gt;导出选中</option><option value="3">&gt;导出全部</option></select>
        <button class="btn btn-sm btn-primary" type="button" onclick="change()">确定</button>
        </label>
        </div>
      </div>
	  </form>
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
<script src="https://cdn.bootcss.com/clipboard.js/2.0.4/clipboard.js"></script>
<script type="text/javascript">
function selectAll(checkbox) {
    $('input[type=checkbox]').prop('checked', $(checkbox).prop('checked'));
}
function change(){
    var ii = layer.msg('正在操作中,请稍后...', {icon: 16, time: 10 * 1000});
    $.ajax({
        type : 'POST',
        url : 'ajax.php?act=appdhk_change',
        data : $('#form1').serialize(),
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if(data.code == 0){
                layer.msg(data.msg, {
                    icon: 6,
                    end: function (layero, index) {
                        listTable();
                    }
                });
            }
			if(data.code == 1){
                layer.open({
                  type: 1,
                  title: '导出卡密',
                  skin: 'layui-layer-rim',
                  content: data.data
                });
			layer.msg(data.msg, {icon: 6});
			}
        },
        error:function(data){
            layer.msg('请求超时', {icon: 5});
        }
    });
    return false;
}
function dhkqk() {
    var confirmobj = layer.confirm('你确定要清空所有兑换卡卡密吗？', {
      btn: ['确定','取消']
    }, function(){
      $.ajax({
        type : 'GET',
        url : 'ajax.php?act=dhkqk',
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
function dhkqk1() {
    var confirmobj = layer.confirm('你确定要清空所有已使用的兑换卡卡密吗？', {
      btn: ['确定','取消']
    }, function(){
      $.ajax({
        type : 'GET',
        url : 'ajax.php?act=dhkqk1',
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
function dhkqk2() {
    var confirmobj = layer.confirm('你确定要清空所有未使用的兑换卡卡密吗？', {
      btn: ['确定','取消']
    }, function(){
      $.ajax({
        type : 'GET',
        url : 'ajax.php?act=dhkqk2',
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
function dhkdel(id) {
    var confirmobj = layer.confirm('你确定要删除这张兑换卡卡密吗？', {
      btn: ['确定','取消']
    }, function(){
      $.ajax({
        type : 'GET',
        url : 'ajax.php?act=dhkdel&id='+id,
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
<script>
    var codeClipboard = new ClipboardJS('#btn_code');
   codeClipboard.on('success', function (e) {
        layer.msg("卡密复制成功", {icon: 6});
        e.clearSelection();
    });
    codeClipboard.on('error', function (e) {
        layer.msg('复制失败,请手动复制~', {icon: 5});
    });
</script>