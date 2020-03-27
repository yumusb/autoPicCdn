# autoPicCdn 

白嫖Github的存储空间,通过jsdelivr全球加速(含有国内节点). 实现图床的目的.

目录介绍:

```
.
├── index.html # 简单上传页面
├── README.md 
└── up.php # 上传接口，需要能访问github，建议放到非大陆服务器

0 directories, 3 files

```

使用:

配置选项

> https://github.com/settings/tokens 去这个页面生成一个有写权限的token（write:packages前打勾）然后配置up.php中的相关字段..
>
> ```php
> define("REPO","testforapi");//必须是下面用户名下的公开仓库
> define("USER","yumusb");//必须是当前GitHub用户名
> define("MAIL","yumusb@foxmail.com");//
> define("TOKEN","YourToken");
> ```
就可以享受白嫖带来的乐趣了!  

更新记录:

+ 2020.03.27 重写交互方式，且现已支持`PicGo`中的`web-uploader`插件（Zqian）,配置API地址就写up.php的地址，POST参数填写`pic`

  

如果有帮助到你 欢迎赞赏 http://33.al/donate
