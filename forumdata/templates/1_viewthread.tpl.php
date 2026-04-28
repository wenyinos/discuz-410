<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed">
<tr><td class="nav" width="85%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> <?=$navigation?></td>
<td align="right" width="15%"><a href="rss.php?fid=<?=$fid?>&auth=<?=$rssauth?>" target="_blank"><img src="images/common/xml.gif" border="0" align="absmiddle" alt="RSS 订阅当前论坛"></a>
&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>
</tr></table><br></div>
<? if(!empty($advlist['text'])) { ?>
<div class="maintable"><table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder"><?=$advlist['text']?></table><br></div>
<? } if($polloptions) { ?>
<div class="maintable">
<? include template('viewthread_poll'); ?>
</div>
<? } if($newpmexists) { ?>
<div class="maintable">
<? include template('pmprompt'); ?>
</div>
<? } ?>
<div class="maintable">
<table width="<?=TABLEWIDTH?>" cellspacing="1" cellpadding="0" align="center">
<tr><td valign="bottom">
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="2" class="tableborder">
<tr class="smalltxt">
<? if($highlightstatus) { ?>
<td class="altbg2"><a href="viewthread.php?tid=<?=$tid?>&page=<?=$page?>" style="font-weight: normal">取消高亮</a></td>
<? } ?>
<td class="altbg2"><a href="redirect.php?fid=<?=$fid?>&tid=<?=$tid?>&goto=nextoldset" style="font-weight: normal">上一主题</a></td>
<td class="altbg2"><a href="redirect.php?fid=<?=$fid?>&tid=<?=$tid?>&goto=nextnewset" style="font-weight: normal">下一主题</a></td>
</tr></table>
<?=$multipage?></td><td align="right" valign="bottom">
<? if($allowpost || !$discuz_uid) { ?>
&nbsp;<a href="post.php?action=newthread&fid=<?=$fid?>&extra=<?=$extra?>"><img src="<?=IMGDIR?>/newtopic.gif" border="0"></a>
<? } if($allowpostpoll || !$discuz_uid) { ?>
&nbsp;<a href="post.php?action=newthread&fid=<?=$fid?>&extra=<?=$extra?>&poll=yes"><img src="<?=IMGDIR?>/poll.gif" border="0"></a>
<? } if(($allowpost || !$discuz_uid) && $allowposttrade) { ?>
&nbsp;<a href="post.php?action=newthread&fid=<?=$fid?>&extra=<?=$extra?>&trade=yes"><img src="<?=IMGDIR?>/newtrade.gif" border="0"></a>
<? } if($allowpostreply || !$discuz_uid) { ?>
&nbsp;<a href="post.php?action=reply&fid=<?=$fid?>&tid=<?=$tid?>&extra=<?=$extra?>"><img src="<?=IMGDIR?>/reply.gif" border="0"></a>
<? } ?>
</td></tr></table></div>

<script language="JavaScript">
function fastreply(subject) {
if(document.input) {
document.input.subject.value = subject;
document.input.message.focus();
}
}
</script>

<div class="maintable">
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" style="border-bottom:none;" class="tableborder">
<tr class="header"><td colspan="2">
<table cellspacing="0" cellpadding="0" border="0" width="100%">
<tr style="color: <?=HEADERTEXT?>"><td class="bold" width="65%">标题: <?=$thread['subject']?></td>
<td width="35%" align="right" nowrap>&nbsp;
<? if($searchboxstatus) { ?>
<img src="<?=IMGDIR?>/search.gif" border="0" align="bottom" width="16" height="16">&nbsp;&nbsp;<input type="text" name="searchbox" value="输入关键词，快速搜索本论坛" size="30" class="altbg2" onmouseover="this.focus()" onfocus="this.select()">
<select name="stype"><option value="" selected>全文</option><option value="1">标题</option></select>
<input name="button" type="button" style="height: 1.8em" onclick="window.open('search.php?srchtype=qihoo&amp;srchtxt='+findobj('searchbox').value+'&amp;stype='+findobj('stype').value+'&amp;searchsubmit=yes');" value="搜帖">
<? } ?>
</td></tr></table></td></tr>
<? if($lastmod['modaction'] || $thread['blog'] || $thread['readperm'] || $thread['price'] > 0) { ?>
<tr class="category"><td colspan="2" align="center" class="bold">
<? if($lastmod['modaction']) { ?>
&nbsp; <a href="misc.php?action=viewthreadmod&tid=<?=$tid?>" title="主题管理记录" target="_blank">本主题由 <?=$lastmod['modusername']?> 于 <?=$lastmod['moddateline']?> <?=$lastmod['modaction']?></a>&nbsp;
<? } if($thread['blog']) { ?>
&nbsp; <a href="blog.php?uid=<?=$thread['authorid']?>" target="_blank">本主题被作者加入到他/她的 Blog 中</a> &nbsp;
<? } if($thread['readperm']) { ?>
&nbsp; 所需阅读权限 <span class=\"bold\"><?=$thread['readperm']?></span> &nbsp;
<? } if($thread['price'] > 0) { ?>
&nbsp; <a href="misc.php?action=viewpayments&tid=<?=$tid?>">浏览需支付 <?=$extcredits[$creditstrans]['title']?> <span class=\"bold\"><?=$thread['price']?></span> <?=$extcredits[$creditstrans]['unit']?></a> &nbsp;
<? } ?>
</td></tr>
<? } ?>
</table>
</div>

