<?php

$lang = array
(

	'username' => '管理员账号:',
	'password' => '管理员密码:',
	'repeat_password' => '重复密码:',
	'admin_email' => '管理员 Email:',

	'succeed' => '成功',
	'fail' => '失败',
	'exit' => '退出安装向导',
	'enabled' => '允许',
	'writeable' => '可写',
	'unwriteable' => '不可写',
	'unlimited' => '不限',

	'env_os' => '操作系统',
	'env_php' => 'PHP 版本',
	'env_mysql' => 'MySQL 版本',
	'env_attach' => '附件上传',
	'env_diskspace' => '磁盘空间',
	'env_dir_writeable' => '目录写入',

	'init_log' => '初始化记录',
	'clear_dir' => '清空目录',
	'select_db' => '选择数据库',
	'create_table' => '建立数据表',
	
	'install_wizard' => 'Discuz! Board Installation Wizard',
	'welcome' => '欢迎来到 Crossday Discuz! Board 安装向导，安装前请仔细阅读 license 档的每个细节，在您确定可以完全满足 Discuz! 的授权协议之后才能开始安装。readme 档提供了有关软件安装的说明，请您同样仔细阅读，以保证安装进程的顺利进行。',
	'current_process' => '当前状态:',
	'show_license' => 'Discuz! 用户许可协议',
	'agreement' => '请您务必仔细阅读下面的许可协议',
	'agreement_yes' => '我完全同意',
	'agreement_no' => '我不能同意',
	'configure' => '配置 config.inc.php',
	'check_config' => '检查配置文件状态',
	'check_existence' => '存在检查',
	'check_writeable' => '可写检查',
	'edit_config' => '浏览/编辑当前配置',
	'variable' => '设置选项',
	'value' => '当前值',
	'comment' => '注释',
	'dbhost' => '数据库服务器:',
	'dbhost_comment' => '数据库服务器地址, 一般为 localhost',
	'dbuser' => '数据库用户名:',
	'dbuser_comment' => '数据库账号用户名',
	'dbpw' => '数据库密码:',
	'dbpw_comment' => '数据库账号密码',
	'dbname' => '数据库名:',
	'dbname_comment' => '数据库名称',
	'email' => '系统 Email:',
	'email_comment' => '用于发送程序错误报告',
	'tablepre' => '表名前缀:',
	'tablepre_comment' => '同一数据库安装多论坛时可改变默认',
	'tablepre_prompt' => '除非您需要在同一数据库安装多个 Discuz! \n论坛,否则,强烈建议您不要修改表名前缀.',
	'save_config' => '保存配置信息',
	'confirm_config' => '上述配置正确',
	'refresh_config' => '刷新修改结果',
	'recheck_config' => '重新检查设置',
	'check_env' => '检查当前服务器环境',
	'compare_env' => 'Discuz! 所需环境和当前服务器配置对比',
	'env_required' => 'Discuz! 所需配置',
	'env_best' => 'Discuz! 最佳配置',
	'env_current' => '当前服务器',
	'confirm_preparation' => '请确认已完成如下步骤',
	'install_note' => '安装向导提示',
	'add_admin' => '设置管理员账号',
	'start_install' => '开始安装 Discuz!',
	'installing' => '检查管理员账号信息并开始安装 Discuz!。',
	'check_admin' => '检查管理员账号',
	'check_admin_validity' => '检查信息合法性',
	'admin_username_invalid' => '用户名空, 长度超过限制或包含非法字符.',
	'admin_password_invalid' => '两次输入密码不一致.',
	'admin_email_invalid' => 'Email 地址无效',
	'admin_invalid' => '您的信息没有填写完整.',
	'fail_reason' => '失败. 原因:',
	'go_back' => '返回上一页修改',
	'init_file' => '初始化运行目录与文件',

	'config_nonexistence' => '您的 config.inc.php 不存在, 无法继续安装, 请用 FTP 将该文件上传后再试.',
	'config_comment' => '请在下面填写您的数据库账号信息, 通常情况下不需要修改红色选项内容.',
	'config_unwriteable' => '安装向导无法写入配置文件, 请核对现有信息, 如需修改, 请通过 FTP 将改好的 config.inc.php 上传.',

	'php_version_406' => '您的 PHP 版本小于 4.0.6, 无法使用 Discuz!。',
	'attach_enabled' => '允许/最大尺寸 ',
	'attach_enabled_info' => '您可以上传附件的最大尺寸: ',
	'attach_disabled' => '不允许上传附件',
	'attach_disabled_info' => '附件上传或相关操作被服务器禁止。',
	'mysql_version_323' => '您的 MySQL 版本低于 3.23，安装无法继续进行。',
	'unwriteable_template' => '模板目录(./templates)属性非 777 或无法写入，在线编辑模板功能将无法使用。',
	'unwriteable_attach' => '附件目录(默认是 ./attachments)属性非 777 或无法写入，附件上传功能将无法使用。',
	'unwriteable_avatar' => '自定义头像目录(./customavatars)属性非 777 或无法写入，上传头像功能将无法使用。',
	'unwriteable_forumdata' => '数据目录(./forumdata)属性非 777 或无法写入，论坛运行记录和备份到数据库功能将无法使用。',
	'unwriteable_forumdata_template' => '编译模板目录(./forumdata/templates)属性非 777 或无法写入，安装无法继续进行。',
	'unwriteable_forumdata_cache' => '数据缓存目录(./forumdata/cache)属性非 777 或无法写入，安装无法继续进行。',
	'tablepre_invalid' => '您指定的数据表前缀包含点字符(".")，请返回修改。',
	'db_invalid' => '指定的数据库不存在, 系统也无法自动建立, 无法安装 Discuz!.',
	'db_auto_created' => '指定的数据库不存在, 但系统已成功建立, 可以继续安装.',
	'db_not_null' => '数据库中已经安装过 Discuz!, 继续安装会清空原有数据.',
	'db_drop_table_confirm' => '继续安装会清空全部原有数据，您确定要继续吗?',

	'install_abort' => '由于您目录属性或服务器配置原因, 无法继续安装 Discuz!, 请仔细阅读安装说明.',
	'install_process' => '您的服务器可以安装和使用 Discuz!, 请进入下一步安装.',
	'install_succeed' => '恭喜您，Discuz! 安装成功！',
	'goto_forum' => '点击这里进入论坛',

	'init_credits_karma' => '威望',
	'init_credits_money' => '金钱',

	'init_group_0' => '会员',
	'init_group_1' => '管理员',
	'init_group_2' => '超级版主',
	'init_group_3' => '版主',
	'init_group_4' => '禁止发言',
	'init_group_5' => '禁止访问',
	'init_group_6' => '禁止 IP',
	'init_group_7' => '游客',
	'init_group_8' => '等待验证会员',
	'init_group_9' => '乞丐',
	'init_group_10' => '新手上路',
	'init_group_11' => '注册会员',
	'init_group_12' => '中级会员',
	'init_group_13' => '高级会员',
	'init_group_14' => '金牌会员',
	'init_group_15' => '论坛元老',

	'init_rank_1' => '新生入学',
	'init_rank_2' => '小试牛刀',
	'init_rank_3' => '实习记者',
	'init_rank_4' => '自由撰稿人',
	'init_rank_5' => '特聘作家',

	'init_cron_1' => '清空今日发帖数',
	'init_cron_2' => '清空本月在线时间',
	'init_cron_3' => '每日数据清理家',
	'init_cron_4' => '生日统计与邮件祝福',
	'init_cron_5' => '主题回复通知',
	'init_cron_6' => '每日公告清理',
	'init_cron_7' => '限时操作清理',
	'init_cron_8' => '论坛推广清理',

	'init_default_style' => '默认风格',
	'init_default_forum' => '默认论坛',
	'init_default_template' => '默认模板套系',
	'init_default_template_copyright' => '北京康盛世纪科技有限公司',
	
	'init_dataformat' => 'Y-n-j',
	'init_modreasons' => '广告/SPAM\r\n恶意灌水\r\n违规内容\r\n文不对题\r\n重复发帖\r\n\r\n我很赞同\r\n精品文章\r\n原创内容',
	'init_link' => 'Discuz! 官方论坛',
	'init_link_note' => '提供最新 Discuz! 产品新闻、软件下载与技术交流',

	'license' => '<p>版权所有 (c) 2001-2004，北京康盛世纪科技有限公司<br>
保留所有权利。

<p>感谢您选择 Discuz! 论坛产品。希望我们的努力能为您提供一个高效快速和强大的 web 论坛解决方案。

<p>Discuz! 英文全称为 Crossday Discuz! Board，中文全称为 Discuz! 论坛，以下简称 Discuz!。

<p>北京康盛世纪科技有限公司（Comsenz Technology Ltd）为 Discuz! 产品的开发商，依法独立拥有 Discuz! 产品著作权（中国国家版权局
著作权登记号 2003SR6623）。北京康盛世纪科技有限公司网址为 http://www.comsenz.com，Discuz! 官方网站网址为 http://www.discuz.com，
Discuz! 官方讨论区网址为 http://www.discuz.net。

<p>本授权协议适用且仅适用于 Discuz! 4.x.x 版本，北京康盛世纪科技有限公司拥有对本授权协议的最终解释权。

<p>在开始安装 Discuz! 之前，请务必仔细阅读本授权文档，在确定同意并满足授权协议的全部条款后，即可继续 Discuz! 论坛的安装。

<p>Discuz! 著作权已在中华人民共和国国家版权局注册，著作权受到法律和国际公约保护。4.x.x 版本为商业软件，使用者：无论个人或组织、
盈利与否、用途如何（包括以学习和研究为目的）：查看、安装或使用 Discuz! 的整体或部分，都必须支付商业授权费用，获得正式授权后，
方可成为授权用户。

<ul type="I">
<p><li><b>协议许可的权利</b>
<ul type="1">
<li>在授权期限内拥有至多二个 Discuz! 的授权拷贝安装，前提是拷贝必须在同一 IP 的服务器上，或在同一主域名的两个主机下（如 
domain1.your.com，domain2.your.com)。
<li>依据所购买的授权类型中确定的免费升级期限、技术支持期限、技术支持方式和技术支持内容，自购买时刻起，用户可在免费升级期限内
获得并安装使用最新的 Discuz! 论坛软件包；在技术支持期限内通过指定的方式获得指定范围内的技术支持内容。授权用户享有反映和提出
意见的权力，相关意见将被作为首要考虑，但没有一定被采纳的承诺或保证。
<li>您可以在协议规定的约束和限制范围内修改 Discuz! 源代码(如果被提供的话)或界面以适应您的网站要求。
<li>授权用户拥有其论坛全部会员资料、文章及相关信息的所有权，并独立承担与文章内容的相关法律义务。
</ul>

