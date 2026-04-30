# Discuz 4.1.0 升级到 PHP 8.4+ 可行性与改造报告

## 1. 结论摘要

- 可行性：**已完成改造**
- 风险等级：**低（剩余项均为不可替代或已有守卫）**
- 改造状态：**阶段 A + B 已完成，阶段 C 待部署验证**
- 语法检查：`forumdata/templates` 之外 **170 个 PHP 文件全部通过 `php -l`**（PHP 8.4.20）

说明：所有阻断项和告警治理项已修复。剩余 6 处 `eval()` 为动态公式/插件钩子（不可替代），8 处 `$_SERVER` 和 9 处 `$_GET` 均已有 `isset()`/`empty()` 守卫。下一步需部署到 PHP 8.4 环境执行阶段 C 回归测试。

## 2. 扫描范围与方法

- 范围：项目根目录全部 `*.php`，重点覆盖 `include/`、`admin/`、入口脚本与缓存模板目录。
- 方法：
  - 语法检查：逐文件 `php -l`
  - 关键字检索：已删除/收紧特性（`/e`、`count` 非数组、短标签等）
  - 运行时风险抽样：`$_SERVER` 等直接索引、模板编译输出链路
  - 实机验证：PHP 8.4.20 下实际运行 `preg_replace /e`、`count(null)`、`@count(null)` 等行为
  - 全量扫描：`extract()` 缺失 `EXTR_SKIP`、`eval()` 调用、`htmlspecialchars()` flags、`mysqli` 异常模式、已删除函数/特性、死代码

## 3. 阻断项修复（已完成）

### 3.1 `preg_replace` 使用 `/e` 修饰符 — ✅ 已修复

- 文件：`include/global.func.php`
- 位置：`output()` 函数（原第 349-388 行）
- 改造：`preg_replace(.../e, ...)` 全部替换为 `preg_replace_callback()`，`transsid()`、`rewrite_forum()`、`rewrite_thread()`、`rewrite_profile()` 包装为回调闭包
- 验证：`grep -rn "preg_replace.*'/[a-z]*e"` 返回 0 匹配

### 3.2 `count()` 作用于非数组 — ✅ 已修复

- 文件：`topicadmin.php`
- 位置：第 389 行
- 改造：`!is_array($delete) && !count($delete)` → `!is_array($delete) || !count($delete)`

### 3.3 编译模板短标签 — 非阻断项（已清理缓存）

- 编译模板使用 `<?=...?>`（短回显标签），PHP 5.4+ 始终可用
- 已清除 `forumdata/templates/` 下 59 个编译缓存，下次访问自动重编译

## 4. 告警治理（已完成）

### 4.1 `@count()` TypeError — ✅ 已修复

- 文件：`include/cache.func.php` 第 480、486 行
- 改造：`@count($threadarray[$gid])` → `is_array($threadarray[$gid]) ? count($threadarray[$gid]) : 0`

### 4.2 `mysqli` 异常模式 — ✅ 已修复

- 文件：`include/db_mysql.class.php` `connect()` 方法
- 改造：追加 `$this->conn->report_mode = MYSQLI_REPORT_OFF;`

### 4.3 `$_SERVER` 直接索引 — ✅ 已修复（约 50 处）

- 涉及文件：`include/common.inc.php`、`include/global.func.php`、`include/security.inc.php`、`include/counter.inc.php`、`include/discuzcode.func.php`、`archiver/index.php`、`api/javascript.php`、`api/siteengine.php`、`api/passport.php`、`api/shopex.php`、`admin/home.inc.php`、`admin/styles.inc.php`、`admin/plugins.inc.php`、`admincp.php`、`seccode.php`、`relatethread.php`、`attachment.php`、`rss.php`、`post.php`、`pm.php`、`wap/include/myphone.inc.php`
- 改造：统一使用 `$_SERVER['key'] ?? ''` 空合并运算符
- 剩余 8 处均有 `isset()`/`!empty()` 守卫，无需处理

