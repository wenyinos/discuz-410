# Discuz 4.1.0 升级到 PHP 8.4+ 可行性与改造报告

## 1. 结论摘要

- 可行性：**高（约 80%）**
- 风险等级：**中等**
- 最小可运行改造工作量：**0.5~1.5 人日**
- 稳定上线（含告警治理+回归）工作量：**3~7 人日**

说明：在本地 `PHP 8.4.20` 环境完成基线扫描后，`forumdata/templates` 之外 `170` 个 PHP 文件语法检查通过；但存在少量运行时阻断项，需先修复再上线。

## 2. 扫描范围与方法

- 范围：项目根目录全部 `*.php`，重点覆盖 `include/`、`admin/`、入口脚本与缓存模板目录。
- 方法：
  - 语法检查：逐文件 `php -l`
  - 关键字检索：已删除/收紧特性（`/e`、`count` 非数组、短标签等）
  - 运行时风险抽样：`$_SERVER` 等直接索引、模板编译输出链路
  - 实机验证：PHP 8.4.20 下实际运行 `preg_replace /e`、`count(null)`、`@count(null)` 等行为
  - 全量扫描：`extract()` 缺失 `EXTR_SKIP`、`eval()` 调用、`htmlspecialchars()` flags、`mysqli` 异常模式、已删除函数/特性、死代码

## 3. 必改阻断项（上线前必须完成）

### 3.1 `preg_replace` 使用 `/e` 修饰符（PHP 7.0 已移除，PHP 8 报错）

- 文件：`include/global.func.php`
- 位置：第 `356` 行（`/ies` 修饰符）、第 `368`、`369`、`370` 行（`/e` 修饰符），均在 `output()` 函数内
- 影响：URL 重写与 `sid` 注入链路。PHP 8.4 实测返回 `PHP Warning: preg_replace(): Unknown modifier 'e'`，匹配结果为空字符串，页面输出异常。
- ⚠️ **历史矛盾**：`/e` 修饰符在 PHP 7.0 已被移除。项目文档声称"PHP 7.4 兼容"，但此代码在 PHP 7.0+ 下同样无法工作。建议同步修正项目文档。
- 改造动作：
  1. 将 `preg_replace($searcharray, $replacearray, ...)` 改为 `preg_replace_callback()`
  2. 将 `transsid()`、`rewrite_forum()`、`rewrite_thread()`、`rewrite_profile()` 包装为回调闭包
  3. 保持现有重写语义与参数顺序不变

**改造示例**（`output()` 函数中 `$transsidstatus` 分支）：

```php
// 改造前
$searcharray = array(
    "/\<a(\s*[^\>]+\s*)href\=([\"|\']?)([^\"\'\s]+)/ies",
    "/(\<form.+?\>)/is"
);
$replacearray = array(
    "transsid('\\3','<a\\1href=\\2')",
    "\\1\n<input type=\"hidden\" name=\"sid\" value=\"$sid\">"
);
$content = preg_replace($searcharray, $replacearray, ob_get_contents());

// 改造后
$content = ob_get_contents();
$content = preg_replace_callback(
    "/\<a(\s*[^\>]+\s*)href\=([\"|\']?)([^\"\'\s]+)/is",
    function($m) { return transsid($m[3], '<a'.$m[1].'href='.$m[2]); },
    $content
);
$content = preg_replace(
    "/(\<form.+?\>)/is",
    "\\1\n<input type=\"hidden\" name=\"sid\" value=\"$sid\">",
    $content
);
```

`rewritestatus` 分支同理，将三个 `rewrite_*` 调用改为 `preg_replace_callback`。

---

### 3.2 `count()` 作用于非数组的 `TypeError`（PHP 7.2+ 行为变更）

- 文件：`topicadmin.php`
- 位置：第 `389` 行
- 现状：`!is_array($delete) && !count($delete)`
- 风险：PHP 8.4 实测 `count(null)`、`count(false)`、`count('')` 均抛出 `TypeError: count(): Argument #1 ($value) must be of type Countable|array`。`&&` 运算符在左操作数为 `true`（即非数组）时仍会执行 `count()`，触发异常。
- 改造动作：
  1. 逻辑修正为 `!is_array($delete) || !count($delete)`（`||` 短路求值，非数组时不再执行 `count()`）
  2. 或标准化输入：`$delete = is_array($delete) ? $delete : array();` 后再判断

---

### 3.3 编译模板短标签 — **经验证为非阻断项**

- 目录：`forumdata/templates/`
- 现状：
  - 模板文件数：`59`
  - 含 `<?=...?>` 标签文件数：`54`
  - 命中 `<?=...?>` 行数：`911`
