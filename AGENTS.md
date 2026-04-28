# Discuz! 4.1.0 - Agent Guide

## Project Overview

Legacy Chinese BBS forum (Comsenz Inc., 2006). PHP 4/5 era code, now MIT-licensed.
No modern tooling: no composer, no npm, no CI/CD, no test suite, no linter, no .gitignore.

## Architecture

**Bootstrap chain**: Every page script sets `define('CURSCRIPT', '...')` then requires `include/common.inc.php`, which:
- Loads `config.inc.php` (DB credentials, table prefix `cdb_`)
- Connects to MySQL via `include/db_mysql.class.php`
- Extracts `$_GET`, `$_POST`, `$_COOKIE` into global scope via `extract(daddslashes(...))`
- Loads cached settings from `forumdata/cache/cache_settings.php`
- Restores user session from cookie `auth`
- Loads page-specific cache if CURSCRIPT is in `[index, forumdisplay, viewthread, post, blog]`
- Runs plugin hooks, cron tasks, then hands control back to the page script

**Key directories**:
- `include/` - core: functions, classes, cron jobs (`crons/`), charset tables (`tables/`)
- `admin/` - admin panel modules, each a `.inc.php` loaded by `admincp.php`
- `templates/default/` - `.htm` template files with custom Discuz syntax
- `forumdata/cache/` - generated PHP cache files (settings, styles, usergroups, etc.)
- `forumdata/templates/` - compiled template PHP files (generated, do not edit)
- `api/` - external integrations (passport, avatar, qihoo search, alipay)
- `plugins/` - plugin scripts (empty by default)
- `wap/` - mobile WAP interface
- `install/discuz.sql` - full database schema (1795 lines)

**Entry points** (root `.php` files):
`index.php`, `forumdisplay.php`, `viewthread.php`, `post.php`, `register.php`,
`logging.php`, `member.php`, `pm.php`, `search.php`, `admincp.php`, `plugin.php`,
`misc.php`, `blog.php`, `stats.php`, `attachment.php`, `archiver/`

## Code Patterns

- **Access guard**: Every PHP file starts with `if(!defined('IN_DISCUZ')) exit('Access Denied');` - never remove this
- **No autoloading**: All includes are manual `require_once` with `DISCUZ_ROOT` paths
- **Global variables everywhere**: `$db`, `$discuz_uid`, `$tablepre`, `$charset`, `$timestamp`, etc. are all globals
- **Template system**: `template('name')` returns compiled template path from `forumdata/templates/`; source is `templates/default/{name}.htm`
- **Language files**: `language('name')` loads from `templates/default/{name}.lang.php`
- **Cache regeneration**: If `forumdata/cache/` files are missing, `common.inc.php` auto-regenerates them via `include/cache.func.php` and exits with a "Caches successfully created" message

## Security Concerns (Legacy Code)

- `config.inc.php:17` contains plaintext database credentials - never commit real credentials
- Extensive use of `extract()` on raw user input (`common.inc.php:35-37`) - all user input becomes global variables
- SQL queries built via string concatenation, not parameterized queries
- `magic_quotes` handling code (`set_magic_quotes_runtime`, `get_magic_quotes_gpc`) is PHP 4/5 legacy

## Development

**No build/test/lint commands exist.** Manual verification only.

To run locally:
1. Requires PHP + MySQL (or PostgreSQL)
2. Import `install/discuz.sql` into database
3. Configure `config.inc.php` with DB credentials
4. Ensure `attachments/` and `forumdata/` are writable by web server
5. Access `install/` for initial setup, then `admincp.php` for admin

**Cache files** in `forumdata/cache/` are generated, not hand-edited. Editing them directly has no lasting effect - they get regenerated.

**Template workflow**: Edit `.htm` files in `templates/default/`, not the compiled `.tpl.php` files in `forumdata/templates/`. Set `$tplrefresh = 1` in `config.inc.php` to auto-recompile.

## Conventions

- File encoding: mixed (GBK source with UTF-8 output; `$charset = 'utf-8'` in config)
- PHP open tags: `<?php` (standard) but some files use short tags `<?` (e.g., `plugin.php`)
- No namespace usage; all functions are global
- Database queries use `$db->query()` / `$db->fetch_array()` / `$db->result()`
- Table names use `$tablepre` prefix (default `cdb_`)
- Error suppression with `@` operator is common throughout
