<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed"> 
<tr><td class="nav" width="90%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> &raquo; 论坛统计</td>
<td align="right" width="10%">&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>        
</tr></table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td class="header">论坛统计
</td></tr><tr class="altbg1"><td>
<? include template('stats_navbar'); ?>
<br><table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="95%" align="center" class="tableborder">
<tr class="altbg2"><td class="smalltxt"><span class="bold">会员统计:</span><br>
注册会员: <span class="bold"><?=$members?></span> &nbsp;|&nbsp; 管理成员: <span class="bold"><?=$admins?></span> &nbsp;|&nbsp; 新会员: <span class="bold"><?=$lastmember?></span> &nbsp;|&nbsp; 今日论坛之星 <span class="bold"><?=$bestmem?></span> - 发帖数 <span class="bold"><?=$bestmemposts?></span> <br>
发帖会员: <span class="bold"><?=$mempost?></span> &nbsp;|&nbsp; 未发帖会员: <span class="bold"><?=$memnonpost?></span> &nbsp;|&nbsp; 发帖会员占总数 <span class="bold"><?=$mempostpercent?>%</span> &nbsp;|&nbsp; 平均每人发帖数: <span class="bold"><?=$mempostavg?></span> 
</td></tr></table>

<br><table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="95%" align="center" class="tableborder">
<tr class="altbg2"><td class="smalltxt"><span class="bold">论坛统计:</span><br>
论坛数: <span class="bold"><?=$forums?></span> &nbsp;|&nbsp; 主题数: <span class="bold"><?=$threads?></span> &nbsp;|&nbsp; 帖子数: <span class="bold"><?=$posts?></span> &nbsp;|&nbsp; 平均每个主题被回复次数: <span class="bold"><?=$threadreplyavg?></span> <br>
平均每日新增帖子数: <span class="bold"><?=$postsaddavg?></span> | 注册会员: <span class="bold"><?=$membersaddavg?></span> &nbsp;|&nbsp; 最近 24 小时新增帖子数: <span class="bold"><?=$postsaddtoday?></span> , 会员数: <span class="bold"><?=$membersaddtoday?></span><br>
最热门的论坛: <span class="bold"><a href="forumdisplay.php?fid=<?=$hotforum['fid']?>"><?=$hotforum['name']?></a></span> - 主题数 <span class="bold"><?=$hotforum['threads']?></span>, 帖子数 <span class="bold"><?=$hotforum['posts']?></span> &nbsp;|&nbsp; Discuz! 论坛活跃指数: <span class="bold"><?=$activeindex?></span><br>
</td></tr></table>
<? if($statstatus) { ?>
<br><table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="95%" align="center" class="tableborder">
<tr class="altbg2"><td class="smalltxt"><span class="bold">流量概况: </span><br>
总页面流量: <span class="bold"><?=$stats_total['hits']?></span> &nbsp;|&nbsp; 共计来访: <span class="bold"><?=$stats_total['visitors']?></span> 人次 - 会员: <span class="bold"><?=$stats_total['members']?></span>, 游客: <span class="bold"><?=$stats_total['guests']?></span> &nbsp;|&nbsp; 平均每人浏览: <span class="bold"><?=$pageviewavg?></span><br>
被访问最多的月份: <span class="bold"><?=$maxmonth_year?></span> 年 <span class="bold"><?=$maxmonth_month?></span> 月, 总页面流量 <span class="bold"><?=$maxmonth?></span> &nbsp;|&nbsp; 
时段: <span class="bold"><?=$maxhourfrom?></span> 到 <span class="bold"><?=$maxhourto?></span>, 总页面流量 <span class="bold"><?=$maxhour?></span>
</td></tr></table>

<br><table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="95%" align="center" class="tableborder">
<tr class="altbg2"><td class="smalltxt"><span class="bold">月份流量:</span><br>
<table border="0" cellspacing="0" cellpadding="<?=TABLESPACE?>" width="655" class="smalltxt">
<?=$statsbar_month?>
</table></td></tr></table>
<? } else { ?>
<br><table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="95%" align="center" class="tableborder">
<tr class="altbg2"><td class="smalltxt"><span class="bold">每月新增帖子记录:</span><br>
<table border="0" cellspacing="0" cellpadding="<?=TABLESPACE?>" width="655" class="smalltxt">
<?=$statsbar_monthposts?>
</table></td></tr></table>

<br><table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="95%" align="center" class="tableborder">
<tr class="altbg2"><td class="smalltxt"><span class="bold">每日新增帖子记录:</span><br>
<table border="0" cellspacing="0" cellpadding="<?=TABLESPACE?>" width="655" class="smalltxt">
<?=$statsbar_dayposts?>
</table></td></tr></table>
<? } ?>
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center" class="smalltxt">
<tr><td align="right">统计数据已被缓存，上次于 <?=$lastupdate?> 被更新，下次将于 <?=$nextupdate?> 进行更新</td></tr></table><br>

</td></tr></table>
<? include template('footer'); ?>
