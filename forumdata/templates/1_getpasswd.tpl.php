<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed"> 
<tr><td class="nav" width="90%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> &raquo; 重置密码</td>
<td align="right" width="10%">&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>        
</tr></table><br>

<form method="post" action="member.php?action=getpasswd&uid=<?=$uid?>&id=<?=$id?>">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">

<tr class="header">
<td colspan="2">重置密码</td>
</tr>

<tr>
<td class="altbg1" width="21%">用户名:</td>
<td class="altbg2"><?=$member['username']?></td>
</tr>

<tr>
<td class="altbg1" width="21%">新密码:</td>
<td class="altbg2"><input type="password" name="newpasswd1" size="25"></td>
</tr>

<tr>
<td class="altbg1">确认新密码:</td>
<td class="altbg2"><input type="password" name="newpasswd2" size="25"><br>
</tr>

</table><br>

<center><input type="submit" name="getpwsubmit" value="提 &nbsp; 交"></center>
</form>
<? include template('footer'); ?>
