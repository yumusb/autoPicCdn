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
define("REPO","testforapi");//必须是下面用户名下的公开仓库
define("USER","yumusb");//必须是当前GitHub用户名
define("MAIL","yumusb@foxmail.com");//
define("TOKEN","YourToken");//https://github.com/settings/tokens 去这个页面生成一个有写权限的token（repo：Full control of private repositories 和write:packages前打勾）

//数据库配置文件
$database = array(
        'dbname' => 'pic',
        'host' => 'localhost',
        'port' => 3306,
        'user' => 'pic',
        'pass' => '123456',
    );
    

$table = 'remote_imgs'; //表名字

/* 创建表
CREATE TABLE `pic`.`remote_imgs` ( `imgmd5` VARCHAR(32) NOT NULL COMMENT '文件md5' , `imguploadtime` INT(10) NOT NULL COMMENT '上传时间，10位时间戳' , `imguploadip` VARCHAR(20) NOT NULL COMMENT '上传IP' , `imgurl` VARCHAR(200) NOT NULL COMMENT '远程访问URL' , PRIMARY KEY (`imgmd5`)) ENGINE = InnoDB COMMENT = '图片统计表';
*/

try {
    $db = new PDO("mysql:dbname=" . $database['dbname'] . ";host=" . $database['host'] . ";" . "port=" . $database['port'] . ";", $database['user'], $database['pass'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8"));
} catch (PDOException $e) {
    $return['code'] = 500;
    $return['msg'] = "数据库出错，请检查 up.php中的database配置项.<br> " . $e->getMessage();
    $return['url'] = null;
    die(json_encode($return));
}


function GetIP(){ 
	if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) 
	$ip = getenv("HTTP_CLIENT_IP"); 
	else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) 
	$ip = getenv("HTTP_X_FORWARDED_FOR"); 
	else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) 
	$ip = getenv("REMOTE_ADDR"); 
	else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) 
	$ip = $_SERVER['REMOTE_ADDR']; 
	else
	$ip = "unknow"; 
	return($ip); 
}
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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_FILES["pic"]["error"] <= 0 && $_FILES["pic"]["size"] >100 ) {
    $filename = date('Y') . '/' . date('m') . '/' . date('d') . '/' . md5(time().mt_rand(10,1000)) . ".png";
    $url = "https://api.github.com/repos/" . USER . "/" . REPO . "/contents/" . $filename;
    $tmpName = './tmp' . md5($filename);
    move_uploaded_file($_FILES['pic']['tmp_name'], $tmpName);
    $filemd5 = md5_file($tmpName);
    $row = $db->query("SELECT `imgurl` FROM `{$table}` WHERE `imgmd5`= '{$filemd5}' ")->fetch(PDO::FETCH_ASSOC);
    if($row){
    	$remoteimg=$row['imgurl'];
    }else{
    	$content = base64_encode(file_get_contents($tmpName));
    	$res = json_decode(upload($url, $content), true);
		if($res['content']['path'] != ""){
			$remoteimg = 'https://cdn.jsdelivr.net/gh/' . USER . '/' . REPO . '@latest/' . $res['content']['path'];
	    	$tmp = $db->prepare("INSERT INTO `{$table}`(`imgmd5`, `imguploadtime`, `imguploadip`,`imgurl`) VALUES (?,?,?,?)");
	    	$tmp->execute(array($filemd5, time(), GetIP(), $remoteimg));
		}
    }
    unlink($tmpName);
    if ($remoteimg != "") {
        $return['code'] = 'success';
        $return['data']['url'] = $remoteimg;
        $return['data']['filemd5'] = $filemd5;
    } else {
        $return['code'] = 500;
        $return['msg'] = '上传失败，我们会尽快修复';
        $return['url'] = null;
    }
} else {
    $return['code'] = 404;
    $return['msg'] = '无法识别你的文件';
    $return['url'] = null;
}
exit(json_encode($return));
