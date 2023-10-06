<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
$title='收支明细';
include_once './header.php';
?>
<?php
if(isset($_GET['uid'])){
	$uid = intval($_GET['uid']);
	$sql = " uid=$uid";
	$link = '&uid='.$uid;
}else{
	$uid = 0;
	$sql = " 1";
}
$thtime=date("Y-m-d").' 00:00:00';
$lastday=date("Y-m-d",strtotime("-1 day")).' 00:00:00';
$income_today=$DB->count("SELECT sum(point) FROM yixi_points WHERE action='提成' AND{$sql} AND addtime>'$thtime'");
$outcome_today=$DB->count("SELECT sum(point) FROM yixi_points WHERE action='消费' AND{$sql} AND addtime>'$thtime'");
$income_lastday=$DB->count("SELECT sum(point) FROM yixi_points WHERE action='提成' AND{$sql} AND addtime<'$thtime' AND addtime>'$lastday'");
$outcome_lastday=$DB->count("SELECT sum(point) FROM yixi_points WHERE action='消费' AND{$sql} AND addtime<'$thtime' AND addtime>'$lastday'");
if(isset($_GET['uid'])){
$income_all=$DB->count("SELECT sum(point) FROM yixi_points WHERE action='提成' AND{$sql}");
$outcome_all=$DB->count("SELECT sum(point) FROM yixi_points WHERE action='消费' AND{$sql}");
}

$numrows=$DB->count("SELECT count(*) from yixi_points WHERE{$sql}");
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <?php echo ($uid>0?'用户UID:<b>'.$uid.'</b> ':'全部用户')?>收支明细
            </div>
            <div class="card-body">
                <table class="layui-table layuiadmin-page-table">
                    <tbody>
                        <tr height="25">
                            <td align="center"><font color="#808080"><b><span class="glyphicon glyphicon-tint"></span>今日收益</b></br><?php echo round($income_today,2)?>元</font></td>
                            <td align="center"><font color="#808080"><b><i class="glyphicon glyphicon-check"></i>今日消费</b></br></span><?php echo round($outcome_today,2)?>元</font></td>
                            <td align="center"><font color="#808080"><b><span class="glyphicon glyphicon-tint"></span>昨日收益</b></br><?php echo round($income_lastday,2)?>元</font></td>
                            <td align="center"><font color="#808080"><b><i class="glyphicon glyphicon-check"></i>昨日消费</b></br></span><?php echo round($outcome_lastday,2)?>元</font></td>
                            <?php if(isset($_GET['uid'])){?>
                            <td align="center"><font color="#808080"><b><span class="glyphicon glyphicon-tint"></span>总计收益</b></br><?php echo round($income_all,2)?>元</font></td>
                            <td align="center"><font color="#808080"><b><i class="glyphicon glyphicon-check"></i>总计消费</b></br></span><?php echo round($outcome_all,2)?>元</font></td>
                            <?php }?>
                        </tr>
                    </tbody>
                </table>
                <div style="white-space:nowrap;overflow-x: auto;">
                    <table class="layui-table layuiadmin-page-table">
                        <thead><tr><th>ID</th><th>用户UID</th><th>类型</th><th>金额</th><th>详情</th><th>时间</th></tr></thead>
                        <tbody>
                        <?php
                        $pagesize=30;
                        $pages=ceil($numrows/$pagesize);
                        $page=isset($_GET['page'])?intval($_GET['page']):1;
                        $offset=$pagesize*($page - 1);
                        $rs=$DB->query("SELECT * FROM yixi_points WHERE{$sql} order by id desc limit $offset,$pagesize");
                        while($res = $DB->fetch($rs))
                        {
                        echo '<tr><td><b>'.$res['id'].'</b></td><td><a href="userlist.php?uid='.$res['uid'].'">'.$res['uid'].'</a></td><td>'.$res['action'].'</td><td><font color="'.(in_array($res['action'],array('提成','奖励','赠送','退款','退回','充值','加款','中奖','邀请奖励','收款','转换'))?'red':'green').'">'.$res['point'].'</font></td><td>'.$res['bz'].'</td><td>'.$res['addtime'].'</td></tr>';
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