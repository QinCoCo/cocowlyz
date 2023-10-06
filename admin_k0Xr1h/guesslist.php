<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
$title='用户竞猜记录';
include_once './header.php';
$count=$DB->count("SELECT count(*) from yixi_guess WHERE 1");
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                用户竞猜记录
                <a href="set.php?mod=guess"  class="badge badge-primary-lighten">前往配置</a>
            </div>
            <div class="card-body">
                <div class="layui-elem-quote">平台共有[<?php echo $count?>]个竞猜历史</div>
                <div style="white-space:nowrap;overflow-x: auto;">
                    <table class="layui-table layuiadmin-page-table">
                        <thead><tr><th>ID</th><th>期数</th><th>押注</th><th>胜者</th><th>结果</th><th>资金</th><th>返利</th><th>中奖用户</th><th>操作</th></tr></thead>
                            <tbody>
                            <?php
                            $pagesize=30;
                            $pages=ceil($numrows/$pagesize);
                            $page=isset($_GET['page'])?intval($_GET['page']):1;
                            $offset=$pagesize*($page - 1);
                            $rs=$DB->query("SELECT * FROM yixi_guess WHERE 1 order by id desc limit $offset,$pagesize");
                            while($res = $DB->fetch($rs))
                            {
                                $user = $DB->get_row("select * from yixi_user where uid='" . $res['uid'] . "' limit 1");
                                    if($res['yzhm']=='1'){
                                        $iuser='①号选手';
                                    }else if($res['yzhm']=='2'){
                                        $iuser='②号选手';
                                    }else if($res['yzhm']=='3'){
                                        $iuser='③号选手';
                                    }else if($res['yzhm']=='4'){
                                        $iuser='④号选手';
                                    }else if($res['yzhm']=='5'){
                                        $iuser='⑤号选手';
                                    }else if($res['yzhm']=='6'){
                                        $iuser='⑥号选手';
                                    }else if($res['yzhm']=='7'){
                                        $iuser='⑦号选手';
                                    }else if($res['yzhm']=='8'){
                                        $iuser='⑧号选手';
                                    }else if($res['yzhm']=='9'){
                                        $iuser='⑨号选手';
                                    }else{
                                        $iuser='未知选手';
                                    }
                                    if($res['hm']=='1'){
                                        $islj='①号选手';
                                    }else if($res['hm']=='2'){
                                        $islj='②号选手';
                                    }else if($res['hm']=='3'){
                                        $islj='③号选手';
                                    }else if($res['hm']=='4'){
                                        $islj='④号选手';
                                    }else if($res['hm']=='5'){
                                        $islj='⑤号选手';
                                    }else if($res['hm']=='6'){
                                        $islj='⑥号选手';
                                    }else if($res['hm']=='7'){
                                        $islj='⑦号选手';
                                    }else if($res['hm']=='8'){
                                        $islj='⑧号选手';
                                    }else if($res['hm']=='9'){
                                        $islj='⑨号选手';
                                    }else{
                                        $islj='请敬请期待';
                                    }
                                    if(!$res['hm']){
                                        $isuse='未开奖，请耐心等待';
                                        $flmoney=$res['money'];
                                    }else if($res['yzhm']==$res['hm']){
                                        $isuse='<font color="green">恭喜您，中奖啦</font>';
                                        $flmoney=$res['money']*$conf['guess_multiple'];
                                    }else{
                                        $isuse='<font color="red">很遗憾，未中奖</font>';
                                        $flmoney='0.00';
                                    }
                                    echo '<tr><td>'.$res['id'].'</td><td>第'.$res['jcqs'].'期</td><td>'.$iuser.'</td><td>'.$islj.'</td><td>'.$isuse.'</td><td>'.$res['money'].'元</td><td>'.$flmoney.'元</td><td>'.$user['user'].'</td><td><span onclick="guessdel(' . $res['id'] . ')" class="layui-btn layui-btn-xs btn-danger">删除</span></td></tr>';
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
    </div>
</div>
<?php
include_once './bottom.php';
?>
<script type="text/javascript">
function guessdel(id) {
    var confirmobj = layer.confirm('你确实要删除这条竞猜记录吗？', {
      btn: ['确定','取消']
    }, function(){
      $.ajax({
        type : 'GET',
        url : 'ajax.php?act=guessdel&id='+id,
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