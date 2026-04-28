# Discuz! 4.1.0 → PHP 7.4 升级评估报告

## 评估结论

**可行性：可行，但工作量大，风险高。**

项目共 132 个 PHP 源文件（32,386 行代码），涉及 6 类致命兼容性问题。核心改造集中在数据库抽象层和模板编译器，其余为散落全局的 API 替换。预计总工时 3-5 人天，需全量回归测试。

---

## 一、致命错误（PHP 7.0+ 直接无法运行）

### 1.1 mysql_* 扩展已移除（PHP 7.0 删除）

`mysql_*` 系列函数在 PHP 7.0 中被完全移除，必须迁移至 `mysqli_*` 或 PDO。

**涉及文件：**

| 文件 | 行号 | 函数调用 |
|------|------|----------|
| `include/db_mysql.class.php` | 21, 25, 37, 41, 46, 52, 56, 61, 70, 74, 78, 82, 87, 92, 96, 100, 105, 110, 114, 118 | `mysql_connect`, `mysql_pconnect`, `mysql_query`, `mysql_select_db`, `mysql_fetch_array`, `mysql_affected_rows`, `mysql_error`, `mysql_errno`, `mysql_result`, `mysql_num_rows`, `mysql_num_fields`, `mysql_free_result`, `mysql_insert_id`, `mysql_fetch_row`, `mysql_fetch_field`, `mysql_get_server_info`, `mysql_close` |
| `admin/global.func.php` | 234, 253 | `mysql_escape_string` |

**影响范围：** 全站所有数据库操作均经过 `include/db_mysql.class.php`，此文件是改造核心。

**改造方案：**
- 将 `db_mysql.class.php` 重写为 `db_mysqli.class.php`
- 所有 `mysql_*` 调用替换为 `mysqli_*` 对应方法
- `mysql_connect` → `mysqli_connect`，需额外传递连接对象
- `mysql_query($sql)` → `mysqli_query($conn, $sql)`
- `mysql_fetch_array($result)` → `mysqli_fetch_array($result)`
- `mysql_escape_string` → `mysqli_real_escape_string($conn, $str)`
- `MYSQL_ASSOC` / `MYSQL_NUM` 常量改为 `MYSQLI_ASSOC` / `MYSQLI_NUM`
- `config.inc.php:74` 的 `$database = 'mysql'` 改为 `$database = 'mysqli'`

**预计改动：** `include/db_mysql.class.php`（全量重写，约 126 行）、`admin/global.func.php`（2 处）、`config.inc.php`（1 处）

---

### 1.2 preg_replace /e 修饰符已移除（PHP 7.0 删除）

`/e` 修饰符允许在正则替换中执行 PHP 代码，PHP 7.0 中被移除，必须改用 `preg_replace_callback()`。

**涉及文件（15 处）：**

| 文件 | 行号 | 说明 |
|------|------|------|
| `include/template.func.php` | 37, 41, 42, 47, 48, 49, 53, 54, 55 | 模板编译器核心，9 处 |
| `include/discuzcode.func.php` | 141, 145, 224 | BBCode 解析，3 处 |
| `viewthread.php` | 290 | 附件标签解析，1 处 |
| `archiver/include/thread.inc.php` | 69 | 归档页面 jammer，1 处 |
| `wap/include/global.func.php` | 76 | WAP URL 重写，1 处 |

**改造方案（示例）：**

```php
// 改前
$template = preg_replace("/\{lang\s+(.+?)\}/ies", "languagevar('\\1')", $template);

// 改后
$template = preg_replace_callback("/\{lang\s+(.+?)\}/is", function($matches) {
    return languagevar($matches[1]);
}, $template);
```

**风险点：** `include/template.func.php` 是模板编译器的核心，改造后必须验证所有模板能正确编译。

---

### 1.3 $HTTP_*_VARS 超全局变量已移除（PHP 5.4 删除）