<form method="post" name="delpost" action="topicadmin.php?action=delpost&fid=<?=$fid?>&tid=<?=$tid?>&page=<?=$page?>">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<? if(is_array($postlist)) { foreach($postlist as $post) { ?>
<div class="maintable">
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" style="margin-top:-1px;border-bottom:none;border-top:none" class="tableborder">
<tr><td colspan="2" class="singleborder"><?=$post['newpostanchor']?>&nbsp;<?=$post['lastpostanchor']?></td></tr>
<tr class="<?=$post['thisbg']?>" height="100%">
<td width="21%" valign="top" style="word-break: break-all">
<? if($forum['ismoderator']) { if($allowviewip) { ?>
<a href="topicadmin.php?action=getip&fid=<?=$fid?>&tid=<?=$tid?>&pid=<?=$post['pid']?>"><img src="<?=IMGDIR?>/ip.gif" border="0" align="right" alt="查看 IP"></a>
<? } } if($post['authorid'] && $post['username'] && !$post['anonymous']) { ?>
<a href="viewpro.php?uid=<?=$post['authorid']?>" target="_blank" class="bold"><?=$post['author']?></a>
<? if($post['nickname']) { ?>
 <span class="smalltxt">(<?=$post['nickname']?>)</span>
<? } ?>
<br><span class="smalltxt">
<?=$post['authortitle']?>
<br>
<? showstars($post['stars']); ?>
<br>
<? if($post['customstatus']) { ?>
<?=$post['customstatus']?><br>
<? } ?>
<br>
<? if($avatarshowpos == 3 && $post['avatarshow']) { ?>
<center><?=$post['avatarshow']?></center><br>
<? } elseif($post['avatar']) { ?>
<table width="95%" border="0" cellspacing="0" cellpadding="0" style="table-layout: fixed">
<tr><td align="center"><?=$post['avatar']?></td></tr></table><br>
<? } else { ?>
<br><br>
<? } if($post['medals']) { if(is_array($post['medals'])) { foreach($post['medals'] as $medal) { ?>
<img src="images/common/<?=$medal['image']?>" border="0" alt="<?=$medal['name']?>"> &nbsp;
<? } } ?>
<br>
<? } ?>
UID <?=$post['uid']?><br>
精华 
<? if($post['digestposts']) { ?>
<a href="digest.php?authorid=<?=$post['authorid']?>"><?=$post['digestposts']?></a>
<? } else { ?>
<?=$post['digestposts']?>
<? } ?>
<br>
积分 <?=$post['credits']?><br>
帖子 <?=$post['posts']?><br>
<? if(is_array($extcredits_thread)) { foreach($extcredits_thread as $key => $credit) { ?>
<?=$credit['title']?> <?=$post[$key]?> <?=$credit['unit']?><br>
<? } } ?>
阅读权限 <?=$post['readaccess']?><br>
注册 <?=$post['regdate']?>
<? if(is_array($_DCACHE['fields_thread'])) { foreach($_DCACHE['fields_thread'] as $field) { ?>
<br><?=$field['title']?>
<? if($field['selective']) { ?>
<?=$field['choices'][$post['field_'.$field['fieldid']]]?>
<? } else { ?>
<?=$post['field_'.$field['fieldid']]?>
<? } } } if($post['location']) { ?>
<br>来自 <?=$post['location']?>
<? } ?>
<br>
<? if($vtonlinestatus && $post['authorid']) { if($timestamp - $post['lastactivity'] <= 10800 && !$post['invisible']) { ?>
状态 <b>在线</b>
<? } else { ?>
状态 离线
<? } } if($avatarshowpos == 2) { ?>
<br><br><center><?=$post['avatarshow']?></center>
<? } ?>
</span>
<? if($post['alipay']) { ?>
<br><a href="https://www.alipay.com/payto:<?=$post['alipay']?>?partner=20880020258585430156" target="_blank"><img src="<?=IMGDIR?>/payto.gif" border="0" alt="用支付宝求购"></a>
<? } } else { if(!$post['authorid']) { ?>
<span class="bold">游客</span> <span class="smalltxt"><?=$post['useip']?></span><br><span class="smalltxt">未注册
<? } elseif($post['authorid'] && $post['username'] && $post['anonymous']) { ?>
<span class="bold">
<? if($forum['ismoderator']) { ?>
<a href="viewpro.php?uid=<?=$post['authorid']?>" target="_blank">匿名</a>
<? } else { ?>
匿名
<? } ?>
</span><br>该用户匿名发贴
<? } else { ?>
<span class="bold"><?=$post['author']?></span><br>
该用户已被删除
<? } ?>
<br><br><br><br><br><br><br><br><br><br>
<? } ?>
</td>
<td width="79%" valign="top">
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout: fixed; word-wrap: break-word">
<tr><td valign="top">
<? if($avatarshowpos == 1) { ?>
<div style="float: right"><?=$post['avatarshow']?></div>
<? } ?>
<a name="pid<?=$post['pid']?>" href="misc.php?action=viewratings&tid=<?=$tid?>&pid=<?=$post['pid']?>" alt="查看评分记录"><?=$post['ratings']?></a>
<? if(!empty($advlist['thread'][$post['count']])) { ?>
<span class="smalltxt"><span class="bold">[广告]:</span> <?=$advlist['thread'][$post['count']]?></span><hr width="100%" style="height: 1px; color: <?=INNERBORDERCOLOR?>">
<? } if($post['subject']) { ?>
<span class="smalltxt"><span class="bold"><?=$post['subject']?></span></span><br><br>
<? } if($bannedmessages && (($post['authorid'] && !$post['username']) || ($post['groupid'] == 4 || $post['groupid'] == 5))) { ?>
*** 作者被禁止或删除 内容自动屏蔽 ***
<? } else { ?>
<span style="font-size: <?=MSGFONTSIZE?>"><?=$post['message']?></span>
<? if($post['attachment']) { ?>
<br><br><img src="images/attachicons/common.gif">&nbsp;附件: <i>您所在的用户组无法下载或查看附件</i>
<? } else { if(is_array($post['attachments'])) { foreach($post['attachments'] as $attach) { ?>
<br><br><?=$attach['attachicon']?>
<? if($attach['attachimg']) { ?>
<a href="member.php?action=credits&view=getattach" title="查看积分策略说明" target="_blank">图片附件</a>:
<? if($attach['readperm']) { ?>
, 阅读权限 <?=$attach['readperm']?>
<? } if($attach['description']) { ?>
[<?=$attach['description']?>]
<? } ?>
 <a href="attachment.php?aid=<?=$attach['aid']?>" target="_blank" class="bold"><?=$attach['filename']?></a> (<?=$attach['dateline']?>, <?=$attach['attachsize']?>)<br><br>
<? if($attachrefcheck) { ?>
<img src="attachment.php?aid=<?=$attach['aid']?>&noupdate=yes" border="0" onload="if(this.width>screen.width*0.7) {this.resized=true; this.width=screen.width*0.7; this.alt='点击在新窗口查看全图\nCTRL+鼠标滚轮放大或缩小';}" onmouseover="if(this.resized) this.style.cursor='hand';" onclick="if(!this.resized) {return false;} else {window.open('attachment.php?aid=<?=$attach['aid']?>');}" onmousewheel="return imgzoom(this);">
<? } else { ?>
<img src="<?=$attachurl?>/<?=$attach['attachment']?>" border="0" onload="if(this.width>screen.width*0.7) {this.resized=true; this.width=screen.width*0.7; this.alt='点击在新窗口查看全图\nCTRL+鼠标滚轮放大或缩小';}" onmouseover="if(this.resized) this.style.cursor='hand';" onclick="if(!this.resized) {return false;} else {window.open('<?=$attachurl?>/<?=$attach['attachment']?>');}" onmousewheel="return imgzoom(this);">
<? } } else { ?>
<a href="member.php?action=credits&view=getattach" title="查看积分策略说明" target="_blank">附件</a>:
<? if($attach['description']) { ?>
[<?=$attach['description']?>]
<? } ?>
 <a href="attachment.php?aid=<?=$attach['aid']?>" target="_blank" class="bold"><?=$attach['filename']?></a> (<?=$attach['dateline']?>, <?=$attach['attachsize']?>)<br>
<span class="smalltxt">该附件被下载次数 <?=$attach['downloads']?>
<? if($attach['readperm']) { ?>
, 阅读权限 <?=$attach['readperm']?>
<? } ?>
</span><br>
<? } } } } if($post['number'] == 1 && $relatedkeywords) { ?>
<br><br><span class="bold">相关关键字:</span> <?=$relatedkeywords?><br><br><br>
<? } if($post['signature'] && !$post['anonymous']) { ?>
<br><br><br></td></tr><tr><td valign="bottom" <?=$maxsigrows?>><img src="images/common/sigline.gif"><br><?=$post['signature']?>
<? } } ?>
</td></tr></table>
</td></tr>
<tr class="<?=$post['thisbg']?>"><td valign="middle">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="smalltxt">
<tr><td align="left">
<? if($forum['ismoderator'] && $allowdelpost) { if($post['number'] == 1) { ?>
<input type="checkbox" disabled>
<? } else { ?>
<input type="checkbox" name="delete[]" value="<?=$post['pid']?>">
<? } } ?>
<?=$post['dateline']?></td><td align="right"><a href="###" class="bold" onclick="window.clipboardData.setData('text','<?=$boardurl?>viewthread.php?tid=<?=$tid?>&page=<?=$page?>#pid<?=$post['pid']?>')">#<?=$post['number']?></a></td></tr></table>
</td><td valign="bottom">
<table width="100%" height="100%" border="0" cellspacing="2" cellpadding="0">
<tr class="smalltxt"><td align="left">
<? if($post['username'] && !$post['anonymous']) { ?>
<a href="viewpro.php?uid=<?=$post['authorid']?>"><img src="<?=IMGDIR?>/profile.gif" border="0" align="absmiddle" alt="查看资料"></a>&nbsp;
<? if($post['site']) { ?>
<a href="<?=$post['site']?>" target="_blank"><img src="<?=IMGDIR?>/site.gif" border="0" align="absmiddle" alt="访问主页"></a>&nbsp;
<? } if($post['allowuseblog']) { ?>
<a href="blog.php?uid=<?=$post['authorid']?>" target="_blank"><img src="<?=IMGDIR?>/blog.gif" border="0" align="absmiddle" alt="Blog"></a>&nbsp;
<? } ?>
<a href="pm.php?action=send&uid=<?=$post['authorid']?>" target="_blank"><img src="<?=IMGDIR?>/pm.gif" border="0" align="absmiddle" alt="发短消息"></a>&nbsp;
<? if($post['qq']) { ?>
<a href="http://wpa.qq.com/msgrd?V=1&Uin=<?=$post['qq']?>&Site=<?=$bbname?>&Menu=yes" target="_blank"><img src="http://wpa.qq.com/pa?p=1:<?=$post['qq']?>:4" align="absmiddle" border="0" alt="QQ"></a>&nbsp;
<? } if($post['icq']) { ?>
<a href="http://wwp.icq.com/scripts/search.dll?to=<?=$post['icq']?>" target="_blank"><img src="http://web.icq.com/whitepages/online?icq=<?=$post['icq']?>&img=5" alt="ICQ 状态" border="0" align="absmiddle"></a>&nbsp;
<? } if($post['yahoo']) { ?>
<a href="http://edit.yahoo.com/config/send_webmesg?.target=<?=$post['yahoo']?>&.src=pg" target="_blank"><img src="<?=IMGDIR?>/yahoo.gif" alt="Yahoo!" border="0" align="absmiddle"></a>&nbsp;
<? } if($post['taobao']) { ?>
<script language="JavaScript">document.write('<a target="_blank" href="http://amos1.taobao.com/msg.ww?v=2&uid='+encodeURIComponent('<?=$post['taobao']?>')+'&s=2"><img src="http://amos1.taobao.com/online.ww?v=2&uid='+encodeURIComponent('<?=$post['taobao']?>')+'&s=2" alt="淘宝旺旺" border="0" align="absmiddle"></a>&nbsp;');</script>
<? } } ?>
</td><td align="right">
<? if($forum['ismoderator'] || $post['authorid'] == $discuz_uid) { ?>
&nbsp;<a href="post.php?action=edit&fid=<?=$fid?>&tid=<?=$tid?>&pid=<?=$post['pid']?>&page=<?=$page?>&extra=<?=$extra?>"><img src="<?=IMGDIR?>/edit.gif" border="0" align="absmiddle" alt="编辑帖子"></a>
<? } if($allowpostreply) { ?>
&nbsp;<a href="post.php?action=reply&fid=<?=$fid?>&tid=<?=$tid?>&repquote=<?=$post['pid']?>&extra=<?=$extra?>"><img src="<?=IMGDIR?>/quote.gif" border="0" align="absmiddle" alt="引用回复"></a>
<? } if($discuz_uid && $reportpost) { ?>
&nbsp;<a href="misc.php?action=report&fid=<?=$fid?>&tid=<?=$tid?>&pid=<?=$post['pid']?>&page=<?=$page?>"><img src="<?=IMGDIR?>/report.gif" border="0" align="absmiddle" alt="向版主反映这个帖子"></a>
<? } if($raterange && $post['authorid']) { ?>
&nbsp;<a href="misc.php?action=rate&tid=<?=$tid?>&pid=<?=$post['pid']?>&page=<?=$page?>"><img src="<?=IMGDIR?>/rate.gif" border="0" align="absmiddle" alt="给本帖评分"></a>
<? } if($fastpost && $allowpostreply) { ?>
<a href="###" onclick="fastreply('回复 #<?=$post['number']?><?=$post['authoras']?> 的帖子')"><img src="<?=IMGDIR?>/fastreply.gif" border="0" align="absmiddle" alt="回复"></a>
<? } ?>
<a href="###" onclick="scroll(0,0)"><img src="<?=IMGDIR?>/top.gif" border="0" align="absmiddle" alt="顶部"></a>
</td></tr></table>
</td></tr></table></div>
<? } } ?>
<div class="maintable">
<table width="<?=TABLEWIDTH?>" cellspacing="0" cellpadding="0" border="0" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>" height="1"></td></tr>
</table></div>
</form>

