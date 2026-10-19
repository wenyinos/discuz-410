## Discuz! 4.1.2

本版本是一次安全加固，不包含新功能。

### 修复的漏洞

- **SQL 注入**：数组派生的 `IN()` 子句（`msgtobuddys[]`、`delete[]`、`split[]`）与数组键直接进入列名/条件的注入点
- **XSS**：后台框架页未认证反射型 XSS、附件文件名存储型 XSS、公告主题存储型 XSS，以及 `dreferer()` / `PHP_SELF` / `bbcodeurl()` 的回显漏洞
- **CSRF**：`submitcheck()` 的 `$allowget` 不再跳过 formhash 校验；投票补上令牌校验
- **文件上传**：扩展名白名单为空时回退内置安全列表；危险扩展名过滤补全 `php4/5/7/8`、`pht`、`phar`、`shtml` 等
- **信息泄露**：数据库错误仅对管理员显示 SQL 详情；日志首次创建时写入保护头
- **认证与会话**：从请求输入中剔除身份变量（阻断 `?adminid=` 提权）、Cookie 增加 `HttpOnly`、令牌改用 CSPRNG、凭据比较改为恒定时间

### 安装包说明

`discuz-4.1.2.tar.gz` 是完整代码包，可直接覆盖论坛目录。**不包含**：

- `config.inc.php`（你的数据库配置）
- `forumdata/cache/`、`forumdata/templates/`（生成缓存与编译模板）
- `forumdata/*.php` 日志与 `attachments/`

升级前请先备份，并避免覆盖上述运行数据。

### 数据库

- 生产库**无需结构变更**：表清单与 4.1.0 一致
- `install/discuz.sql` 已改为 UTF-8 + `ENGINE=InnoDB`，可在 MySQL 5.7 / 8.0 直接导入

### 升级提示

- 用户需重新登录一次（会话 Cookie 现为 `HttpOnly`）
- 若论坛原未配置附件扩展名白名单，升级后将使用内置白名单（图片、压缩包、文档、音视频等），如需其它类型请在后台添加
- 完整变更见仓库中的 `CHANGELOG.md`
