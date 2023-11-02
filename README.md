SoonCMP 1.0.1 让你更自由地飞
===============

### SoonCMP1.0主要特性
* 框架协议依旧为`MIT`,让你更自由地飞
* 基于`ThinkPHP 6.0`重构，核心代码兼容5.1版本，保证老用户最小升级成本
* API增加Swagger支持
* 增加`.env`环境配置支持

### 环境推荐
> php7.2

> mysql 5.7+

> 打开rewrite


### 最低环境要求
> php7.1+

> mysql 5.5+ (mysql5.1安装时选择utf8编码，不支持表情符)

> 打开rewrite

### 安装程序

1. public目录做为网站根目录,入口文件在 public/index.php
2. 配置好网站，请访问http://你的域名

enjoy your cmp~!  

### Swagger
#### 开启swagger
调试模式下访问: http://你的域名/swagger

#### 相关文档
**OpenAPI** (https://www.openapis.org)  
**Swagger-PHP** (https://zircote.github.io/swagger-php/)


### 待优化功能
- [ ] 总结数据库和模型统一使用规范
- [ ] 应用单独配置目录（待定）
- [ ] 移动Model的逻辑方法到Service里

### 更新日志
#### 6.0.0
* 升级到ThinkPHP6.0
* API增加Swagger支持
* 增加`.env`环境配置支持













