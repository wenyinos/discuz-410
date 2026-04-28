<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed"> 
<tr><td class="nav" width="90%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> &raquo; <a href="faq.php">帮助</a> &raquo; 用户须知</td>
<td align="right" width="10%">&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>        
</tr></table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td width="100%" class="header">用户须知</td></tr>
<tr><td width="100%" class="altbg2" class="smalltxt"><ul>
<li><a href="faq.php?page=usermaint#1">我必须要注册吗？</a></li>
<li><a href="faq.php?page=usermaint#2">Discuz! 论坛使用 Cookies 吗？</a></li>
<li><a href="faq.php?page=usermaint#3">如何使用签名？</a></li>
<li><a href="faq.php?page=usermaint#4">如何使用个性化的头像？</a></li>
<li><a href="faq.php?page=usermaint#5">如果我遗忘了密码，我该怎么办？</a></li>
<li><a href="faq.php?page=usermaint#6">什么是“短消息”？</a></li>
</td></tr></table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td width="100%" class="header">我必须要注册吗？</td><a name=#1></a></tr>
<tr><td width="100%" class="altbg2" class="smalltxt">&nbsp; &nbsp; 这取决于管理员如何设置 Discuz! 论坛的用户组权限选项，您甚至有可能必须在注册成正式用户后后才能浏览帖子。当然，在通常情况下，您至少应该是正式用户才能发新帖和回复已有帖子。请 <a href="register.php">点击这里</a> 免费注册成为我们的新用户！<br><br>&nbsp; &nbsp; 强烈建议您注册，这样会得到很多以游客身份无法实现的功能。</td></tr>
</table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td width="100%" class="header">Discuz! 论坛使用 Cookies 吗？</td><a name=#2></a></tr>
<tr><td width="100%" class="altbg2" class="smalltxt">&nbsp; &nbsp; Discuz! 采用 Session+Cookie 的双重方式保存用户信息，以确保在各种环境，包括 Cookie 完全无法使用的情况下您都能正常使用论坛各项功能。但 Cookies 的使用仍然可以为您带来一系列的方便和好处，因此我们强烈建议您在正常情况下不要禁止 Cookie 的应用，Discuz! 的安全设计将全力保证您的资料安全。<br><br>&nbsp; &nbsp; 在登录页面中，您可以选择 Cookie 记录时间，在该时间范围内您打开浏览器访问论坛将始终保持您上一次访问时的登录状态，而不必每次都输入密码。但出于安全考虑，如果您在公共计算机访问论坛，建议选择“浏览器进程”(默认)，或在离开公共计算机前选择“退出”(<a href="logging.php?action=logout">点击这里</a> 退出论坛)以杜绝资料被非法使用的可能。</td></tr>
</table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td width="100%" class="header">如何使用签名？</td><a name=#3></a></tr>
<tr><td width="100%" class="altbg2" class="smalltxt">&nbsp; &nbsp; 签名是加在您发表的帖子下面的小段文字，注册之后，您就可以设置自己的个性签名了。<br><br>&nbsp; &nbsp; <a href="memcp.php">点击这里</a> 进入控制面板，在签名框中输入签名文字，并确定不要超过管理员设置的相关限制(如字数、贴图等)，这样系统会自动选中您登录后发帖页面的显示签名选项，您的的签名将在帖子中自动被显示。</td></tr>
</table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td width="100%" class="header">如何使用个性化的头像？</td><a name=#4></a></tr>
<tr><td width="100%" class="altbg2" class="smalltxt">&nbsp; &nbsp; 同样在 <a href="memcp.php">控制面板</a> 中，有一处“头像”选项。头像是显示在您用户名下面的小图像，使用头像可能需要一定的权限，否则将不会显示出来。详情请查询<a href="faq.php?page=misc#3">本论坛的级别设定</a>，一般头像图像宽度应控制在 150 像素以下，以免影响界面美观。</td></tr>
</table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td width="100%" class="header">如果我遗忘了密码，我该怎么办？</td><a name=#5></a></tr>
<tr><td width="100%" class="altbg2" class="smalltxt">&nbsp; &nbsp; Discuz! 提供发送取回密码链接到 Email 的服务，点击登录页面中的 <a href="member.php?action=lostpasswd">取回密码</a> 功能，可以为您把取回密码的方法发送到注册时填写的 Email 信箱中。如果您的 Email 已失效或无法收到信件，请与论坛管理员联系。</td></tr>
</table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td width="100%" class="header">什么是“短消息”？</td><a name=#6></a></tr>
<tr><td width="100%" class="altbg2" class="smalltxt">&nbsp; &nbsp; “短消息”是论坛注册用户间交流的工具，信息只有发件和收件人可以看到，收到信息后系统会出现铃声和相应提示，您可以通过短消息功能与同一论坛上的其他用户保持私人联系。<a href="pm.php" target="_blank">收件箱</a> 或 <a href="memcp.php">控制面板</a> 中提供了短消息的收发服务。</td></tr>
</table>
<? include template('footer'); ?>
