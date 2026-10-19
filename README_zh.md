# Discuz! 4.1.2（社区维护分支）

[English](README.md)

康盛创想（Comsenz Inc.）在 2001-2006 年开发的经典论坛/BBS 系统。  
本仓库是社区维护分支，采用 MIT 许可证，并已完成面向现代 PHP 环境的兼容改造。

## 当前维护分支状态

- **4.1.2（2026-10-19）**：安全加固 —— 修复数组派生的 `IN()` 子句 SQL 注入、多处 XSS（后台框架页、附件名、公告）、强制 CSRF 校验、附件扩展名白名单、数据库错误不再向非管理员回显 SQL、从请求输入中剔除身份变量、Cookie 增加 HttpOnly、令牌改用 CSPRNG、凭据比较改为恒定时间（详见 `CHANGELOG.md`）。
- **4.1.1（2026-10-19）**：完成 PHP 8.4+ 兼容性收尾；移除三个已停服的集成 —— 奇虎搜索、支付宝支付与交易帖功能（详见 `CHANGELOG.md`）。发布包不含运行数据（`config.inc.php`、`forumdata/cache/`、日志、`attachments/`）。
- 已完成 **PHP 8.4+ 兼容改造**（详见 `php84-upgrade-report.md`）
- 已修复大量 PHP8 常见告警场景（未定义变量/数组键、空偏移等）
- 已加入运行时兼容告警拦截（`include/common.inc.php`，仅拦截指定 legacy 告警前缀）
- 已默认屏蔽历史外部集成功能：
  - 通行证（Passport）
  - 电子商务（Alipay / Orders / ShopEx）
  - 天下秀（AvatarShow）
- 后台对应入口已拦截（`admincp.php` blocked actions），菜单项已隐藏
- 后台框架页不再在 frame URL 中携带 `sid` 会话参数——在拦截 `sid=` 请求的 WAF 规则下必需；会话由 cookie 维持
- 修复 PHP 8 下用户资料页致命错误：`viewpro.htm` 积分循环改用带引号的数组键
- PHP 8 修复：`register.php` 设置引导中的变量变量改为带引号写法（裸 `$$setting['key']` 在 PHP 8 下静默赋值失败）
- `transsid()` 不再产生重复引号（`href="…""`）；已关闭 URL 传递 sid（`transsidstatus=0`，因 WAF 拦截含 `sid=` 的请求）
- 注册规则页倒计时改为回调写法，在站点 CSP（无 `unsafe-eval`）下正常工作

## 功能特性（当前可用）

- 论坛系统：分类、版块、子版块、主题、回复、投票
- 用户系统：注册、登录、用户组、权限、积分、资料、头像
- 内容能力：BBCode、附件上传、表情、主题分类、管理工具
- 站内通信：短消息（PM）、公告、邮件通知
- 后台管理：`admincp.php`（版块、用户、用户组、模板、插件、数据库、日志等）
- 插件机制：`plugins/` Hook 架构
- 多端界面：`wap/`、`archiver/`
- 多语言包：简体中文、繁体中文、英文

## 已默认屏蔽的功能

为减少外部依赖和老旧接口风险，本分支默认关闭以下模块：

- Passport / SiteEngine / ShopEx
- Alipay / Orders
- AvatarShow

说明：
- 前台展示入口已移除或条件隐藏
- 后台管理入口已拦截为不可访问
- 运行时设置在 `include/common.inc.php` 强制关闭相关开关

## 反垃圾机制

纯服务端实现，不依赖 JavaScript，完全兼容老旧浏览器（含 IE4）：

- 游客发帖与回复全局关闭（游客组权限，主题与回复均受限）
- 注册需回答社区防护问题（问题文本在 `templates/default/register.htm`，校验在 `register.php`）
- 注册表单含蜜罐字段（"请保持本行为空"），自动填写即拒绝
- 注册邮箱域名黑名单（`censoremail` 设置）；注册洪水控制（`regfloodctrl` 限制同 IP 注册尝试次数）
- 新兵时间限制（`newbiespan` 小时数，注册后一段时间内无法发帖）
- `cdb_words` 内容过滤：`{BANNED}` 条目直接拒绝含垃圾特征的主题/回复/私信
- 验证码（`seccode.php`）输出 GIF（兼容旧浏览器），无 GD 环境时回退 BMP

注意：通过 SQL 导入反垃圾设置或修改 `cdb_words` 后，请删除
`forumdata/cache/cache_settings.php` 与 `forumdata/cache/cache_censor.php` 使其重建。

## 环境要求

- 推荐：**PHP 8.4+**
- 数据库：MySQL 4.1+ 或 PostgreSQL
- Web 服务器：Apache / Nginx

说明：历史版本（如 PHP 7.4）可能仍可运行，但本分支的主要验证环境是 PHP 8.4。

## 安装步骤

1. 导入数据库结构
   ```bash
   mysql -u root -p your_database < install/discuz.sql
   ```

2. 配置 `config.inc.php` 数据库连接
   ```php
   $dbhost = '127.0.0.1';
   $dbuser = 'your_user';
   $dbpw   = 'your_password';
   $dbname = 'your_database';
   $tablepre = 'cdb_';
   ```

3. 设置可写目录权限
   ```bash
   chmod -R 777 attachments/ forumdata/
   ```

4. 浏览器访问 `install/` 完成安装初始化

5. 访问 `admincp.php` 进入后台

## 项目结构

```text
├── index.php               # 首页 / 版块列表
├── forumdisplay.php        # 版块主题列表
├── viewthread.php          # 查看主题
├── post.php                # 发帖 / 回复
├── register.php            # 用户注册
├── logging.php             # 登录 / 登出
├── member.php              # 个人资料 & 设置
├── pm.php                  # 站内短消息
├── search.php              # 搜索
├── admincp.php             # 管理后台入口
├── include/                # 核心引导、函数、数据库抽象、缓存、安全逻辑
├── admin/                  # 后台模块
├── templates/default/      # 模板源文件（编辑这里）
├── forumdata/cache/        # 运行时缓存（自动生成）
├── forumdata/templates/    # 编译模板（自动生成，不直接编辑）
├── api/                    # 外部接口脚本
├── plugins/                # 插件目录
├── wap/                    # WAP 界面
├── archiver/               # 纯文本归档
└── install/discuz.sql      # 数据库结构
```

## 维护与开发注意事项

- 每个入口页都先定义 `CURSCRIPT`，再引入 `include/common.inc.php`
- 所有核心文件保留访问守卫：`if(!defined('IN_DISCUZ')) exit('Access Denied');`
- 不要直接改 `forumdata/cache/` 和 `forumdata/templates/`（会被重建）
- 修改模板请编辑 `templates/default/*.htm`
- 若模板未刷新，可开启 `$tplrefresh = 1` 或清理 `forumdata/templates/`
- 页脚会显示当前实际 PHP 版本并链接到对应官方版本分支页面

## PHP 8.4 兼容改造摘要

- `mysql_*` -> `mysqli_*`
- `preg_replace /e` -> `preg_replace_callback`
- 补充大量空值/类型守卫，减少 PHP8 严格行为导致的 Warning/Fatal
- `extract()` 补充 `EXTR_SKIP`
- 处理短标签与字符串插值在 PHP8 下的不兼容点

完整清单见：[`php84-upgrade-report.md`](php84-upgrade-report.md)

## 许可证

MIT License，详见 [LICENSE](LICENSE)。

原作者：Comsenz Inc.  
维护者：[Wenyin Root](https://github.com/wenyinos/discuz-410)
