目录结构基本如下：

网站主要目录结构
www
├─images		
├─pay				微信jsAPI支付目录
├─statics			网站前端目录
│  ├─plugin			插件目录
│  │  ├─calendar	日历插件
│  │  ├─layer		弹出层插件
│  │  ├─PHPExcel	phpexcel插件（用来订单的导出和导入）
│  │  ├─style		后台的前端插件目录（css、js、image）
│  │  ├─ueditor		文本编辑器插件
│  │  ├─uploadify	上传插件
│  │  ├─...			更多插件库
│  ├─templates		网站前台视图目录（html、css、js、image、font）
│  │  ├─vshop		视图目录
│  │  │  ├─css		样式目录
│  │  │  ├─font		字体库
│  │  │  ├─images	图片目录
│  │  │  ├─img		系统图片目录		
│  │  │  ├─js		js目录
│  │  │  └─v1		前台html目录（手机端的网页是在mobile子目录中）
│  │  └─yungouquanqiu 原视图目录
│  └─uploads		上传图片目录  
├─system			系统核心目录（所有的控制器、类库等）
│  ├─caches			系统的缓存目录
│  ├─config			系统配置目录
│  ├─funcs			系统函数目录
│  ├─libs			系统类库目录
│  ├─modules		模块目录（包括前台和后台）
│  ├─phpqrcode		php生成二维码目录
│  ├─plugin			第三方类库
│  └─wechat			微信公众号设置目录
├─.htaccess			重写文件
├─alipay.txt		支付宝支付日志
├─index.php			网站的入口文件
├─server.php		websocket服务端文件（抽奖功能需要运行的，平时可以不用运行）
├─v4wshop.sql		网站数据库
└─README.txt		网站说明

system/config 下的文件说明：
database.inc.php（数据库连接文件），
domain.inc.php（域名绑定配置文件），
email.inc.php（邮箱设置文件），
global.php（全局变量定义），
mobile.inc.php（手机短信通道配置文件），
param.inc.php（路由文件），
pay.inc.php（支付配置），
send.inc.php（短信发送通道配置），
system.inc.php（系统全局配置），
templates.inc.php（模板配置），
upload.inc.php（上传文件相关的配置项），
version.inc.php（系统版本号）

system/modules/mobile/ 下的文件说明：
lib（手机端调用基础方法存放地址）
ajax.action.php（手机端异步响应处理文件）
cart.action.php
home.action.php（个人中心）

系统自动加载函数：
Func：&load_sys_class()加载系统类函数
      load_class_file_name()获取加载的类文件名称


网站部署说明
PHP版本要求：php 5.3
①创建虚拟主机
把整个网站目录通过ftp上传到虚拟主机的目录下，或者，通过svn或git提交到代码库，然后再更新到根目录下
②导入数据库
把.sql的数据库文件通过phpmyadmin导入到数据库，或者通过命令提示符进行导入
③打开system>>config>>databases.inc.php文件，进行数据库的配置（数据库名、数据库密码等）
④打开system>>config>>domain.inc.php文件，进行域名模块绑定。
'pt1618.cn' =>		//域名
        array (
        'm' => 'mobile',	//模块
        'c' => 'mobile',	//控制器
        'a' => 'init',		//方法
        ),
⑤登录后台进行进一步的配置：域名/admin  后台账号：maple 密码：1397*/520 或 admin   123456
⑥配置微信服务器等配置步骤，请参照拼团户外目录中的操作文档
  数据库账号：root  密码：ubuntu