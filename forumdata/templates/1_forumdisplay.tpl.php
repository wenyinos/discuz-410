<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed">
<tr><td class="nav" width="85%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> <?=$navigation?></td>
<td align="right" width="15%">
<? if($rssstatus) { ?>
<a href="rss.php?fid=<?=$fid?>&auth=<?=$rssauth?>" target="_blank"><img src="images/common/xml.gif" border="0" align="absmiddle" alt="RSS 订阅当前论坛"></a>
<? } ?>
&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>
</tr></table><br></div>

<div class="maintable">
<table width="<?=TABLEWIDTH?>" cellspacing="0" cellpadding="0" align="center" class="outertxt">
<tr class="mediumtxt"><td class="smalltxt">(版主: <span class="bold">
<? if($moderatedby) { ?>
<?=$moderatedby?>
<? } else { ?>
*空缺中*
<? } ?>
</span>)</td><td align="right" class="smalltxt"><img src="<?=IMGDIR?>/showdigest.gif" border="0" align="absmiddle">
<? if($filter == 'digest') { ?>
<a href="forumdisplay.php?fid=<?=$fid?>">查看全部主题</a>
<? } else { ?>
<a href="forumdisplay.php?fid=<?=$fid?>&filter=digest">查看本版精华</a>
<? } ?>
&nbsp; <img src="<?=IMGDIR?>/mytopic.gif" border="0" align="absmiddle"> <a href="search.php?srchuid=<?=$discuz_uid?>&srchfid=<?=$fid?>&mytopics=yes&searchsubmit=yes">我的话题</a>
<? if($allowmodpost && $forum['modnewposts']) { ?>
&nbsp; <img src="<?=IMGDIR?>/moderate.gif" border="0" align="absmiddle"> 审核
<a href="admincp.php?action=modthreads&frames=yes" target="_blank">[新主题]</a>
<? if($forum['modnewposts'] == 2) { ?>
<a href="admincp.php?action=modreplies&frames=yes" target="_blank">[新回复]</a>
<? } } if($adminid == 1 && $forum['recyclebin']) { ?>
&nbsp; <img src="<?=IMGDIR?>/recyclebin.gif" border="0" align="absmiddle"> <a href="admincp.php?action=recyclebin&frames=yes" target="_blank">回收站</a>
<? } ?>
</td></tr></table></div>
<? if($forum['rules']) { ?>
<div class="maintable">
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr class="header"><td><a href="###" onclick="toggle_collapse('rules_<?=$fid?>');"><img id="rules_<?=$fid?>_img" src="<?=IMGDIR?>/<?=$rulescollapseimg?>" align="right" border="0"></a>本版规则</td></tr>
<tbody id="rules_<?=$fid?>" style="<?=$collapserules?>">
<tr><td class="altbg2" colspan="2" onMouseOver="this.className='altbg1'" onMouseOut="this.className='altbg2'"><?=$forum['rules']?></td></tr>
</tbody></table><br>
</div>
<? } if(!empty($newpmexists)) { ?>
<div class="maintable">
<? include template('pmprompt'); ?>
</div>
<? } if($subexists) { ?>
<div class="maintable">
<? include template('forumdisplay_subforum'); ?>
</div>
<? } if(!empty($advlist['text'])) { ?>
<div class="maintable"><br><table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder"><?=$advlist['text']?></table><br></div>
<? } ?>
<form method="post" name="moderate" action="topicadmin.php?action=moderate&fid=<?=$fid?>">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">

<div class="maintable">
<table width="<?=TABLEWIDTH?>" cellspacing="0" cellpadding="0" align="center">
<tr><td valign="bottom"><?=$multipage?></td>
<td align="right" valign="bottom">
<? if($allowpost || !$discuz_uid) { ?>
&nbsp;<a href="post.php?action=newthread&fid=<?=$fid?>&extra=<?=$extra?>"><img src="<?=IMGDIR?>/newtopic.gif" border="0"></a>
<? } if($allowpostpoll || !$discuz_uid) { ?>
&nbsp;<a href="post.php?action=newthread&fid=<?=$fid?>&extra=<?=$extra?>&poll=yes"><img src="<?=IMGDIR?>/poll.gif" border="0"></a>
<? } if(($allowpost || !$discuz_uid) && $allowposttrade) { ?>
&nbsp;<a href="post.php?action=newthread&fid=<?=$fid?>&extra=<?=$extra?>&trade=yes"><img src="<?=IMGDIR?>/newtrade.gif" border="0"></a>
<? } if($forum['threadtypes'] && $forum['threadtypes']['listable']) { ?>
<br><table cellspacing="0" cellpadding="0" border="0"><tr><td>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="2" class="tableborder"><tr class="smalltxt">
<? if(is_array($forum['threadtypes']['types'])) { foreach($forum['threadtypes']['types'] as $id => $name) { if($typeid != $id) { ?>
<td class="altbg1">&nbsp;<a href="forumdisplay.php?fid=<?=$fid?>&filter=type&typeid=<?=$id?>"><?=$name?></a>&nbsp;</td>
<? } else { ?>
<td class="header">&nbsp;<span class="bold"><?=$name?></span>&nbsp;</td>
<? } } } if($typeid) { ?>
<td class="altbg1">&nbsp;<a href="forumdisplay.php?fid=<?=$fid?>">全部</a>&nbsp;</td>
<? } else { ?>
<td class="header">&nbsp;<span class="bold">全部</span>&nbsp;</td>
<? } ?>
</tr></table></td></tr><tr><td height="3"></td></tr></table>
<? } ?>
</td></tr></table></div>

