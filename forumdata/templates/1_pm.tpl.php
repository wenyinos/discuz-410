<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed"> 
<tr><td class="nav" width="90%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> &raquo; 短消息</td>
<td align="right" width="10%">&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>        
</tr></table><br>

<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center">
<tr><td width="200" valign="top">

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="98%" class="tableborder">
<tr class="header"><td colspan="3" align="center">快捷方式</td></tr>
<tr><td class="altbg1" width="20" align="center"><img src="<?=IMGDIR?>/foldersmall.gif"></td><td class="altbg2">
<? if($action == 'send') { ?>
<b>发送短消息</b>
<? } else { ?>
<a href="pm.php?action=send">发送短消息</a>
<? } ?>
</td></tr>
<? if($allowsearch) { ?>
<tr><td class="altbg1" width="20" align="center"><img src="<?=IMGDIR?>/foldersmall.gif"></td><td class="altbg2">
<? if($action == 'search') { ?>
<b><a href="pm.php?action=search">搜索短消息</a></b>
<? } else { ?>
<a href="pm.php?action=search">搜索短消息</a>
<? } ?>
</td></tr>
<? } ?>
<tr><td class="altbg1" width="20" align="center"><img src="<?=IMGDIR?>/foldersmall.gif"></td><td class="altbg2">
<? if($action == 'archive') { ?>
<b>导出短消息</b>
<? } else { ?>
<a href="pm.php?action=archive">导出短消息</a>
<? } ?>
</td></tr>
<tr><td class="altbg1" width="20" align="center"><img src="<?=IMGDIR?>/foldersmall.gif"></td><td class="altbg2">
<? if($action == 'ignore') { ?>
<b>忽略列表</b>
<? } else { ?>
<a href="pm.php?action=ignore">忽略列表</a>
<? } ?>
</td></tr>
</table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="98%" class="tableborder">
<tr class="header"><td colspan="3" align="center">文件夹</td></tr>
<tr><td class="altbg1" width="20" align="center"><img src="<?=IMGDIR?>/foldersmall.gif"></td><td class="altbg2">
<a href="pm.php?folder=inbox">
<? if($folder == 'inbox') { ?>
<b>收件箱</b>
<? } else { ?>
收件箱
<? } ?>
</a></td><td class="altbg1" width="20" align="center"><?=$pm_inbox?></td></tr>
<tr><td class="altbg1" width="20" align="center"><img src="<?=IMGDIR?>/foldersmall.gif"></td><td class="altbg2">
<a href="pm.php?folder=outbox">
<? if($folder == 'outbox') { ?>
<b>发件箱</b>
<? } else { ?>
发件箱
<? } ?>
</a></td><td class="altbg1" width="20" align="center"><?=$pm_outbox?></td></tr>
<tr><td class="altbg1" width="20" align="center"><img src="<?=IMGDIR?>/foldersmall.gif"></td><td class="altbg2" colspan="2">
<a href="pm.php?folder=track">
<? if($folder == 'track') { ?>
<b>消息跟踪</b>
<? } else { ?>
消息跟踪
<? } ?>
</a></td></tr>
</table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="98%" class="tableborder">
<tr class="header"><td colspan="3" align="center">空间使用</td></tr>
<tr class="altbg2"><td colspan="3" class="smalltxt">
<b>&raquo;</b> 共有短消息: <?=$pm_total?><br>
<b>&raquo;</b> 短消息上限: <?=$maxpmnum?>
</td></tr><tr class="altbg1"><td colspan="3">
<table cellspacing="2" cellpadding="0" border="0" width="100%" height="15" align="center">
<tr><td width="<?=$storage_percent?>" class="header" style="font-size: 1px">&nbsp;</td><td style="font-size: 1px">&nbsp;</td></tr>
</table>
</td></tr>
<tr class="altbg2">
<td width="33%" align="left" class="smalltxt">0%</td>
<td width="34%" align="center" class="smalltxt">50%</td>
<td width="33%" align="right" class="smalltxt">100%</td>
</tr></table><br>

</td><td align="right" valign="top">
<? if(empty($action)) { include template('pm_folder'); } elseif($action == 'view') { include template('pm_view'); } elseif($action == 'send') { include template('pm_send'); } elseif($action == 'search') { if($searchid) { include template('pm_search_result'); } else { include template('pm_search'); } } elseif($action == 'archive') { include template('pm_archive'); } elseif($action == 'ignore') { include template('pm_ignore'); } ?>
</td></tr></table>
<? include template('footer'); ?>
