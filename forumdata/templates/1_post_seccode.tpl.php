<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed"> 
<tr><td class="nav" width="90%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> <?=$navigation?>
<? if($action == 'newthread') { ?>
&nbsp;&raquo;&nbsp;发新话题
<? } elseif($action == 'reply') { ?>
&nbsp;&raquo;&nbsp;发表回复
<? } ?>
</td>
<td align="right" width="10%">&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>        
</tr></table><br>

<form name="input" method="<?=$request['method']?>" action="<?=$request['action']?>" <?=$enctype?>>
<?=$request['elements']?>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr class="header"><td colspan="2">请输入验证码以便最终发表</td></tr>
<tr><td class="altbg1" width="21%">验证码:</td>
<td class="altbg2"><input type="text" name="seccodeverify" size="4" maxlength="4">
<img src="seccode.php" align="absmiddle"> 请在空白处输入图片中的数字<br></td>
</tr></table>
<? if($allowpostattach) { include template('post_attachments'); } ?>
<br><center><input type="submit" value="提 &nbsp; 交"></center>
</form>

<script language="JavaScript">
document.input.seccodeverify.focus();
</script>
<? include template('footer'); ?>