- **实机验证**：编译模板使用的是 `<?=...?>`（短回显标签），而非 `<?...?>`（短代码标签）。`<?=...?>` 自 PHP 5.4 起**始终可用**，不受 `short_open_tag` 配置影响。
- 源文件（非编译目录）已全部使用 `<?php` 标准标签，无遗留短标签。
- 模板编译器（`include/template.func.php`）生成的标签格式为 `<?php ... ?>`（代码块）和 `<?=...?>`（输出），均兼容 PHP 8.4。
- **结论**：此项无需改造。原报告将 `<?=...?>` 统计为"短标签"属于误判。

## 4. 强烈建议整改项（降低告警与灰度风险）

### 4.1 `@` 操作符不能抑制 `TypeError`（PHP 8.0+ 行为变更）

- 文件：`include/cache.func.php`
- 位置：第 `480`、`486` 行
- 现状：`intval(@count($threadarray[$gid]))` 和 `intval(@count($threadarray['global']))`
- 风险：PHP 8 中 `@` 不能抑制 `TypeError`。若 `$threadarray[$gid]` 或 `$threadarray['global']` 为 `null`/`false`，`@count()` 仍会抛出 `TypeError`。
- 建议：改为 `intval(is_array($threadarray[$gid] ?? null) ? count($threadarray[$gid]) : 0)`

### 4.2 `mysqli` PHP 8.1+ 默认异常行为变更

- 文件：`include/db_mysql.class.php`
- 位置：第 `58-69` 行（`query()` 方法）
- 现状：`query()` 方法使用 `@$this->conn->query($sql)` 抑制错误，依赖返回 `false` 判断失败。
- 风险：PHP 8.1 将 `mysqli.report_mode` 默认值从 `0` 改为 `MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT`，mysqli 在查询失败时**抛出 `mysqli_sql_exception`** 而非返回 `false`。`@` 操作符不能抑制异常，异常会向上传播导致未捕获的 Fatal Error。
- 建议：在 `connect()` 方法中显式设置 `mysqli_report(MYSQLI_REPORT_OFF)` 以保持旧行为，或在 `query()` 中增加 `try-catch` 捕获 `mysqli_sql_exception`。

```php
// 在 connect() 方法末尾追加
$this->conn->report_mode = MYSQLI_REPORT_OFF;
```

### 4.3 统一修复 `$_SERVER/$_GET/$_POST` 直接索引

- 现状：检测到约 `116` 处超全局直接索引（`$_SERVER` 47 处、`$_GET`/`$_POST`/`$_COOKIE`/`$_FILES` 约 69 处）。
- 风险：PHP 8 将未定义键提升为 `E_WARNING`，可污染输出并干扰 header 行为。
- 建议：
  - 将热路径改为 `isset(...) ? ... : ''` 或 `??`
  - 优先入口与安全链路：`include/common.inc.php`、`include/security.inc.php`、`admincp.php`、`api/*`

### 4.4 错误策略分环境

- 现状：多处使用 `error_reporting(E_ERROR | E_WARNING | E_PARSE)`。
- 建议：
  - 生产：关闭 `display_errors`，仅写日志
  - 测试/预发：开启 `E_ALL` 并收敛 deprecation/warning

### 4.5 运行扩展清单前置校验

- 必需/高概率依赖：`mysqli`、`xml`、`gd`、`iconv`
- ⚠️ 当前语法检查环境（PHP 8.4.20 CLI）**未加载 `mysqli` 扩展**，`php -l` 仅检查语法不检查运行时依赖。实际部署必须确认 `php -m | grep mysqli` 输出 `mysqli`。
- 部署动作：
  - 发布前脚本化检查扩展可用性
  - 缺失即阻断部署

### 4.6 `extract()` 缺少 `EXTR_SKIP`（安全风险）

- 现状：多数 `extract()` 调用已使用 `EXTR_SKIP`，但以下位置遗漏：
  - `include/post.func.php:344` — `extract($db->fetch_array($query))`
  - `admin/counter.inc.php:89` — `extract($db->fetch_array($query))`
  - `rss.php:50` — `extract($member)`
  - `logging.php:97` — `extract($member)`
- 风险：无 `EXTR_SKIP` 时，`extract()` 默认使用 `EXTR_OVERWRITE`，数据库返回的字段名若与全局变量（如 `$discuz_uid`、`$adminid`）同名，可覆盖当前用户会话变量，导致权限提升或身份伪造。
- 建议：统一补加 `EXTR_SKIP`，或改用数组下标访问（`$row['field']`）替代 `extract()`。

### 4.7 `htmlspecialchars()` PHP 8.1 默认标志变更