### 4.4 错误策略 — 保持现状

- 现有 `error_reporting(E_ERROR | E_WARNING | E_PARSE)` 已满足 PHP 8.4 运行需求

### 4.5 运行扩展清单 — 部署时验证

- 必需扩展：`mysqli`、`xml`、`gd`、`iconv`
- 部署前执行 `php -m | grep mysqli` 确认扩展可用

### 4.6 `extract()` 缺失 `EXTR_SKIP` — ✅ 已修复（4 处）

- `include/post.func.php:344` — `extract($db->fetch_array($query))` → `extract(..., EXTR_SKIP)`
- `admin/counter.inc.php:89` — 同上
- `rss.php:50` — `extract($member)` → `extract($member, EXTR_SKIP)`
- `logging.php:97` — 同上

### 4.7 `htmlspecialchars()` 默认标志变更 — ✅ 已修复（6 处）

- `include/editpost.inc.php:72` — `htmlspecialchars()` → `dhtmlspecialchars()`
- `include/discuzcode.func.php:107` — 同上
- `faq.php:44` — 同上
- `admincp.php:131` — 同上
- `admin/templates.inc.php:109,199` — 同上
- 剩余 2 处在 `include/db_mysql_error.inc.php`（错误显示页，无风险）

### 4.8 死代码清理 — ✅ 已修复

- `include/common.inc.php` — 删除 `$magic_quotes_gpc = false;`
- `admin/home.inc.php` — 删除 `ini_get('safe_mode')` 条件分支

### 4.9 `eval()` 调用 — ✅ 已大幅减少（18 → 6 处）

- 新增 `dinterpolate()` 安全变量插值函数（`include/global.func.php`），支持 `$var`、`{$var}`、`$arr[key]` 三种模式
- 已替换 12 处语言包 `eval()` 调用：
  - `include/global.func.php` — `sendpm()` 2 处、`showmessage()` 1 处
  - `include/sendmail.inc.php` — 2 处
  - `include/newreply.inc.php` — 1 处
  - `include/editpost.inc.php` — 1 处
  - `include/discuzcode.func.php` — `creditshide()` 2 处
  - `include/post.func.php` — `updatepostcredits()` 1 处（改用 `str_replace`）
  - `admin/global.func.php` — `cpmsg()` 1 处
  - `wap/include/global.func.php` — `wapmsg()` 1 处
- 剩余 6 处不可替代：
  - 积分公式计算 3 处（`include/global.func.php:232`、`admin/members.inc.php:925`、`admin/settings.inc.php:381`）
  - 公式校验 1 处（`admin/settings.inc.php:288`）
  - 插件钩子 1 处（`admin/plugins.inc.php:390`）
  - 动态排序 1 处（`stats.php:893`）

### 4.10 `$_GET` 直接索引 — ✅ 已修复（28 → 9 处）

- 涉及文件：`relatethread.php`、`rss.php`、`api/siteengine.php`、`api/passport.php`、`api/shopex.php`
- 改造：统一使用 `$_GET['key'] ?? ''` 或提取为局部变量
- 剩余 9 处均有 `empty()` 守卫，无需处理

### 4.11 模板编译器 `addquote()` 不支持 `$` 变量插值 — ✅ 已修复（2 处）

- 根因：模板编译器 `addquote()` 的正则字符类 `[a-zA-Z0-9_\-\.\x7f-\xff]` 不含 `$`（ASCII 36），导致 `$GLOBALS[extcredits.$id]` 编译为 `<?=$GLOBALS[extcredits.$id]?>`（`extcredits` 被 PHP 8 视为未定义常量，Fatal Error）
- 文件：`templates/default/index.htm:15`、`templates/default/memcp_credits.htm:39`
- 改造：`$GLOBALS[extcredits.$id]` → `{$GLOBALS['extcredits'.$id]}`（使用 `{...}` 语法绕过 `addquote()`，编译为 `<?=$GLOBALS['extcredits'.$id]?>`，PHP 8 合法）