<div class="maintable">
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder" style="border-bottom:none;">
<tr class="header"><td colspan="7">
<table cellspacing="0" cellpadding="0" width="100%" border="0">
<tr><td width="65%" class="smalltxt"><a href="forumdisplay.php?fid=<?=$fid?>" class="bold"><?=$forum['name']?></a></td>
<td width="35%" align="right" nowrap>&nbsp;
<? if($searchboxstatus) { ?>
<img src="<?=IMGDIR?>/search.gif" border="0" align="bottom" width="16" height="16">&nbsp;&nbsp;<input type="text" name="searchbox" value="输入关键词，快速搜索本论坛" size="30" class="altbg2" onmouseover="this.focus()" onfocus="this.select()">
<select name="stype"><option value="" selected>全文</option><option value="1">标题</option></select>
<input name="button" type="button" style="height: 1.8em" onclick="window.open('search.php?srchtype=qihoo&amp;srchtxt='+findobj('searchbox').value+'&amp;stype='+findobj('stype').value+'&amp;searchsubmit=yes');" value="搜帖">
<? } ?>
</td></tr></table></td></tr>
<tr class="category">
<td width="4%">&nbsp;</td>
<td width="4%">&nbsp;</td>
<td width="47%" align="center">标题</td>
<td width="14%" align="center" nowrap>作者</td>
<td width="6%" align="center" nowrap>回复</td>
<td width="6%" align="center" nowrap>查看</td>
<td width="19%" align="center">最后发表</td>
</tr>
<? if($page == 1 && !empty($announcement)) { ?>
<tr>
<td class="altbg2" align="center"><a href="announcement.php?id=<?=$announcement['id']?>#<?=$announcement['id']?>" target="_blank"><img src="<?=IMGDIR?>/lock_folder.gif" border="0"></a></td>
<td class="altbg2" colspan="2" onMouseOver="this.className='altbg1'" onMouseOut="this.className='altbg2'">论坛公告: <a href="announcement.php?id=<?=$announcement['id']?>#<?=$announcement['id']?>"><?=$announcement['subject']?></a></td>
<td class="altbg1" align="center"><a href="viewpro.php?uid=<?=$announcement['authorid']?>"><?=$announcement['author']?></a><br><span class="smalltxt"><?=$announcement['starttime']?></span></td>
<td class="altbg2" align="center">-</td>
<td class="altbg1" align="center">-</td>
<td class="altbg2" align="center">-</td>
</tr>
<? } ?>
</table></div>
<? if($threadcount) { if(is_array($threadlist)) { foreach($threadlist as $key => $thread) { ?>
<div class="maintable">
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" style="margin-top:-1px;border-bottom:none;border-top:none" class="tableborder">
<? if($separatepos == $key + 1) { ?>
<tr class="category"><td>&nbsp;</td><td colspan="6"><span class="bold">论坛主题</span></td></tr>
<? } ?>
<tr>
<td width="4%" class="altbg2" align="center"><a href="viewthread.php?tid=<?=$thread['tid']?>" target="_blank"><img src="<?=IMGDIR?>/<?=$thread['folder']?>" border="0"></a></td>
<td width="4%" class="altbg1" align="center"><?=$thread['icon']?></td>
<td width="47%" class="altbg2" onMouseOver="this.className='altbg1'" onMouseOut="this.className='altbg2'">
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="table-layout: fixed; word-wrap: break-word"><tr><td>
<? if($thread['rate'] > 0) { ?>
<img src="<?=IMGDIR?>/agree.gif" align="right">
<? } elseif($thread['rate'] < 0) { ?>
<img src="<?=IMGDIR?>/disagree.gif" align="right">
<? } if($forum['ismoderator']) { if($thread['fid'] == $fid) { ?>
<input type="checkbox" name="moderate[]" value="<?=$thread['tid']?>">
<? } else { ?>
<input type="checkbox" disabled>
<? } } if($thread['new']) { ?>
<a href="redirect.php?tid=<?=$thread['tid']?>&goto=newpost<?=$highlight?>#newpost"><img src="<?=IMGDIR?>/firstnew.gif" border="0" align="absmiddle"></a> 
<? } if($thread['moved']) { if($forum['ismoderator']) { ?>
<a href="topicadmin.php?action=delete&tid=<?=$thread['moved']?>">移动:</a>
<? } else { ?>
移动:
<? } } elseif($thread['digest']) { ?>
<img src="<?=IMGDIR?>/digest.gif" align="absmiddle"> 精华<b>
<? echo substr('III', - $thread['digest']); ?>
</b>:&nbsp;
<? } elseif($thread['displayorder']) { ?>
<img src="<?=IMGDIR?>/pin.gif" align="absmiddle"> 置顶<b>
<? echo substr('III', - $thread['displayorder']); ?>
</b>:&nbsp;
<? } elseif($thread['poll']) { ?>
<img src="<?=IMGDIR?>/pollsmall.gif" align="absmiddle"> 投票:&nbsp;
<? } ?>
<?=$thread['typeid']?>
<? if($thread['attachment']) { ?>
<img src="images/attachicons/common.gif">
<? } ?>
<a href="viewthread.php?tid=<?=$thread['tid']?>&extra=<?=$extra?>"<?=$thread['highlight']?>><?=$thread['subject']?></a>
<? if($thread['readperm']) { ?>
 - [阅读权限 <span class="bold"><?=$thread['readperm']?></span>]
<? } if($thread['price'] > 0) { ?>
 - [售价 <?=$extcredits[$creditstrans]['title']?> <span class="bold"><?=$thread['price']?></span> <?=$extcredits[$creditstrans]['unit']?>]
<? } ?>
<?=$thread['multipage']?>
</td></tr></table>
</td><td width="14%" class="altbg1" align="center">
<? if($thread['authorid'] && $thread['author']) { ?>
<a href="viewpro.php?uid=<?=$thread['authorid']?>"><?=$thread['author']?></a>
<? } else { ?>
 
<? if($forum['ismoderator']) { ?>
<a href="viewpro.php?uid=<?=$thread['authorid']?>">匿名</a>
<? } else { ?>
匿名
<? } } ?>
<br><span class="smalltxt"><?=$thread['dateline']?></span></td>
<td width="6%" class="altbg2" align="center"><?=$thread['replies']?></td>
<td width="6%" class="altbg1" align="center"><?=$thread['views']?></td>
<td width="19%" class="altbg2">
<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr align="right">
<td nowrap><font class="smalltxt"><?=$thread['lastpost']?><br>
by
<? if($thread['lastposter']) { ?>
<a href="viewpro.php?username=<?=$thread['lastposterenc']?>"><?=$thread['lastposter']?></a>
<? } else { ?>
匿名
<? } ?>
</font></td><td nowrap>&nbsp;<a href="redirect.php?tid=<?=$thread['tid']?>&goto=lastpost<?=$highlight?>#lastpost"><img src="<?=IMGDIR?>/lastpost.gif" border="0"></a>
</td></tr></table></td></tr></table></div>
<? } } } else { ?>
<div class="maintable">
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" style="margin-top:-1px;border-top:none" class="tableborder">
<tr><td colspan="7" class="altbg1">本论坛或指定的范围内尚无主题。</td></tr>
</table>
</div>
<? } if($forum['ismoderator'] && $threadcount) { ?>
<div class="maintable">
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" style="border-top:none;" class="tableborder">
<tr><td colspan="7" class="altbg1" align="center">
<span class="bold">批量管理选项</span> &nbsp;
<input type="checkbox" name="chkall" onclick="checkall(this.form, 'moderate')"> 全选
<? if($allowdelpost) { ?>
<input type="radio" name="operation" value="delete"> 删除主题 
<? } ?>
<input type="radio" name="operation" value="move"> 移动主题
<input type="radio" name="operation" value="highlight"> 高亮显示
<input type="radio" name="operation" value="type"> 主题分类
<input type="radio" name="operation" value="close"> 关闭/打开主题
<? if($allowstickthread) { ?>
<input type="radio" name="operation" value="stick"> 置顶/解除置顶 
<? } ?>
<input type="radio" name="operation" value="digest"> 加入/解除精华 &nbsp;
<a href="###" onclick="javascript: document.moderate.submit()" class="bold">[提 &nbsp; 交]</a>
</td></tr></table></div>
<? } else { ?>
<div class="maintable">
<table width="<?=TABLEWIDTH?>" cellspacing="0" cellpadding="0" border="0" align="center">
<tr><td bgcolor="<?=BORDERCOLOR?>"></td></tr>
</table></div>
<? } ?>
<div class="maintable">
<table width="<?=TABLEWIDTH?>" cellspacing="0" cellpadding="0" align="center">
<tr><td valign="top"><?=$multipage?></td><td align="right">
<? if($allowpost || !$discuz_uid) { ?>
&nbsp;<a href="post.php?action=newthread&fid=<?=$fid?>&extra=<?=$extra?>"><img src="<?=IMGDIR?>/newtopic.gif" border="0"></a>
<? } if($allowpostpoll || !$discuz_uid) { ?>
&nbsp;<a href="post.php?action=newthread&fid=<?=$fid?>&extra=<?=$extra?>&poll=yes"><img src="<?=IMGDIR?>/poll.gif" border="0"></a>
<? } if(($allowpost || !$discuz_uid) && $allowposttrade) { ?>
&nbsp;<a href="post.php?action=newthread&fid=<?=$fid?>&extra=<?=$extra?>&trade=yes"><img src="<?=IMGDIR?>/newtrade.gif" border="0"></a>
<? } ?>
</td></tr></table><br></div></form>
<? if($fastpost && $allowpost) { ?>
<script language="JavaScript">
var postminchars = parseInt('<?=$minpostsize?>');
var postmaxchars = parseInt('<?=$maxpostsize?>');
var disablepostctrl = parseInt('<?=$disablepostctrl?>');
var typerequired = parseInt('<?=$forum['threadtypes']['required']?>');
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
<form method="post" name="input" action="post.php?action=newthread&fid=<?=$fid?>&extra=<?=$extra?>&topicsubmit=yes" onSubmit="return validate(this)">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<div class="maintable">
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td colspan="2" class="header"><a href="member.php?action=credits&view=forum_post&fid=<?=$fid?>" target="_blank"><img src="<?=IMGDIR?>/credits.gif" alt="查看积分策略说明" align="right" border="0"></a>快速发新话题</td></tr>
<tr>
<td width="18%" class="altbg1">标题:</td>
<td width="82%" class="altbg2"><?=$typeselect?> <input type="text" name="subject" size="80" value="" tabindex="1"></td>
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
<input type="checkbox" name="emailnotify" value="1"> 接收新回复邮件通知
<? if($allowuseblog && $forum['allowblog']) { ?>
<br><input type="checkbox" name="addtoblog" value="1"> 加入 Blog
<? } ?>
</span>
</td>
<td width="82%" class="altbg2" valign="middle"><span class="smalltxt">
<textarea rows="7" name="message" style="width: 80%; word-break: break-all" onKeyDown="ctlent(event);" tabindex="2"></textarea><br>
<input type="submit" name="topicsubmit" value="发表帖子" tabindex="3">&nbsp; &nbsp;
<input type="submit" name="previewpost" value="预览帖子" tabindex="4">&nbsp; &nbsp;
<input type="reset" name="topicsreset" value="清空内容" tabindex="5">&nbsp; &nbsp;[完成后可按 Ctrl+Enter 发布]</span>
</td></tr></table><br></div></form>
<? } if($whosonlinestatus) { ?>
<div class="maintable">
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<? if($detailstatus) { ?>
<tr class="header"><td width="100%">
<a name="online"></a><a href="forumdisplay.php?fid=<?=$fid?>&page=<?=$page?>&showoldetails=no#online"><img src="<?=IMGDIR?>/collapsed_no.gif" align="right" border="0"></a>正在浏览此论坛的会员
</td></tr>
<tr><td class="altbg2" onMouseOver="this.className='altbg1'" onMouseOut="this.className='altbg2'">
<table cellspacing="0" cellpadding="0" border="0" width="98%" align="center">
<tr><td nowrap>
<? if(is_array($whosonline)) { foreach($whosonline as $key => $online) { if($key % 7 == 0) { ?>
</td></tr><tr><td width="15%" nowrap>
<? } else { ?>
</td><td width="15%" nowrap>
<? } ?>
<img src="images/common/<?=$online['icon']?>" align="absmiddle">
<a href="viewpro.php?uid=<?=$online['uid']?>" title="时间: <?=$online['lastactivity']?><?="\n"?>
操作: <?=$online['action']?><?="\n"?>
论坛: <?=$forumname?>"><?=$online['username']?></a>
<? } } ?>
</td></tr></table></td></tr>
<? } else { ?>
<tr class="header"><td width="100%">
<a name="online"></a><a href="forumdisplay.php?fid=<?=$fid?>&page=<?=$page?>&showoldetails=yes#online"><img src="<?=IMGDIR?>/collapsed_yes.gif" align="right" border="0"></a>正在浏览此论坛的会员
</td></tr>
<? } ?>
</table><br></div>
<? } ?>
<div class="maintable">
<br>
<form method="get" action="forumdisplay.php">
<input type="hidden" name="fid" value="<?=$fid?>">
<table width="<?=TABLEWIDTH?>" cellspacing="0" cellpadding="0" align="center" class="outertxt">
<tr class="mediumtxt"><td align="left" class="smalltxt">
<? if($forumjump) { ?>
<select onchange="if(this.options[this.selectedIndex].value != '') {
window.location=('forumdisplay.php?fid='+this.options[this.selectedIndex].value+'&sid=<?=$sid?>') }">
<option value="">论坛跳转 ...</option>
<?=$forumselect?>
</select>
<? } if($visitedforums) { ?>
<select onchange="if(this.options[this.selectedIndex].value != '')
window.location=('forumdisplay.php?fid='+this.options[this.selectedIndex].value+'&sid=<?=$sid?>')">
<option value="">最近访问的论坛 ...</option>
<?=$visitedforums?>
</select>
<? } ?>
</td><td align="right" class="smalltxt">
<? if($filter == 'digest' || $filter == 'type') { ?>
<input type="hidden" name="filter" value="<?=$filter?>">
<input type="hidden" name="typeid" value="<?=$typeid?>">
<? } else { ?>
查看 <select name="filter">
<option value="0" <?=$check['0']?>>全部主题</option>
<option value="86400" <?=$check['86400']?>>1 天以来主题</option>
<option value="172800" <?=$check['172800']?>>2 天以来主题</option>
<option value="604800" <?=$check['604800']?>>1 周以来主题</option>
<option value="2592000" <?=$check['2592000']?>>1 个月以来主题</option>
<option value="7948800" <?=$check['7948800']?>>3 个月以来主题</option>
<option value="15897600" <?=$check['15897600']?>>6 个月以来主题</option>
<option value="31536000" <?=$check['31536000']?>>1 年以来主题</option>
</select>&nbsp;
<? } ?>
排序方式&nbsp;
<select name="orderby">
<option value="lastpost" <?=$check['lastpost']?>>最后回复时间</option>
<option value="dateline" <?=$check['dateline']?>>发布时间</option>
<option value="replies" <?=$check['replies']?>>回复数量</option>
<option value="views" <?=$check['views']?>>浏览次数</option>
</select>&nbsp;
<select name="ascdesc">
<option value="ASC" <?=$check['ASC']?>>按升序排列</option>
<option value="DESC" <?=$check['DESC']?>>按降序排列</option>
</select>&nbsp;
<input type="submit" value="提 &nbsp; 交">
</td></tr></table></form>

<br><table cellspacing="0" cellpadding="0" border="0" width="500" align="center" class="outertxt">
<tr class="smalltxt"><td><img src="<?=IMGDIR?>/red_folder.gif" alt="有新回复" align="absmiddle">&nbsp; 有新回复</td><td class="smalltxt">(&nbsp;<img src="<?=IMGDIR?>/hot_red_folder.gif" alt="多于 <?=$hottopic?> 篇回复" align="absmiddle">&nbsp; 多于 <?=$hottopic?> 篇回复 )</td><td class="smalltxt"><img src="<?=IMGDIR?>/lock_folder.gif" alt="关闭的主题" align="absmiddle">&nbsp; 关闭的主题</td></tr>
<tr class="smalltxt"><td><img src="<?=IMGDIR?>/folder.gif" alt="无新回复" align="absmiddle">&nbsp; 无新回复</td><td class="smalltxt">(&nbsp;<img src="<?=IMGDIR?>/hot_folder.gif" alt="多于 <?=$hottopic?> 篇回复" align="absmiddle">&nbsp; 多于 <?=$hottopic?> 篇回复 )</td><td class="smalltxt">&nbsp;</td></tr>
</table>
<? include template('footer'); ?>