- 现状：约 `10` 处 `htmlspecialchars()` 调用未指定 flags 参数，分布在 `include/editpost.inc.php`、`include/db_mysql_error.inc.php`、`include/discuzcode.func.php`、`admin/templates.inc.php`、`faq.php`、`admincp.php` 等文件。
- 风险：PHP 8.1 将默认 flags 从 `ENT_COMPAT` 改为 `ENT_QUOTES | ENT_SUBSTITUTE`。`ENT_QUOTES` 会额外转义单引号 `'` → `&#039;`，可能导致含单引号的表单值或 JavaScript 字符串输出异常。
- 建议：明确指定 `htmlspecialchars($string, ENT_COMPAT, $charset)` 以保持旧行为，或改用项目自定义的 `dhtmlspecialchars()`（不转义单引号，行为一致）。

### 4.8 死代码清理

- `$magic_quotes_gpc = false`（`include/common.inc.php:23`）：`magic_quotes_gpc` 在 PHP 5.4 已移除，此赋值为死代码。
- `ini_get('safe_mode')`（`admin/home.inc.php:88`）：`safe_mode` 在 PHP 5.4 已移除，条件永假。
- 建议：删除死代码以减少维护困惑。

### 4.9 `eval()` 使用广泛（安全与可维护性风险）

- 现状：全项目约 `18` 处 `eval()` 调用，分布在 `include/global.func.php`、`include/discuzcode.func.php`、`include/post.func.php`、`include/sendmail.inc.php`、`admin/settings.inc.php`、`admin/global.func.php`、`stats.php` 等文件。
- 主要用途：语言包变量插值（`eval("\$message = \"$language[msg]\";")`）、积分公式计算、插件钩子执行。
- 风险：`eval()` 在 PHP 8 中行为未变，但若语言包或用户输入含未转义的 `"` 或 `$`，可导致代码注入。
- 建议：此项非 PHP 8.4 阻断项，但作为长期技术债务应在后续版本中用 `str_replace` 或 `sprintf` 替代。

## 5. 分阶段实施方案

### 阶段 A：阻断项修复（必须）

1. 修复 `/e`（`include/global.func.php` 第 356、368-370 行）
2. 修复 `count` 非数组（`topicadmin.php` 第 389 行）
3. 清理模板缓存并重编译（确保 `forumdata/templates/` 内容与当前代码一致）

交付标准：前台与后台主链路无 Fatal，页面可正常渲染。

### 阶段 B：告警治理（建议）

1. 修复 `@count()` 潜在 TypeError（`include/cache.func.php` 第 480、486 行）
2. 设置 `mysqli_report(MYSQLI_REPORT_OFF)`（`include/db_mysql.class.php` `connect()` 方法）
3. 补全 `extract()` 缺失的 `EXTR_SKIP`（4 处）
4. 批量修复超全局未定义索引（116 处）
5. 统一 `htmlspecialchars()` flags 参数或改用 `dhtmlspecialchars()`
6. 收敛 PHP 8 deprecation/warning 到可接受阈值

交付标准：`display_errors=Off` 下业务可稳定运行，日志不出现高频噪音告警。

### 阶段 C：回归与灰度

1. 手工回归关键入口：
   - `index.php`
   - `forumdisplay.php`
   - `viewthread.php`
   - `post.php`
   - `register.php`
   - `logging.php`
   - `admincp.php`
   - `plugin.php`
   - `rss.php`
   - `wap/index.php`
2. 灰度观察 `24~72` 小时，监控错误日志与慢请求

## 6. 回滚方案

1. 保留 PHP 7.4 运行池（或镜像）并与 PHP 8.4 并行。
2. 所有改造在独立分支完成，按文件粒度可逆。
3. 若灰度异常：
   - 切回 PHP 7.4 池
   - 回退本次改造提交
   - 清理模板缓存并重新编译

## 7. 风险审计备注

- 文档（README.md、AGENTS.md）中声明"已修复 `preg_replace /e`"，但源码 `include/global.func.php:356-370` 仍存在 4 处 `/e` 使用点，文档与代码状态不一致。建议先更新项目文档，避免后续误判。
- WAP 模块（`wap/include/global.func.php:76`）的 `wmloutput()` 已使用 `preg_replace_callback` 修复，可作为主模块 `output()` 改造的参考实现。
- 编译模板短标签（`<?=...?>`）经实机验证为非阻断项，原报告 3.3 节描述需修正。
- `mysqli` 扩展在当前 CLI 环境未加载，语法检查不覆盖运行时依赖，部署前必须验证扩展可用性。
- PHP 8.1 默认 `mysqli.report_mode` 变更为异常模式，需在 `db_mysql.class.php` 中显式关闭以保持兼容。

## 8. 官方参考

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
