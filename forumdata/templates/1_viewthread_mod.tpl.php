<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed"> 
<tr><td class="nav" width="90%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> <?=$navigation?> &raquo; 主题管理记录</td>
<td align="right" width="10%">&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>        
</tr></table><br><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr align="center" class="header"><td width="15%">操作者</td><td width="25%">时间</td><td width="30%">管理操作</td><td width="30%">有效期</td></tr>
<? if(is_array($loglist)) { foreach($loglist as $log) { ?>
<tr align="center">
<td class="altbg1">
<? if($log['uid']) { ?>
<a href="viewpro.php?uid=<?=$log['uid']?>" target="_blank"><?=$log['username']?></a>
<? } else { ?>
任务系统
<? } ?>
</td>
<td class="altbg2"><?=$log['dateline']?></td>
<td class="altbg1" <?=$log['status']?>><?=$modactioncode[$log['action']]?></td>
<td class="altbg2" <?=$log['status']?>>
<? if($log['expiration']) { ?>
<?=$log['expiration']?>
<? } elseif(in_array($log['action'], array('STK', 'HLT', 'DIG', 'CLS', 'OPN'))) { ?>
永久有效
<? } ?>
</td></tr>
<? } } ?>
</table><br>
<? include template('footer'); ?>
