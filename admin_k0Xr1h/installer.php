<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
$id=isset($_GET['id'])?intval($_GET['id']):sysmsg("参数错误",2,'./',true);
$row=$DB->get_row("SELECT * FROM yixi_program WHERE id='{$id}' limit 1");
if(!$row)sysmsg("程序不存在",2,'./prolist.php',true);
$title='程序安装包上传';
include_once './header.php';
?>
<?php
    //功能流程开始
    include_once '../includes/pclzip.php';//zip压缩类
    echo '<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                程序安装包(请打包安装包，系统会自动解压)
            </div>
            <div class="card-body">';
    if($_POST['s']==1){
        $extension=explode('.',$_FILES['file']['name']);
        if(($length=count($extension))>1){
            $ext=strtolower($extension[$length - 1]);
        }
        if($ext=='zip'){//判断是否是zip类 防止恶意上传
            echo "上传成功，正在解压中";
            copy($_FILES['file']['tmp_name'], ROOT.PACKAGE_DIR.'/'.$row['catalog'].'/release6000/release6000.zip');
            $zip = new ZipArchive;
            if($zip->open(ROOT.PACKAGE_DIR.'/'.$row['catalog'].'/release6000/release6000.zip') && $zip->extractTo(ROOT.PACKAGE_DIR.'/'.$row['catalog'].'/release6000/')){
                echo '<p>安装包解压成功</p>';
                $zip->close(ROOT.PACKAGE_DIR.'/'.$row['catalog'].'/release6000/release6000.zip');
                unlink(ROOT.PACKAGE_DIR.'/'.$row['catalog'].'/release6000/release6000.zip');
                exit ('<p>安装包已经自动删除</p>');
            }else{
                exit('<p>安装包解压失败</p>');
            }
        }
        else{
            echo "<font color=red>请打包好安装包再上传，系统会自动解压！</font>";
        }
    }
    if($_GET['mod']=='clean'){
        if(delFile(ROOT.PACKAGE_DIR.'/'.$row['catalog'].'/release6000')==true){
            exit('<p>原旧数据已经删除</p>');
        }else{
            exit('<p>原旧数据未能删除，请检查目录是否有权限</p>');
        }
    }
    //核心流程结束
    echo '     <form action="installer.php?id='.$id.'" method="POST" enctype="multipart/form-data">
                <label for="file"></label>
                <input type="file" name="file" id="file"/>
                <input type="hidden" name="s" value="1"/><br>
                <input type="submit" class="btn btn-block btn-xs btn-outline-primary" value="确认上传"/>
                <a href="installer.php?mod=clean" class="btn btn-block btn-xs btn-outline-info">清空原数据</a>
            </form>';
    echo '</div> <div class="card-footer">
          <span class="layui-icon layui-icon-tips"></span> 
          <font color=red>注意:上传之前请先清空原安装包数据<br></font>
          <span class="layui-icon layui-icon-tips"></span> 
          <font color=red>上传成功后，点击"<font color=blue>安装包设置</font>"更改安装信息</font></div>';
?>
</div>
</div>
</div>
<?php
include_once './bottom.php';
?>
<script type="text/javascript">
var items = $("select[default]");
for (i = 0; i < items.length; i++) {
    $(items[i]).val($(items[i]).attr("default"));
}
</script>