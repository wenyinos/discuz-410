# Discuz! 4.1.0

[中文文档](README_zh.md)

Open-source BBS/forum system originally developed by Comsenz Inc. (2001-2006). This is a community-maintained fork released under the MIT license.

## Features

- **Forum system**: Hierarchical categories, forums, sub-forums, threads, replies, polls
- **User system**: Registration, login, user groups, permissions, credits/reputation, profiles, avatars
- **Content**: BBCode editor, attachments (images/files with watermark support), smilies, thread types, moderation tools
- **Communication**: Private messaging (PM), buddy lists, announcements, email notifications
- **Administration**: Full admin control panel (`admincp.php`) for forums, users, groups, styles, templates, plugins, database, logs
- **Plugin system**: Hook-based plugin architecture with `plugins/` directory
- **Multi-language**: Simplified Chinese, Traditional Chinese (Big5/UTF-8), English language packs
- **Mobile**: WAP interface (`wap/`), text-only archiver (`archiver/`)
- **Integrations**: Passport SSO, Qihoo search, Alipay, Shopex, Avatar API
- **Cron**: Built-in scheduled tasks (cleanup, statistics, promotions, announcements)

## Requirements

- PHP 7.4+
- MySQL 4.1+ or PostgreSQL
- Web server (Apache/Nginx)

## Installation

1. Create a MySQL database and import the schema:
   ```bash
   mysql -u root -p your_database < install/discuz.sql
   ```

2. Copy `config.inc.php` and configure database credentials:
   ```php
   $dbhost = '127.0.0.1';
   $dbuser = 'your_user';
   $dbpw   = 'your_password';
   $dbname = 'your_database';
   $tablepre = 'cdb_';
   ```

3. Set directory permissions:
   ```bash
   chmod -R 777 attachments/ forumdata/
   ```

4. Access `install/` in your browser for initial setup, then delete the `install/` directory.

5. Access `admincp.php` for the admin panel.

## Project Structure

```
├── index.php               # Homepage / forum list
├── forumdisplay.php        # Forum thread listing
├── viewthread.php          # Thread view
├── post.php                # New thread / reply
├── register.php            # User registration
├── logging.php             # Login / logout
├── member.php              # User profile & settings
├── pm.php                  # Private messaging
├── search.php              # Search
├── admincp.php             # Admin control panel (entry point)
├── plugin.php              # Plugin dispatcher
├── misc.php                # Miscellaneous (rules, help, etc.)
├── blog.php                # User blog
├── stats.php               # Forum statistics
├── attachment.php          # Attachment download
├── config.inc.php          # Main configuration (DB, paths)
├── mail_config.inc.php     # Email configuration
├── include/
│   ├── common.inc.php      # Bootstrap (loaded by every page)
│   ├── global.func.php     # Core utility functions
│   ├── db_mysql.class.php  # Database abstraction (MySQLi)
│   ├── template.func.php   # Template compiler
│   ├── cache.func.php      # Cache generation
│   ├── forum.func.php      # Forum display functions
│   ├── post.func.php       # Post/attachment handling
│   ├── misc.func.php       # IP lookup, misc utilities
│   ├── security.inc.php    # Attack evasion
│   ├── attachment.func.php # Attachment type handling
│   ├── crons/              # Scheduled task scripts
│   └── tables/             # Charset conversion tables
├── admin/
│   ├── settings.inc.php    # Site settings
│   ├── forums.inc.php      # Forum management
│   ├── members.inc.php     # User management
│   ├── groups.inc.php      # User group management
│   ├── plugins.inc.php     # Plugin management
│   ├── templates.inc.php   # Template management
│   ├── database.inc.php    # Database backup/restore
│   └── ...                 # Other admin modules
├── templates/default/      # Template source files (.htm)
├── forumdata/
│   ├── cache/              # Generated PHP cache files
│   └── templates/          # Compiled template PHP files
├── api/                    # External API endpoints
├── plugins/                # Plugin scripts
├── wap/                    # Mobile WAP interface
├── archiver/               # Text-only archive interface
├── images/                 # Static images & CSS
├── ipdata/                 # IP geolocation database
├── install/                # Installation files & DB schema
└── customavatars/          # User-uploaded avatars
```

## PHP 7.4 Upgrade

This fork has been upgraded from PHP 4/5 to PHP 7.4 compatible:

- **Database**: `mysql_*` extension replaced with `mysqli_*` (`include/db_mysql.class.php`)
- **Template compiler**: `preg_replace /e` modifier replaced with `preg_replace_callback` (`include/template.func.php`)
- **Security**: `extract()` now uses `EXTR_SKIP` to prevent variable overwriting
- **Compatibility**: Removed `set_magic_quotes_runtime`, `get_magic_quotes_gpc`, `$HTTP_*_VARS`
- **Syntax**: All short open tags `<?` replaced with `<?php`

See [UPGRADE_PHP74.md](UPGRADE_PHP74.md) for the full upgrade report.

## Key Technical Notes

- **Bootstrap**: Every page defines `CURSCRIPT` then requires `include/common.inc.php`
- **Access guard**: All PHP files start with `if(!defined('IN_DISCUZ')) exit('Access Denied');` — never remove this
- **Cache system**: `forumdata/cache/` files are auto-generated; editing them directly has no lasting effect
- **Template system**: Edit `.htm` files in `templates/default/`, not the compiled `.tpl.php` files in `forumdata/templates/`
- **Database**: Uses `$tablepre` prefix (default `cdb_`), queries via `$db->query()` / `$db->fetch_array()`

## License

MIT License — see [LICENSE](LICENSE) for details.

Originally developed by Comsenz Inc. (2001-2006).
Maintained by [Wenyin Root](https://github.com/wenyinos/discuz-410).