**涉及文件（18 处）：**

| 文件 | 行号 | 变量 |
|------|------|------|
| `include/common.inc.php` | 24-28 | `$HTTP_GET_VARS`, `$HTTP_POST_VARS`, `$HTTP_COOKIE_VARS`, `$HTTP_SERVER_VARS`, `$HTTP_ENV_VARS` |
| `api/passport.php` | 14-15 | `$HTTP_GET_VARS`, `$HTTP_SERVER_VARS` |
| `api/shopex.php` | 14-15 | 同上 |
| `api/siteengine.php` | 14-15 | 同上 |
| `api/javascript.php` | 17-18 | 同上 |
| `rss.php` | 22-23 | 同上 |
| `relatethread.php` | 24-25 | `$HTTP_SERVER_VARS`, `$HTTP_GET_VARS` |
| `archiver/index.php` | 41 | `$HTTP_SERVER_VARS` |

**改造方案：**

```php
// 改前
if(PHP_VERSION < '4.1.0') {
    $_GET = &$HTTP_GET_VARS;
    $_POST = &$HTTP_POST_VARS;
    // ...
}

// 改后：直接删除整个 if 块，PHP 7.4 原生支持 $_GET/$_POST/$_COOKIE/$_SERVER
```

---

### 1.4 set_magic_quotes_runtime() 已移除（PHP 7.4 删除）

**涉及文件：**

| 文件 | 行号 |
|------|------|
| `include/common.inc.php` | 15 |
| `relatethread.php` | 13 |

**改造方案：** 直接删除 `set_magic_quotes_runtime(0);` 调用。

---

### 1.5 get_magic_quotes_gpc() 已移除（PHP 7.4 删除）

**涉及文件：**

| 文件 | 行号 |
|------|------|
| `include/common.inc.php` | 34 |

**影响链：**
- `common.inc.php:34` 设置 `$magic_quotes_gpc`
- `include/global.func.php:105` 的 `daddslashes()` 函数依赖此变量
- `daddslashes()` 被全站广泛调用（`extract(daddslashes(...))` 等）

**改造方案：**

```php
// 改前
$magic_quotes_gpc = get_magic_quotes_gpc();
if(!$magic_quotes_gpc) {
    $_FILES = daddslashes($_FILES);
}

// 改后：PHP 7.4 不会自动转义，始终需要 addslashes
$magic_quotes_gpc = false;
$_FILES = daddslashes($_FILES);
```

同时修改 `include/global.func.php:104-115` 的 `daddslashes()` 函数，移除 `$magic_quotes_gpc` 判断，始终执行 `addslashes`。

---

### 1.6 PHP4 风格构造函数

**涉及文件：**

| 文件 | 行号 | 说明 |
|------|------|------|
| `include/chinese.class.php` | 31 | `function Chinese(...)` 与类名同名 |

PHP 7.0 中，与类名同名的方法不再被视为构造函数（已 deprecated），PHP 8.0 中被完全移除。

**改造方案：**

```php
// 改前
function Chinese($SourceLang, $TargetLang) { ... }

// 改后
function __construct($SourceLang, $TargetLang) { ... }
```

---

## 二、高优先级问题（警告/弃用通知）

### 2.1 短开标签 `<?`（115+ 处）

项目大量使用 `<?` 而非 `<?php` 作为 PHP 开标签。PHP 7.4 中短开标签是否生效取决于 `php.ini` 的 `short_open_tag` 设置，默认为 `Off`。

**涉及范围：**
- `plugin.php`（1 处，文件首行）
- `admincp.php`（2 处）
- `include/db_mysql_error.inc.php`（2 处）
- `admin/` 目录下几乎所有 `.inc.php` 文件（80+ 处）
- `forumdata/templates/` 编译模板（大量，但属生成文件）

**改造方案：** 将所有 `<?` 替换为 `<?php`。建议用脚本批量处理：

