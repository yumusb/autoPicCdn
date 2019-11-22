<?php
/*
URL https://github.com/yumusb/autoPicCdn
*/

error_reporting(0);
header('Content-Type: text/html; charset=UTF-8');
date_default_timezone_set("PRC");
define("REPO","testforapi");//必须是下面用户名下的公开仓库
define("USER","yumusb");//必须是当前GitHub用户名
define("MAIL","yumusb@foxmail.com");//
define("TOKEN","YourToken");//https://github.com/settings/tokens 去这个页面生成一个有写权限的token（write:packages前打勾）

function upload($url, $content)
{
    $ch = curl_init();
    $defaultOptions=[
        CURLOPT_URL => $url,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST=>"PUT",
        CURLOPT_POSTFIELDS=>json_encode([
            "message"=>"uploadfile",
            "committer"=> [
                "name"=> USER,
                "email"=>MAIL,
            ],
            "content"=> $content,
        ]),
        CURLOPT_HTTPHEADER => [
            "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
            "Accept-Language:zh-CN,en-US;q=0.7,en;q=0.3",
            "User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36",
            'Authorization:token '.TOKEN,
        ],
    ];
    curl_setopt_array($ch, $defaultOptions);
    $chContents = curl_exec($ch);
    curl_close($ch);
    return $chContents;
}

$ext=trim($_POST['ext']);
$content=trim($_POST['content']);

if($ext!="" && $content !=""){
    $filename=date('Y').'/'.date('m').'/'.date('d').'/'.md5(time()).".png";
    $url="https://api.github.com/repos/".USER."/".REPO."/contents/".$filename;
    $res=json_decode(upload($url,$content),true);
    if($res['content']['path']!=""){
        $return['code']=200;
        $return['url']='https://cdn.jsdelivr.net/gh/'.USER.'/'.REPO.'@master/'.$res['content']['path'];
    }else{
        $return['code']=500;
        $return['url']=null;
    }
}else{
    $return['code']=404;
    $return['url']=null;
}


exit(json_encode($return));
