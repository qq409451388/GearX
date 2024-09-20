# GearX简介


<svg xmlns="http://www.w3.org/2000/svg" width="105" height="20">
    <linearGradient id="b" x2="0" y2="100%">
        <stop offset="0" stop-color="#bbb" stop-opacity=".1"/>
        <stop offset="1" stop-opacity=".1"/>
    </linearGradient>
    <mask id="a">
        <rect width="105" height="20" rx="3" fill="#fff"/>
    </mask>
    <g mask="url(#a)">
        <path fill="#555" d="M0 0h42v20H0z"/>
        <path fill="#007ec6" d="M42 0h63v20H42z"/>
        <path fill="url(#b)" d="M0 0h105v20H0z"/>
    </g>
    <g fill="#fff" text-anchor="middle" font-family="DejaVu Sans,Verdana,Geneva,sans-serif" font-size="11">
        <text x="21" y="15" fill="#010101" fill-opacity=".3">github</text>
        <text x="21" y="14">github</text>
        <text x="73.5" y="15" fill="#010101" fill-opacity=".3">GearX</text>
        <text x="73.5" y="14">GearX</text>
    </g>
</svg>

<svg xmlns="http://www.w3.org/2000/svg" width="105" height="20">
    <linearGradient id="b" x2="0" y2="100%">
        <stop offset="0" stop-color="#bbb" stop-opacity=".1"/>
        <stop offset="1" stop-opacity=".1"/>
    </linearGradient>
    <mask id="a">
        <rect width="105" height="20" rx="3" fill="#fff"/>
    </mask>
    <g mask="url(#a)">
        <path fill="#555" d="M0 0h42v20H0z"/>
        <path fill="#fe7d37" d="M42 0h63v20H42z"/>
        <path fill="url(#b)" d="M0 0h105v20H0z"/>
    </g>
    <g fill="#fff" text-anchor="middle" font-family="DejaVu Sans,Verdana,Geneva,sans-serif" font-size="11">
        <text x="21" y="15" fill="#010101" fill-opacity=".3">version</text>
        <text x="21" y="14">version</text>
        <text x="73.5" y="15" fill="#010101" fill-opacity=".3">beta 0.2.5</text>
        <text x="73.5" y="14">beta 0.2.5</text>
    </g>
</svg>

> GearX是一款纯PHP实现的轻量化的开发框架，
> 旨在提供快速且舒适的方式开发项目，
> 尤其是对于有过Java开发经验的开发者而言
> <br/>
> <br/>
> **设计文档：**[点击跳转](./DESIGN.md)
> <br/>
> **变更文档：**[点击跳转](./CHANGELIST.md)
> <br/>
> **开源许可：**[点击跳转](./LICENSE)

# 特性&能力
<svg xmlns="http://www.w3.org/2000/svg" width="105" height="20">
    <linearGradient id="b" x2="0" y2="100%">
        <stop offset="0" stop-color="#bbb" stop-opacity=".1"/>
        <stop offset="1" stop-opacity=".1"/>
    </linearGradient>
    <mask id="a">
        <rect width="105" height="20" rx="3" fill="#fff"/>
    </mask>
    <g mask="url(#a)">
        <path fill="#555" d="M0 0h42v20H0z"/>
        <path fill="#44cc11" d="M42 0h63v20H42z"/>
        <path fill="url(#b)" d="M0 0h105v20H0z"/>
    </g>
    <g fill="#fff" text-anchor="middle" font-family="DejaVu Sans,Verdana,Geneva,sans-serif" font-size="11">
        <text x="21" y="15" fill="#010101" fill-opacity=".3">特性</text>
        <text x="21" y="14">特性</text>
        <text x="73.5" y="15" fill="#010101" fill-opacity=".3">纯PHP</text>
        <text x="73.5" y="14">纯PHP</text>
    </g>
