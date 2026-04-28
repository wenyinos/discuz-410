<? if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>

<br><table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="95%" align="center" class="tableborder">
<tr align="center" class="altbg2" style="font: <?=SMFONTSIZE?> <?=SMFONT?>">
<? if(empty($type)) { ?>
<td class="altbg1" width="<?=$navwidth?>"><a href="stats.php">基本概况</a></td>
<? } else { ?>
<td class="altbg2" width="<?=$navwidth?>"><a href="stats.php">基本概况</a></td>
<? } if($statstatus) { ?>
<td <?=$navstyle['views']?> width="<?=$navwidth?>"><a href="stats.php?type=views">流量统计</a></td>
<? } if($statstatus) { ?>
<td <?=$navstyle['agent']?> width="<?=$navwidth?>"><a href="stats.php?type=agent">客户软件</a></td>
<? } if($statstatus) { ?>
<td <?=$navstyle['posts']?> width="<?=$navwidth?>"><a href="stats.php?type=posts">发帖量记录</a></td>
<? } ?>
<td <?=$navstyle['forumsrank']?> width="<?=$navwidth?>"><a href="stats.php?type=forumsrank">论坛排行</a></td>
<td <?=$navstyle['threadsrank']?> width="<?=$navwidth?>"><a href="stats.php?type=threadsrank">主题排行</a></td>
<td <?=$navstyle['postsrank']?> width="<?=$navwidth?>"><a href="stats.php?type=postsrank">发帖排行</a></td>
<td <?=$navstyle['creditsrank']?> width="<?=$navwidth?>"><a href="stats.php?type=creditsrank">积分排行</a></td>
<? if($oltimespan) { ?>
<td <?=$navstyle['onlinetime']?> width="<?=$navwidth?>"><a href="stats.php?type=onlinetime">在线时间</a></td>
<? } ?>
<td <?=$navstyle['team']?> width="<?=$navwidth?>"><a href="stats.php?type=team">管理团队</a></td>
<? if($modworkstatus) { ?>
<td <?=$navstyle['modworks']?> width="<?=$navwidth?>"><a href="stats.php?type=modworks">管理统计</a></td>
<? } ?>
</tr>
</table><br>