# Discuz! 4.1.0 (Community-Maintained Fork)

[中文文档](README_zh.md)

Discuz! is a classic BBS/forum system originally developed by Comsenz Inc. (2001-2006).  
This repository is a community-maintained fork under the MIT license, updated for modern PHP environments.

## Current Fork Status

- **PHP 8.4+ compatibility upgrade completed** (see `php84-upgrade-report.md`)
- Major PHP8 warning/error-prone legacy paths have been hardened
- Runtime compatibility warning filter added in `include/common.inc.php` (targeted legacy warning prefixes only)
- Deprecated external integrations are force-disabled by default:
  - Passport
  - E-commerce (Alipay / Orders / ShopEx)
  - AvatarShow
- Matching admin actions are blocked in `admincp.php` and related menu entries are hidden
- Admin panel frameset no longer embeds the `sid` session token in frame URLs — required behind WAF rules that block requests containing `sid=`; the session continues via cookie
- Fixed a PHP 8 fatal on user profile pages: the credits loop in `viewpro.htm` now uses a quoted array key
- PHP 8 fix: quoted variable-variable access in the `register.php` settings bootstrap (bare `$$setting['key']` silently fails to assign under PHP 8)
- `transsid()` no longer duplicates quotes (`href="…""`); URL session-id passing is disabled (`transsidstatus=0`) because a WAF rule blocks `sid=` requests
- The registration rules-page countdown was rewritten in callback form so it works under the site CSP (which has no `unsafe-eval`)

## Features Currently Available

- Forum core: categories, forums, sub-forums, threads, replies, polls
- User core: registration, login, user groups, permissions, credits, profile, avatar
- Content: BBCode, attachments, smilies, moderation tooling
- Communication: PM, announcements, mail notifications
- Admin control panel: `admincp.php` (forums, users, groups, templates, plugins, DB, logs)
- Plugin mechanism: hook-based `plugins/`
- Multi-interface: `wap/`, `archiver/`
- Language packs: Simplified Chinese, Traditional Chinese, English

## Features Disabled by Default

To reduce dependency and legacy integration risk, this fork disables:

- Passport / SiteEngine / ShopEx
- Alipay / Orders
- AvatarShow

Notes:
- Frontend entry points are removed or conditionally hidden
- Admin entry points are blocked
- Runtime switches are force-disabled in `include/common.inc.php`

## Anti-Spam Measures

Server-side only, no JavaScript required — fully compatible with legacy browsers (including IE4):

- Guest posting and replying are disabled (guest group permissions, enforced for threads and replies)
- Registration requires answering a community question (question text in `templates/default/register.htm`, validation in `register.php`)
- Registration form carries a honeypot field ("keep this row empty"); submissions that fill it are rejected
- Registration email domain blacklist via the `censoremail` setting; flood control (`regfloodctrl`) limits per-IP registration attempts
- Newbie posting span (`newbiespan`, hours) blocks posting shortly after registration
- Content filter via `cdb_words`: `{BANNED}` entries reject posts/private messages containing spam patterns (covers threads, replies and PM)
- CAPTCHA (`seccode.php`) outputs GIF (legacy-browser compatible), with BMP fallback when GD is unavailable

Note: after importing anti-spam settings via SQL or editing `cdb_words`, delete
`forumdata/cache/cache_settings.php` and `forumdata/cache/cache_censor.php` so the caches are rebuilt.

## Requirements

- Recommended: **PHP 8.4+**
- Database: MySQL 4.1+ or PostgreSQL
- Web server: Apache / Nginx

Note: Older versions (such as PHP 7.4) may still run, but primary validation in this fork is done against PHP 8.4.

## Installation

1. Import database schema
   ```bash
   mysql -u root -p your_database < install/discuz.sql
   ```

2. Configure `config.inc.php`
   ```php
   $dbhost = '127.0.0.1';
   $dbuser = 'your_user';
   $dbpw   = 'your_password';
   $dbname = 'your_database';
   $tablepre = 'cdb_';
   ```

3. Set writable permissions
   ```bash
   chmod -R 777 attachments/ forumdata/
   ```

4. Open `install/` in browser for initial setup

5. Open `admincp.php` for administration

## Project Layout

```text
├── index.php               # Homepage / forum list
├── forumdisplay.php        # Forum thread list
├── viewthread.php          # Thread view
├── post.php                # New thread / reply
├── register.php            # User registration
├── logging.php             # Login / logout
├── member.php              # Profile & settings
├── pm.php                  # Private messages
├── search.php              # Search
├── admincp.php             # Admin entry
├── include/                # bootstrap, core funcs, DB layer, cache, security
├── admin/                  # admin modules
├── templates/default/      # template source files (edit here)
├── forumdata/cache/        # generated runtime cache
├── forumdata/templates/    # compiled templates (generated)
├── api/                    # external API scripts
├── plugins/                # plugin directory
├── wap/                    # WAP interface
├── archiver/               # text archive
└── install/discuz.sql      # DB schema
```

## Maintenance Notes

- Each entry script defines `CURSCRIPT` then includes `include/common.inc.php`
- Keep access guard lines: `if(!defined('IN_DISCUZ')) exit('Access Denied');`
- Do not edit `forumdata/cache/` or `forumdata/templates/` directly (generated files)
- Edit templates in `templates/default/*.htm`
- If template updates do not show up, enable `$tplrefresh = 1` or clear `forumdata/templates/`
- Footer displays the real runtime PHP version and links to the matching official PHP release branch page

## PHP 8.4 Upgrade Summary

- `mysql_*` replaced with `mysqli_*`
- `preg_replace /e` replaced with `preg_replace_callback`
- Added many null/type guards for stricter PHP8 behavior
- Added `EXTR_SKIP` to risky `extract()` calls
- Fixed short-tag/string interpolation incompatibilities under PHP8

Full details: [`php84-upgrade-report.md`](php84-upgrade-report.md)

## License

MIT License. See [LICENSE](LICENSE).

Originally developed by Comsenz Inc.  
Maintained by [Wenyin Root](https://github.com/wenyinos/discuz-410)
