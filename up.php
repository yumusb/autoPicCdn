<?php
/*
 * @Author: yumusb
 * @Date: 2020-03-27 14:45:07
 * @LastEditors: yumusb
 * @LastEditTime: 2020-03-27 14:45:34
 * @Description: 
 */
/*
URL https://github.com/yumusb/autoPicCdn
*/

error_reporting(0);
header('Content-Type: text/html; charset=UTF-8');
date_default_timezone_set("PRC");
define("REPO","repo");//必须是下面用户名下的公开仓库
define("USER","username");//必须是当前GitHub用户名
define("MAIL","mail@foxmail.com");//
define("TOKEN","token");//https://github.com/settings/tokens 去这个页面生成一个有写权限的token（write:packages前打勾）

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
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_FILES["pic"]["error"] <= 0) {
    $filename = date('Y') . '/' . date('m') . '/' . date('d') . '/' . substr(md5(time()) , 0 , 6) . ".png";
    $url = "https://api.github.com/repos/" . USER . "/" . REPO . "/contents/" . $filename;
    $tmpName = './tmp' . md5($filename);
    move_uploaded_file($_FILES['pic']['tmp_name'], $tmpName);
    $content = base64_encode(file_get_contents($tmpName));
    $res = json_decode(upload($url, $content), true);
    unlink($tmpName);
    if ($res['content']['path'] != "") {
        $return['code'] = 'success';
        $return['data']['filename'] = $filename;
        $return['data']['url'] = 'https://cdn.jsdelivr.net/gh/' . USER . '/' . REPO . '/' . $res['content']['path'];
    } else {
        $return['code'] = 500;
        $return['url'] = null;
    }
} else {
    $return['code'] = 404;
    $return['url'] = null;
}
exit(json_encode($return));
