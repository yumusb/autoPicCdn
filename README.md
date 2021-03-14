# autoPicCdn 

# 实现功能：

1. 选择Github  

   白嫖Github的存储空间，通过jsdelivr全球加速(含有国内节点)。实现图床的目的。

2. 选择Gitee

   白嫖Gitee的存储空间， 实现图床的目的。存储和访问节点都在国内，请在遵循国内相关法律的前提下使用。 文件在1M以上需要访客登录才能访问。1M以下，相当稳。

# 目录介绍:

```shell
.
├── index.html # 前端首页
├── pic.sql # SQL语句用以创建数据表
├── README.md 
├── static # 静态文件夹
└── up.php # 上传接口文件
```

# 使用:

**配置选项**

### 1.配置上传类型：



> + 选择github
>
> https://github.com/settings/tokens 去这个页面生成一个有写权限的token（repo：Full control of private repositories 和write:packages前打勾）然后配置up.php中的相关字段。
>
> ```php
> define("TYPE","GITHUB");//选择github
> define("USER","pic-cdn");//你的GitHub/Gitee的用户名
> define("REPO","cdn2");//必须是上面用户名下的 公开仓库
> define("MAIL","yumusb@foxmail.com");//邮箱无所谓，随便写
> define("TOKEN","YourToken");
> ```
> + 选择Gitee
>
> 去往这个页面 https://gitee.com/personal_access_tokens 生成你的token，然后建立空白仓库且初始化master分支。可以看这里 https://gitee.com/help/articles/4122
>
> 然后配置up.php的字段。
>
> ```php
> define("TYPE","GITEE");//选择gitee
> define("USER","pic-cdn");//你的GitHub/Gitee的用户名
> define("REPO","cdn2");//必须是上面用户名下的 公开仓库
> define("MAIL","yumusb@foxmail.com");//邮箱无所谓，随便写
> define("TOKEN","YourToken");
> ```
>
> 就可以享受白嫖带来的乐趣了!  

### 2.配置数据库：

请确保把源码目录下的 pic.sql 导入到你的数据库，然后更改相关配置项。

```php
$database = array(
        'dbname' => 'YourDbName',//你的数据库名字
        'host' => 'localhost',
        'port' => 3306,
        'user' => 'YourDbUser',//你的数据库用户名
        'pass' => 'YourDbPass',//你的数据库用户名对应的密码
    );
$table = 'remote_imgs'; //表名字
```

### 3. Enjoy it！

----------------



# 更新记录:  
+ 2021.03.14 

  chuibi.cn 提供跨域上传功能，可以在登陆后设置Origin白名单，同时提供单文件模板，让你两分钟内把autoPicCdn部署到任意Html托管平台。更多内容请登录后查看。

+ 2020.10.25 

  Github上传的文件生成CDN链接不再采用branch参数，而是直接调用Push后的sha值。解决了仓库大小限制问题【已经通过chuibi.cn在线测试】与Github不再使用master分支的问题。

+ 2020.10.16 

  添加gitee线上版本，详情见 https://chuibi.net/ 。 

+ 2020.07.07

  添加gitee上传方式，请在使用的同时遵循相关法律。

+ 2020.05.18

  优化Github线上版本，现已经开放接口，可用于对接PicGo等本地客户端。详情见 https://chuibi.cn/ 。 

+ 2020.05.17

  添加Github线上版本，可以在我们的网站直接配置仓库信息，而不必搭建。详情见 https://chuibi.cn/ 。 

+ 2020.04.21

  优化交互提示，添加粘贴板上传（Chrome）。

+ 2020.04.21

  换了一套前端，同时支持数据库记录文件Md5，相同md5文件第二次上传会直接从数据库调用。

+ 2020.03.27

  重写交互方式，且现已支持`PicGo`中的`web-uploader`插件（Zqian）,配置API地址就写up.php的地址，POST参数填写`pic`

# 线上版本：

+ https://chuibi.cn/ 

  支持Github的在线上传与API接口。通过github授权登录并设置仓库等信息后可用。

+ https://chuibi.net/

  支持Gitee的在线上传与API接口。通过gitee授权登录并设置仓库等信息后可用。

# bug提交:  

目前程序对于自用来说，应该问题不大。当然也不排除有未考虑到的bug。  
鉴于程序特殊性，在issue中只提交一个截图是没办法解决问题。  
如果在多次尝试后还是不能使用的话，可以发送配置信息或者ftp信息到邮箱 2[at]33.al。  
暂不接受单截图、单提示 等无效bug信息提交。

## 如果有帮助到你 欢迎赞赏 http://33.al/donate
