<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed"> 
<tr><td class="nav" width="90%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> &raquo; <a href="faq.php">帮助</a> &raquo; 论坛使用</td>
<td align="right" width="10%">&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>        
</tr></table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td width="100%" class="header">论坛使用</td> </tr>
<tr><td width="100%" class="altbg2" class="smalltxt"><ul>
<li><a href="faq.php?page=using#1">在哪里可以登录？</a></li>
<li><a href="faq.php?page=using#2">在哪里可以退出？</a></li>
<li><a href="faq.php?page=using#3">我要搜索论坛，应该怎么做？</a></li>
<li><a href="faq.php?page=using#4">怎样给其他人发送“短消息”？</a></li>
<li><a href="faq.php?page=using#5">怎样看到全部的会员？</a></li>
</td></tr></table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td width="100%" class="header">在哪里可以登录？</td> <a name=#1></a></tr>
<tr><td width="100%" class="altbg2" class="smalltxt">&nbsp; &nbsp; 如果您尚未登录，点击左上角的“登录”，输入用户名和密码，确定即可。如果需要保持登录，请选择相应的 Cookie 时间，在此时间范围内您可以不必输入密码而保持上次的登录状态。</td></tr>
</table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td width="100%" class="header">在哪里可以退出？</td><a name=#2></a></tr>
<tr><td width="100%" class="altbg2" class="smalltxt">&nbsp; &nbsp; 如果您已经登录，点击左上角的“退出”，系统会清除 Cookie，退出登录状态。</td></tr>
</table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td width="100%" class="header">我要搜索论坛，应该怎么做？</td><a name=#3></a></tr>
<tr><td width="100%" class="altbg2" class="smalltxt">&nbsp; &nbsp; 点击上面的 <a href="search.php">搜索</a>，输入搜索的关键字并选择一个范围，就可以检索到您有权限访问论坛中的相关的帖子。</td></tr>
</table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td width="100%" class="header">怎样给其他人发送“短消息”？</td><a name=#4></a></tr>
<tr><td width="100%" class="altbg2" class="smalltxt">&nbsp; &nbsp; 如果您已登录，菜单上会显示出 <a href="pm.php" target="_blank">短消息</a>  项，点击后弹出短消息窗口，通过类似发送邮件一样的填写，点“发送”，消息就被发到对方收件箱中了。当他/她访问论坛的主要页面时，系统都会提示他/她收信息。</td></tr>
</table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td width="100%" class="header">怎样看到全部的会员？</td><a name=#5></a></tr>
<tr><td width="100%" class="altbg2" class="smalltxt">&nbsp; &nbsp; 当管理员设置可用此项功能时，您可以通过点击 <a href="member.php?action=list">会员</a> 查看所有的会员及其资料，并可实现会员资料的检索和排序输出。</td></tr>
</table>
<? include template('footer'); ?>
