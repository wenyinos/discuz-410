# Changelog

本项目是 Discuz! 4.1.0 的社区维护分支。本文件记录本分支的版本变更。

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