### 4.12 运行时 `Undefined array key` / `Undefined variable` — ✅ 已修复（8 处）

- `include/common.inc.php:337` — `$plugins['include']` 键可能不存在 → 补充 `!empty()` 守卫
- `index.php:38` — `$qihoo_links['keywords']` 键可能不存在 → `?? ''`
- `index.php:39` — `$qihoo_links['topics']` 键可能不存在 → `?? ''`
- `index.php:43` — `$_DCOOKIE['customkw']` 键可能不存在 → `?? ''`
- `include/forum.func.php:130` — `$modlist` 变量未初始化 → `.= ` 改为 `= `
- `admin/menu.inc.php:33` — `$change`、`$collapse` 变量未初始化 → 补充 `??` 默认值
- `admin/menu.inc.php:35` — `$collapse` 同上
- `admin/home.inc.php:157` — `$lang['welcome_to']` 键名错误 → 改为 `$lang['home_welcome_to']`

### 4.13 双引号字符串内裸常量数组键 — ✅ 已修复（68 文件，约 824 处）

- 根因：PHP 8.0 不再将 `$var[key]` 中的 `key` 作为字符串回退（Fatal Error: Undefined constant）
- 涉及全部 `admin/*.inc.php`、`include/*.php`、根目录 `*.php`、`api/*.php`、`wap/include/*.php`
- 改造：
  - 双引号字符串内 `$var[key]` → `{$var['key']}`
  - 嵌套 `{$var[{$inner}][key]}` → `{$var[$inner]['key']}`（花括号嵌套在 PHP 8 中不合法）
  - `<?={$var[key]}?>` → `<?=$var['key']?>`（短标签内花括号不合法）
- 注意：模板编译器 `include/template.func.php` 的 `addquote()` 正则字符类不含 `$`，无法自动处理动态键名

### 4.14 缓存插件结构初始化 — ✅ 已修复

- `include/cache.func.php:375` — `$data['plugins'] = array()` → `array('links' => array(), 'include' => array())`
- 确保 `$plugins['links']` 和 `$plugins['include']` 键始终存在，避免模板和入口脚本访问时 Warning

### 4.15 稀疏数组缺键 Warning — ✅ 已修复（7 文件）

- 根因：`array($value => 'checked')` 仅创建当前值的键，访问其他键（如 `$arr[0]`、`$arr[1]`）时 PHP 8 产生 `Undefined array key` Warning
- 涉及文件：`admin/settings.inc.php`、`admin/forums.inc.php`、`admin/groups.inc.php`、`admin/members.inc.php`、`admin/avatarshow.inc.php`、`admin/qihoo.inc.php`
- 改造：预填充所有可能的键，如 `array(0 => '', 1 => '', 2 => ''); $arr[$value] = 'checked';`

## 5. 实施完成清单

| # | 改造项 | 涉及文件数 | 状态 |
|---|--------|-----------|------|
| 3.1 | `preg_replace /e` → `preg_replace_callback()` | 1 | ✅ |
| 3.2 | `count()` 逻辑修复 | 1 | ✅ |
| 3.3 | 模板缓存清理 | 59 文件 | ✅ |
| 4.1 | `@count()` → `is_array()` 守卫 | 1 | ✅ |
| 4.2 | `mysqli_report` 异常模式 | 1 | ✅ |
| 4.3 | `$_SERVER` 直接索引 | 21 | ✅ |
| 4.6 | `extract()` 补加 `EXTR_SKIP` | 4 | ✅ |
| 4.7 | `htmlspecialchars()` → `dhtmlspecialchars()` | 5 | ✅ |
| 4.8 | 死代码清理 | 2 | ✅ |
| 4.9 | `eval()` 语言包替换 | 10 | ✅ |
| 4.10 | `$_GET` 直接索引 | 5 | ✅ |
| 4.11 | 模板 `$GLOBALS[extcredits.$id]` Fatal Error | 2 | ✅ |
| 4.12 | 运行时 `Undefined array key` / `Undefined variable` | 5 | ✅ |
| 4.13 | 双引号字符串内裸常量数组键 | 68 | ✅ |
| 4.14 | 缓存插件结构初始化 | 1 | ✅ |
| 4.15 | 稀疏数组缺键 Warning | 6 | ✅ |
| — | 新增 `dinterpolate()` 函数 | 1 | ✅ |
| — | 项目文档更新 | 3 | ✅ |
| **合计** | | **111 源文件 + 59 编译模板** | |