<p><li><b>协议规定的约束和限制</b>
<ul type="1">
<li>不得将一份商业授权安装于不同 IP 的并且不在同一主域名下的服务器空间上，也不得在同一 IP 或同一域名下将一份授权安装为两个以上
的拷贝使用。
<li>禁止以任何目的，包括以学习或研究为目的通过 Internet 或其他媒介将所获授权的产品提供给第三人或公众。
<li>禁止任何形式的重新分发，更不得利用非法重新分发获利。
<li>不得对本软件进行出租、租借、发放子许可证、出售或抵押。
<li>无论如何，即无论用途如何、是否经过修改或美化、修改程度如何，只要使用 Discuz! 的整体或任何部分，未经书面许可，论坛页面页脚处
的 Discuz! 名称和北京康盛世纪科技有限公司下属网站（http://www.discuz.com 或 http://www.discuz.net） 的链接都必须保留，而不能清除
或修改。
<li>如果您未能遵守本协议的条款，您的授权将被终止，所被许可的权利将被收回。
</ul>

<p><li><b>有限担保和免责声明</b>
<ul type="1">
<li>Discuz! 及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式出售的。
<li>北京康盛世纪科技有限公司不对使用 Discuz! 构建的论坛中的文章或信息承担责任。
<li>您必须了解使用本软件的风险。十分必要时，北京康盛世纪科技有限公司所承担的责任仅限于产品版本更换，或在第一次成功安装 Discuz! 
前退还购买费用。
</ul>
</ul>

