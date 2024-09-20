# GearX简介 / GearX Introduction

![GitHub](https://img.shields.io/badge/github-GearX-007ec6?style=flat-square)
![Version](https://img.shields.io/badge/version-beta%200.2.5-fe7d37?style=flat-square)

> GearX是一款纯PHP实现的轻量化的开发框架，旨在提供快速且舒适的方式开发项目，尤其是对于有过Java开发经验的开发者而言。
>
> GearX is a lightweight development framework implemented purely in PHP, designed to provide a fast and comfortable way to develop projects, especially for developers with Java experience.

**设计文档 / Design Document:** [点击跳转 / Click Here](./DESIGN.md)  
**变更文档 / Changelog:** [点击跳转 / Click Here](./CHANGELIST.md)  
**开源许可 / License:** [点击跳转 / Click Here](./LICENSE)

# 特性&能力 / Features & Capabilities

![Feature](https://img.shields.io/badge/特性-PHP%20Implemented-44cc11?style=flat-square)
![Feature](https://img.shields.io/badge/特性-常驻内存-e05d44?style=flat-square)
![Feature](https://img.shields.io/badge/特性-自定义注解-fe7d37?style=flat-square)

1. **框架完全由PHP实现 / Fully Implemented in PHP**
   - 包括项目开发中的常用功能，并可以按需获取引入，以下是部分模块：
   - Includes common functions in project development, and can be introduced as needed. Here are some modules:
      1. **GearModule-DB**: [点击跳转 / Click Here](https://github.com/qq409451388/GearModule-DB) - 数据库的查询组件，简化了大量操作，支持常见的数据库协议。
      2. **GearModule-ORM**: [点击跳转 / Click Here](https://github.com/qq409451388/GearModule-ORM) - ORM组件，对于Java Web开发来说必然倍感亲切。
      3. **GearModule-EzCache**: [点击跳转 / Click Here](https://github.com/qq409451388/GearModule-EzCache) - 缓存组件，基于PHP socket模块实现了Redis的服务端和客户端，也包括基于内存或文件实现的本地缓存。
      4. **GearModule-EzCurl**: [点击跳转 / Click Here](https://github.com/qq409451388/GearModule-EzCurl) - 基于curl实现的web请求工具。
      5. **GearModule-Web**: [点击跳转 / Click Here](https://github.com/qq409451388/GearModule-Web) - 网络组件，支持多种协议。

2. **支持多种启动模式 / Supports Multiple Start Modes**
   - Web服务启动、脚本模式启动、定时任务模式启动。
   - Web service start, script mode start, scheduled task mode start.

3. **Web服务使用常驻内存的方式 / Web Service Uses Resident Memory**
   - 一次启动，多次复用。
   - Start once, reuse multiple times.

4. **内置了注解功能 / Built-in Annotation Functionality**
   - 除了常用的注解外，支持开发者自定义注解。
   - Supports developer-defined annotations in addition to common annotations.

# 快速开始 / Quick Start

## 1. 运行环境 / Runtime Environment

![PHP](https://img.shields.io/badge/PHP-8.1+-44cc11?style=flat-square)
![PHP](https://img.shields.io/badge/PHP-7.4+-dfb317?style=flat-square)

> PHP 7.4+ (推荐8.1+)  
> PHP 需要开启常用自带模块：

## 2. 开发环境 / Development Environment

- **语言 / Language**: 开发初期 PHP 7.4，一些模块组件基于 PHP 8.1  
  Early development PHP 7.4, some modules based on PHP 8.1
- **操作系统 / OS**: MacOS
- **IDE**: PhpStorm

## 3. 初始化工作 / Initialization

> 假设我们使用Linux系统进行搭建，项目目录统一放在了/home/release/  
> <i>Assume we are using Linux, and the project directory is unified in /home/release/</i>

### 3.1. 拉取框架代码 / Clone Framework Code

```bash
mkdir /home/release && cd /home/release/ && git clone https://github.com/qq409451388/GearX
```
**拉取完成框架之后，在Gear/bin下面我们提供了一个安装脚本，可以完成接下来的操作，不过也可以手动继续执行3.2开始的内容**

<i>**After cloning, an install script is provided under Gear/bin which can complete the following steps, or you can manually proceed from 3.2**</i>

```bash
  bash /home/release/GearX/bin/install.sh
```

### 3.2. 安装模块 / Install Modules
#### >>>> 3.2.1 使用Https安装 / Install via HTTPS
```bash
  php /home/release/GearX/bin/init_dependency.php -r true -m https
```
#### >>>> 3.2.2 使用SSH安装 / Install via SSH
###### 如果您的git版本过高，可以使用ssh的方式拉取项目依赖，命令如下：
###### If your git version is too high, you can use SSH to fetch project dependencies:
```bash
  php /home/release/GearX/bin/init_dependency.php -r true -m ssh
```
###### 可以传入参数 -i 指定本地证书
###### You can pass parameter -i to specify a local certificate:
```bash
  php /home/release/GearX/bin/init_dependency.php -r true -m ssh -i /.ssh/github_rsa
```

### 3.3. 拉取示例项目代码 / Clone Example Project Code
```bash
  cd /home/release/ && git clone https://github.com/qq409451388/GearXExample
```

### 3.4 启动服务 / Start Service
```bash
   php /home/release/GearXExample/scripts/http_server.php -PappPath=/home/release/GearXExample -PgearPath=/home/release/GearX -PconfigPath=/home/release/GearXExample/config
```

### 3.5 启动日志 / Start Log

```bash
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
## 4. 访问服务 / Access the Service

```bash
curl http://127.0.0.1:8888/test/helloworld && echo ''
```
**看到正常输出 hello world! 说明项目部署完成**
<br/>
<i>**If you see the output hello world!, it means the project has been successfully deployed.**</i>

## 5. 其他 / Additional Information
+ **【可选 / Optional】DB 工具需要配置 config 目录下的 dbcon.json、syshash.json**
<br/>
<i>DB tools require configuration in config directory: dbcon.json, syshash.json</i>

+ **【可选 / Optional】Redis 工具需要配置 config 目录下的 rediscluster.json**
<i>Redis tools require configuration in config directory: rediscluster.json</i>

+ **【可选 / Optional】Mail 工具需要配置 config 目录下的 mail.json**
<i>Mail tools require configuration in config directory: mail.json</i>

+ **【可选 / Optional】微信工具人需要配置 config 目录下的 wechatrobot.json**
<i>WeChat tools require configuration in config directory: wechatrobot.json</i>
