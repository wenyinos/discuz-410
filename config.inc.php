<?php

/*
	[DISCUZ!] config.inc.php - basically configuration of Discuz! Board
	This is NOT a freeware, use is subject to license terms
*/

// [EN]	Set below parameters according to your account information provided by your hosting
// [CH] 以下变量请根据空间商提供的账号参数修改,如有疑问,请联系服务器提供商

	$dbhost = '127.0.0.1';			// database server
						// 数据库服务器

	$dbuser = 'sql_bbs_wenyinos';			// database username
						// 数据库用户名

	$dbpw = '4NNPf8Hcn37pFam8';			// database password
						// 数据库密码

	$dbname = 'sql_bbs_wenyinos';			// database name
						// 数据库名

	$adminemail = 'admin@wenyinos.com';		// admin email
						// 论坛系统 Email

	$dbreport = 0;				// send db error report? 1=yes
						// 是否发送数据库错误报告? 0=否, 1=是


// [EN] If you have problems logging in Discuz!, then modify the following parameters, else please leave default
// [CH] 如您对 cookie 作用范围有特殊要求,或论坛登录不正常,请修改下面变量,否则请保持默认

	$cookiedomain = ''; 			// cookie domain
						// cookie 作用域

	$cookiepath = '/';			// cookie path
						// cookie 作用路径


// [EN] Special parameters, DO NOT modify these unless you are an expert in Discuz!
// [CH] 以下变量为特别选项,一般情况下没有必要修改

	$headercharset = 0;			// force outputing charset header
						// 强制设置字符集,只乱码时使用

	$errorreport = 1;			// reporting php error, 0=only report to admins(safer), 1=report to all
						// 是否报告 PHP 错误, 0=只报告给管理员和版主(更安全), 1=报告给任何人

	$forcesecques = 0;			// require security question for administrators' control panel, 0=off, 1=on
						// 管理人员必须设置安全提问才能进入系统设置, 0=否, 1=是

	$onlinehold = 900;			// time span of online recording
						// 在线保持时间

	$pconnect = 0;				// persistent database connection, 0=off, 1=on
						// 数据库持久连接 0=关闭, 1=打开


// [EN] !ATTENTION! Do NOT modify following after your board was settle down
// [CH] 论坛投入使用后不能修改的变量

	$tablepre = 'cdb_';   			// 表名前缀, 同一数据库安装多个论坛请修改此处
						// table prefix, modify this when you are installingmore than 1 Discuz! in the same database.

	$attachdir = './attachments';		// 附件保存位置 (服务器路径, 属性 777, 必须为 web 可访问到的目录, 不加 "/", 相对目录务必以 "./" 开头)
						// attachments saving dir. (chmod to 777, visual web dir only, ending without slash

	$attachurl = 'attachments';		// 附件路径 URL 地址 (可为当前 URL 下的相对地址或 http:// 开头的绝对地址, 不加 "/")


// [EN] !ATTENTION! Preservation or debugging for developing
// [CH] 切勿修改以下变量,仅供程序开发调试用!

	$database = 'mysql';			// 'mysql' for MySQL version and 'pgsql' for PostgreSQL version
						// MySQL 版本请设置 'mysql', PgSQL 版本请设置 'pgsql'

	$dbcharset = '';			// default database character set, 'gbk', 'big5', 'utf8', 'latin1' and blank are available
						// MySQL 字符集, 可选 'gbk', 'big5', 'utf8', 'latin1', 留空为按照论坛字符集设定

	$charset = 'utf-8';			// default character set, 'gbk', 'big5', 'utf-8' are available
						// 论坛默认字符集, 可选 'gbk', 'big5', 'utf-8'

	$attackevasive = 0;			// protect against attacks via common request, 0=off, 1=cookie refresh limitation, 2=deny proxy request, 3=both
						// 防护大量正常请求造成的拒绝服务攻击, 0=关闭, 1=cookie 刷新限制, 2=限制代理访问, 3=cookie+代理限制

	$tplrefresh = 1;			// auto check validation of templates, 0=off, 1=on
						// 模板自动刷新开关 0=关闭, 1=打开

// ============================================================================