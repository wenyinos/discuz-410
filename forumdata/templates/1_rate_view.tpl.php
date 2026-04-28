<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed"> 
<tr><td class="nav" width="90%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> <?=$navigation?> &raquo; 查看评分记录</td>
<td align="right" width="10%">&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>        
</tr></table><br><br>
<? if($thread['price'] <= 0 && $post['message'] != '') { ?>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr class="header"><td colspan="2">原帖内容</td></tr>
<tr class="altbg1">
<td rowspan="2" valign="top" width="20%"><span class="bold">
<? if($post['author'] && !$post['anonymous']) { ?>
<a href="viewpro.php?uid=<?=$post['authorid']?>"><?=$post['author']?></a>
<? } else { ?>
匿名
<? } ?>
</span><br><br></td>
<td class="smalltxt"><?=$post['dateline']?></td></tr>
<tr class="altbg1"><td>
<table height="100%" width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout: fixed; word-wrap: break-word">
<tr><td><span class="bold"><span class="smalltxt"><?=$post['subject']?></span></span><br><br><?=$post['message']?></td></tr></table></td></tr>
</table><br>
<? } ?>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr align="center" class="header">
<td width="15%">用户名</td><td width="30%">时间</td><td width="15%">积分</td><td width="40%">理由</td></tr>
</tr>
<? if(is_array($loglist)) { foreach($loglist as $log) { ?>
<tr align="center">
<td class="altbg1"><a href="viewpro.php?uid=<?=$log['uid']?>"><?=$log['username']?></a></td>
<td class="altbg2"><?=$log['dateline']?></td>
<td class="altbg1"><?=$extcredits[$log['extcredits']]['title']?> <span class="bold"><?=$log['score']?></span> <?=$extcredits[$log['extcredits']]['unit']?></td>
<td class="altbg2"><?=$log['reason']?></td>
</tr>
<? } } ?>
</table><br>
<? include template('footer'); ?>
