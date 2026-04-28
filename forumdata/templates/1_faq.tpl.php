<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed"> 
<tr><td class="nav" width="90%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> &raquo; 帮助</td>
<td align="right" width="10%">&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>        
</tr></table><br>
<? if(is_array($customfaq)) { ?>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td width="100%" class="header"><?=$customfaq['title']?></td></tr>
<tr><td width="100%" class="altbg2" class="smalltxt">
<ul>
<? if(is_array($customfaq['item'])) { foreach($customfaq['item'] as $id => $item) { ?>
<li><a href="faq.php?page=custom#<?=$id?>"><?=$item['subject']?></a></li>
<? } } ?>
</td></tr></table>
<br>
<? } ?>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<td width="100%" class="header">用户须知</td>
</tr>
<tr>
<td width="100%" class="altbg2" class="smalltxt">
<ul>
<li><a href="faq.php?page=usermaint#1">我必须要注册吗？</a></li>
<li><a href="faq.php?page=usermaint#2">Discuz! 论坛使用 Cookies 吗？</a></li>
<li><a href="faq.php?page=usermaint#3">如何使用签名？</a></li>
<li><a href="faq.php?page=usermaint#4">如何使用个性化的头像？</a></li>
<li><a href="faq.php?page=usermaint#5">如果我遗忘了密码，我该怎么办？</a></li>
<li><a href="faq.php?page=usermaint#6">什么是“短消息”？</a></li>
</td>
</tr></table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td width="100%" class="header">论坛使用</td></tr>
<tr>
<td width="100%" class="altbg2" class="smalltxt">
<ul>
<li><a href="faq.php?page=using#1">在哪里可以登录？</a></li>
<li><a href="faq.php?page=using#2">在哪里可以退出？</a></li>
<li><a href="faq.php?page=using#3">我要搜索论坛，应该怎么做？</a></li>
<li><a href="faq.php?page=using#4">怎样给其他人发送“短消息”？</a></li>
<li><a href="faq.php?page=using#5">怎样看到全部的会员？</a></li>
</td>              
</tr></table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr>
<td width="100%" class="header">读写帖子和收发短消息</td> 
</tr>
<tr>
<td width="100%" class="altbg2" class="smalltxt">
<ul>
<li><a href="faq.php?page=messages#1">如何发布新帖子？</a></li>
<li><a href="faq.php?page=messages#2">如何回复帖子？</a></li>
<li><a href="faq.php?page=messages#3">我能够删除主题吗？</a></li>
<li><a href="faq.php?page=messages#4">怎样编辑自己发表的帖子？</a></li>
<li><a href="faq.php?page=messages#5">我可不可以上传附件？</a></li>
<li><a href="faq.php?page=messages#6">什么是 Smilies？</a></li>
<li><a href="faq.php?page=messages#7">该怎样发起一个投票？</a></li>
</td>
</tr></table>

<br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<td width="100%" class="header">其他问题</td> 
</tr>
<tr>
<td width="100%" class="altbg2" class="smalltxt">
<ul>
<li><a href="faq.php?page=misc#1">能说明一下 Discuz! 代码的用法吗？</a></li>
<li><a href="faq.php?page=misc#2">普通用户如何成为版主？</a></li>
<li><a href="faq.php?page=misc#3">我如何在 <?=$bbname?> 具有更多的权限？</a></li>
</td>
</tr></table>
<? include template('footer'); ?>
