<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
$title='压缩包上传';
include_once './header.php';
$path = isset($_POST['path']) ? (purge($_POST['path'])) : (isset($_GET['path']) ? purge($_GET['path']) : '');
?>
<?php
    //功能流程开始
    include_once '../includes/pclzip.php';//zip压缩类
    echo '<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                压缩包上传(请打包压缩包，系统会自动解压)
            </div>
		<form action="uploadfile.php?path='.$path.'" method="POST" enctype="multipart/form-data">
	          <div class="card-body">
			   <div class="form-group mb-3">
                        <label for="example-input-normal" style="font-weight: 500">文件路径：</label>
                        <input type="text" class="form-control" name="path" value="'.$path.'" lay-verType="tips" lay-verify="required">
                    </div>
				<div class="form-group mb-3">
                <label for="file"></label>
                <input type="file" name="file" id="file"/>
				</div>
                <input type="hidden" name="s" value="1"/><br>
                <input type="submit" class="btn btn-block btn-xs btn-outline-primary" value="确认上传"/>
                <a href="uploadfile.php?mod=clean&path='.$path.'" class="btn btn-block btn-xs btn-outline-info">清空原数据</a>
            </form>';
	if($_GET['mod']=='clean'){
		if(!$path)exit('<hr>未填写上传目录');
		if(!is_dir(ROOT.PACKAGE_DIR.'/'.$path))exit('<hr>目录不存在');
        if(deldir(ROOT.PACKAGE_DIR.'/'.$path)){
            exit('<hr><p>原旧数据已经删除</p>');
        }else{
            exit('<hr><p>原旧数据未能删除，请检查目录是否有权限</p>');
        }
    }
    if($_POST['s']==1){
        $extension=explode('.',$_FILES['file']['name']);
        if(($length=count($extension))>1){
            $ext=strtolower($extension[$length - 1]);
        }
        if($ext=='zip'){//判断是否是zip类 防止恶意上传
			if(!$path)exit('<hr>未填写上传目录');
            echo "<hr>上传成功，正在解压中";
            copy($_FILES['file']['tmp_name'], ROOT.PACKAGE_DIR.'/'.$path.'/release/release.zip');
            $zip = new ZipArchive;
            if($zip->open(ROOT.PACKAGE_DIR.'/'.$path.'/release/release.zip') && $zip->extractTo(ROOT.PACKAGE_DIR.'/'.$path.'/release/')){
                echo '<p>压缩包解压成功</p>';
                $zip->close(ROOT.PACKAGE_DIR.'/'.$path.'/release/release.zip');
                unlink(ROOT.PACKAGE_DIR.'/'.$path.'/release/release.zip');
                exit ('<p>压缩包已经自动删除</p>');
            }else{
                exit('<p>压缩包解压失败</p>');
            }
        }else{
            exit("<hr><font color=red>请打包好压缩包再上传，系统会自动解压！</font>");
        }
    }
    //核心流程结束
    echo '<div class="card-footer">
          <span class="layui-icon layui-icon-tips"></span> 
          <font color=red>注意:上传之前请先清空原压缩包数据</span> 
          </div>';
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