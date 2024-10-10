# 框架手册
![GitHub](https://img.shields.io/badge/Github-GearX-007ec6?style=flat-square)
![Version](https://img.shields.io/badge/Version-Beta%20v0.2.5-fe7d37?style=flat-square)
> GearX 框架使用常驻内存模式启动，基于注解实现完成各项功能
> <br/>
> 本手册针对各项功能和能力进行介绍

# GearX概览：
1. 启动器
2. 依赖管理
3. 

## 1. 框架启动
> GearX Application类介绍
### 1.1 [启动参数](./guide/1.1启动参数.md)
### 1.2 [启动模式](./guide/1.2启动模式.md)
### 1.3 [类加载](./guide/1.3类加载.md)
### 1.4 [依赖管理](./guide/1.4依赖管理.md)
### 1.5 [应用上下文](./guide/1.5应用上下文.md)
### 1.6 [框架能力](./guide/1.6框架能力.md)

## 2. 自动配置
### 2.1 [配置加载详解](./guide/2.1配置加载详解.md)
### 2.2 [配置类使用](./guide/2.2配置类使用.md)

## 3. 注解
### 3.1 [注解加载过程](./guide/3.1注解加载过程.md)
### 3.2 [注解类型](./guide/3.2注解类型.md)
### 3.3 [注解生效位置](./guide/3.3注解生效位置.md)
### 3.4 [注解属性传递类型](./guide/3.4注解属性传递类型.md)
### 3.5 [内置注解](./guide/3.5内置注解.md)
### 3.6 [自定义注解](./guide/3.6自定义注解.md)

## 4. 容器
### 4.1 单位身份注解
#### 4.1.1 [EzBean](./guide/4.1.1EzBean.md)
#### 4.1.2 [EzComponent](./guide/4.1.2EzComponent.md)
#### 4.1.3 [Serializer & Deserializer](./guide/4.1.3Serializer&Deserializer.md)
#### 4.1.4 [EzHelper](./guide/4.1.4EzHelper.md)
#### 4.1.5 [EzStarter](./guide/4.1.5EzStarter.md)
#### 4.1.6 DataObject
##### 4.1.6.1 [EzDataObject](./guide/4.1.6.1EzDataObject.md)
##### 4.1.6.2 [EzSerializeDataObject](./guide/4.1.6.2EzSerializeDataObject.md)
### 4.2 [代理类](./guide/4.2代理类.md)

## 5. 数据对象
### 5.1 基础对象
#### 5.1.1 [EzArray](./guide/5.1.1EzArray.md)
#### 5.1.2 [EzDate](./guide/5.1.2EzDate.md)
#### 5.1.3 [EzInteger](./guide/5.1.3EzInteger.md)
#### 5.1.4 [EzLong](./guide/5.1.4EzLong.md)
#### 5.1.5 [EzObject](./guide/5.1.5EzObject.md)
#### 5.1.6 [EzString](./guide/5.1.6EzString.md)
### 5.2 集合对象
#### 5.2.1 [EzList](./guide/5.2.1EzList.md)
#### 5.2.2 [EzMap](./guide/5.2.2EzMap.md)
### 5.3 [Clazz类对象](./guide/5.3Clazz类对象.md)

## 6. 反射
### 6.1 [类反射](./guide/6.1类反射.md)
### 6.2 [方法反射](./guide/6.2方法反射.md)
### 6.3 [方法参数反射](./guide/6.3方法参数反射.md)
### 6.4 [属性反射](./guide/6.4属性反射.md)

## 7. 序列化 & 反序列化
### 7.1 [EzDate对象专用序列化](./guide/7.1EzDate对象专用序列化.md)
### 7.2 [自定义序列化](./guide/7.2自定义序列化.md)

## 8. 异常
### 8.1 [GearRunTimeException](./guide/8.1GearRunTimeException.md)
### 8.2 [GearShutDownException](./guide/8.2GearShutDownException.md)
### 8.3 [GearIllegalArgumentException](./guide/8.3GearIllegalArgumentException.md)
### 8.4 [GearUnsupportedOperationException](./guide/8.4GearUnsupportedOperationException.md)

## 9. 拓展组件
### 9.1 [数据库组件 - DB](./guide/9.1数据库组件-DB.md)
### 9.2 [数据库实体对象映射 - ORM](./guide/9.2数据库实体对象映射-ORM.md)
### 9.3 [缓存 - EzCache](./guide/9.3缓存-EzCache.md)
### 9.4 [增强Http Client - EzCurl](./guide/9.4增强HttpClient-EzCurl.md)
### 9.5 [网络协议服务 - Web](./guide/9.5网络协议服务-Web.md)
### 9.6 [连接池 - ConnectionPool](./guide/9.6连接池-ConnectionPool.md)
### 9.7 [常用工具 - Utils](./guide/9.7常用工具-Utils.md)