<div class="maintable">
<table width="<?=TABLEWIDTH?>" cellspacing="0" cellpadding="0" align="center">
<tr>
<td valign="top"><?=$multipage?></td><td align="right">
<? if($allowpost || !$discuz_uid) { ?>
&nbsp;<a href="post.php?action=newthread&fid=<?=$fid?>&extra=<?=$extra?>"><img src="<?=IMGDIR?>/newtopic.gif" border="0"></a>
<? } if($allowpostpoll || !$discuz_uid) { ?>
&nbsp;<a href="post.php?action=newthread&fid=<?=$fid?>&extra=<?=$extra?>&poll=yes"><img src="<?=IMGDIR?>/poll.gif" border="0"></a>
<? } if(($allowpost || !$discuz_uid) && $allowposttrade) { ?>
&nbsp;<a href="post.php?action=newthread&fid=<?=$fid?>&extra=<?=$extra?>&trade=yes"><img src="<?=IMGDIR?>/newtrade.gif" border="0"></a>
<? } if($allowpostreply || !$discuz_uid) { ?>
&nbsp;<a href="post.php?action=reply&fid=<?=$fid?>&tid=<?=$tid?>&extra=<?=$extra?>"><img src="<?=IMGDIR?>/reply.gif" border="0"></a>
<? } ?>
</td></tr>
</table><br></div>
<? if($relatedthreadlist) { ?>
<div class="maintable">
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr class="header"><td colSpan=6><table cellspacing="0" cellpadding="0" border="0" width="100%" class="smalltxt" style="table-layout: fixed; word-wrap: break-word">
<tr style="color: <?=HEADERTEXT?>"><td class="bold" width="60%">相关主题</td>
<td width="40%" align="right" nowrap><a href="http://search.qihoo.com/usearch.html?kw=<?=$searchkeywords?>&relate=<?=$thread['subjectenc']?>&sort=rdate&site=discuzall&site=<?=$site?>" target="_blank">更多相关主题</a>
&nbsp;<a href="###" onclick="toggle_collapse('relatedthreads');"><img id="relatedthreads_img" src="<?=IMGDIR?>/<?=$relatedthreads['img']?>" border="0"></td></tr></table></td></tr>
<tbody id="relatedthreads" style="<?=$relatedthreads['style']?>">
<tr class="category">
<td width="45%" align="center">标题</td>
<td width="14%" align="center">论坛</td>
<td width="14%" align="center">作者</td>
<td width="6%" align="center">回复</td>
<td width="6%" align="center">查看</td>
<td width="15%" align="center">最后发表</td>
</tr>
<? if(is_array($relatedthreadlist)) { foreach($relatedthreadlist as $key => $threads) { if($threads['title']) { ?>
<tr>
<td class="altbg2" onMouseOver="this.className='altbg1'" onMouseOut="this.className='altbg2'"><a href="viewthread.php?tid=<?=$threads['tid']?>" target="_blank"><?=$threads['title']?></td>
<td class="altbg1" align="center"><a href="forumdisplay.php?fid=<?=$threads['fid']?>" target="_blank"><?=$threads['chanl']?></a></td>
<td class="altbg2" align="center"><a href="viewpro.php?username=
<? echo rawurlencode($threads['author']); ?>
" target="_blank"><?=$threads['author']?></a><br><?=$threads['pdate']?></td>
<td class="altbg1" align="center"><?=$threads['rnum']?></td>
<td class="altbg2" align="center"><?=$threads['vnum']?></td>
<td class="altbg1" align="center"><?=$threads['rdate']?></td>
</tr>
<? } } } ?>
</tbody>
</table><br></div>
<? } if($fastpost && $allowpostreply) { ?>
<script language="JavaScript">
var postminchars = parseInt('<?=$minpostsize?>');
var postmaxchars = parseInt('<?=$maxpostsize?>');
var disablepostctrl = parseInt('<?=$disablepostctrl?>');
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
<form method="post" name="input" action="post.php?action=reply&fid=<?=$fid?>&tid=<?=$tid?>&extra=<?=$extra?>&replysubmit=yes" onSubmit="return validate(this)">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<div class="maintable">
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td colspan="2" class="header"><a href="member.php?action=credits&view=forum_reply&fid=<?=$fid?>" target="_blank"><img src="<?=IMGDIR?>/credits.gif" alt="查看积分策略说明" align="right" border="0"></a>快速回复主题</td></tr>
<tr>
<td width="18%" class="altbg1">标题:</td>
<td width="82%" class="altbg2"><input type="text" name="subject" size="80" value="" tabindex="1"> &nbsp; <span class="smalltxt">(可选)</span></td>
</tr>
<tr>
<td width="18%" class="altbg1" valign="top">选项:<br><span class="smalltxt">
<input type="checkbox" name="parseurloff" value="1"> 禁用 URL 识别<br>
<input type="checkbox" name="smileyoff" value="1"> 禁用 <a href="faq.php?page=messages#6" target="_blank">Smilies</a><br>
<input type="checkbox" name="bbcodeoff" value="1"> 禁用 <a href="faq.php?page=misc#1" target="_blank">Discuz! 代码</a><br>
<? if($allowanonymous || $forum['allowanonymous']) { ?>
<input type="checkbox" name="isanonymous" value="1"> 使用匿名发帖<br>
<? } ?>
<input type="checkbox" name="usesig" value="1" <?=$usesigcheck?>> 使用个人签名<br>
<input type="checkbox" name="emailnotify" value="1"> 接收新回复邮件通知</span>
</td>
<td width="82%" class="altbg2"><span class="smalltxt">
<textarea rows="7" name="message" style="width: 80%; word-break: break-all" onKeyDown="ctlent(event);" tabindex="2"></textarea><br>
<input type="submit" name="replysubmit" value="发表帖子" tabindex="3">&nbsp;&nbsp;&nbsp;
<input type="submit" name="previewpost" value="预览帖子" tabindex="4">&nbsp;&nbsp;&nbsp;
<input type="reset" name="topicsreset" value="清空内容" tabindex="5">&nbsp; &nbsp;[完成后可按 Ctrl+Enter 发布]</span>
</td></tr></table></div></form>
<? } ?>
<div class="maintable"><br><table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" align="center" class="tableborder">
<tr class="smalltxt"><td class="altbg2" align="center" nowrap> &nbsp;
<a href="viewthread.php?action=printable&tid=<?=$tid?>" target="_blank">可打印版本</a> |
<a href="misc.php?action=emailfriend&tid=<?=$tid?>">推荐给朋友</a> |
<a href="memcp.php?action=subscriptions&subadd=<?=$tid?>&lastpost=<?=$thread['lastpost']?>">订阅主题</a> |
<a href="memcp.php?action=favorites&favadd=<?=$tid?>">收藏主题</a>
<? if($thread['authorid'] && ($thread['authorid'] == $discuz_uid || $forum['ismoderator'])) { if($thread['blog']) { ?>
 | <a href="misc.php?action=blog&tid=<?=$tid?>">移除 Blog</a>
<? } elseif($allowuseblog && $forum['allowblog'] && $thread['authorid'] == $discuz_uid) { ?>
 | <a href="misc.php?action=blog&tid=<?=$tid?>">加入 Blog</a>
<? } } ?>
&nbsp; </td></tr></table></div>

