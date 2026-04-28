<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed"> 
<tr><td class="nav" width="90%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> <?=$navigation?> &raquo; 发新话题</td>
<td align="right" width="10%">&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>        
</tr></table><br>

<script language="JavaScript">
var postminchars = parseInt('<?=$minpostsize?>');
var postmaxchars = parseInt('<?=$maxpostsize?>');
var disablepostctrl = parseInt('<?=$disablepostctrl?>');
var typerequired = parseInt('<?=$forum['threadtypes']['required']?>');
function checklength(theform) {
if (postmaxchars != 0) { message = "系统限制: "+postminchars+" 到 "+postmaxchars+" 字节"; }
else { message = ""; }
alert("\n当前长度: "+theform.message.value.length+" 字节\n\n"+message);
}
function validate(theform) {
if (theform.typeid && theform.typeid.options[theform.typeid.selectedIndex].value == 0 && typerequired) {
alert("请选择主题对应的分类。");
return false;
} else if (theform.subject.value == "" || theform.message.value == "") {
alert("请完成标题和内容栏。");
return false;
} else if (theform.subject.value.length > 80) {
alert("您的标题超过 80 个字符的限制。");
return false;
}
if (!disablepostctrl && ((postminchars != 0 && theform.message.value.length < postminchars) || (postmaxchars != 0 && theform.message.value.length > postmaxchars))) {
alert("您的帖子长度不符合要求。\n\n当前长度: "+theform.message.value.length+" 字节\n系统限制: "+postminchars+" 到 "+postmaxchars+" 字节");
return false;
}			
theform.topicsubmit.disabled = true;
return true;
}
</script>
<? if(isset($previewpost)) { include template('post_preview'); } ?>
<form method="post" name="input" action="post.php?action=newthread&fid=<?=$fid?>&extra=<?=$extra?>&topicsubmit=yes" <?=$enctype?> onSubmit="return validate(this)">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<input type="hidden" name="isblog" value="<?=$isblog?>">

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr>
<td colspan="2" class="header"><a href="member.php?action=credits&view=forum_post&fid=<?=$fid?>" target="_blank"><img src="<?=IMGDIR?>/credits.gif" alt="查看积分策略说明" align="right" border="0"></a>发新话题</td>
</tr>
<? if($discuz_uid) { ?>
<tr><td class="altbg1">用户名:</td>
<td class="altbg2"><?=$discuz_userss?> <span class="smalltxt">[<a href="<?=$link_logout?>">退出登录</a>]</span></td>
</tr>
<? } if($allowsetreadperm) { ?>
<tr>
<td class="altbg1">所需阅读权限:</td>
<td class="altbg2"><input type="text" name="readperm" size="6" value="<?=$readperm?>"> <span class="smalltxt">(0 为不限制)</span></td>
</tr>
<? } if($maxprice) { ?>
<tr>
<td class="altbg1">售价(<?=$extcredits[$creditstrans]['title']?>):</td>
<td class="altbg2"><input type="text" name="price" size="6" value="<?=$price?>"> <span class="smalltxt"><?=$extcredits[$creditstrans]['unit']?> (最高 <?=$maxprice?> <?=$extcredits[$creditstrans]['unit']?>
<? if($maxincperthread) { ?>
，单一主题作者最高收入 <?=$maxincperthread?> <?=$extcredits[$creditstrans]['unit']?>
<? } if($maxchargespan) { ?>
，最高出售时限 <?=$maxchargespan?> 小时
<? } ?>
)</span>
您可以使用 <b>[free]</b>message<b>[/free]</b> 代码发表无需付费也能查看的免费信息
</td>
</tr>
<? } ?>
<tr>
<td class="altbg1" width="20%">标题:</td>
<td class="altbg2"><?=$typeselect?> <input type="text" name="subject" size="45" value="<?=$subject?>" tabindex="3"></td>
</tr>
<? if(empty($trade)) { ?>
<tr>
<td class="altbg1">图标:</td><td class="altbg2"><input type="radio" name="iconid" value="0" checked> 无 <?=$icons?></td>
</tr>
<? } include template('post_bbinsert'); if(isset($poll) && $allowpostpoll) { ?>
<input type="hidden" name="poll" value="yes">
<tr>
<td class="altbg1" valign="top">投票选项:<br>
<span class="smalltxt">每一行为一个选项<br>最大选项数: <?=$maxpolloptions?><br><br>
<input type="checkbox" name="multiplepoll" value="1" 
<? if(isset($multiplepoll)) { ?>
checked
<? } ?>
> 多选投票
</span></td><td class="altbg2">
<textarea rows="4" name="polloptions" style="width: 80%; word-break: break-all" tabindex="5"><?=$polloptions?></textarea></td>
</tr>
<? } elseif(isset($trade) && $allowposttrade) { ?>
<input type="hidden" name="trade" value="yes">
<tr>
<td class="altbg1">我的支付宝账户:</td>
<td class="altbg2"><input type="text" name="seller" size="30" value="<?=$seller?>"></td>
</tr>
<tr>
<td class="altbg1">商品名称:</td>
<td class="altbg2"><input type="text" name="item_name" size="30" value="<?=$item_name?>"></td>
</tr>
<tr>
<td class="altbg1">商品价格:</td>
<td class="altbg2"><input type="text" name="item_price" size="30" value="<?=$item_price?>"></td>
</tr>
<tr>
<td class="altbg1">商品成色:</td>
<td class="altbg2"><input type="text" name="item_quality" size="30" value="<?=$item_quality?>"> 可选</td>
</tr>
<tr>
<td class="altbg1">商品所在地:</td>
<td class="altbg2"><input type="text" name="item_locus" size="30" value="<?=$item_locus?>"> 可选</td>
</tr>
<tr>
<td class="altbg1" valign="top">物流方式:</td>
<td class="altbg2">
<input type="radio" name="transport" value="virtual" <?=$checktp['virtual']?> onclick="this.form.postage_mail.disabled=true; this.form.postage_express.disabled=true"> 虚拟物品或无需邮递<br>
<input type="radio" name="transport" value="seller" <?=$checktp['seller']?> onclick="this.form.postage_mail.disabled=true; this.form.postage_express.disabled=true"> 卖家承担邮费<br>
<input type="radio" name="transport" value="buyer" <?=$checktp['buyer']?> onclick="this.form.postage_mail.disabled=false; this.form.postage_express.disabled=false"> 买家承担邮费<br>
通过邮费承担方选择注明该交易是由买卖哪方承担运费。<br>如果是买家承担运费，请选择可以提供的物流方式以及相应费用。<br>
平邮 <input type="text" name="postage_mail" size="3" value="<?=$postage_mail?>" <?=$postagedisabled?>> 元 (不填视作不提供平邮)<br>
快递 <input type="text" name="postage_express" size="3" value="<?=$postage_express?>" <?=$postagedisabled?>> 元 (不填视作不提供快递)<br>
</td>
</tr>
<? } ?>
<tr>
<td class="altbg1" valign="top">
<? include template('post_sminsert'); ?>
</td>

<td align="left" class="altbg2"><span class="smalltxt">
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
<input type="checkbox" name="emailnotify" value="1" <?=$notifycheck?>> 接收新回复邮件通知
<? if($forum['ismoderator'] && ($allowdirectpost || !$forum['modnewposts'])) { ?>
<br><input type="checkbox" name="sticktopic" value="1" <?=$stickcheck?>> 主题置顶
<br><input type="checkbox" name="addtodigest" value="1" <?=$digestcheck?>> 精华帖子
<? } if($allowuseblog && $forum['allowblog']) { ?>
<br><input type="checkbox" name="addtoblog" value="1" <?=$blogcheck?>> 加入 Blog
<? } ?>
</span>
</td></tr></table>
<? if($allowpostattach && !$seccodecheck) { include template('post_attachments'); } ?>
<br><center><input type="submit" name="topicsubmit" value="发新话题" tabindex="5">
<input type="submit" name="previewpost" value="预览帖子" tabindex="6"></center>
</form>
<? include template('footer'); ?>
