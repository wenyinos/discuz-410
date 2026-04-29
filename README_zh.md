# Discuz! 4.1.0

[English](README.md)

康盛创想（Comsenz Inc.）开发的开源论坛/BBS 系统（2001-2006）。本仓库为社区维护的 MIT 许可证分支。

## 功能特性

- **论坛系统**：层级分类、版块、子版块、主题、回复、投票
- **用户系统**：注册、登录、用户组、权限管理、积分/声望、个人资料、头像
- **内容管理**：BBCode 编辑器、附件上传（图片/文件，支持水印）、表情、主题分类、管理工具
- **站内通信**：短消息（PM）、好友列表、公告、邮件通知
- **管理后台**：完整的管理面板（`admincp.php`），支持版块、用户、用户组、风格、模板、插件、数据库、日志管理
- **插件系统**：基于 Hook 的插件架构，插件目录 `plugins/`
- **多语言**：简体中文、繁体中文（Big5/UTF-8）、英文语言包
- **移动端**：WAP 纯文本界面（`wap/`）、纯文本归档（`archiver/`）
- **外部集成**：通行证 SSO、奇虎搜索、支付宝、Shopex、头像 API
- **定时任务**：内置计划任务（清理、统计、推广、公告等）

## 环境要求

- PHP 7.4+
- MySQL 4.1+ 或 PostgreSQL
- Web 服务器（Apache/Nginx）

## 安装步骤

1. 创建 MySQL 数据库并导入表结构：
   ```bash
   mysql -u root -p your_database < install/discuz.sql
   ```

2. 复制 `config.inc.php` 并配置数据库连接信息：
   ```php
   $dbhost = '127.0.0.1';
   $dbuser = 'your_user';
   $dbpw   = 'your_password';
   $dbname = 'your_database';
   $tablepre = 'cdb_';
   ```

3. 设置目录权限：
   ```bash
   chmod -R 777 attachments/ forumdata/
   ```

4. 浏览器访问 `install/` 完成初始化设置，然后删除 `install/` 目录。

5. 访问 `admincp.php` 进入管理后台。

## 项目结构

```
├── index.php               # 首页 / 版块列表
├── forumdisplay.php        # 版块主题列表
├── viewthread.php          # 查看主题
├── post.php                # 发帖 / 回复
├── register.php            # 用户注册
├── logging.php             # 登录 / 登出
├── member.php              # 个人资料 & 设置
├── pm.php                  # 站内短消息
├── search.php              # 搜索
├── admincp.php             # 管理后台（入口文件）
├── plugin.php              # 插件调度器
├── misc.php                # 杂项（规则、帮助等）
├── blog.php                # 用户博客
├── stats.php               # 论坛统计
├── attachment.php          # 附件下载
├── config.inc.php          # 主配置文件（数据库、路径）
├── mail_config.inc.php     # 邮件配置
├── include/
│   ├── common.inc.php      # 引导文件（每个页面加载）
│   ├── global.func.php     # 核心工具函数
│   ├── db_mysql.class.php  # 数据库抽象层（MySQLi）
│   ├── template.func.php   # 模板编译器
│   ├── cache.func.php      # 缓存生成
│   ├── forum.func.php      # 版块显示函数
│   ├── post.func.php       # 帖子/附件处理
│   ├── misc.func.php       # IP 查询等杂项工具
│   ├── security.inc.php    # 攻击防护
│   ├── attachment.func.php # 附件类型处理
│   ├── crons/              # 定时任务脚本
│   └── tables/             # 字符集转换表
├── admin/
│   ├── settings.inc.php    # 站点设置
│   ├── forums.inc.php      # 版块管理
│   ├── members.inc.php     # 用户管理
│   ├── groups.inc.php      # 用户组管理
│   ├── plugins.inc.php     # 插件管理
│   ├── templates.inc.php   # 模板管理
│   ├── database.inc.php    # 数据库备份/恢复
│   └── ...                 # 其他管理模块
├── templates/default/      # 模板源文件（.htm）
├── forumdata/
│   ├── cache/              # 生成的 PHP 缓存文件
│   └── templates/          # 编译后的模板 PHP 文件
├── api/                    # 外部 API 接口
├── plugins/                # 插件脚本
├── wap/                    # 移动端 WAP 界面
├── archiver/               # 纯文本归档界面
├── images/                 # 静态图片 & CSS
├── ipdata/                 # IP 地理位置数据库
├── install/                # 安装文件 & 数据库表结构
└── customavatars/          # 用户上传头像
```

## PHP 8.4+ 升级说明

本分支已从 PHP 4/5 升级至 PHP 8.4+ 兼容：

- **数据库层**：`mysql_*` 扩展替换为 `mysqli_*`（`include/db_mysql.class.php`）
- **模板编译器**：`preg_replace /e` 修饰符替换为 `preg_replace_callback`（`include/template.func.php`）
- **URL 重写**：`output()` 中 `preg_replace /e` 替换为 `preg_replace_callback`（`include/global.func.php`）
- **安全性**：`extract()` 使用 `EXTR_SKIP` 防止变量覆盖
- **兼容性**：移除 `set_magic_quotes_runtime`、`get_magic_quotes_gpc`、`$HTTP_*_VARS`、`$magic_quotes_gpc`
- **语法**：所有短开标签 `<?` 替换为 `<?php`
- **PHP 8.1+**：禁用 `mysqli` 异常模式以保持旧行为（`db_mysql.class.php`）
- **PHP 8.0+**：修复 `count()` 非数组 TypeError，`@count()` 改用 `is_array()` 守卫
- **PHP 8.1+**：`htmlspecialchars()` 替换为 `dhtmlspecialchars()` 避免默认标志变更

详见 [php84-upgrade-report.md](php84-upgrade-report.md) 升级报告。

## 技术要点

- **引导机制**：每个页面定义 `CURSCRIPT` 常量后引入 `include/common.inc.php`
- **访问守卫**：所有 PHP 文件以 `if(!defined('IN_DISCUZ')) exit('Access Denied');` 开头 —— 请勿移除
- **缓存系统**：`forumdata/cache/` 文件自动生成，直接编辑无效（会被覆盖）
- **模板系统**：编辑 `templates/default/` 中的 `.htm` 文件，不要编辑 `forumdata/templates/` 中的编译文件
- **数据库**：使用 `$tablepre` 前缀（默认 `cdb_`），通过 `$db->query()` / `$db->fetch_array()` 查询

## 许可证

MIT 许可证 —— 详见 [LICENSE](LICENSE)。

原作者：康盛创想（Comsenz Inc.，2001-2006）。
维护者：[Wenyin Root](https://github.com/wenyinos/discuz-410)。
