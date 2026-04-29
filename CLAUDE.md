# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Discuz! 4.1.0 — legacy Chinese BBS/forum system (Comsenz Inc., 2001-2006), MIT-licensed community fork.
**PHP 7.4 compatible** — upgraded from mysql_* to mysqli, removed magic_quotes, fixed preg_replace /e modifier.
No modern tooling: no composer, no npm, no CI/CD, no test suite, no linter.

## Common Commands

```bash
# Database setup
mysql -u root -p your_database < install/discuz.sql

# Directory permissions (required for web server)
chmod -R 777 attachments/ forumdata/

# No build, lint, or test commands exist. Manual verification only.
```

## Architecture

### Bootstrap Chain

Every page script defines `CURSCRIPT` then requires `include/common.inc.php`, which:
1. Loads `config.inc.php` (DB credentials, table prefix `cdb_`)
2. Connects to MySQL via `include/db_mysql.class.php` (mysqli extension)
3. Extracts `$_GET`, `$_POST`, `$_COOKIE` into global scope via `extract(daddslashes(...), EXTR_SKIP)`
4. Loads cached settings from `forumdata/cache/cache_settings.php`
5. Restores user session from cookie `auth`
6. Loads page-specific cache if CURSCRIPT is in `[index, forumdisplay, viewthread, post, blog]`
7. Runs plugin hooks, cron tasks, then hands control back to the page script

### Key Directories

- `include/` — core functions, classes, cron jobs (`crons/`), charset tables (`tables/`)
- `admin/` — admin panel modules, each a `.inc.php` loaded by `admincp.php`
- `templates/default/` — `.htm` template source files (custom Discuz syntax)
- `forumdata/cache/` — generated PHP cache files (settings, styles, usergroups)
- `forumdata/templates/` — compiled template PHP files (generated, do not edit)
- `api/` — external integrations (passport, avatar, qihoo search, alipay)
- `plugins/` — plugin scripts (empty by default)
- `install/discuz.sql` — full database schema

### Entry Points

Root `.php` files: `index.php`, `forumdisplay.php`, `viewthread.php`, `post.php`, `register.php`, `logging.php`, `member.php`, `pm.php`, `search.php`, `admincp.php`, `plugin.php`, `misc.php`, `blog.php`, `stats.php`, `attachment.php`

## Code Patterns

- **Access guard**: Every PHP file starts with `if(!defined('IN_DISCUZ')) exit('Access Denied');` — never remove this
- **No autoloading**: All includes are manual `require_once` with `DISCUZ_ROOT` paths
- **Global variables everywhere**: `$db`, `$discuz_uid`, `$tablepre`, `$charset`, `$timestamp`, etc. are all globals
- **Template system**: `template('name')` compiles `templates/default/{name}.htm` → `forumdata/templates/{id}_name.tpl.php`
- **Language files**: `language('name')` loads from `templates/default/{name}.lang.php`
- **Cache regeneration**: If `forumdata/cache/` files are missing, `common.inc.php` auto-regenerates them via `include/cache.func.php` and exits with "Caches successfully created" message

## Development Workflow

- **No build/test/lint commands exist** — manual verification only
- **Cache files** in `forumdata/cache/` are auto-generated; editing them directly has no lasting effect
- **Template workflow**: Edit `.htm` files in `templates/default/`, not the compiled `.tpl.php` files in `forumdata/templates/`. Set `$tplrefresh = 1` in `config.inc.php` to auto-recompile
- **Database queries**: Use `$db->query()` / `$db->fetch_array()` / `$db->result()` — no parameterized queries, SQL built via string concatenation

## Security Notes (Legacy Code)

- `config.inc.php` contains plaintext DB credentials — never commit real credentials
- `extract()` on raw user input in `common.inc.php` — mitigated with `EXTR_SKIP`
- SQL queries use string concatenation, not prepared statements
- `@` error suppression is common throughout
