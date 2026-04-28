<? if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr class="header"><td colspan="2">预览帖子</td></tr>
<tr class="altbg1">
<td rowspan="2" valign="top" width="20%"><span class="bold">
<? if($action == 'edit') { if($postinfo['authorid']) { ?>
<a href="viewpro.php?uid=<?=$postinfo['authorid']?>"><?=$postinfo['author']?></a>
<? } else { ?>
游客
<? } } else { if($discuz_uid) { ?>
<?=$discuz_userss?>
<? } else { ?>
游客
<? } } ?>
</span><br><br></td>
<td class="smalltxt"><?=$currtime?></td></tr>
<tr class="altbg1"><td>
<table height="100%" width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout: fixed; word-wrap: break-word">
<tr><td><span class="bold"><span class="smalltxt"><?=$subject_preview?></span></span><br><br><?=$message_preview?></td></tr></table>
</td></tr></table><br>