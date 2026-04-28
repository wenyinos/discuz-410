<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed"> 
<tr><td class="nav" width="90%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> <?=$navigation?> &raquo; 编辑帖子</td>
<td align="right" width="10%">&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>        
</tr></table><br>

<script language="JavaScript">
var postminchars = <?=$minpostsize?>;
var postmaxchars = <?=$maxpostsize?>;
var disablepostctrl = <?=$disablepostctrl?>;
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
theform.editsubmit.disabled = true;
return true;
}
</script>
<? if(isset($previewpost)) { include template('post_preview'); } ?>
<form method="post" name="input" action="post.php?action=edit&extra=<?=$extra?>&editsubmit=yes" <?=$enctype?> onSubmit="return validate(this)">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<input type="hidden" name="page" value="<?=$page?>">

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr>
<td colspan="2" class="header">编辑帖子</td>
</tr>
<? if($discuz_uid) { ?>
<tr><td class="altbg1">用户名:</td>
<td class="altbg2"><?=$discuz_userss?> <span class="smalltxt">[<a href="<?=$link_logout?>">退出登录</a>]</span></td>
</tr>
<? } if($allowsetreadperm && $isfirstpost) { ?>
<tr>
<td class="altbg1">所需阅读权限:</td>
<td class="altbg2"><input type="text" name="readperm" size="6" value="<?=$thread['readperm']?>"> <span class="smalltxt">(0 为不限制)</span></td>
</tr>
<? } if($maxprice) { ?>
<tr>
<td class="altbg1">售价(<?=$extcredits[$creditstrans]['title']?>):</td>
<td class="altbg2">
<? if($thread['price'] == -1 || $thread['freecharge']) { ?>
<input type="text" name="price" size="6" value="<?=$thread['pricedisplay']?>" disabled> <span class="smalltxt"><?=$extcredits[$creditstrans]['unit']?>&nbsp;
<? if($thread['price'] == -1) { ?>
(本主题被强制退款)
<? } else { ?>
(本主题自发表起已超过最长出售时限)
<? } ?>
</span>
<? } else { ?>
<input type="text" name="price" size="6" value="<?=$thread['pricedisplay']?>"> <span class="smalltxt"><?=$extcredits[$creditstrans]['unit']?> (最高 <?=$maxprice?> <?=$extcredits[$creditstrans]['unit']?>
<? if($maxincperthread) { ?>
，单一主题作者最高收入 <?=$maxincperthread?> <?=$extcredits[$creditstrans]['unit']?>
<? } if($maxchargespan) { ?>
，最高出售时限 <?=$maxchargespan?> 小时
<? } ?>
)</span>
您可以使用 <b>[free]</b>message<b>[/free]</b> 代码发表无需付费也能查看的免费信息
<? } ?>
</td></tr>
<? } ?>
<tr>
<td class="altbg1" width="20%">标题:</td>
<td class="altbg2">
<?=$typeselect?> 
<input type="text" name="subject" size="45" value="<?=$postinfo['subject']?>" tabindex="3">
<input type="hidden" name="origsubject" value="<?=$postinfo['subject']?>">
</td></tr>
<? if($isfirstpost) { ?>
<tr>
<td class="altbg1">图标:</td><td class="altbg2"><input type="radio" name="iconid" value="0" checked> 无 <?=$icons?></td>
</tr>
<? } include template('post_bbinsert'); if($polloptions) { ?>
<input type="hidden" name="poll" value="yes">
<tr>
<td class="altbg1" valign="top">投票选项:<br>
<span class="smalltxt">每一行为一个选项<br>最大选项数:<br><br>
<input type="checkbox" name="multiplepoll" value="1" 
<? if($polloptions['multiple']) { ?>
checked
<? } ?>
> 多选投票
</span></td><td class="altbg2">
<? if(is_array($polloptions['options'])) { foreach($polloptions['options'] as $key => $option) { ?>
<input type="text" name="polloptions[<?=$key?>]" value="<?=$option['0']?>" size="55"><br>
<? } } ?>
</td></tr>
<? } ?>
<tr>
<td class="altbg1" valign="top">
<? include template('post_sminsert'); ?>
</td>

