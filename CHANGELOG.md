# Changelog

本项目是 Discuz! 4.1.0 的社区维护分支。本文件记录本分支的版本变更。

## [4.1.2] - 2026-10-19

### 安全加固

- **SQL 注入**：修复数组派生的 `IN()` 子句注入（`pm.php` 的 `msgtobuddys[]`、`memcp.php` 的 `delete[]`、`topicadmin.php` 的 `split[]`、后台批量操作）；修复数组**键**直接作为列名或条件值的注入（`memcp.php` 好友备注、`admin/members.inc.php` 的积分搜索与修改）；管理后台的表名、`LIMIT` 与 `api/javascript.php` 的 `fids/fups` 统一做白名单或 `intval`。
- **XSS**：修复后台框架页未认证反射型 XSS（参数名未编码）、附件原始文件名存储型 XSS、公告主题转义被注释掉的存储型 XSS；`dreferer()`、`PHP_SELF` 回显与 `bbcodeurl()` 的引号过滤补齐。
- **CSRF**：`submitcheck()` 的 `$allowget` 不再跳过 formhash 校验；后台改状态动作仍要求令牌。投票处理补上 `submitcheck()`。
- **文件上传**：扩展名白名单为空时回退到内置安全白名单（不再放行任意扩展名）；危险扩展名过滤补上 `php4/5/7/8`、`pht`、`phar`、`shtml`、`htaccess` 等。
- **信息泄露**：数据库错误页仅对管理员显示 SQL 语句与错误详情，访客只看到通用提示；`errorlog` 与失败登录日志在首次创建时写入 `exit` 保护头。
- **认证与会话**：从请求输入中剔除 `adminid`、`groupid`、`discuz_uid`、`discuz_pw`、`credits` 等身份变量，阻止通过 URL 参数覆盖会话身份；`dsetcookie()` 统一加 `HttpOnly`；令牌生成改用 `random_int()`；密码、安全提问与找回密码令牌改为 `hash_equals()` 恒定时间比较。

### 数据库

- `install/discuz.sql` 改为 UTF-8 编码、`ENGINE=InnoDB`、显式 `DEFAULT CHARSET=utf8`，并设置 `sql_mode=''` 以兼容历史零日期；已在 MySQL 5.7 实测导入成功（59 张表）。
- 生产库无需结构变更：表清单与 4.1.0 一致，已废弃功能对应的表均为空。

## [4.1.1] - 2026-10-19

### PHP 8.4+ 兼容性

- 修复 PHP 8 下会致命的数组裸下标（如 `$attach[filesize]`），共 9 处。PHP 8 起未定义的裸词按常量解析，会抛出 `Undefined constant`。
- 修复花括号复杂插值中的裸键（如 `{$forum[fid]}`），共 41 处。PHP 8 中这类键按常量解析，而简单插值 `$forum[fid]` 才是字符串键 —— 这也是最容易被静态扫描漏掉的一类。
- 修复 mysqli 错误报告模式设置失效。原写法 `$conn->report_mode = MYSQLI_REPORT_OFF` 中 `report_mode` 并非 mysqli 属性，赋值既不生效也会产生动态属性弃用；PHP 8.1+ 默认让 mysqli 抛异常，导致数据库错误变成未捕获异常白屏。改为连接前调用 `mysqli_report(MYSQLI_REPORT_OFF)`。
- 修复数据库导出文件的保护头失效：`<?exit();?>` 在 PHP 8 默认 `short_open_tag=Off` 下不再执行，备份内容可被直接读取；改为 `<?php exit();?>`。
- 修复 `include/relatethreads.inc.php` 中 `<?php/*` 缺少空白字符导致的语法错误。
- 修复 `(double)` 非规范类型转换（PHP 8.5 弃用）。
- 修复 `archiver` 中 `$_DCACHE['timeoffset']` 的层级错误、`rss.php` 未初始化变量导致的运行时警告。
- 修复语言包中 HTML 上下文的 `\"` 被当作字面量输出、导致跳转链接损坏的问题。

### 移除已停服的集成

- 移除奇虎搜索：搜索页选项、各页搜索框、后台配置、相关主题功能（依赖早已停服的 search.qihoo.com）。
- 移除支付宝支付：`[payto]` 代码、帖子支付按钮、积分充值、后台电子商务设置。
- 移除交易帖功能：发帖类型、版块设置、相关模板与语言项。
- 共删除 10 个文件，并清理后台菜单、权限项、模板与语言包中的相关残留。其中 `api/qihoo.php` 与 `api/alipaynotify.php` 是 Zend 加密文件，在 PHP 8 下本就无法运行。

### 升级提示

- 数据库结构未改动：`cdb_forums.allowtrade`、`cdb_memberfields.alipay` 以及 `cdb_settings` 中的 `qihoo_*`/`ec_*` 设置项均保留。
- 历史帖子中的 `[payto]` 代码块会显示为原始文本。
- 覆盖部署时请勿覆盖生产环境的 `config.inc.php` 与 `forumdata/`（缓存、日志、编译后的模板）。