<div class="maintable">
<br><br><table width="<?=TABLEWIDTH?>" cellspacing="0" cellpadding="0" align="center" class="outertxt">
<tr><td align="left" class="smalltxt">
<? if($forumjump) { ?>
<select onchange="if(this.options[this.selectedIndex].value != '') {
window.location=('forumdisplay.php?fid='+this.options[this.selectedIndex].value+'&sid=<?=$sid?>') }">
<option value="">论坛跳转 ...</option>
<?=$forumselect?>
</select>&nbsp;
<? } if($visitedforums) { ?>
<select onchange="if(this.options[this.selectedIndex].value != '') {
window.location=('forumdisplay.php?fid='+this.options[this.selectedIndex].value+'&sid=<?=$sid?>') }">
<option value="">最近访问的论坛 ...</option>
<?=$visitedforums?>
</select>
<? } ?>
</td><td align="right" class="smalltxt">
<? if($forum['ismoderator']) { ?>
<span class="bold">管理选项:</span>
<select name="action" id="action" onchange="if(this.options[this.selectedIndex].value != '') { if(this.options[this.selectedIndex].value != 'delpost') {
window.location=('topicadmin.php?tid=<?=$tid?>&fid=<?=$fid?>&action='+this.options[this.selectedIndex].value+'&sid=<?=$sid?>');
} else { document.delpost.submit(); } }">
<option value="" selected>管理选项</option>
<? if($allowdelpost) { ?>
<option value="delpost">删除回帖</option>
<option value="delete">删除主题</option>
<? } ?>
<option value="close">关闭主题</option>
<option value="move">移动主题</option>
<option value="highlight">高亮显示</option>
<option value="digest">设置精华</option>
<? if($allowstickthread) { ?>
<option value="stick">主题置顶</option>
<? } if($thread['price'] > 0 && $allowrefund) { ?>
<option value="refund">强制退款</option>
<? } ?>
<option value="split">分割主题</option>
<option value="merge">合并主题</option>
<option value="bump">提升主题</option>
<option value="repair">修复主题</option>
</select>
<? } ?>
</td>
</tr></table>
<? include template('footer'); if($relatedthreadupdate) { ?>
<script language="JavaScript" src="relatethread.php?tid=<?=$tid?>&subjectenc=<?=$thread['subjectenc']?>&verifykey=<?=$verifykey?>"></script>
<? } ?>
