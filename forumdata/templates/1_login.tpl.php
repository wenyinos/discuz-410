<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed"> 
<tr><td class="nav" width="90%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> &raquo; 会员登录</td>
<td align="right" width="10%">&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>        
</tr></table><br>

<form method="post" name="login" action="logging.php?action=login">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<input type="hidden" name="referer" value="<?=$referer?>">

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">

<tr class="header">
<td colspan="2">会员登录</td>
</tr>

<tr>
<td class="altbg1">隐身登录:</td>
<td class="altbg2"><span class="smalltxt">
<select name="loginmode">
<option value=""> - 使用默认 -</option>
<option value="normal"> 正常模式</option>
<option value="invisible"> 隐身模式</option>
</select></span>
</td>
</tr>

<tr>
<td class="altbg1">界面风格:</td>
<td class="altbg2"><select name="styleid"><option value="">- 使用默认 -</option>
<?=$styleselect?>
</select>
</td>
</tr>

<tr>
<td class="altbg1">Cookie 有效期:</td>
<td class="altbg2"><span class="smalltxt">
<input type="radio" name="cookietime" value="315360000" <?=$cookietimecheck['315360000']?>> 永久 &nbsp;
<input type="radio" name="cookietime" value="2592000" <?=$cookietimecheck['2592000']?>> 一个月 &nbsp;
<input type="radio" name="cookietime" value="86400" <?=$cookietimecheck['86400']?>> 一天 &nbsp;
<input type="radio" name="cookietime" value="3600" <?=$cookietimecheck['3600']?>> 一小时 &nbsp;
<input type="radio" name="cookietime" value="0" <?=$cookietimecheck['0']?>> 浏览器进程 &nbsp; &nbsp;
<a href="faq.php?page=usermaint#2" target="_blank">[相关帮助]</a></span>
</td></tr>

<tr><td colspan="2" class="singleborder">&nbsp;</td></tr>
<? if($seccodecheck) { ?>
<tr>
<td class="altbg1" width="21%">验证码:</td>
<td class="altbg2"><span class="smalltxt"><input type="text" name="seccodeverify" size="4" maxlength="4" tabindex="1"> <img src="seccode.php" align="absmiddle"> 请在空白处输入图片中的数字</span></td>
</tr>
<? } ?>
<tr>
<td class="altbg1" width="21%">
<input type="radio" name="loginfield" value="username" checked onclick="document.login.username.focus();">用户名:<br>
<input type="radio" name="loginfield" value="uid" onclick="document.login.username.focus();">UID:</td>
<td class="altbg2"><span class="smalltxt"><input type="text" name="username" size="25" maxlength="40" tabindex="2"> &nbsp;<a href="register.php">立即注册</a></span></td>
</tr>

<tr>
<td class="altbg1">密码:</td>
<td class="altbg2"><span class="smalltxt"><input type="password" name="password" size="25" tabindex="3"> &nbsp;<a href="member.php?action=lostpasswd">忘记密码</a></span></td>
</tr>

<tr>
<td class="altbg1">安全提问:</td>
<td class="altbg2"><span class="smalltxt">
<select name="questionid" tabindex="4">
<option value="0">无安全提问</option>
<option value="1">母亲的名字</option>
<option value="2">爷爷的名字</option>
<option value="3">父亲出生的城市</option>
<option value="4">您其中一位老师的名字</option>
<option value="5">您个人计算机的型号</option>
<option value="6">您最喜欢的餐馆名称</option>
<option value="7">驾驶执照的最后四位数字</option>
</select> 如果您设置了安全提问，请在此输入正确的问题和回答</span>
</td></tr>

<tr>
<td class="altbg1">回答:</td>
<td class="altbg2"><input type="text" name="answer" size="25" tabindex="5"></td>
</tr>

</table><br>
<center><input type="submit" name="loginsubmit" value="提 &nbsp; 交"></center>
</form>

<script language="JavaScript">
document.login.username.focus();

var mydate = new Date();
var mytimestamp = parseInt(mydate.valueOf() / 1000);
if(Math.abs(mytimestamp - <?=$timestamp?>) > 86400) {
window.alert('注意:\n\n您本地计算机的时间设定与论坛时间相差超过 24 个小时，\n这可能会影响您的正常登录，请调整本地计算机设置。\n\n当前论坛时间是: <?=$thetimenow?>\n如果您认为论坛时间不准确，请与论坛管理员联系。');
}
</script>
<? include template('footer'); ?>
