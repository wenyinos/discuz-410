## Discuz! 4.1.1

本版本完成 PHP 8.4+ 兼容性收尾，并移除了三个已停服的集成。

### PHP 8.4+ 兼容性

- 修复 PHP 8 下会致命的数组裸下标（`$attach[filesize]` 等 9 处）
- 修复花括号复杂插值中的裸键（`{$forum[fid]}` 等 41 处）
- 修复 mysqli 错误报告模式设置失效（数据库错误曾会变成未捕获异常白屏）
- 修复数据库备份文件的保护头在 `short_open_tag=Off` 下失效
- 修复 `<?php/*` 语法错误、`(double)` 弃用，以及 `archiver`/`rss.php` 的运行时警告
- 修复语言包中 HTML 上下文的 `\"` 字面量导致跳转链接损坏

### 移除已停服的集成

- 奇虎搜索（含相关主题、后台配置、各页搜索框）
- 支付宝支付（含 `[payto]` 代码、帖子支付按钮、积分充值、后台电子商务设置）
- 交易帖功能（含版块设置、发帖类型、相关模板与语言项）

共删除 10 个文件，并清理后台菜单、权限项、模板与语言包中的残留。

### 安装包说明

`discuz-4.1.1.tar.gz` 是完整代码包，可直接覆盖论坛目录。**不包含**：

- `config.inc.php`（你的数据库配置）
- `forumdata/cache/`、`forumdata/templates/`（生成缓存与编译模板）
- `forumdata/*.php` 日志与 `attachments/`

升级前请先备份，并避免覆盖上述运行数据。

### 升级提示

- 数据库结构未改动：`cdb_forums.allowtrade`、`cdb_memberfields.alipay` 与 `cdb_settings` 中的 `qihoo_*`/`ec_*` 设置项均保留
- 历史帖子中的 `[payto]` 代码块会显示为原始文本
- 完整变更见仓库中的 `CHANGELOG.md`
