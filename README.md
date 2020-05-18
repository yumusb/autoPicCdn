# autoPicCdn 

白嫖Github的存储空间,通过jsdelivr全球加速(含有国内节点). 实现图床的目的.

目录介绍:

```
.
├── index.html # 前端首页
├── pic.sql # SQL语句用以创建数据表
├── README.md 
├── static # 静态文件夹
└── up.php #接口文件


```

使用:

配置选项

> https://github.com/settings/tokens 去这个页面生成一个有写权限的token（repo：Full control of private repositories 和write:packages前打勾）然后配置up.php中的相关字段..
>
> ```php
> define("REPO","testforapi");//必须是下面用户名下的公开仓库
> define("USER","yumusb");//必须是当前GitHub用户名
> define("MAIL","yumusb@foxmail.com");//
> define("TOKEN","YourToken");
>## 数据库配置（添加了统计上传文件功能，使用mysql存储）
>$database = array(
>		'dbname' => 'pic',
>		'host' => 'localhost',
>		'port' => 3306,
>		'user' => 'pic',
>		'pass' => '123456',
>	);
>$table = 'remote_imgs'; //存放数据表名字
> ```
就可以享受白嫖带来的乐趣了!  

更新记录:  
+ 2020.05.18 优化线上版本，现已经开放接口，可用于对接PicGo等本地客户端。详情见 https://chuibi.cn/ 。 
+ 2020.05.17 添加线上版本，可以在我们的网站直接配置仓库信息，而不必搭建。详情见 https://chuibi.cn/ 。 
+ 2020.04.21 优化交互提示，添加粘贴板上传（Chrome）。
+ 2020.04.21 换了一套前端，同时支持数据库记录文件Md5，相同md5文件第二次上传会直接从数据库调用。
+ 2020.03.27 重写交互方式，且现已支持`PicGo`中的`web-uploader`插件（Zqian）,配置API地址就写up.php的地址，POST参数填写`pic`

bug提交:  
目前程序对于自用来说，应该问题不大。当然也不排除有未考虑到的bug。  
鉴于程序特殊性，在issue中只提交一个截图是没办法解决问题。  
如果在多次尝试后还是不能使用的话，可以发送配置信息或者ftp信息到邮箱 2[at]33.al。  
暂不接受单截图、单提示 等无效bug信息提交。

如果有帮助到你 欢迎赞赏 http://33.al/donate