<td class="altbg2"><span class="smalltxt">
<textarea rows="18" name="message" style="width: 85%; word-break: break-all" tabindex="4" onSelect="javascript: storeCaret(this);" onClick="javascript: storeCaret(this);" onKeyUp="javascript: storeCaret(this);" onKeyDown="ctlent(event);"><?=$postinfo['message']?></textarea>
<br><br>
<input type="checkbox" name="parseurloff" value="1" <?=$urloffcheck?>> 禁用 URL 识别<br>
<input type="checkbox" name="smileyoff" value="1" <?=$smileyoffcheck?>> 禁用 <a href="faq.php?page=messages#6" target="_blank">Smilies</a><br>
<input type="checkbox" name="bbcodeoff" value="1" <?=$codeoffcheck?>> 禁用 <a href="faq.php?page=misc#1" target="_blank">Discuz! 代码</a><br>
<? if($orig['allowhtml']) { ?>
<input type="checkbox" name="htmlon" value="1" <?=$htmloncheck?>> 启用 Html 代码<br>
<? } if($allowanonymous || (!$allowanonymous && $orig['anonymous'])) { ?>
<input type="checkbox" name="isanonymous" value="1" 
<? if($orig['anonymous']) { ?>
checked
<? } ?>
> 使用匿名发帖<br>
<? } ?>
<input type="checkbox" name="usesig" value="1" <?=$usesigcheck?>> 使用个人签名<br>
<? if($isorigauthor) { ?>
<input type="checkbox" name="delete" value="1"> <b>!删除本帖</b>
<? } ?>
</span>
</td></tr>

</table>
<? if($postinfo['attachment']) { ?>
<br>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td colspan="8" class="header">附件</td></tr>
<tr align="center" class="category"><td>删?</td><td>附件</td><td>aid</td><td>时间</td><td>文件尺寸</td><td>下载次数</td><td>阅读权限</td><td>描述</td></tr>
<? if(is_array($attachments)) { foreach($attachments as $attach) { ?>
<tr align="center">
<td class="altbg1"><input type="checkbox" name="deleteaid[]" value="<?=$attach['aid']?>"></td>
<td class="altbg2"><?=$attach['filetype']?> <a href="attachment.php?aid=<?=$attach['aid']?>" target="_blank"><?=$attach['filename']?></a></td>
<td class="altbg1"><a href="###" onclick="AddText('[attach]'+<?=$attach['aid']?>+'[/attach]')" title="点击这里将本附件插入帖子内容中当前光标的位置"><?=$attach['aid']?></a></td>
<td class="altbg2"><?=$attach['dateline']?></td>
<td class="altbg1"><?=$attach['filesize']?></td>
<td class="altbg2"><?=$attach['downloads']?></td>
<td class="altbg1">
<? if($allowsetattachperm) { ?>
<input type="text" size="3" name="attachpermnew[<?=$attach['aid']?>]" value="<?=$attach['readperm']?>">
<? } else { ?>
<input type="text" size="3" value="<?=$attach['readperm']?>" disabled>
<? } ?>
</td>
<td class="altbg2"><input type="text" size="25" name="attachdescnew[<?=$attach['aid']?>]" value="<?=$attach['description']?>"></td>
</tr>
<? } } ?>
<tr><td class="singleborder" colspan="8">&nbsp;</td></tr>
<tr><td class="altbg1" colspan="8"><span class="bold">技巧提示:</span> 您可以在帖子中使用 [attach]aid[/attach] 标签将附件显示于帖子内容的指定位置，而不按照默认方式排列在内容的尾部。点击 aid 数字可以在当前光标位置自动插入此代码。</td></tr>
</table>
<? } if($allowpostattach) { include template('post_attachments'); } ?>
<input type="hidden" name="fid" value="<?=$fid?>">
<input type="hidden" name="tid" value="<?=$tid?>">
<input type="hidden" name="pid" value="<?=$pid?>">
<input type="hidden" name="postsubject" value="<?=$postinfo['subject']?>">
<br><center><input type="submit" name="editsubmit" value="编辑帖子" tabindex="5">
<input type="submit" name="previewpost" value="预览帖子" tabindex="6">
</center>
</form>
<? include template('footer'); ?>
