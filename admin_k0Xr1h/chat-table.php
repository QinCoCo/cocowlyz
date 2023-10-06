<?php
include_once '../includes/common.php';
if($islogin==1){}else sysmsg("您还未登录，请先登录",2,'./login.php',true);
function ubb_parse($content) {
    $n = htmlspecialchars($content, ENT_QUOTES); 
    if(strpos($n,'[img]')!==false) {
        $n = preg_replace('/\[img\](.+?)\[\/img\]/is','<img src="\\1" width="100%" style="border-radius: 5px;max-width: 150px;" onclick="add_face(\'\\1\');">',$n);
    }
    if(strpos($n,'[br]')!==false) {
        $n = str_replace("[br]","<br/>",$n);
    }
    if(strpos($n,'[color')!==false) {
        $n = preg_replace("/\[color=(.+?)\](.+?)\[\/color\]/is","<font color=\"\\1\">\\2</font>",$n);
    }
    if(strpos($n,'[url]')!==false) {
        $n=preg_replace("/\[url=(http:\/\/.+?)\](.+?)\[\/url\]/is","<u><a href='\\1' target='_blank'>\\2</a></u>",$n);
        $n=preg_replace("/\[url\](http:\/\/.+?)\[\/url\]/is","<u><a href='\\1' target='_blank'>\\1</a></u>",$n); 
    }
    if(strpos($n,'[move]')!==false) {
        $n=preg_replace("/\[move\](.+?)\[\/move\]/is","<marquee width=\"98%\" scrollamount=\"3\">\\1</marquee>",$n);
    }
    return $n;
}
include_once '../chat_template/chat'.$conf['chat_template'].'/chat-table.php';