<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed">
<tr><td class="nav" width="85%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> <?=$navigation?></td>
<td align="right" width="15%">
<? if($rssstatus) { ?>
<a href="rss.php?auth=<?=$rssauth?>" target="_blank"><img src="images/common/xml.gif" border="0" align="absmiddle" alt="RSS 订阅全部论坛"></a>
<? } ?>
&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>
</tr></table><br>
</div>

<div class="maintable">
<table cellspacing="<?=TABLESPACE?>" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" class="outertxt">
<tr><td class="smalltxt">
现在时间是 <?=$currenttime?>, 您上次访问是在 <?=$lastvisittime?><br>
积分: <span class="bold"><?=$credits?></span>&nbsp;
<? if(is_array($extcredits)) { foreach($extcredits as $id => $credit) { ?>
<?=$credit['title']?>: <span class="bold"><?=$GLOBALS[extcredits.$id]?></span><?=$credit['unit']?>&nbsp;
<? } } ?>
/ 头衔:
<? if($validdays) { ?>
<a href="member.php?action=groupexpiry"><span class="bold"><?=$grouptitle?></span>(<?=$validdays?>)</a>
<? } else { ?>
<span class="bold"><?=$grouptitle?></span>
<? } if(!empty($invisible)) { ?>
 / 隐身模式
<? } ?>
</td><td align="right" nowrap class="smalltxt">
<a href="search.php?srchfrom=<?=$newthreads?>&searchsubmit=yes">查看新帖</a> |
<? if($discuz_uid) { ?>
<a href="search.php?srchuid=<?=$discuz_uid?>&mytopics=yes&searchsubmit=yes">我的话题</a> |
<? if($allowuseblog) { ?>
<a href="blog.php?uid=<?=$discuz_uid?>" target="_blank">Blog</a> |
<? } } ?>
<a href="digest.php">精华区</a> |
<a href="member.php?action=markread">标记已读</a>
| 欢迎新会员 <a href="viewpro.php?username=<?=$memberenc?>"><span class="bold"><?=$lastmember?></span></a><br>
共 <span class="bold"><?=$threads?></span> 篇主题 / <span class="bold"><?=$posts?></span> 篇帖子 / 今日 <span class="bold"><?=$todayposts?></span> 篇帖子 / <span class="bold"><?=$totalmembers?></span> 位会员
</td></tr></table>
</div>
<? if(empty($gid)) { ?>
<div class="maintable">
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr class="header"><td colspan="3"><?=$bbname?> 公告</td></tr>
<tr class="altbg2" align="center">
<td colspan="3" align="center">
<? if(empty($announcements)) { ?>
到目前为止没有论坛公告。
<? } else { ?>
<marquee direction="left" scrollamount="3" onMouseOver="this.stop();" onMouseOut="this.start();">
<?=$announcements?>
</marquee>
<? } ?>
</td></tr>
<? if($qihoo_status && $searchboxstatus) { ?>
<tr class="altbg2"><td>
<table width="100%" border="0" cellpadding="1" cellspacing="0">
<tr><td width="50%">
<? if($qihoo_links['keywords']) { ?>
<span class="bold">热门搜索</span>&nbsp;
<? if(is_array($qihoo_links['keywords'])) { foreach($qihoo_links['keywords'] as $link) { ?>
<?=$link?>&nbsp;
<? } } } ?>
</td><td align="right">
<img src="<?=IMGDIR?>/search.gif" border="0" align="bottom" width="16" height="16">&nbsp;&nbsp;<input type="text" name="searchbox" value="输入关键词，快速搜索本论坛" size="30" class="altbg2" onmouseover="this.focus()" onfocus="this.select()">
<select name="stype"><option value="" selected>全文</option><option value="1">标题</option></select>
<input name="button" type="button" style="height: 1.8em" onclick="window.open('search.php?srchtype=qihoo&amp;srchtxt='+findobj('searchbox').value+'&amp;stype='+findobj('stype').value+'&amp;searchsubmit=yes');" value="搜帖">
</td></tr>
<tr><td>
<? if($qihoo_links['topics']) { ?>
<span class="bold">论坛专题</span>&nbsp;
<? if(is_array($qihoo_links['topics'])) { foreach($qihoo_links['topics'] as $url) { ?>
<?=$url?> &nbsp;
<? } } } ?>
</td><td align="right">
<? if($customtopics) { ?>
<span class="bold">用户专题</span>&nbsp;&nbsp;<?=$customtopics?> [<a href="###" onclick="window.open('misc.php?action=customtopics', '', 'width=320,height=450,resizable=yes,scrollbars=yes');">编辑</a>]
<? } ?>
</td></tr>
</table>
</td></tr>
<? } ?>
</table><br></div>
<? } if(!empty($newpmexists)) { ?>
<div class="maintable">
<? include template('pmprompt'); ?>
</div>
<? } if(!empty($advlist['text'])) { ?>
<div class="maintable"><table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder"><?=$advlist['text']?></table><br></div>
<? } if(is_array($forumlist)) { foreach($forumlist as $key => $forum) { if($forum['type'] == 'group' && $forumlist[($key + 1)]['type'] == 'forum') { if($key) { ?>
</tbody></table><br></div>
<? } ?>
<div class="maintable">
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td colspan="7" class="header"><table cellspacing="0" cellpadding="0" width="100%">
<tr class="smalltxt"><td class="bold"><a href="index.php?gid=<?=$forum['fid']?>"><?=$forum['name']?></a></td><td align="right">
<? if($forum['moderators']) { ?>
<font color="<?=HEADERTEXT?>">分类版主: <?=$forum['moderators']?></font> 
<? } ?>
<a href="###" onclick="toggle_collapse('category_<?=$forum['fid']?>');"><img id="category_<?=$forum['fid']?>_img" src="<?=IMGDIR?>/<?=$forum['collapseimg']?>" border="0"></a>
</td></tr></table></td></tr>
<tr class="category" align="center">
<td width="5%">&nbsp;</td>
<td width="51%">论坛</td>
<td width="5%">主题</td>
<td width="5%">帖数</td>
<td width="5%">今日</td>
<td width="13%">最后发表</td>
<td width="16%">版主</td>
</tr><tbody id="category_<?=$forum['fid']?>" style="<?=$collapse['category_'.$forum['fid']]?>">
<? } elseif($forum['permission']) { ?>
<tr>
<td class="altbg1" align="center"><?=$forum['folder']?></td>
<td class="altbg2" align="left" onMouseOver="this.className='altbg1'" onMouseOut="this.className='altbg2'">
<?=$forum['icon']?><a href="forumdisplay.php?fid=<?=$forum['fid']?>"><span class="bold"><?=$forum['name']?></span></a>
<br><span class="smalltxt"><?=$forum['description']?>
<? if(isset($forum['subforums'])) { ?>
<br><span class="bold">子论坛:</span> <?=$forum['subforums']?>
<? } ?>
</span></td>
<td class="altbg1" align="center"><?=$forum['threads']?></td>
<td class="altbg2" align="center"><?=$forum['posts']?></td>
<td class="altbg1" align="center"><?=$forum['todayposts']?></td>
<? if($forum['permission'] == 1) { ?>
<td class="altbg2" align="center"><span class="smalltxt"></span>私密论坛</span></td>
<? } else { if(is_array($forum['lastpost'])) { ?>
<td class="altbg2"><table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td align="right" class="smalltxt" title="标题: <?=$forum['lastpost']['subject']?>" nowrap>
<?=$forum['lastpost']['dateline']?><br>by 
<? if($forum['lastpost']['author']) { ?>
<?=$forum['lastpost']['author']?>
<? } else { ?>
匿名
<? } ?>
</td>
<td nowrap>&nbsp;<a href="redirect.php?tid=<?=$forum['lastpost']['tid']?>&goto=lastpost#lastpost"><img src="<?=IMGDIR?>/lastpost.gif" border="0"></a></td>
</tr></table></td>
<? } else { ?>
<td class="altbg2" align="center"><span class="smalltxt">从未</span></td>
<? } } ?>
<td class="altbg1" align="center" style="word-break: keep-all"><span class="smalltxt"><?=$forum['moderators']?></span></td></tr>
<? } } } if(empty($gid) && ($_DCACHE['forumlinks'] || $whosonlinestatus || $bdaystatus == 1 || $bdaystatus == 3)) { if(empty($forumlist)) { ?>
<br><br>
<? } else { ?>
</table><br></div><div class="maintable">
<? } ?>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<? } if(empty($gid)) { if($_DCACHE['forumlinks']) { ?>
<tr><td colspan="3" class="header"><a href="###" onclick="toggle_collapse('forumlinks');"><img id="forumlinks_img" src="<?=IMGDIR?>/<?=$linkcollapseimg?>" align="right" border="0"></a>
<span class="bold">联盟论坛</span></td></tr>
<tbody id="forumlinks" style="<?=$collapse['forumlinks']?>">
<? if(is_array($_DCACHE['forumlinks'])) { foreach($_DCACHE['forumlinks'] as $flink) { if($flink['type'] == 1) { ?>
<tr>
<td class="altbg1" width="5%" align="center" valign="middle"><img src="<?=IMGDIR?>/forumlink.gif"></td>
<td class="altbg2" width="77%" valign="middle" onMouseOver="this.className='altbg1'" onMouseOut="this.className='altbg2'"><?=$flink['content']?></td>
<td class="altbg2" width="18%" align="center" valign="middle"><img src="<?=$flink['logo']?>" border="0"></a></td>
</tr>
<? } elseif($flink['type'] == 2) { ?>
<tr>
<td class="altbg1" width="5%" align="center" valign="middle"><img src="<?=IMGDIR?>/forumlink.gif"></td>
<td class="altbg2" width="95%" colspan="2" valign="middle" style="word-break: keep-all" onMouseOver="this.className='altbg1'" onMouseOut="this.className='altbg2'"><?=$flink['content']?></td>
</tr>
<? } else { ?>
<tr>
<td class="altbg1" width="5%" align="center" valign="middle"><img src="<?=IMGDIR?>/forumlink.gif"></td>
<td class="altbg2" width="95%" colspan="2" valign="middle" style="word-break: keep-all" onMouseOver="this.className='altbg1'" onMouseOut="this.className='altbg2'"><?=$flink['content']?></td>
</tr>
<? } } } ?>
</tbody>
<? } if($bdaystatus == 1 || $bdaystatus == 3) { ?>
<tr><td colspan="3" class="header"><a href="###" onclick="toggle_collapse('birthdays');"><img id="birthdays_img" src="<?=IMGDIR?>/<?=$linkcollapseimg?>" align="right" border="0"></a>
<span class="bold">今日生日</span></td></tr>
<tbody id="birthdays" style="<?=$collapse['birthdays']?>">
<tr>
<td class="altbg1" width="5%" align="center" valign="middle"><img src="<?=IMGDIR?>/birthday.gif"></td>
<td class="altbg2" width="95%" colspan="2" valign="middle" style="word-break: keep-all" onMouseOver="this.className='altbg1'" onMouseOut="this.className='altbg2'">
<? if($_DCACHE['settings']['todaysbdays']) { ?>
<?=$_DCACHE['settings']['todaysbdays']?>
<? } else { ?>
今天没有过生日的用户
<? } ?>
</td></tr>
</tbody>
<? } if($whosonlinestatus) { if($detailstatus) { ?>
<tr class="header"><td colspan="3" class="smalltxt" style="font-weight: normal; color: <?=HEADERTEXT?>">
<a name="online"></a><a href="index.php?showoldetails=no#online"><img src="<?=IMGDIR?>/collapsed_no.gif" align="right" border="0">	</a>
<span class="bold"><a href="member.php?action=online">在线用户</a></span> -
&nbsp;<span class="bold"><?=$onlinenum?></span> 人在线 - <span class="bold"><?=$membercount?></span> 位会员(<span class="bold"><?=$invisiblecount?></span> 隐身),
<span class="bold"><?=$guestcount?></span> 位游客 | 最高纪录是 <span class="bold"><?=$onlineinfo['0']?></span> 于 <span class="bold"><?=$onlineinfo['1']?></span>.
</td></tr>
<? } else { ?>
<tr class="header"><td colspan="3" class="smalltxt" style="font-weight: normal; color: <?=HEADERTEXT?>">
<a name="online"></a><a href="index.php?showoldetails=yes#online"><img src="<?=IMGDIR?>/collapsed_yes.gif" align="right" border="0"></a>
<span class="bold"><a href="member.php?action=online">在线用户</a></span> -
&nbsp;共 <span class="bold"><?=$onlinenum?></span> 人在线 | 最高纪录是 <span class="bold"><?=$onlineinfo['0']?></span> 于 <span class="bold"><?=$onlineinfo['1']?></span>.
</td></tr>
<? } ?>
<tr><td class="altbg1" width="5%" align="center"><img src="<?=IMGDIR?>/online.gif"></td>
<td class="altbg2" colspan="2" onMouseOver="this.className='altbg1'" onMouseOut="this.className='altbg2'">
<table cellspacing="0" cellpadding="0" border="0" width="98%" align="center" class="smalltxt">
<tr><td colspan="7" valign="middle"><?=$_DCACHE['onlinelist']['legend']?></td></tr>
<? if($detailstatus) { ?>
<tr><td colspan="7"><hr noshade size="0" width="100%" color="<?=BORDERCOLOR?>" align="center"></td></tr><tr><td nowrap>
<? if($whosonline) { if(is_array($whosonline)) { foreach($whosonline as $key => $online) { if($key % 7 == 0) { ?>
</td></tr><tr><td width="15%" nowrap>
<? } else { ?>
</td><td width="15%" nowrap>
<? } ?>
<img src="images/common/<?=$online['icon']?>" align="absmiddle">
<a href="viewpro.php?uid=<?=$online['uid']?>" title="时间: <?=$online['lastactivity']?><?="\n"?>
操作: <?=$online['action']?>
<? if($online['fid']) { ?>
<?="\n"?>论坛: <?=$online['fid']?>
<? } ?>
"><?=$online['username']?></a>
<? } } } else { ?>
&nbsp; &nbsp; 当前只有游客或隐身会员在线
<? } } ?>
</td></tr></table></td></tr>
<? } } ?>
</table><br><br></div><div class="maintable"><table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" class="outertxt">
<tr><td align="center" class="smalltxt">
<img src="<?=IMGDIR?>/red_forum.gif" align="absmiddle">&nbsp; 有新帖的论坛&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
<img src="<?=IMGDIR?>/forum.gif" align="absmiddle">&nbsp; 无新帖的论坛</td></tr></table>
<? include template('footer'); ?>
