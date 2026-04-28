<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed">
<tr><td class="nav" width="90%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> &raquo; 论坛统计</td>
<td align="right" width="10%">&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>
</tr></table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td class="header">论坛统计
</td></tr><tr class="altbg1"><td>
<? include template('stats_navbar'); if($type == 'views') { ?>
<br><table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="95%" align="center" class="tableborder">
<tr class="altbg2"><td class="smalltxt"><span class="bold">星期流量:</span><br>
<table border="0" cellspacing="0" cellpadding="<?=TABLESPACE?>" width="655" class="smalltxt">
<?=$statsbar_week?>
</table></td></tr></table><br>
<br><table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="95%" align="center" class="tableborder">
<tr class="altbg2"><td class="smalltxt"><span class="bold">时段流量:</span><br>
<table border="0" cellspacing="0" cellpadding="<?=TABLESPACE?>" width="655" class="smalltxt">
<?=$statsbar_hour?>
</table></td></tr></table><br>
<? } elseif($type == 'agent') { ?>
<br><table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="95%" align="center" class="tableborder">
<tr class="altbg2"><td class="smalltxt"><span class="bold">操作系统:</span><br>
<table border="0" cellspacing="0" cellpadding="<?=TABLESPACE?>" width="655" class="smalltxt">
<?=$statsbar_os?>
</table></td></tr></table>

<br><table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="95%" align="center" class="tableborder">
<tr class="altbg2"><td class="smalltxt"><span class="bold">浏览器:</span><br>
<table border="0" cellspacing="0" cellpadding="<?=TABLESPACE?>" width="655" class="smalltxt">
<?=$statsbar_browser?>
</table></td></tr></table><br>
<? } elseif($type == 'posts') { ?>
<br><table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="95%" align="center" class="tableborder">
<tr class="altbg2"><td class="smalltxt"><span class="bold">每月新增帖子记录:</span><br>
<table border="0" cellspacing="0" cellpadding="<?=TABLESPACE?>" width="655" class="smalltxt">
<?=$statsbar_monthposts?>
</table></td></tr></table>

<br><table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="95%" align="center" class="tableborder">
<tr class="altbg2"><td class="smalltxt"><span class="bold">每日新增帖子记录:</span><br>
<table border="0" cellspacing="0" cellpadding="<?=TABLESPACE?>" width="655" class="smalltxt">
<?=$statsbar_dayposts?>
</table></td></tr></table><br>
<? } elseif($type == 'forumsrank') { ?>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="95%" align="center" class="tableborder">
<tr><td class="altbg1"><table border="0" cellspacing="0" cellpadding="<?=TABLESPACE?>" width="98%" align="center" class="smalltxt">
<tr align="center" class="bold">
<td class="altbg2" width="23%" colspan="2">主题 排行榜</td><td width="2%"></td>
<td class="altbg2" width="23%" colspan="2">发帖 排行榜</td><td width="2%"></td>
<td class="altbg2" width="23%" colspan="2">最近 30 天发帖 排行榜</td><td width="2%"></td>
<td class="altbg2" width="23%" colspan="2">最近 24 小时发帖 排行榜</td></tr>
<?=$forumsrank?>
</td></table></td></tr></table>

<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center" class="smalltxt">
<tr><td align="right">统计数据已被缓存，上次于 <?=$lastupdate?> 被更新，下次将于 <?=$nextupdate?> 进行更新</td></tr></table><br>
<? } elseif($type == 'threadsrank') { ?>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="95%" align="center" class="tableborder">
<tr><td class="altbg1"><table border="0" cellspacing="0" cellpadding="<?=TABLESPACE?>" width="99%" align="center" class="smalltxt">
<tr align="center" class="bold">
<td class="altbg2" colspan="2">被浏览最多的主题</td><td width="1%"></td>
<td class="altbg2" colspan="2">被回复最多的主题</td></tr>
<?=$threadsrank?>
</table></td></tr></table><br>
<? } elseif($type == 'postsrank') { ?>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="95%" align="center" class="tableborder">
<tr><td class="altbg1"><table border="0" cellspacing="0" cellpadding="<?=TABLESPACE?>" width="98%" align="center" class="smalltxt">
<tr align="center" class="bold">
<td class="altbg2" width="23%" colspan="2">发帖 排行榜</td><td width="2%"></td>
<td class="altbg2" width="23%" colspan="2">精华帖 排行榜</td><td width="2%"></td>
<td class="altbg2" width="23%" colspan="2">最近 30 天发帖 排行榜</td><td width="2%"></td>
<td class="altbg2" width="23%" colspan="2">最近 24 小时发帖 排行榜</td></tr>
<?=$postsrank?>
</td></table></td></tr></table>

<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center" class="smalltxt">
<tr><td align="right">统计数据已被缓存，上次于 <?=$lastupdate?> 被更新，下次将于 <?=$nextupdate?> 进行更新</td></tr></table><br>
<? } elseif($type == 'creditsrank') { ?>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="95%" align="center" class="tableborder">
<tr><td class="altbg1"><table border="0" cellspacing="0" cellpadding="<?=TABLESPACE?>" width="98%" align="center" class="smalltxt">
<tr align="center" class="bold">
<? if(is_array($extcredits)) { foreach($extcredits as $id => $credit) { ?>
<td class="altbg2" width="<?=$columnwidth?>" colspan="2"><?=$credit['title']?> 排行榜</td><td width="2%"></td>
<? } } ?>
<td class="altbg2" width="<?=$columnwidth?>" colspan="2">积分 排行榜</td>
</tr>
<?=$creditsrank?>
</td></table></td></tr></table>
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center" class="smalltxt">
<tr><td align="right">统计数据已被缓存，上次于 <?=$lastupdate?> 被更新，下次将于 <?=$nextupdate?> 进行更新</td></tr></table><br>
<? } elseif($type == 'modworks' && $uid) { ?>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="95%" align="center" class="tableborder">
<tr class="header"><td colspan="<?=$tdcols?>">管理统计 - <?=$member['username']?></td></tr>
<tr class="category" align=center><td width="8%">时间</td>
<? if(is_array($modactioncode)) { foreach($modactioncode as $key => $val) { ?>
<td width="<?=$tdwidth?>"><?=$val?></td>
<? } } ?>
</tr>
<? if(is_array($modactions)) { foreach($modactions as $day => $modaction) { ?>
<tr align="center">
<td class="altbg1"><span class="smalltxt"><?=$day?></span></td>
<? if(is_array($modactioncode)) { foreach($modactioncode as $key => $val) { if($modaction[$key]['posts']) { ?>
<td class="<?=$bgarray[$key]?>" title="帖子: <?=$modaction[$key]['posts']?>"><?=$modaction[$key]['count']?>
<? } else { ?>
<td class="<?=$bgarray[$key]?>">
<? } ?>
</td>
<? } } ?>
</tr>
<? } } ?>
<tr class="singleborder"><td colspan="<?=$tdcols?>"></td></tr>
<tr align="center">
<td class="altbg1">本月管理</td>
<? if(is_array($modactioncode)) { foreach($modactioncode as $key => $val) { ?>
<td class="<?=$bgarray[$key]?>" 
<? if($totalactions[$key]['posts']) { ?>
title="帖子: <?=$totalactions[$key]['posts']?>"
<? } ?>
><?=$totalactions[$key]['count']?></td>
<? } } ?>
</tr>
</table>
<table cellspacing="0" cellpadding="4" border="0" width="95%" align="center" class="smalltxt">
<tr><td align="right">月份: 
<? if(is_array($monthlinks)) { foreach($monthlinks as $link) { ?>
 &nbsp;<?=$link?>&nbsp; 
<? } } ?>
</td></tr></table>
<? } elseif($type == 'modworks') { ?>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="95%" align="center" class="tableborder">
<tr class="header"><td colspan="<?=$tdcols?>">管理统计 - 全体管理人员</td></tr>
<tr class="category" align=center><td width="8%">用户名</td>
<? if(is_array($modactioncode)) { foreach($modactioncode as $key => $val) { ?>
<td width="<?=$tdwidth?>"><?=$val?></td>
<? } } ?>
</tr>
<? if(is_array($members)) { foreach($members as $uid => $member) { ?>
<tr align="center">
<td class="altbg1"><a href="stats.php?type=modworks&before=<?=$before?>&uid=<?=$uid?>" title="查看详细管理统计"><?=$member['username']?></a></td>
<? if(is_array($modactioncode)) { foreach($modactioncode as $key => $val) { if($member[$key]['posts']) { ?>
<td class="<?=$bgarray[$key]?>" title="帖子: <?=$member[$key]['posts']?>"><span class="smalltxt"><?=$member[$key]['count']?></span>
<? } else { ?>
<td class="<?=$bgarray[$key]?>">
<? } ?>
</td>
<? } } ?>
</tr>
<? } } ?>
</table>
<table cellspacing="0" cellpadding="4" border="0" width="95%" align="center" class="smalltxt">
<tr><td align="right">月份: 
<? if(is_array($monthlinks)) { foreach($monthlinks as $link) { ?>
 &nbsp;<?=$link?>&nbsp; 
<? } } ?>
</td></tr></table>
<? } ?>
<br></td></tr></table>
<? include template('footer'); ?>