<p>有关 Discuz! 授权包含的服务范围，付费方式等，Discuz! 官方网站提供惟一的解释和官方价目表。北京康盛世纪科技有限公司拥有在不事先
通知的情况下，修改授权协议和价目表的权力，修改后的协议或价目表对自改变之日起的新授权用户生效。

<p>电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和等同的法律效力。您一旦开始安装 Discuz!，即被视为完全理解并接受
本协议的各项条款，在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本授权协议并构成侵权，
我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。',
	'preparation' => '<li>将压缩包中 Discuz! 目录下全部文件和目录上传到服务器.</li><li>修改服务器上的 config.inc.php 文件以适合您的配置, 有关数据库账号信息请咨询您的空间服务提供商.</li><li>如果您使用非 WINNT 系统请修改以下属性:<br>&nbsp; &nbsp; <b>./templates</b> 目录 777;&nbsp; &nbsp; <b>./attachments</b> 目录 777;&nbsp; &nbsp; <b>./customavatars</b> 目录 777;&nbsp; &nbsp; <b>./forumdata</b> 目录 777;<br><b>&nbsp; &nbsp; ./forumdata/cache</b> 目录 777;&nbsp; &nbsp; <b>./forumdata/templates</b> 目录 777;&nbsp; &nbsp; <br></li><li>确认 URL 中 /attachments 可以访问服务器目录 ./attachments 内容.</li>',

);

?>