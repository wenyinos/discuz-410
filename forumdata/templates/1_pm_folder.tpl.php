<? if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>

<form method="post" action="pm.php?action=delete&folder=<?=$folder?>">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="99%" class="tableborder" style="table-layout: fixed; word-break: break-all">
<tr align="center" class="header"><td width="48"><input type="checkbox" name="chkall" class="header" onclick="checkall(this.form)">删?</td>
<td width="60%">标题</td>
<td width="15%">
<? if($folder != 'outbox' && $folder != 'track') { ?>
来自
<? } else { ?>
到
<? } ?>
</td>
<td width="25%">时间</td></tr>
<? if(is_array($pmlist)) { foreach($pmlist as $pm) { ?>
<tr>
<td class="altbg1" align="center">
<? if($folder == 'track' && !$pm['new']) { ?>
<input type="checkbox" disabled>
<? } else { ?>
<input type="checkbox" name="delete[]" value="<?=$pm['pmid']?>">
<? } ?>
</td>
<td class="altbg2" onMouseOver="this.className='altbg1'" onMouseOut="this.className='altbg2'"><a href="pm.php?action=view&folder=<?=$folder?>&pmid=<?=$pm['pmid']?>"><?=$pm['subject']?></a></td>
<td class="altbg1" align="center">
<? if($folder == 'inbox') { ?>
<a href="viewpro.php?uid=<?=$pm['msgfromid']?>"><?=$pm['msgfrom']?></a>
<? } else { ?>
<a href="viewpro.php?uid=<?=$pm['msgtoid']?>"><?=$pm['msgto']?></a>
<? } ?>
</td><td class="altbg2" align="center"><span class="smalltxt"><?=$pm['dateline']?></span></td>
</tr>
<? } } ?>
</table>

<table cellspacing="0" cellpadding="0" border="0" width="99%">
<tr><td><?=$multipage?></td></tr>
</table><br><center><input type="submit" value="提 &nbsp; 交"></center>
</form>