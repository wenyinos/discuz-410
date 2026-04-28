<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); include template('memcp_navbar'); if($action == 'buddylist') { ?>
<form method="post" action="memcp.php?action=buddylist">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr align="center" class="header">
<td>删?</td>
<td>用户名</td>
<td>备注</td>
<td>加入时间</td>
</tr>
<? if(is_array($buddylist)) { foreach($buddylist as $buddy) { ?>
<tr align="center">
<td class="altbg1" width="8%"><input type="checkbox" name="delete[]" value="<?=$buddy['buddyid']?>"></td>
<td class="altbg2" width="16%"><a href="viewpro.php?uid=<?=$buddy['buddyid']?>" target="_blank"><?=$buddy['username']?></a></td>
<td class="altbg1" width="60%"><input type="text" name="descriptionnew[<?=$buddy['buddyid']?>]" value="<?=$buddy['description']?>" style="width: 80%"></td>
<td class="altbg2" width="16%"><?=$buddy['dateline']?></td>
</tr>
<? } } ?>
<tr align="center">
<td class="altbg1" width="8%">新增:</td>
<td class="altbg2" width="16%"><input type="text" name="newbuddy" size="15"></td>
<td class="altbg1" width="60%"><input type="text" name="newdescription" style="width: 80%"></td>
<td class="altbg2" width="16%">&nbsp;</td>
</tr></table><br>
<center><input type="submit" name="buddysubmit" value="提 &nbsp; 交"></center>
</form>
<? } elseif($action == 'favorites') { ?>
<form method="post" action="memcp.php?action=favorites">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr align="center" class="header">
<td width="48"><input type="checkbox" name="chkall" class="header" onclick="checkall(this.form)">删?</td>
<td width="45%">标题</td>
<td>论坛</td>
<td>回复</td>
<td>最后发表</td>
</tr>
<? if($favlist) { if(is_array($favlist)) { foreach($favlist as $fav) { ?>
<tr>
<td class="altbg1" align="center"><input type="checkbox" name="delete[]" value="<?=$fav['tid']?>"></td>
<td class="altbg2" onMouseOver="this.className='altbg1'" onMouseOut="this.className='altbg2'"><a href="viewthread.php?tid=<?=$fav['tid']?>"><?=$fav['subject']?></a></td>
<td class="altbg1" align="center"><a href="forumdisplay.php?fid=<?=$fav['fid']?>"><?=$fav['name']?></a></td>
<td class="altbg2" align="center"><?=$fav['replies']?></td>
<td class="altbg1" align="center"><font class="smalltxt"><?=$fav['lastpost']?> by 
<? if($fav['lastposter']) { ?>
<a href="viewpro.php?username=<?=$fav['lastposterenc']?>"><?=$fav['lastposter']?></a>
<? } else { ?>
匿名
<? } ?>
</font></td>
</tr>
<? } } } else { ?>
<td class="altbg1" colspan="5">目前没有被收藏的主题。</td></tr>
<? } ?>
</table><br>
<center><input type="submit" name="favsubmit" value="提 &nbsp; 交"></center>
</form>
<? } elseif($action == 'subscriptions') { ?>
<form method="post" action="memcp.php?action=subscriptions">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr align="center" class="header">
<td width="48"><input type="checkbox" name="chkall" class="header" onclick="checkall(this.form)">删?</td>
<td width="45%" align="center">标题</td>
<td align="center">论坛</td>
<td align="center">回复</td>
<td align="center">最后发表</td>
</tr>
<? if($subslist) { if(is_array($subslist)) { foreach($subslist as $subs) { ?>
<tr>
<td class="altbg1" align="center"><input type="checkbox" name="delete[]" value="<?=$subs['tid']?>"></td>
<td class="altbg2" onMouseOver="this.className='altbg1'" onMouseOut="this.className='altbg2'"><a href="viewthread.php?tid=<?=$subs['tid']?>"><?=$subs['subject']?></a></td>
<td class="altbg1" align="center"><a href="forumdisplay.php?fid=<?=$subs['fid']?>"><?=$subs['name']?></a></td>
<td class="altbg2" align="center"><?=$subs['replies']?></td>
<td class="altbg1" align="center"><font class="smalltxt"><?=$subs['lastpost']?> by 
<? if($subs['lastposter']) { ?>
<a href="viewpro.php?username=<?=$subs['lastposterenc']?>"><?=$subs['lastposter']?></a>
<? } else { ?>
匿名
<? } ?>
</font></td>
</tr>
<? } } } else { ?>
<td class="altbg1" colspan="5">目前没有被订阅的主题。</td></tr>
<? } ?>
</table><br>
<center><input type="submit" name="subsubmit" value="提 &nbsp; 交"></center>
</form>
<? } elseif($action == 'viewavatars') { ?>
<form method="post" action="memcp.php?action=viewavatars">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center">
<tr><td><?=$multipage?></td></tr>
</table>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder" style="table-layout: fixed">
<tr><td colspan="4" class="header">论坛头像列表</td></tr>
<?=$avatarlist?>
</table>

<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center">
<tr><td><?=$multipage?></td></tr></table><br>
<center><input type="submit" name="avasubmit" value="提 &nbsp; 交"></center>
</form>
<? } include template('footer'); ?>
