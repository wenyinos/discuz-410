<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed"> 
<tr><td class="nav" width="90%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> &raquo; <a href="faq.php">帮助</a> &raquo; 读写帖子和收发短消息</td>
<td align="right" width="10%">&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>        
</tr></table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td width="100%" class="header">读写帖子和收发短消息</td></tr>
<tr><td width="100%" class="altbg2" class="smalltxt"><ul>
<li><a href="faq.php?page=messages#1">如何发布新帖子？</a></li>
<li><a href="faq.php?page=messages#2">如何回复帖子？</a></li>
<li><a href="faq.php?page=messages#3">我能够删除主题吗？</a></li>
<li><a href="faq.php?page=messages#4">怎样编辑自己发表的帖子？</a></li>
<li><a href="faq.php?page=messages#5">我可不可以上传附件？</a></li>
<li><a href="faq.php?page=messages#6">什么是 Smilies？</a></li>
<li><a href="faq.php?page=messages#7">该怎样发起一个投票？</a></li>
</td></tr></table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td width="100%" class="header">如何发布新帖子？</td><a name=#1></a></tr>
<tr><td width="100%" class="altbg2" class="smalltxt">&nbsp; &nbsp; 在论坛版块中，点“发新帖”即可进入功能齐全的发帖界面。当然您也可以使用版块下面的“快速发帖”发表新帖(如果此选项打开)。注意，一般论坛都设置为需要登录后才能发帖。</td></tr>
</table>

<br>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td width="100%" class="header">如何回复帖子？</td><a name=#2></a></tr>
<tr><td width="100%" class="altbg2" class="smalltxt">&nbsp; &nbsp; 在浏览帖子时，点“回复帖子”即可进入功能齐全的回复界面。当然您也可以使用版块下面的“快速回复”发表回复(如果此选项打开)。注意，一般论坛都设置为需要登录后才能回复。</td></tr>
</table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td width="100%" class="header">我能够删除主题吗？</td><a name=#3></a></tr>
<tr><td width="100%" class="altbg2" class="smalltxt">&nbsp; &nbsp; 浏览帖子时可以按下面的“编辑帖子”，对于您自己发表的帖子，可以很容易的编辑和删除。但当这帖是整个主题的起始帖时，则会删除该主题和全部回复。</td></tr>
</table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td width="100%" class="header">怎样编辑自己发表的帖子？</td><a name=#4></a></tr>
<tr><td width="100%" class="altbg2" class="smalltxt">&nbsp; &nbsp; 和上面一样，用“编辑帖子”就可以编辑自己发表的帖子。如果管理员通过论坛设置将这个功能屏蔽掉则不再可以进行此操作。</td></tr>
</table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td width="100%" class="header">我可不可以上传附件？</td> <a name=#5></a></tr>
<tr><td width="100%" class="altbg2" class="smalltxt">&nbsp; &nbsp; 可以。您可以在任何支持上传附件的版块中，通过发新帖、或者回复的方式上传附件（只要您的权限足够）。附件不能超过系统限定尺寸，且在可用类型的范围内才能上传。</td></tr>
</table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td width="100%" class="header">什么是 Smilies？</td><a name=#6></a></tr>
<tr><td width="100%" class="altbg2" class="smalltxt">&nbsp; &nbsp; Smilies 是一些用字符表示的表情符号，如果打开 Smilies 功能，Discuz! 会把一些符号转换成小图像，显示在帖子中，更加美观明了。目前支持下面这些 Smilies:<br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="30%" align="center" class="tableborder">
<tr><td width="25%" class="header" align="center">表情符号</td>
<td width="75%" class="header" align="center">对应图像</td>
</tr>
<? if(is_array($smilies)) { foreach($smilies as $smiley) { ?>
<tr>
<td width="25%" class="altbg2" align="center"><?=$smiley['code']?></td>
<td width="75%" class="altbg2" align="center"><img src="<?=SMDIR?>/<?=$smiley['url']?>"></td>
</tr>
<? } } ?>
</table></td></tr></table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td width="100%" class="header">该怎样发起一个投票？</td><a name=#7></a></tr>
<tr><td width="100%" class="altbg2" class="smalltxt">&nbsp; &nbsp; 您可以像发帖一样在版块中发起投票。每行输入一个可能的选项（最多10个），您可以通过阅读这个投票帖选出自己的答案，每人只能投票一次，之后将不能再对您的选择做出更改。<br><br>&nbsp; &nbsp; 管理员拥有随时关闭和修改投票选项的权力。</td></tr>
</table>
<? include template('footer'); ?>