</svg>
<svg xmlns="http://www.w3.org/2000/svg" width="105" height="20">
    <linearGradient id="b" x2="0" y2="100%">
        <stop offset="0" stop-color="#bbb" stop-opacity=".1"/>
        <stop offset="1" stop-opacity=".1"/>
    </linearGradient>
    <mask id="a">
        <rect width="105" height="20" rx="3" fill="#fff"/>
    </mask>
    <g mask="url(#a)">
        <path fill="#555" d="M0 0h42v20H0z"/>
        <path fill="#e05d44" d="M42 0h63v20H42z"/>
        <path fill="url(#b)" d="M0 0h105v20H0z"/>
    </g>
    <g fill="#fff" text-anchor="middle" font-family="DejaVu Sans,Verdana,Geneva,sans-serif" font-size="11">
        <text x="21" y="15" fill="#010101" fill-opacity=".3">特性</text>
        <text x="21" y="14">特性</text>
        <text x="73.5" y="15" fill="#010101" fill-opacity=".3">常驻内存</text>
        <text x="73.5" y="14">常驻内存</text>
    </g>
</svg>
<svg xmlns="http://www.w3.org/2000/svg" width="105" height="20">
    <linearGradient id="b" x2="0" y2="100%">
        <stop offset="0" stop-color="#bbb" stop-opacity=".1"/>
        <stop offset="1" stop-opacity=".1"/>
    </linearGradient>
    <mask id="a">
        <rect width="105" height="20" rx="3" fill="#fff"/>
    </mask>
    <g mask="url(#a)">
        <path fill="#555" d="M0 0h42v20H0z"/>
        <path fill="#fe7d37" d="M42 0h63v20H42z"/>
        <path fill="url(#b)" d="M0 0h105v20H0z"/>
    </g>
    <g fill="#fff" text-anchor="middle" font-family="DejaVu Sans,Verdana,Geneva,sans-serif" font-size="11">
        <text x="21" y="15" fill="#010101" fill-opacity=".3">特性</text>
        <text x="21" y="14">特性</text>
        <text x="73.5" y="15" fill="#010101" fill-opacity=".3">自定义注解</text>
        <text x="73.5" y="14">自定义注解</text>
    </g>
</svg>

