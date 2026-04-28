<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed"> 
<tr><td class="nav" width="90%" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> &raquo; 论坛公告</td>
<td align="right" width="10%"><a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>        
</tr></table><br>

<br><table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center">
<tr><td><?=$multipage?></td></tr>
</table>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<? if(is_array($announcelist)) { foreach($announcelist as $announce) { ?>
<tr class="header"><td align="center" style="font-size: <?=FONTSIZE?>"><a name="<?=$announce['id']?>"></a><span class="bold">&lt;&lt; <?=$announce['subject']?> &gt;&gt;</span></td></tr>
<tr><td class="altbg2"><br><?=$announce['message']?><br><br></td></tr>
<tr><td class="altbg1"><table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr><td><span class="bold">作者: </span><a href="viewpro.php?username=<?=$announce['authorenc']?>"><?=$announce['author']?></a></td>
<td align="right"><span class="bold">起始时间: </span><?=$announce['starttime']?> &nbsp; &nbsp; 
<span class="bold">结束时间: </span>
<? if($announce['endtime']) { ?>
<?=$announce['endtime']?>
<? } else { ?>
不限
<? } ?>
</td></tr></table></td></tr>
<tr><td class="singleborder"></td></tr>
<? } } ?>
</table>

<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center">
<tr><td><?=$multipage?></td></tr>
</table>
<? include template('footer'); ?>
