<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed"> 
<tr><td class="nav" width="90%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> &raquo; 提示信息</td>
<td align="right" width="10%">&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>        
</tr></table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">

<tr class="header"><td><?=$bbname?> 提示信息</td></tr>
<tr><td class="altbg2">

<br>您无权进行当前操作，这可能因以下原因之一造成:<br><br>
<ol>
<? if($show_message) { ?>
<li><b><?=$show_message?></b>
<? } ?>
<li>
<? if($discuz_uid) { ?>
您已经登录，但您的帐号或其所在的用户组无权访问当前页面。
<? } else { ?>
您还没有登录，请填写下面的登录表单后再尝试访问。
<? } ?>
</ol>

<br>
<? if($discuz_uid) { ?>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="60%" align="center" class="tableborder">

<tr>
<td class="altbg1" width="35%">用户名:</td>
<td class="altbg2"><?=$discuz_userss?> <a href="<?=$link_logout?>" class="smalltxt">[退出登录]</a></td>
</tr>

</table>
<? } else { ?>
<form name="login" method="post" action="logging.php?action=login">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<input type="hidden" name="referer" value="<?=$referer?>">
<input type="hidden" name="cookietime" value="2592000">

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="60%" align="center" class="tableborder">

<tr>
<td colspan="2" class="header">会员登录</td>
</tr>
<? if($seccodecheck) { ?>
<tr>
<td class="altbg1" width="35%">验证码:</td>
<td class="altbg2"><input type="text" name="seccodeverify" size="4" maxlength="4" tabindex="1"> <img src="seccode.php" align="absmiddle"> <span class="smalltxt">请在空白处输入图片中的数字</span></td>
</tr>
<? } ?>
<tr>
<td class="altbg1" width="35%">
<input type="radio" name="loginfield" value="username" checked onclick="document.login.username.focus();">用户名:<br>
<input type="radio" name="loginfield" value="uid" onclick="document.login.username.focus();">UID:</td>
<td class="altbg2"><input type="text" name="username" size="25" maxlength="40" tabindex="1"> &nbsp;<span class="smalltxt"><a href="register.php">立即注册</a></span></td>
</tr>

<tr>
<td class="altbg1">密码:</td>
<td class="altbg2"><input type="password" name="password" size="25" tabindex="2"> &nbsp;<span class="smalltxt"><a href="member.php?action=lostpasswd">忘记密码</a></span></td>
</tr>

<tr>
<td class="altbg1">安全提问:</td>
<td class="altbg2"><select name="questionid" tabindex="3">
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
<td class="altbg2"><input type="text" name="answer" size="25" tabindex="4"></td>
</tr>

</table><br>
<center><input type="submit" name="loginsubmit" value="会员登录"></center>
</form>
<? } ?>
<br><br>
</td></tr></table>
<? include template('footer'); ?>
