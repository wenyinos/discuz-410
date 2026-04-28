<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed"> 
<tr><td class="nav" width="90%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> <?=$navigation?> &raquo; 参与评分</td>
<td align="right" width="10%">&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>        
</tr></table><br>

<form method="post" action="misc.php?action=rate">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<input type="hidden" name="referer" value="<?=$referer?>">

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">

<tr class="header">
<td colspan="2">参与评分</td>
</tr>

<tr>
<td class="altbg1" width="21%">用户名:</td>
<td class="altbg2"><?=$discuz_userss?> <span class="smalltxt">[<a href="<?=$link_logout?>">退出登录</a>]</span></td>
</tr>
<tr>
<td class="altbg1" width="21%">作者:</td>
<td class="altbg2">
<? if($post['author'] && !$post['anonymous']) { ?>
<a href="viewpro.php?uid=<?=$post['authorid']?>"><?=$post['author']?></a>
<? } else { ?>
匿名
<? } ?>
</td>
</tr>

<tr>
<td class="altbg1" width="21%">标题:</td>
<td class="altbg2"><a href="viewthread.php?tid=<?=$tid?>"><?=$thread['subject']?></a></td>
</tr>

<tr>
<td class="altbg1" valign="top" width="21%">评分:</td>
<td class="altbg2">
<? if(is_array($ratelist)) { foreach($ratelist as $id => $options) { ?>
<select onchange="this.form.score<?=$id?>.value=this.value" style="width: 8em">
<option value="0"><?=$extcredits[$id]['title']?></option>
<option value="0">----</option>
<?=$options?>
</select> <input type="text" name="score<?=$id?>" value="0" size="3"> <?=$extcredits[$id]['unit']?> (今日还能评分 <?=$maxratetoday[$id]?> <?=$extcredits[$id]['unit']?>)<br>
<? } } ?>
</td></tr>
<? include template('topicadmin_reason'); ?>
</table><br>

<input type="hidden" name="tid" value="<?=$tid?>">
<input type="hidden" name="pid" value="<?=$pid?>">
<input type="hidden" name="page" value="<?=$page?>">
<center><input type="submit" name="ratesubmit" value="提 &nbsp; 交"></center>
</form>
<? include template('footer'); ?>
