<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed"> 
<tr><td class="nav" width="90%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> &raquo; 忘记密码</td>
<td align="right" width="10%">&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>        
</tr></table><br>

<form method="post" action="member.php?action=lostpasswd">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">

<tr class="header">
<td colspan="2">忘记密码</td>
</tr>

<tr>
<td class="altbg1" width="21%">用户名:</td>
<td class="altbg2"><input type="text" name="username" size="25"></td>
</tr>

<tr>
<td class="altbg1" width="21%">Email:</td>
<td class="altbg2"><input type="text" name="email" size="25"><br>
</tr>

<tr>
<td class="altbg1">安全提问:</td>
<td class="altbg2"><select name="questionid">
<option value="0">&nbsp;</option>
<option value="1">母亲的名字</option>
<option value="2">爷爷的名字</option>
<option value="3">父亲出生的城市</option>
<option value="4">您其中一位老师的名字</option>
<option value="5">您个人计算机的型号</option>
<option value="6">您最喜欢的餐馆名称</option>
<option value="7">驾驶执照的最后四位数字</option>
</select></td>
</tr>

<tr>
<td class="altbg1">回答:</td>
<td class="altbg2"><input type="text" name="answer" size="25"></td>
</tr>

</table><br>

<center><input type="submit" name="lostpwsubmit" value="提 &nbsp; 交"></center>
</form>
<? include template('footer'); ?>
