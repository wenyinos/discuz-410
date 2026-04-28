<? if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>

<form method="post" name="input" action="pm.php?action=send&pmsubmit=yes" onSubmit="javascript: this.pmsubmit.disabled=true">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="99%" class="tableborder">
<tr>
<td width="100%" colspan="2" class="header"><a href="member.php?action=credits&view=pm" target="_blank"><img src="<?=IMGDIR?>/credits.gif" alt="查看积分策略说明" align="right" border="0"></a>发送短消息</td>
</tr>
<? if($seccodecheck) { ?>
<tr>
<td class="altbg1" width="18%">验证码:</td>
<td class="altbg2"><input type="text" name="seccodeverify" size="4" maxlength="4"> <img src="seccode.php" align="absmiddle"> <span class="smalltxt">请在空白处输入图片中的数字</span></td>
</tr>
<? } ?>
<tr><td class="altbg1" width="18%">到:</td>
<td class="altbg2" width="82%"><input type="text" name="msgto" size="65" value="<?=$touser?>"></td></tr>
<? if($buddylist) { ?>
<tr><td class="altbg1" valign="top" id="buddy">好友群发:<br>
<span class="smalltxt">全选</span><input type="checkbox" name="chkall" onclick="checkall(this.form, 'msgtobuddys')"></td>
<td class="altbg2">
<table border="0" cellspacing="0" cellpadding="0" width="100%"><tr><td>
<? if(is_array($buddylist)) { foreach($buddylist as $key => $buddy) { if($key % 3 == 0) { ?>
</td></tr><tr><td width="33%" nowrap>
<? } else { ?>
</td><td width="33%" nowrap>
<? } ?>
<input type="checkbox" name="msgtobuddys[]" value="<?=$buddy['buddyid']?>"> <?=$buddy['buddyname']?>
<? } } ?>
</table>
</td></tr>
<? } ?>
<tr>
<td class="altbg1">标题:</td>
<td class="altbg2"><input type="text" name="subject" size="65" value="<?=$subject?>"></td>
</tr>

<tr>
<td valign="top" class="altbg1">内容:</td>
<td class="altbg2"><textarea rows="8" name="message" style="width: 85%; word-break: break-all" onKeyDown="ctlent(event);">
<? if($do == 'reply') { ?>
[b]原始短消息:[/b] [url=<?=$boardurl?>pm.php?action=view&folder=inbox&pmid=<?=$pm['pmid']?>]<?=$pm['subject']?>[/url]<?="\n"?>
<? } elseif($do == 'forward') { ?>
[b]原始短消息[/b] [url=<?=$boardurl?>pm.php?action=send&pmid=<?=$pm['pmid']?>&do=reply](回复)[/url]
[b]来自:[/b] [url=<?=$boardurl?>viewpro.php?uid=<?=$pm['msgfromid']?>]<?=$pm['msgfrom']?>[/url]
[b]到:[/b] [url=<?=$boardurl?>viewpro.php?uid=<?=$discuz_uid?>]<?=$discuz_user?>[/url]
[b]时间:[/b] <?=$pm['dateline']?><?="\n"?><?="\n"?>
<? } ?>
<?=$message?>
</textarea><br><span class="smalltxt"><input type="checkbox" name="saveoutbox" value="1">保存到发件箱中 &nbsp; [完成后可按 Ctrl+Enter 发布]</span></td>
</tr>

</table><br>
<center><input type="submit" name="pmsubmit" value="提 &nbsp; 交">
</center></form>