```bash
find . -name "*.php" -not -path "*/forumdata/templates/*" \
  -exec sed -i 's/^<\?$/<?php/g' {} \;
```

注意：`forumdata/templates/` 下的编译模板会在下次模板刷新时自动重新生成，无需手动修改。

---

### 2.2 `var` 关键字声明类属性

**涉及文件（5 处）：**

| 文件 | 行号 |
|------|------|
| `include/db_mysql.class.php` | 17 |
| `include/chinese.class.php` | 20-23 |

`var` 是 PHP 4 的属性声明方式，PHP 7.4 中仍可用但会产生 `E_DEPRECATED` 警告。

**改造方案：**

```php
// 改前
var $querynum = 0;

// 改后
public $querynum = 0;
```

---

### 2.3 `count()` 对非可数类型调用（PHP 7.2+ 警告）

PHP 7.2 起，对非 `Countable` 类型（如 `null`、`false`、字符串）调用 `count()` 会触发 `E_WARNING`。

**涉及范围：** 44 处 `count()` 调用，部分传入可能为 `null` 或 `false` 的变量。

**改造方案：** 在关键调用前添加类型检查：

```php
// 改前
count($members)

// 改后
is_array($members) ? count($members) : 0
// 或使用 PHP 7.3+ 的 is_countable()
```

---

## 三、中优先级问题（安全隐患，非版本相关但应一并修复）

### 3.1 `extract()` 对用户输入的直接解包

**涉及文件（8 处）：**

| 文件 | 行号 | 输入来源 |
|------|------|----------|
| `include/common.inc.php` | 35 | `$_COOKIE` |
| `include/common.inc.php` | 36 | `$_POST` |
| `include/common.inc.php` | 37 | `$_GET` |
| `include/common.inc.php` | 84 | `$_DCACHE['settings']` |
| `include/common.inc.php` | 191, 310 | `$_DSESSION` |
| `wap/include/login.inc.php` | 42 | `$member` |
| `wap/include/stats.inc.php` | 20 | `$db->fetch_array()` |

**风险：** 攻击者可通过 GET/POST/COOKIE 参数覆盖任意全局变量（如 `$tablepre`、`$dbhost`、`$adminid`）。

**改造建议：** 使用 `EXTR_SKIP` 或 `EXTR_PREFIX_ALL` 替代默认的 `EXTR_OVERWRITE`，或逐步迁移到显式变量赋值。

---

### 3.2 `unserialize()` 未限制允许的类

**涉及范围：** 64 处 `unserialize()` 调用，部分反序列化用户可控数据（如插件导入）。

**风险：** PHP 对象注入攻击。

**改造方案：** 对不受信任的数据使用 `unserialize($data, ['allowed_classes' => false])`。

---

## 四、低优先级问题

### 4.1 `eval()` 使用

19 处 `eval()` 调用，主要用于：
- 积分公式计算（`include/global.func.php:214`）
- 语言变量插值（多处）
- 插件钩子执行（`admin/plugins.inc.php:390`）
- 排序函数动态调用（`stats.php:893`）

虽非 PHP 7.4 兼容性问题，但属于安全隐患。

### 4.2 错误抑制符 `@`

全站广泛使用 `@` 抑制错误。PHP 7.4 中 `@` 仍有效，但会略微影响性能。建议在升级后逐步移除，改用正确的错误处理。

---

## 五、改造文件清单（按优先级排序）

### 必须修改（不改则无法运行）

