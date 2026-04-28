<? if(!defined('IN_DISCUZ')) exit('Access Denied'); if($pmsound) { ?>
<bgsound src="images/sound/pm_<?=$pmsound?>.wav" border="0">
<? } ?>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td class="header">
<table cellspacing="0" cellpadding="0" border="0" width="100%" align="center">
<tr class="smalltxt"><td style="color: <?=HEADERTEXT?>" class="bold">您有 <?=$newpmnum?> 条新消息</td>
<td align="right"><a href="pm.php" target="_blank">[查看详情]</a> 
<a href="<?=$ignorelink?>">[不再提示]</a></td></tr></table>
</tr>
<? if($pmlist) { ?>
<tr>
<td class="altbg2" colspan="8" onMouseOver="this.className='altbg1'" onMouseOut="this.className='altbg2'">
<table cellspacing="0" cellpadding="0" border="0" width="98%" align="center">
<tr><td>
<table cellspacing="0" cellpadding="0" border="0" width="100%" align="center">
<? if(is_array($pmlist)) { foreach($pmlist as $pm) { ?>
<tr><td><li></li></td><td width="20%" nowrap><span class="bold">来自:</span> <a href="viewpro.php?uid=<?=$pm['msgfromid']?>"><?=$pm['msgfrom']?></a></td>
<td width="25%"><span class="bold" nowrap>标题:</span> <a href="pm.php?action=view&pmid=<?=$pm['pmid']?>" target="_blank"><?=$pm['subject']?></a></td>
<td width="55%"><span class="bold">内容:</span> <?=$pm['message']?></td></tr>
<? } } ?>
</table></td></tr></table></td></tr>
<? } ?>
</table><br>