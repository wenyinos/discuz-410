<?php

$lang = array
(

	'username' => 'Admin Username:',
	'password' => 'Admin Password:',
	'repeat_password' => 'Repeat Password:',
	'admin_email' => 'Admin Email:',

	'succeed' => 'Succeeded',
	'fail' => 'Failed',
	'exit' => 'Abort Installation',
	'enabled' => 'Enabled',
	'writeable' => 'Writeable',
	'unwriteable' => 'Unwriteable',
	'unlimited' => 'Unlimited',

	'env_os' => 'Operating System',
	'env_php' => 'PHP Version',
	'env_mysql' => 'MySQL Version',
	'env_attach' => 'File Uploading',
	'env_diskspace' => 'Disk Space',
	'env_dir_writeable' => '(Writeable)',

	'init_log' => 'Initializing log',
	'clear_dir' => 'Clearing directory',
	'select_db' => 'Selecting database',
	'create_table' => 'Creating table',
	
	'install_wizard' => 'Discuz! Board Installation Wizard',
	'welcome' => 'Welcome to Crossday Discuz! Board Installation Wizard, please read carefully and make sure you accept all terms of End User License Agreement ("license.txt") before you start.', 
	'current_process' => 'Current Step:',
	'show_license' => 'Discuz! End User License Agreement',
	'agreement' => 'Please read all terms carefully',
	'agreement_yes' => 'I ACCEPT',
	'agreement_no' => 'I DO NOT ACCEPT',
	'configure' => 'Configuring config.inc.php',
	'check_config' => 'Checking Files',
	'check_existence' => 'Checking existence',
	'check_writeable' => 'Checking writing',
	'edit_config' => 'View/Edit Current Configure',
	'variable' => 'Variable',
	'value' => 'Current Value',
	'comment' => 'Comment',
	'dbhost' => 'Database Server:',
	'dbhost_comment' => 'Address of database server, "localhost" as default',
	'dbuser' => 'Database Username:',
	'dbuser_comment' => 'Account for database server',
	'dbpw' => 'Database Password:',
	'dbpw_comment' => 'Password for database server',
	'dbname' => 'Database Name:',
	'dbname_comment' => 'Name of database you want to use',
	'email' => 'Admin Email:',
	'email_comment' => 'Email for sending error report',
	'tablepre' => 'Table Prefix:',
	'tablepre_comment' => 'Modifying this for 2 or more Discuz! in single database',
	'tablepre_prompt' => 'We strongly recommend you not to change the default table prefix,\nunless you are installing more than one Discuz! in single database',
	'save_config' => 'Save Config',
	'confirm_config' => 'Accept Current Configure',
	'refresh_config' => 'Refresh Configure',
	'recheck_config' => 'Re-check Configure',
	'check_env' => 'Checking Environment',
	'compare_env' => 'Comparison of Environment',
	'env_required' => 'Required',
	'env_best' => 'Best Upon',
	'env_current' => 'Current',
	'confirm_preparation' => 'Please make sure you have finished the following steps',
	'install_note' => 'Notes of Installation',
	'add_admin' => 'Add Administrator',
	'start_install' => 'Start Installation',
	'installing' => 'Checking Administrator Account & Starting Installation',
	'check_admin' => 'Checking Account',
	'check_admin_validity' => 'Check administrator validity',
	'admin_username_invalid' => 'Illegal username(is null, out of range or illegal chars included).',
	'admin_password_invalid' => 'Two passwords do not match.',
	'admin_email_invalid' => 'Invalid email address.',
	'admin_invalid' => 'You do not complete required fields.',
	'fail_reason' => 'Failed. Reason:',
	'go_back' => 'Go back to previous page',
	'init_file' => 'Initializing Directories and Files',

	'config_nonexistence' => 'Configure file(config.inc.php) does not exist, please upload it via FTP and retry.',
	'config_comment' => 'Please fill in with database parameters. In most occasions, you don\'t need to modify the "red items".',
	'config_unwriteable' => 'Unable to write configure file, please check validity of current settings. If modifies required, edit it locally and upload to your server.',

	'php_version_406' => 'Your PHP version is older than 4.0.6, installation aborted.',
	'attach_enabled' => 'Allow/Max Size ',
	'attach_enabled_info' => 'Max size allowed for attachments: ',
	'attach_disabled' => 'Disallow Attachments',
	'attach_disabled_info' => 'Attachments was disallowed by server configuration.',
	'mysql_version_323' => 'MySQL 3.23 or later is required, installation aborted.',
	'unwriteable_template' => 'Mode of templates directory(./templates) has not been set to 777 or unable to write in, online templates editing and importing was disabled.',
	'unwriteable_attach' => 'Mode of attachments directory(./attachments as default) has not been set to 777 or unable to write in, attachments uploading was disabled.',
	'unwriteable_avatar' => 'Mode of custom avatars directory(./customavatars) has not been set to 777 or unable to write in, avatars uploading was disabled.',
	'unwriteable_forumdata' => 'Mode of data directory(./forumdata) has not been set to 777 or unable to write in, server side data dumping and board logs were disabled.',
	'unwriteable_forumdata_template' => 'Mode of templates cache directory(./forumdata/templates) has not been set to 777 or unable to write in, installation aborted.',
	'unwriteable_forumdata_cache' => 'Mode of cache directory(./forumdata/cache) has not been set to 777 or unable to write in, installation aborted.',
	'unwriteable_forumdata_accesslog' => 'Mode access logs directory(./forumdata/accesslog) has not been set to 777 or unable to write in, DoS evasive was disabled.',
	'tablepre_invalid' => 'Your specified table prefix has dot(".") included, please return to modify it\'s value and try again.',
	'db_invalid' => 'Specified database doesn\'t exist and can not be created automatically, installation aborted.',
	'db_auto_created' => 'Specified database didn\'t exist but was created automatically.',
	'db_not_null' => 'You have already installed Discuz!, continuing will clear all data of board in specified database.',
	'db_drop_table_confirm' => 'Continue will clear all data of original board, are you sure to proceed?',

	'install_abort' => 'Invalid settings or server environment, installation aborted, please read documents about this.',
	'install_process' => 'Finished checking environment, please continue to finish installation.',
	'install_succeed' => 'Congratulations, Discuz! installed successfully!',
	'goto_forum' => 'Click here to visit your board',

	'init_credits_karma' => 'Karma',
	'init_credits_money' => 'Money',

	'init_group_0' => 'Member',
	'init_group_1' => 'Administrator',
	'init_group_2' => 'Super Moderator',
	'init_group_3' => 'Moderator',
	'init_group_4' => 'Banned to Post',
	'init_group_5' => 'Banned',
	'init_group_6' => 'IP Banned',
	'init_group_7' => 'Guest',
	'init_group_8' => 'Inactive Member',
	'init_group_9' => 'Beggar',
	'init_group_10' => 'Newbie',
	'init_group_11' => 'Member',
	'init_group_12' => 'Conqueror',
	'init_group_13' => 'Lord',
	'init_group_14' => 'King',
	'init_group_15' => 'Forum Legend',

	'init_rank_1' => 'Beginner',
	'init_rank_2' => 'Poster',
	'init_rank_3' => 'Cool Poster',
	'init_rank_4' => 'Writer',
	'init_rank_5' => 'Excellent Writer',

	'init_cron_1' => 'todayposts_daily',
	'init_cron_2' => 'onlinetime_monthly',
	'init_cron_3' => 'cleanup_daily',
	'init_cron_4' => 'birthdays_daily',
	'init_cron_5' => 'notify_daily',
	'init_cron_6' => 'announcements_daily',
	'init_cron_7' => 'threadexpiries_daily',
	'init_cron_8' => 'promotions_hourly',

	'init_default_style' => 'Default Style',
	'init_default_forum' => 'Default Forum',
	'init_default_style' => 'Default Style',
	'init_default_template' => 'Default Template',
	'init_default_template_copyright' => 'Designed by Discuz! Skins Team',
	
	'init_dataformat' => 'j-n-Y',
	'init_modreasons' => 'SPAM\r\nFlooding\r\nIllegal\r\nIrrelevant\r\nRepetition\r\nFavorable\r\nExcellent\r\nOriginal',
	'init_link' => 'Discuz! Board',
	'init_link_note' => 'Discuz! Board official website, provide latest product news, downloading and technical supports, etc.',

	'license' => '<p>Copyright (c) 2001-2004, Comsenz Technology Ltd<br>
All Rights Reserved.

<p><b>IMPORTANT: THIS SOFTWARE END USER LICENSE AGREEMENT("EULA") IS A LEGAL AGREEMENT BETWEEN YOU AND COMSENZ TECHNOLOGY LTD.
READ IT CAREFULLY BEFORE COMPLETING THE INSTALLATION PROCESS AND USING THE SOFTWARE. IT PROVIDES A LICENSE TO USE THE SOFTWARE
AND CONTAINS WARRANTY INFORMATION AND LIABILITY DISCLAIMERS. BY INSTALLING AND USING THE SOFTWARE, YOU ARE CONFIRMING YOUR
ACCEPTANCE OF THE SOFTWARE AND AGREEING TO BECOME BOUND BY THE TERMS OF THIS AGREEMENT. IF YOU DO NOT AGREE TO BE BOUND BY
THESE TERMS, PLEASE DO NOT INSTALL OR USE THE SOFTWARE. YOU MUST ASSUME THE ENTIRE RISK OF USING THIS PROGRAM. ANY LIABILITY
OF COMSENZ TECHNOLOGY LTD WILL BE LIMITED EXCLUSIVELY TO PRODUCT REPLACEMENT OR REFUND OF PURCHASE PRICE BEFORE FIRST
INSTALLATION.</b>

<p><b>THIS EULA SHALL APPLY ONLY TO THE CROSSDAY DISCUZ! BOARD SOFTWARE VERSION 4.x.x HEREWITH REGARDLESS OF WHETHER OTHER
SOFTWARE IS REFERRED TO OR DESCRIBED HEREIN. COMSENZ TECHNOLOGY LTD RETAINS OWNERSHIP OF THE SOFTWARE AND ANY COPIES OF IT,
REGARDLESS OF THE FORM IN WHICH THE COPIES MAY EXIST.</b>

<ul type="1">
<p><li><b>Definitions</b>
<ul type="a">
<li>"Crossday Discuz! Board"("Discuz!" for short) is a bulletin board product which is developed by Comsenz Technology Ltd
independently and has been released as Commercial Software.
<li>"the Software" means "Crossday Discuz! Board".
<li>"Comsenz Technology Ltd" is the enterprise who responses to Discuz! product.
<li>"Commercial Software" means if any one who wants to get, install or use the Software, he/she must purchase a license.
</ul>

<p><li><b>License Grants</b>
<ul type="a">
<li>One license permits you installing and running two instances of Discuz! in single web server(single IP address) or under
single domain(single domain name) at most.
<li>You may get specified type and form of technical support from Comsenz Technology Ltd(rely on the the license you purchased)
in a specified supporting span since you pay.
<li>You are prior to supplying ideas and opinions to us, but have no guarantee of acceptance.
<li>You may modify the source code(if provied) or interface of the Software to fit your site under the License Restrictions.
<li>You own and response to all of your bulletin board data including members, posts, etc on your own.
</ul>

<p><li><b>License Restrictions</b>
<ul type="a">
<li>You may not install more than two instances of the Software on single web server or under single domain.
<li>Any form of disposing the Software via Internet or other media for any purpose(including for education and researching) was
forbiddened.
<li>You may not modify the Software to create derivative works based upon the Software, any form of redistribution were forbiddend.
<li>You may not rent, lease, sublicense, sell, assign, pledge, transfer the Software.
<li>You may not remove the copyright information or Discuz! links in the footer of board pages, no matter how heavily you modified
without the prior written consent of Comsenz Technology Ltd.
<li>In the event that you fail to comply with this EULA, your license will be terminated.
</ul>

<p><li><b>LIMITED WARRANTY AND DISCLAIMER</b>
<ul type="a">
<li>THE SOFTWARE AND THE ACCOMPANYING FILES ARE SOLD "AS IS" AND WITHOUT WARRANTIES AS TO PERFORMANCE OF MERCHANTABILITY OR ANY
OTHER WARRANTIED WHETHER EXPRESSED OR IMPLIED.
<li>Comsenz Technology is not liable for the content of any message posted on a forum powered by the Software.
<li>You must assume the entire risk of using the Software. ANY LIABILITY OF CROSSDAY STUDIO WILL BE LIMITED EXCLUSIVELY TO PRODUCT
REPLACEMENT, OR REFUND OF PURCHASE PRICE BEFORE YOUR FIRST INSTALLATION.
</ul>

<p><li><b>Official Websites</b>
<ul type="a">
<li>URL of Comsenz Technology Ltd is http://www.comsenz.com.
<li>URL of Discuz! Home is http://www.discuz.com
<li>URL of Discuz! Community is http://www.discuz.net.
</ul>
</ul>

<p>Comsenz Technology Ltd reserves the right to modify this EULA. Discuz! Home provides offical information on license and price,
Comsenz Technology Ltd may modify them without notice. Modified license and price list will apply to new licensed users.',
	'preparation' => '<li>Upload all files of ./Discuz! to the server.</li><li>Modify server-side config.inc.php to fit your environment, if you have trouble in filling database-related parameters, please contact your hosting.</li><li>If Discuz! is installed under non-WINNT OS, please change the attribute of the following directories:<br>&nbsp; &nbsp; <b>./templates</b> 777;&nbsp; &nbsp; <b>./attachments</b> 777;&nbsp; &nbsp; <b>./customavatars</b> 777;&nbsp; &nbsp; <b>./forumdata</b> 777;<br><b>&nbsp; &nbsp; ./forumdata/cache</b> 777;&nbsp; &nbsp; <b>./forumdata/templates</b> 777;&nbsp; &nbsp;<br></li><li>Making sure that web location /attachments is mapped to directory ./attachments on your server.</li>',

);

?>