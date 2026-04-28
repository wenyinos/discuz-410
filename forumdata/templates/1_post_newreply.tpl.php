<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed"> 
<tr><td class="nav" width="90%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> <?=$navigation?> &raquo; 发表回复</td>
<td align="right" width="10%">&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>        
</tr></table><br>

<script language="JavaScript">
var postminchars = parseInt('<?=$minpostsize?>');
var postmaxchars = parseInt('<?=$maxpostsize?>');
var disablepostctrl = parseInt('<?=$disablepostctrl?>');
function checklength(theform) {
if (postmaxchars != 0) { message = "系统限制: "+postminchars+" 到 "+postmaxchars+" 字节"; }
else { message = ""; }
alert("\n当前长度: "+theform.message.value.length+" 字节\n\n"+message);
}
function validate(theform) {
if (theform.message.value == "" && theform.subject.value == "") {
alert("请完成标题或内容栏。");
return false;
} else if (theform.subject.value.length > 80) {
alert("您的标题超过 80 个字符的限制。");
return false;
}
if (!disablepostctrl && ((postminchars != 0 && theform.message.value.length < postminchars) || (postmaxchars != 0 && theform.message.value.length > postmaxchars))) {
alert("您的帖子长度不符合要求。\n\n当前长度: "+theform.message.value.length+" 字节\n系统限制: "+postminchars+" 到 "+postmaxchars+" 字节");
return false;
}			
theform.replysubmit.disabled = true;
return true;
}
</script>
<? if(isset($previewpost)) { include template('post_preview'); } ?>
<form method="post" name="input" action="post.php?action=reply&fid=<?=$fid?>&tid=<?=$tid?>&extra=<?=$extra?>&replysubmit=yes" <?=$enctype?> onSubmit="return validate(this)">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr>
<td colspan="2" class="header"><a href="member.php?action=credits&view=forum_reply&fid=<?=$fid?>" target="_blank"><img src="<?=IMGDIR?>/credits.gif" alt="查看积分策略说明" align="right" border="0"></a>发表回复</td>
</tr>
<? if($discuz_uid) { ?>
<tr><td class="altbg1">用户名:</td>
<td class="altbg2"><?=$discuz_userss?> <span class="smalltxt">[<a href="<?=$link_logout?>">退出登录</a>]</span></td>
</tr>
<? } ?>
<tr>
<td class="altbg1" width="20%">标题:</td>
<td class="altbg2"><input type="text" name="subject" size="45" value="<?=$subject?>" tabindex="3">&nbsp; <span class="smalltxt">(可选)</span></td>
</tr>
<? include template('post_bbinsert'); ?>
<tr>
<td class="altbg1" valign="top">
<? include template('post_sminsert'); ?>
</td>

<td class="altbg2"><span class="smalltxt">
<textarea rows="18" name="message" style="width: 80%; word-break: break-all" tabindex="4" onSelect="javascript: storeCaret(this);" onClick="javascript: storeCaret(this);" onKeyUp="javascript: storeCaret(this);" onKeyDown="ctlent(event);"><?=$message?></textarea>
<br><br>
<input type="checkbox" name="parseurloff" value="1" <?=$urloffcheck?>> 禁用 URL 识别<br>
<input type="checkbox" name="smileyoff" value="1" <?=$smileyoffcheck?>> 禁用 <a href="faq.php?page=messages#6" target="_blank">Smilies</a><br>
<input type="checkbox" name="bbcodeoff" value="1" <?=$codeoffcheck?>> 禁用 <a href="faq.php?page=misc#1" target="_blank">Discuz! 代码</a><br>
<? if($allowhtml) { ?>
<input type="checkbox" name="htmlon" value="1" <?=$htmloncheck?>> 启用 Html 代码<br>
<? } if($allowanonymous) { ?>
<input type="checkbox" name="isanonymous" value="1"> 使用匿名发帖<br>
<? } ?>
<input type="checkbox" name="usesig" value="1" <?=$usesigcheck?>> 使用个人签名<br>
<input type="checkbox" name="emailnotify" value="1" <?=$emailcheck?>> 接收新回复邮件通知</span>
</td></tr></table>
<? if($allowpostattach && !$seccodecheck) { include template('post_attachments'); } ?>
<br><center><input type="submit" name="replysubmit" value="发表回复" tabindex="5">
<input type="submit" name="previewpost" value="预览帖子" tabindex="6">
</center>
</form>

<br>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr class="header">
<td colspan="2">主题回顾</td>
</tr>
<? if($thread['replies'] > $ppp) { ?>
<tr class="altbg1"><td colspan="2" valign="top" width="20%">本主题回复较多，请 <a href="viewthread.php?fid=<?=$fid?>&tid=<?=$tid?>">点击这里</a> 查看。</td></tr>
<? } else { if(is_array($postlist)) { foreach($postlist as $post) { ?>
<tr class="<?=$post['thisbg']?>">
<td rowspan="2" valign="top" width="20%"><span class="bold">
<? if($post['author'] && !$post['anonymous']) { ?>
<?=$post['author']?>
<? } else { ?>
匿名
<? } ?>
</span><br><br></td><td class="smalltxt">
&nbsp;发表于 <?=$post['dateline']?></td></tr>
<tr class="<?=$post['thisbg']?>"><td>
<table height="100%" width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout: fixed; word-wrap: break-word">
<tr><td><p><?=$post['message']?></p><br></td></tr></table></td></tr>
<? } } } ?>
</table>
<? include template('footer'); ?>
