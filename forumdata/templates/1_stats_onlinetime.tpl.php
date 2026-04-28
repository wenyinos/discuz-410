<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed"> 
<tr><td class="nav" width="90%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> &raquo; 论坛统计</td>
<td align="right" width="10%">&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>        
</tr></table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td class="header">论坛统计
</td></tr><tr class="altbg1"><td>
<? include template('stats_navbar'); ?>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="95%" align="center" class="tableborder">
<tr><td class="altbg1"><table border="0" cellspacing="0" cellpadding="<?=TABLESPACE?>" width="99%" align="center" class="smalltxt">
<tr align="center" class="bold">
<td class="altbg2" colspan="2">总在线时间排行(小时)</td><td width="1%"></td>
<td class="altbg2" colspan="2">本月在线时间排行(小时)</td></tr>
<?=$onlines?>
</table></td></tr></table>

<table cellspacing="0" cellpadding="0" border="0" width="85%" align="center" class="smalltxt">
<tr><td align="right">统计数据已被缓存，上次于 <?=$lastupdate?> 被更新，下次将于 <?=$nextupdate?> 进行更新</td></tr></table><br>

<br><br></td></tr></table>
<? include template('footer'); ?>