1. 框架完全由PHP实现，包括项目开发中的常用功能，并可以按需获取引入，以下是部分Module
   1. **GearModule-DB[[点击跳转]](https://github.com/qq409451388/GearModule-DB)** 数据库的查询组件，简化了大量操作，支持常见的数据库协议
   2. **GearModule-ORM[[点击跳转]](https://github.com/qq409451388/GearModule-ORM)** ORM组件，对于Java Web开发来说必然倍感亲切
   3. **GearModule-EzCache[[点击跳转]](https://github.com/qq409451388/GearModule-EzCache)** 缓存组件，基于PHP socket模块实现了Redis的服务端和客户端，也包括基于内存或文件实现的本地缓存
   4. **GearModule-EzCurl[[点击跳转]](https://github.com/qq409451388/GearModule-EzCurl)** 基于curl实现的web请求工具
   5. **GearModule-Web[[点击跳转]](https://github.com/qq409451388/GearModule-Web)** 网络组件，支持多种协议
2. 支持多种启动模式：Web服务启动、脚本模式启动、定时任务模式启动 
3. Web服务使用常驻内存的方式，一次启动，多次复用
4. 内置了注解功能，除了常用的注解外，支持开发者自定义注解

# 快速开始
## 1. 运行环境
<svg xmlns="http://www.w3.org/2000/svg" width="105" height="20">
    <linearGradient id="b" x2="0" y2="100%">
        <stop offset="0" stop-color="#bbb" stop-opacity=".1"/>
        <stop offset="1" stop-opacity=".1"/>
    </linearGradient>
    <mask id="a">
        <rect width="105" height="20" rx="3" fill="#fff"/>
    </mask>
    <g mask="url(#a)">
        <path fill="#555" d="M0 0h42v20H0z"/>
        <path fill="#44cc11" d="M42 0h63v20H42z"/>
        <path fill="url(#b)" d="M0 0h105v20H0z"/>
    </g>
    <g fill="#fff" text-anchor="middle" font-family="DejaVu Sans,Verdana,Geneva,sans-serif" font-size="11">
        <text x="21" y="15" fill="#010101" fill-opacity=".3">PHP</text>
        <text x="21" y="14">PHP</text>
        <text x="73.5" y="15" fill="#010101" fill-opacity=".3">8.1+</text>
        <text x="73.5" y="14">8.1+</text>
    </g>
</svg>

<svg xmlns="http://www.w3.org/2000/svg" width="105" height="20">
    <linearGradient id="b" x2="0" y2="100%">
        <stop offset="0" stop-color="#bbb" stop-opacity=".1"/>
        <stop offset="1" stop-opacity=".1"/>
    </linearGradient>
    <mask id="a">
        <rect width="105" height="20" rx="3" fill="#fff"/>
    </mask>
    <g mask="url(#a)">
        <path fill="#555" d="M0 0h42v20H0z"/>
        <path fill="#dfb317" d="M42 0h63v20H42z"/>
        <path fill="url(#b)" d="M0 0h105v20H0z"/>
    </g>
    <g fill="#fff" text-anchor="middle" font-family="DejaVu Sans,Verdana,Geneva,sans-serif" font-size="11">
        <text x="21" y="15" fill="#010101" fill-opacity=".3">PHP</text>
        <text x="21" y="14">PHP</text>
        <text x="73.5" y="15" fill="#010101" fill-opacity=".3">7.4+</text>
        <text x="73.5" y="14">7.4+</text>
    </g>
</svg>

> PHP 7.4+ (推荐8.1+)
> <br/>
> PHP 需要开启常用自带模块：

## 2. 开发环境
> **Lan**: 开发初期 PHP 7.4，一些模块组件基于 PHP 8.1
> <br/>
> **OS**: MacOS
> <br/>
> **IDE**：PhpStorm

## 3.初始化工作
> 假设我们使用Linux系统进行搭建，项目目录统一放在了/home/release/

### 3.1. 将框架代码拉取到任意目录，这里以/home/release举例
```bash
  mkdir /home/release && cd /home/release/ && git clone https://github.com/qq409451388/GearX
```
**拉取完成框架之后，在Gear/bin下面我们提供了一个安装脚本,可以完成接下来的操作，不过也可以手动继续执行3.2开始的内容**
```bash
  bash /home/release/GearX/bin/install.sh
```
### 3.2. 安装Module
#### \>\>\>\> 3.2.1 使用Https的方式安装
```bash
  php /home/release/GearX/bin/init_dependency.php -r true -m https
```
#### \>\>\>\> 3.2.2 使用SSH的方式安装
###### 如果您的git版本过高，可以使用ssh的方式拉取项目依赖,那么命令如下：
```bash
  php /home/release/GearX/bin/init_dependency.php -r true -m ssh
```
###### 可以传入参数 -i 指定本地证书
```bash
  php /home/release/GearX/bin/init_dependency.php -r true -m ssh -i /.ssh/github_rsa
```

### 3.3. 将示例项目代码拉取到任意目录，这里以/home/release举例
```bash
  cd /home/release/ && git clone https://github.com/qq409451388/GearXExample
```
### 3.4 启动服务
```bash
  php /home/release/GearXExample/scripts/http_server.php -PappPath=/home/release/GearXExample -PgearPath=/home/release/GearX -PconfigPath=/home/release/GearXExample/config
```

### 3.5 启动日志
```zsh
guohan@ubuntu:/home/release# php /home/release/GearXExample/scripts/http_server.php -PappPath=/home/release/GearXExample -PgearPath=/home/release/GearX -PconfigPath=/home/release/GearXExample/config
2024-09-20 14:32:26  [INFO][Configuration] active env:dev

 ___                       _    _ 
(  _`\                    ( )  ( )
| ( (_)   __     _ _  _ __`\`\/'/'
| |___  /'__`\ /'_` )( '__) >  <  
| (_, )(  ___/( (_| || |   /'/\`\ 
(____/'`\____)`\__,_)(_)  (_)  (_)

2024-09-20 14:32:26  [INFO][:::GearX:::] version:RELEASE 2.5 (Last Update 2024-09-01)
============================Loading Framework===================================
2024-09-20 14:32:27  [INFO][Application] Start Searching modules...
2024-09-20 14:32:27  [INFO][Application] >>>>>> Import module: [ezcurl] <<<<<<
2024-09-20 14:32:27  [INFO][Application] Analyse sub module: ezcurl
2024-09-20 14:32:27  [INFO][Application] No subdependencies found.
2024-09-20 14:32:27  [INFO][Application] >>>>>> Import module: [orm] <<<<<<
2024-09-20 14:32:27  [INFO][Application] Analyse sub module: orm
2024-09-20 14:32:27  [INFO][Application] Subdependencies found, Extra Searching subdependencies...
2024-09-20 14:32:27  [INFO][Application] >>>>>> Import module: [orm->db] <<<<<<
2024-09-20 14:32:27  [INFO][Application] Analyse sub module: orm->db
2024-09-20 14:32:27  [INFO][Application] Subdependencies found, Extra Searching subdependencies...
2024-09-20 14:32:27  [INFO][Application] >>>>>> Import module: [orm->db->ezcache] <<<<<<
2024-09-20 14:32:27  [INFO][Application] Analyse sub module: orm->db->ezcache
2024-09-20 14:32:27  [INFO][Application] Subdependencies found, Extra Searching subdependencies...
2024-09-20 14:32:27  [INFO][Application] >>>>>> Import module: [orm->db->ezcache->web] <<<<<<
2024-09-20 14:32:27  [INFO][Application] Analyse sub module: orm->db->ezcache->web
2024-09-20 14:32:27  [INFO][Application] No subdependencies found.
2024-09-20 14:32:27  [INFO][Application] >>>>>> Import module: [orm->ezcache] <<<<<<
2024-09-20 14:32:27  [INFO][Application] Analyse sub module: orm->ezcache
2024-09-20 14:32:27  [INFO][Dependency] The module ezcache has been loaded!
2024-09-20 14:32:27  [INFO][Application] No subdependencies found.
2024-09-20 14:32:27  [INFO][Application] >>>>>> Import module: [db] <<<<<<
2024-09-20 14:32:27  [INFO][Application] Analyse sub module: db
2024-09-20 14:32:27  [INFO][Dependency] The module db has been loaded!
2024-09-20 14:32:27  [INFO][Application] No subdependencies found.
2024-09-20 14:32:27  [INFO][Application] >>>>>> Import module: [utils] <<<<<<
2024-09-20 14:32:27  [INFO][Application] Analyse sub module: utils
2024-09-20 14:32:27  [INFO][Application] No subdependencies found.
2024-09-20 14:32:27  [INFO][Application] >>>>>> Import module: [web] <<<<<<
2024-09-20 14:32:27  [INFO][Application] Analyse sub module: web
2024-09-20 14:32:27  [INFO][Dependency] The module web has been loaded!
2024-09-20 14:32:27  [INFO][Application] No subdependencies found.
2024-09-20 14:32:27  [INFO][Gear]Create Bean BaseController
2024-09-20 14:32:27  [INFO][Gear]Create Bean EzRouter
2024-09-20 14:32:27  [INFO][Gear]Create Bean TestController
2024-09-20 14:32:27  [INFO][Application] Start Register...
2024-09-20 14:32:27  [INFO][Application] Init Third StartRegister AnnoationStarter
2024-09-20 14:32:27  [INFO][EzRouter] Mapping Path [GET]test/helloworld To hello@TestController
2024-09-20 14:32:27  [INFO][Application] Inited AnnoationStarter StartRegister Success!
2024-09-20 14:32:27  [INFO][HTTP]Start HTTP Server...
2024-09-20 14:32:27  [INFO]Start Server Success! http://127.0.0.1:8888
```
## 4. 访问服务
```bash
  curl http://127.0.0.1:8888/test/helloworld && echo ''
```
> 看到正常输出 hello world! 说明项目部署完成

## 5. 其他
+ 【可选】DB工具需要配置config目录下dbcon.json、syshash.json
+ 【可选】redis工具需要配置config目录下rediscluster.json
+ 【可选】mail工具需要配置config目录下mail.json
+ 【可选】微信工具人需要配置config目录下wechatrobot.json

