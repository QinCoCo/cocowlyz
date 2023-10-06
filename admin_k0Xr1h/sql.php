<?php
include_once '../includes/common.php';
//if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
$title='卡密导出';
ini_set("max_execution_time", "180");//避免数据量过大，导出不全的情况出现。
$host=$dbconfig['host'];
$dbname=$dbconfig['dbname'];
$username=$dbconfig['user'];
$passw=$dbconfig['pwd'];
$filename=date("Y-m-d_H-i-s")."-".$dbname.".sql";
$uid = isset($_GET['uid']) ? intval($_GET['uid']) : exit("uid不存在");
header("Content-disposition:filename=".$filename);//所保存的文件名
header("Content-type:application/octetstream");
header("Pragma:no-cache");
header("Expires:0");

//备份数据
$i = 0;
$crlf="\r\n";
global $dbconn;
$dbconn = mysql_connect($host,$username,$passw);//数据库主机，用户名，密码
$db = mysql_select_db($dbname,$dbconn);
mysql_query("SET NAMES 'utf8'");
print "-- filename=".$filename;


print $crlf;
//echo get_table_structure($dbname, 'yixi_apps', $crlf).";$crlf$crlf";//导出表结构
echo get_table_content2($dbname, 'yixi_apps', $crlf, $uid);//导出表内容
//echo get_table_structure($dbname, 'yixi_appkm', $crlf).";$crlf$crlf";//导出表结构
echo get_table_content($dbname, 'yixi_appkm', $crlf, $uid);//导出表内容


/*新增的获得详细表结构*/
function get_table_structure($db,$table,$crlf)
{
global $drop;

$schema_create = "";
if(!empty($drop)){ $schema_create .= "DROP TABLE IF EXISTS `$table`;$crlf";}
$result =mysql_db_query($db, "SHOW CREATE TABLE $table");
$row=mysql_fetch_array($result);
$schema_create .= $crlf."-- ".$row[0].$crlf;
$schema_create .= $row[1].$crlf;
Return $schema_create;
}

//获得表内容
function get_table_content($db, $table, $crlf, $uid)
{
$schema_create = "";
$temp = "";
$result = mysql_db_query($db, "SELECT * FROM $table WHERE upid='".$uid."'");
$i = 0;
while($row = mysql_fetch_row($result))
{
$schema_insert = "INSERT INTO `$table` VALUES (";
for($j=0; $j<mysql_num_fields($result);$j++)
{
if(!isset($row[$j]))
$schema_insert .= " NULL,";
elseif($row[$j] != "")
$schema_insert .= " '".addslashes($row[$j])."',";
else
$schema_insert .= " '',";
}
$schema_insert = ereg_replace(",$", "",$schema_insert);
$schema_insert .= ");$crlf";
$temp = $temp.$schema_insert ;
$i++;
}
return $temp;
}


function get_table_content2($db, $table, $crlf, $uid)
{
$schema_create = "";
$temp = "";
$result = mysql_db_query($db, "SELECT * FROM $table WHERE uid='".$uid."'");
$i = 0;
while($row = mysql_fetch_row($result))
{
$schema_insert = "INSERT INTO `$table` VALUES (";
for($j=0; $j<mysql_num_fields($result);$j++)
{
if($j!=1){
if(!isset($row[$j]))
$schema_insert .= " NULL,";
elseif($row[$j] != "")
$schema_insert .= " '".addslashes($row[$j])."',";
else
$schema_insert .= " '',";
}
}
$schema_insert = ereg_replace(",$", "",$schema_insert);
$schema_insert .= ");$crlf";
$temp = $temp.$schema_insert ;
$i++;
}
return $temp;
}
?>