| 序号 | 文件 | 改动类型 | 复杂度 |
|------|------|----------|--------|
| 1 | `include/db_mysql.class.php` | 全量重写 mysql→mysqli | 高 |
| 2 | `include/template.func.php` | preg_replace /e→callback（9处） | 高 |
| 3 | `include/common.inc.php` | 移除 magic_quotes、HTTP_*_VARS、extract 修复 | 中 |
| 4 | `include/global.func.php` | daddslashes 逻辑修复、var→public | 低 |
| 5 | `include/discuzcode.func.php` | preg_replace /e→callback（3处） | 中 |
| 6 | `include/chinese.class.php` | PHP4 构造函数→__construct、var→public | 低 |
| 7 | `admin/global.func.php` | mysql_escape_string→mysqli_real_escape_string（2处） | 低 |
| 8 | `config.inc.php` | `$database='mysql'`→`'mysqli'` | 低 |
| 9 | `viewthread.php` | preg_replace /e→callback（1处） | 低 |
| 10 | `archiver/include/thread.inc.php` | preg_replace /e→callback（1处） | 低 |
| 11 | `wap/include/global.func.php` | preg_replace /e→callback（1处） | 低 |
| 12 | `relatethread.php` | 移除 magic_quotes、HTTP_*_VARS | 低 |
| 13 | `rss.php` | 移除 HTTP_*_VARS | 低 |
| 14 | `api/passport.php` | 移除 HTTP_*_VARS | 低 |
| 15 | `api/shopex.php` | 移除 HTTP_*_VARS | 低 |
| 16 | `api/siteengine.php` | 移除 HTTP_*_VARS | 低 |
| 17 | `api/javascript.php` | 移除 HTTP_*_VARS | 低 |
| 18 | `archiver/index.php` | 移除 HTTP_*_VARS | 低 |

### 建议修改（不改会出警告）

| 序号 | 文件 | 改动类型 |
|------|------|----------|
| 19 | `plugin.php` | `<?` → `<?php` |
| 20 | `admincp.php` | `<?` → `<?php`（2处） |
| 21 | `admin/*.inc.php`（约20个文件） | `<?` → `<?php`（80+处） |
| 22 | `include/db_mysql_error.inc.php` | `<?` → `<?php` |
| 23 | 44 个文件中的 `count()` 调用 | 添加类型检查 |

---

## 六、推荐升级策略

### 方案 A：最小改动（推荐）

1. **只改必须修改的 18 个文件**，不改模板和 admin 短标签
2. 用批量脚本处理 `<?` → `<?php`（约 115 处）
3. 确保 `php.ini` 中 `short_open_tag = On` 作为过渡
4. 全量手动测试

**预计工时：** 2-3 人天

### 方案 B：全面现代化

在方案 A 基础上：
1. 将 `db_mysqli.class.php` 改为 PDO 封装
2. 修复所有 `extract()` 安全问题
3. 修复所有 `unserialize()` 安全问题
4. 添加错误处理替代 `@` 抑制

**预计工时：** 5-7 人天

---

## 七、测试要点

1. **数据库操作**：注册、登录、发帖、回帖、搜索、管理面板所有 CRUD
2. **模板系统**：切换风格、编辑模板、验证编译输出
3. **用户认证**：Cookie 验证、Session 管理、密码加密（`authcode` 函数）
4. **附件系统**：上传、下载、水印、权限检查
5. **插件系统**：安装、卸载、钩子执行
6. **管理后台**：所有管理功能（数据库备份/恢复特别关注，涉及 `mysql_escape_string`）
7. **API 接口**：通行证、头像、支付宝回调
8. **WAP/Archiver**：移动端和归档页面
9. **定时任务**：所有 cron 脚本

---

## 八、风险总结

| 风险项 | 等级 | 说明 |
|--------|------|------|
| 数据库层改造 | 🔴 高 | 全站核心，任何错误导致全站不可用 |
| 模板编译器改造 | 🔴 高 | 9 处 /e 修饰符，错误导致页面白屏 |
| magic_quotes 移除 | 🟡 中 | 影响全站数据转义逻辑 |
| 短标签批量替换 | 🟡 中 | 数量大，需脚本处理+人工验证 |
| PHP4 构造函数 | 🟢 低 | 仅 1 处 |
| HTTP_*_VARS | 🟢 低 | 直接删除条件块即可 |