## 6. 阶段 C：回归与灰度（待执行）

### 手工回归关键入口

- [ ] `index.php` — 首页/版块列表
- [ ] `forumdisplay.php` — 版块主题列表
- [ ] `viewthread.php` — 查看主题
- [ ] `post.php` — 发帖/回复
- [ ] `register.php` — 用户注册
- [ ] `logging.php` — 登录/登出
- [ ] `admincp.php` — 管理后台
- [ ] `plugin.php` — 插件调度
- [ ] `rss.php` — RSS 订阅
- [ ] `wap/index.php` — 移动端

### 验证要点

1. 页面无 Fatal Error / TypeError / Warning
2. URL 重写功能正常（`rewritestatus` 开启时）
3. 用户登录态保持正常（`sid` 注入、cookie 读写）
4. 管理后台各模块可正常操作
5. 短消息发送/接收正常（`sendpm()` 语言包插值）
6. 积分公式计算正确（`eval()` 路径）

### 灰度观察

- 灰度观察 `24~72` 小时
- 监控 `forumdata/errorlog.php` 和 Web 服务器错误日志
- 关注慢请求和内存异常

## 7. 回滚方案

1. 保留 PHP 7.4 运行池（或镜像）并与 PHP 8.4 并行。
2. 所有改造在独立分支完成，按文件粒度可逆。
3. 若灰度异常：
   - 切回 PHP 7.4 池
   - 回退本次改造提交
   - 清理模板缓存并重新编译

## 8. 风险审计备注

- ~~文档（README.md、AGENTS.md）中声明"已修复 `preg_replace /e`"，但源码仍存在 4 处 `/e` 使用点~~ → **已修复，文档已更新**
- WAP 模块（`wap/include/global.func.php:76`）的 `wmloutput()` 已使用 `preg_replace_callback`，与主模块 `output()` 改造一致
- ~~编译模板短标签~~ → 经实机验证为非阻断项，已清理缓存
- ~~`mysqli` 异常模式~~ → 已在 `connect()` 中显式关闭
- ~~`extract()` 缺失 `EXTR_SKIP`~~ → 4 处已补全
- ~~`eval()` 语言包调用~~ → 12 处已替换为 `dinterpolate()`，剩余 6 处为不可替代的动态公式/插件钩子
- `mysqli` 扩展在当前 CLI 环境未加载，部署前必须验证扩展可用性

## 9. 官方参考

- PHP `preg_replace`：<https://www.php.net/manual/en/function.preg-replace.php>
- PHP `preg_replace_callback`：<https://www.php.net/manual/en/function.preg-replace-callback.php>
- PHP `count`：<https://www.php.net/manual/en/function.count.php>
- PHP `htmlspecialchars`：<https://www.php.net/manual/en/function.htmlspecialchars.php>
- PHP `mysqli_report`：<https://www.php.net/manual/en/mysqli.report-mode.php>
- PHP 8.0 不兼容变更：<https://www.php.net/manual/en/migration80.incompatible.php>
- PHP 8.1 不兼容变更：<https://www.php.net/manual/en/migration81.incompatible.php>
- PHP 8.2 不兼容变更：<https://www.php.net/manual/en/migration82.incompatible.php>
- PHP 8.3 不兼容变更：<https://www.php.net/manual/en/migration83.incompatible.php>
- PHP 8.4 不兼容变更：<https://www.php.net/manual/en/migration84.incompatible.php>
- PHP `<?=` 短回显标签：<https://www.php.net/manual/en/language.basic-syntax.phptags.php>
