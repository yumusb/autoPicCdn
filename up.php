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

注意事项：
1. php中开启 Curl扩展
2. 如果使用github，则服务器需要能和https://api.github.com正常通信。（建议放到国外 http://renzhijia.com/buy/index/7/?yumu 美国免费空间推荐 优惠码 free2 ）
3. 如果使用Gitee，请保证 上传的文件 遵循国内法律
4. 懒的搭建或者不会搭建，就直接用 http://chuibi.cn/
5. 本源码已经开启智能AI授权模式，请到 http://33.al/donate 打赏5元以后 再开始配置
*/

error_reporting(0);
header('Content-Type: text/html; charset=UTF-8');
date_default_timezone_set("PRC");


if(!is_callable('curl_init')){
    $return['code'] = 500;
    $return['msg'] = "服务器不支持Curl扩展";
    $return['url'] = null;
    die(json_encode($return));
}

//必选项
define("TYPE","GITHUB");//选择github
//define("TYPE","GITEE");//选择gitee，如果使用gitee，需要手动建立master分支，可以看这里 https://gitee.com/help/articles/4122

define("USER","pic-cdn");//你的GitHub/Gitee的用户名

define("REPO","cdn2");//必须是上面用户名下的 公开仓库

define("MAIL","yumusb@foxmail.com");//邮箱无所谓，随便写

define("TOKEN","213");
// Github 去这个页面 https://github.com/settings/tokens生成一个有写权限的token（repo：Full control of private repositories 和write:packages前打勾）
// gitee  去往这个页面 https://gitee.com/personal_access_tokens

//数据库配置文件
//请确保把当前目录下的 pic.sql 导入到你的数据库
$database = array(
        'dbname' => 'YourDbName',//你的数据库名字
        'host' => 'localhost',
        'port' => 3306,
        'user' => 'YourDbUser',//你的数据库用户名
        'pass' => 'YourDbPass',//你的数据库用户名对应的密码
    );
    

$table = 'remote_imgs'; //表名字

if(TYPE!=="GITHUB" && TYPE!=="GITEE"){
    $return['code'] = 500;
    $return['msg'] = "Baby，你要传到哪里呢？";
    $return['url'] = null;
    die(json_encode($return));
}
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
function upload_github($filename, $content)
{   
    $url = "https://api.github.com/repos/" . USER . "/" . REPO . "/contents/" . $filename;
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

function upload_gitee($filename, $content)
{   
    $url = "https://gitee.com/api/v5/repos/". USER ."/". REPO ."/contents/".$filename;
    $ch = curl_init();
    $defaultOptions=[
        CURLOPT_URL => $url,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST=>"POST",
        CURLOPT_POSTFIELDS=>[
            "access_token"=>TOKEN,
            "message"=>"uploadfile",
            "content"=> $content,
            "owner"=>USER,
            "repo"=>REPO,
            "path"=>$filename,
            "branch"=>"master"
        ],
        CURLOPT_HTTPHEADER => [
            "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
            "Accept-Language:zh-CN,en-US;q=0.7,en;q=0.3",
            "User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36"
        ],
    ];
    curl_setopt_array($ch, $defaultOptions);
    $chContents = curl_exec($ch);
    curl_close($ch);
    return $chContents;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_FILES["pic"]["error"] <= 0 && $_FILES["pic"]["size"] >100 ) {
    $filename = date('Y') . '/' . date('m') . '/' . date('d') . '/' . md5(time().mt_rand(10,1000)) . ".png";
    $tmpName = './tmp' . md5($filename);
    move_uploaded_file($_FILES['pic']['tmp_name'], $tmpName);
    $filemd5 = md5_file($tmpName);
    $row = $db->query("SELECT `imgurl` FROM `{$table}` WHERE `imgmd5`= '{$filemd5}' ")->fetch(PDO::FETCH_ASSOC);
    if($row){
    	$remoteimg=$row['imgurl'];
    }else{
    	$content = base64_encode(file_get_contents($tmpName));
    	
    	if(TYPE==="GITHUB"){
    	    $res = json_decode(upload_github($filename, $content), true);
    	}
    	else{
    	    $res = json_decode(upload_gitee($filename, $content), true);
    	}
    	
		if($res['content']['path'] != ""){
		    if(TYPE==="GITHUB"){
    	        $remoteimg = 'https://cdn.jsdelivr.net/gh/' . USER . '/' . REPO . '@latest/' . $res['content']['path'];
        	}
        	else{
        	    $remoteimg = $res['content']['download_url'];
        	}
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
