<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed"> 
<tr><td class="nav" width="90%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> &raquo; 论坛统计</td>
<td align="right" width="10%">&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>        
</tr></table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td class="header">论坛统计
</td></tr><tr class="altbg1"><td>
<? include template('stats_navbar'); if($team['admins']) { ?>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="95%" align="center" class="tableborder">
<tr><td colspan="10" class="header">管理员和超级版主</td></tr>
<tr align="center" class="category"><td>用户名</td>
<td>管理头衔</td>
<td>上次访问</td>
<td>离开天数</td>
<td>积分</td>
<td>帖子</td>
<td>最近 30 天发帖</td>
<? if($modworkstatus) { ?>
<td>本月管理</td>
<? } if($oltimespan) { ?>
<td>总计在线</td>
<td>本月在线</td>
<? } ?>
</tr>
<? if(is_array($team['admins'])) { foreach($team['admins'] as $uid) { ?>
<tr align="center" class="smalltxt">
<td class="altbg1"><a href="viewpro.php?uid=<?=$uid?>"><?=$team['members'][$uid]['username']?></a></td>
<td class="altbg2">
<? if($team['members'][$uid]['adminid'] == 1) { ?>
论坛管理员
<? } elseif($team['members'][$uid]['adminid'] == 2) { ?>
超级版主
<? } elseif($team['members'][$uid]['adminid'] == 3) { ?>
版主
<? } ?>
</td>
<td class="altbg1"><?=$team['members'][$uid]['lastactivity']?></td>
<td class="altbg2"><?=$team['members'][$uid]['offdays']?></td>
<td class="altbg1"><?=$team['members'][$uid]['credits']?></td>
<td class="altbg2"><?=$team['members'][$uid]['posts']?></td>
<td class="altbg1"><?=$team['members'][$uid]['thismonthposts']?></td>
<? if($modworkstatus) { ?>
<td class="altbg2"><a href="stats.php?type=modworks&uid=<?=$uid?>"><?=$team['members'][$uid]['modactions']?></a></td>
<? } if($oltimespan) { ?>
<td class="altbg1"><?=$team['members'][$uid]['totalol']?> 小时</td>
<td class="altbg2"><?=$team['members'][$uid]['thismonthol']?> 小时</td>
<? } ?>
</tr>
<? } } ?>
</table><br>
<? } if(is_array($team['categories'])) { foreach($team['categories'] as $category) { ?>
<br><table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="95%" align="center" class="tableborder">
<tr><td colspan="11" class="header"><a href="index.php?gid=<?=$category['fid']?>"><?=$category['name']?></a></td></tr>
<? if($oltimespan) { ?>
<tr align="center" class="category">
<td width="15%">论坛</td>
<td width="12%">用户名</td>
<td width="10%">管理头衔</td>
<td width="12%">上次访问</td>
<td width="7%">离开天数</td>
<td width="7%">积分</td>
<td width="7%">帖子</td>
<td width="7%">最近 30 天发帖</td>
<td width="7%">本月管理</td>
<td width="8%">总计在线</td>
<td width="8%">本月在线</td>
</tr>
<? } else { ?>
<tr align="center" class="category">
<td width="16%">论坛</td>
<td width="12%">用户名</td>
<td width="10%">管理头衔</td>
<td width="20%">上次访问</td>
<td width="8%">离开天数</td>
<td width="8%">积分</td>
<td width="8%">帖子</td>
<td width="8%">最近 30 天发帖</td>
<td width="8%">本月管理</td>
</tr>
<? } if(is_array($team['forums'][$category['fid']])) { foreach($team['forums'][$category['fid']] as $fid => $forum) { if(is_array($team['moderators'][$fid])) { foreach($team['moderators'][$fid] as $key => $uid) { ?>
<tr align="center" class="smalltxt">
<? if($key == 0) { ?>
<td class="altbg1" rowspan="<?=$forum['moderators']?>">
<? if($forum['type'] == 'group') { ?>
<a href="index.php?gid=<?=$fid?>">
<? } else { ?>
<a href="forumdisplay.php?fid=<?=$fid?>">
<? } ?>
<?=$forum['name']?></a></td>
<? } ?>
<td class="altbg2"><a href="viewpro.php?uid=<?=$uid?>">
<? if($forum['inheritedmod']) { ?>
<b><?=$team['members'][$uid]['username']?></b>
<? } else { ?>
<?=$team['members'][$uid]['username']?>
<? } ?>
</a></td>
<td class="altbg1">
<? if($team['members'][$uid]['adminid'] == 1) { ?>
论坛管理员
<? } elseif($team['members'][$uid]['adminid'] == 2) { ?>
超级版主
<? } elseif($team['members'][$uid]['adminid'] == 3) { ?>
版主
<? } ?>
</td>
<td class="altbg2"><?=$team['members'][$uid]['lastactivity']?></td>
<td class="altbg1"><?=$team['members'][$uid]['offdays']?></td>
<td class="altbg2"><?=$team['members'][$uid]['credits']?></td>
<td class="altbg1"><?=$team['members'][$uid]['posts']?></td>
<td class="altbg2"><?=$team['members'][$uid]['thismonthposts']?></td>
<td class="altbg1">
<? if($modworkstatus) { ?>
<a href="stats.php?type=modworks&uid=<?=$uid?>"><?=$team['members'][$uid]['modactions']?></a>
<? } else { ?>
N/A
<? } ?>
</td>
<? if($oltimespan) { ?>
<td class="altbg2"><?=$team['members'][$uid]['totalol']?> 小时</td>
<td class="altbg1"><?=$team['members'][$uid]['thismonthol']?> 小时</td>
<? } ?>
</tr>
<? } } } } ?>
</table>
<? } } ?>
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center" class="smalltxt">
<tr><td align="right">统计数据已被缓存，上次于 <?=$lastupdate?> 被更新，下次将于 <?=$nextupdate?> 进行更新</td></tr></table><br>

</td></tr></table>
<? include template('footer'); ?>
