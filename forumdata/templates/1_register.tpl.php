<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed"> 
<tr><td class="nav" width="90%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> &raquo; 注册</td>
<td align="right" width="10%">&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>        
</tr></table><br>
<? if($bbrules && !$rulesubmit) { ?>
<form name="bbrules" method="post" action="register.php">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<input type="hidden" name="referer" value="<?=$referer?>">

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr>
<td class="header">注册</td>
</tr>

<tr class="altbg1">
<td width="21%"><?=$bbrulestxt?></td>
</tr>

</table><br>
<center>
<input type="submit" name="rulesubmit" value="同 意" style="height: 23px">
<input type="button" name="return" value="不同意" style="height: 23px" onclick="javascript:history.go(-1);">
</center>
</form>

<script language="JavaScript">
var secs = 9;
var wait = secs * 1000;
document.bbrules.rulesubmit.value = "同 意(" + secs + ")";
document.bbrules.rulesubmit.disabled = true;
for(i = 1; i <= secs; i++) {
        window.setTimeout("update(" + i + ")", i * 1000);
}
window.setTimeout("timer()", wait);
function update(num, value) {
        if(num == (wait/1000)) {
                document.bbrules.rulesubmit.value = "同 意";
        } else {
                printnr = (wait / 1000)-num;
                document.bbrules.rulesubmit.value = "同 意(" + printnr + ")";
        }
}
function timer() {
        document.bbrules.rulesubmit.disabled = false;
        document.bbrules.rulesubmit.value = "同 意";
}
</script>
<? } else { ?>
<form method="post" name="register" action="register.php?regsubmit=yes" <?=$enctype?> onSubmit="this.regsubmit.disabled=true;">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<input type="hidden" name="referer" value="index.php?sid=<?=$sid?>">

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr>
<td colspan="2" class="header"><a href="member.php?action=credits&view=promotion_register" target="_blank"><img src="<?=IMGDIR?>/credits.gif" alt="查看积分策略说明" align="right" border="0"></a>注册 - 必填内容</td>
</tr>
<? if($seccodecheck) { ?>
<tr>
<td class="altbg1" width="21%">验证码:</td>
<td class="altbg2"><input type="text" name="seccodeverify" size="4" maxlength="4"> <img src="seccode.php" align="absmiddle"> <span class="smalltxt">请在空白处输入图片中的数字</span></td>
</tr>
<? } ?>
<tr>
<td class="altbg1" width="21%">用户名:</td>
<td class="altbg2"><input type="text" name="username" size="25" maxlength="25"> 
<input type="button" value="检查用户名" onclick="window.open('member.php?action=check&username='+this.form.username.value);">
</td>
</tr>

<tr>
<td class="altbg1">密码:</td>
<td class="altbg2"><input type="password" name="password" size="25"></td>
</tr>

<tr>
<td class="altbg1">确认密码:</td>
<td class="altbg2"><input type="password" name="password2" size="25"></td>
</tr>

<tr>
<td class="altbg1">Email:</td>
<td class="altbg2"><input type="text" name="email" size="25">
<? if($regverify == 1) { ?>
&nbsp; <span class="smalltxt">请确保信箱有效，我们将发送激活说明到这个地址</span>
<? } if($censoremail) { ?>
&nbsp; <span class="smalltxt">请不要使用以 <?=$censoremail?> 结尾的信箱</span>
<? } ?>
</td>
</tr>
<? if($fromuser) { ?>
<tr>
<td class="altbg1">推荐人:</td>
<td class="altbg2"><input type="text" name="fromuser" size="25" value="<?=$fromuser?>" disabled></td>
</tr>
<? } if(is_array($_DCACHE['fields_required'])) { foreach($_DCACHE['fields_required'] as $field) { ?>
<tr>
<td class="altbg1" width="21%"><?=$field['title']?>:
<? if($field['description']) { ?>
<br><span class="smalltxt"><?=$field['description']?></span>
<? } ?>
</td>
<td class="altbg2">
<? if($field['selective']) { ?>
<select name="field_<?=$field['fieldid']?>new">
<option value="">- 请选择 -</option>
<? if(is_array($field['choices'])) { foreach($field['choices'] as $index => $choice) { ?>
<option value="<?=$index?>" 
<? if($index == $member['field_'.$field['fieldid']]) { ?>
selected="selected"
<? } ?>
><?=$choice?></option>
<? } } ?>
</select>
<? } else { ?>
<input type="text" name="field_<?=$field['fieldid']?>new" size="25" value="<?=$member['field_'.$field['fieldid']]?>">
<? } if($field['unchangeable']) { ?>
&nbsp;<span class="smalltxt"><span class="bold">请认真填写本项目，一旦确定将不可修改</span></span>
<? } ?>
</td></tr>
<? } } if($regverify == 2) { ?>
<tr>
<td class="altbg1" valign="top">注册原因:</td>
<td class="altbg2"><textarea rows="4" cols="30" name="regmessage"></textarea></td>
</tr>
<? } ?>
<tr>
<td colspan="2" class="header">注册 - 选填内容</td>
</tr>

<tr>
<td class="altbg1">安全提问:</td>
<td class="altbg2"><select name="questionid">
<option value="0">无安全提问</option>
<option value="1">母亲的名字</option>
<option value="2">爷爷的名字</option>
<option value="3">父亲出生的城市</option>
<option value="4">您其中一位老师的名字</option>
<option value="5">您个人计算机的型号</option>
<option value="6">您最喜欢的餐馆名称</option>
<option value="7">驾驶执照的最后四位数字</option>
</select> <span class="smalltxt"><span class="bold">如果您启用安全提问，登录时需填入相应的项目才能登录</span></span>
</td>
</tr>

<tr>
<td class="altbg1">回答:</td>
<td class="altbg2"><input type="text" name="answer" size="25"></td>
</tr>

<tr><td colspan="2" class="singleborder">&nbsp;</td></tr>
<? if(is_array($_DCACHE['fields_optional'])) { foreach($_DCACHE['fields_optional'] as $field) { ?>
<tr>
<td class="altbg1" width="21%"><?=$field['title']?>:
<? if($field['description']) { ?>
<br><span class="smalltxt"><?=$field['description']?></span>
<? } ?>
</td>
<td class="altbg2">
<? if($field['selective']) { ?>
<select name="field_<?=$field['fieldid']?>new">
<option value="">- 请选择 -</option>
<? if(is_array($field['choices'])) { foreach($field['choices'] as $index => $choice) { ?>
<option value="<?=$index?>"><?=$choice?></option>
<? } } ?>
</select>
<? } else { ?>
<input type="text" name="field_<?=$field['fieldid']?>new" size="25">
<? } if($field['unchangeable']) { ?>
&nbsp;<span class="bold">请认真填写本项目，一旦确定将不可修改</span>
<? } ?>
</td></tr>
<? } } if($groupinfo['allownickname']) { ?>
<tr>
<td class="altbg1">昵称:</td>
<td class="altbg2"><input type="text" name="nickname" size="25">
</tr>
<? } ?>
<tr>
<td class="altbg1">性别:</td>
<td class="altbg2">
<input type="radio" name="gendernew" value="1"> 男 &nbsp; 
<input type="radio" name="gendernew" value="2"> 女 &nbsp; 
<input type="radio" name="gendernew" value="0" checked> 保密
</td></tr>

<tr>
<td class="altbg1">生日:</td>
<td class="altbg2">
<input type="text" name="year" size="4"> 年 
<select name="month">
<option value="">&nbsp;</option>
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
<option value="8">8</option>
<option value="9">9</option>
<option value="10">10</option>
<option value="11">11</option>
<option value="12">12</option>
</select> 月 
<select name="day">
<option value="">&nbsp;</option>
<?=$dayselect?>
</select> 日
</td></tr>

<tr>
<td class="altbg1">来自:</td>
<td class="altbg2"><input type="text" name="locationnew" size="25"></td>
</tr>

<tr>
<td class="altbg1">主页:</td>
<td class="altbg2"><input type="text" name="site" size="25"></td>
</tr>

<tr>
<td class="altbg1">QQ:</td>
<td class="altbg2"><input type="text" name="qq" size="25"></td>
</tr>

<tr>
<td class="altbg1">ICQ:</td>
<td class="altbg2"><input type="text" name="icq" size="25"></td>
</tr>

<tr>
<td class="altbg1">Yahoo:</td>
<td class="altbg2"><input type="text" name="yahoo" size="25"></td>
</tr>

<tr>
<td class="altbg1">MSN:</td>
<td class="altbg2"><input type="text" name="msn" size="25"></td>
</tr>

<tr>
<td class="altbg1">淘宝旺旺:</td>
<td class="altbg2"><input type="text" name="taobao" size="25"></td>
</tr>

<tr>
<td class="altbg1">支付宝账号:</td>
<td class="altbg2"><input type="text" name="alipay" size="25"></td>
</tr>

<tr>
<td class="altbg1" valign="top">自我介绍:</td>
<td class="altbg2"><textarea rows="5" cols="30" name="bio"></textarea></td>
</tr>

<tr>
<td colspan="2" class="header">注册 - 论坛个性化设置</td>
</tr>

<tr>
<td class="altbg1">界面风格:</td>
<td class="altbg2"><select name="styleidnew">
<option value="">- 使用默认 -</option>
<?=$styleselect?>
</select>
</td>
</tr>

<tr>
<td class="altbg1">每页主题数:</td>
<td class="altbg2"><select name="tppnew">
<option value="0">- 使用默认 -</option>
<option value="10">10</option>
<option value="20">20</option>
<option value="30">30</option>
</select></td>
</tr>

<tr>
<td class="altbg1">每页帖数:</td>
<td class="altbg2"><select name="pppnew">
<option value="0">- 使用默认 -</option>
<option value="5">5</option>
<option value="10">10</option>
<option value="15">15</option>
</select></td>
</tr>

<tr>
<td class="altbg1">时差设定:</td>
<td class="altbg2">
<select name="timeoffsetnew">
<option value="9999" selected="selected">- 使用默认 -</option>
<option value="-12">(GMT -12:00) Eniwetok, Kwajalein</option>
<option value="-11">(GMT -11:00) Midway Island, Samoa</option>
<option value="-10">(GMT -10:00) Hawaii</option>
<option value="-9">(GMT -09:00) Alaska</option>
<option value="-8">(GMT -08:00) Pacific Time (US &amp; Canada), Tijuana</option>
<option value="-7">(GMT -07:00) Mountain Time (US &amp; Canada), Arizona</option>
<option value="-6">(GMT -06:00) Central Time (US &amp; Canada), Mexico City</option>
<option value="-5">(GMT -05:00) Eastern Time (US &amp; Canada), Bogota, Lima, Quito</option>
<option value="-4">(GMT -04:00) Atlantic Time (Canada), Caracas, La Paz</option>
<option value="-3.5">(GMT -03:30) Newfoundland</option>
<option value="-3">(GMT -03:00) Brassila, Buenos Aires, Georgetown, Falkland Is</option>
<option value="-2">(GMT -02:00) Mid-Atlantic, Ascension Is., St. Helena</option>
<option value="-1">(GMT -01:00) Azores, Cape Verde Islands</option>
<option value="0">(GMT) Casablanca, Dublin, Edinburgh, London, Lisbon, Monrovia</option>
<option value="1">(GMT +01:00) Amsterdam, Berlin, Brussels, Madrid, Paris, Rome</option>
<option value="2">(GMT +02:00) Cairo, Helsinki, Kaliningrad, South Africa</option>
<option value="3">(GMT +03:00) Baghdad, Riyadh, Moscow, Nairobi</option>
<option value="3.5">(GMT +03:30) Tehran</option>
<option value="4">(GMT +04:00) Abu Dhabi, Baku, Muscat, Tbilisi</option>
<option value="4.5">(GMT +04:30) Kabul</option>
<option value="5">(GMT +05:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
<option value="5.5">(GMT +05:30) Bombay, Calcutta, Madras, New Delhi</option>
<option value="5.75">(GMT +05:45) Katmandu</option>
<option value="6">(GMT +06:00) Almaty, Colombo, Dhaka, Novosibirsk</option>
<option value="6.5">(GMT +06:30) Rangoon</option>
<option value="7">(GMT +07:00) Bangkok, Hanoi, Jakarta</option>
<option value="8">(GMT +08:00) Beijing, Hong Kong, Perth, Singapore, Taipei</option>
<option value="9">(GMT +09:00) Osaka, Sapporo, Seoul, Tokyo, Yakutsk</option>
<option value="9.5">(GMT +09:30) Adelaide, Darwin</option>
<option value="10">(GMT +10:00) Canberra, Guam, Melbourne, Sydney, Vladivostok</option>
<option value="11">(GMT +11:00) Magadan, New Caledonia, Solomon Islands</option>
<option value="12">(GMT +12:00) Auckland, Wellington, Fiji, Marshall Island</option>
</select></td>
</tr>

<tr>
<td class="altbg1">时间格式:</td>
<td class="altbg2"><input type="radio" value="0" name="timeformatnew" checked>默认 &nbsp; 
<input type="radio" value="1" name="timeformatnew">12 小时 &nbsp; 
<input type="radio" value="2" name="timeformatnew">24 小时</td>
</tr>

<tr>
<td class="altbg1">日期格式:<br><span class="smalltxt">例如 yyyy/mm/dd, mm/dd/yy</span></td>
<td class="altbg2"><input type="radio" value="0" name="dateformatnew" checked>默认 &nbsp; 
<input type="radio" value="1" name="dateformatnew">自定义: 
<input type="text" name="cdateformatnew" size="25"></td>
</tr>

<tr>
<td class="altbg1">短消息提示音:</td>
<td class="altbg2"><input type="radio" value="0" name="pmsoundnew">无 &nbsp; 
<input type="radio" value="1" name="pmsoundnew" checked><a href="images/sound/pm_1.wav">#1</a> &nbsp; 
<input type="radio" value="2" name="pmsoundnew"><a href="images/sound/pm_2.wav">#2</a> &nbsp; 
<input type="radio" value="3" name="pmsoundnew"><a href="images/sound/pm_3.wav">#3</a></td>
</tr>
<? if($groupinfo['allowcstatus']) { ?>
<tr>
<td class="altbg1" width="21%">自定义头衔:</td>
<td class="altbg2">
<input type="text" name="cstatus" size="25"></td>
</tr>
<? } ?>
<tr>
<td class="altbg1">其他选项:</td>
<td class="altbg2">
<? if($groupinfo['allowinvisible']) { ?>
<input type="checkbox" name="invisiblenew" value="1"> 在线列表中隐身<br>
<? } ?>
<input type="checkbox" name="showemail" value="1" checked="checked"> Email 地址可见<br>
<input type="checkbox" name="newsletter" value="1" checked="checked"> 同意接收论坛通知 (Email 或短消息)<br>
</tr>
<? if($groupinfo['allowavatar'] == 1) { ?>
<tr>
<td class="altbg1">头像:</td>
<td class="altbg2"><input type="button" value="论坛头像列表" onclick="this.form.action='?referer=memcp.php?action=viewavatars&regsubmit=yes';this.form.submit();"></td>
</tr>
<? } elseif($groupinfo['allowavatar'] == 2) { ?>
<tr>
<td class="altbg1">头像:</td>
<td class="altbg2"><input type="text" name="avatar" size="25">&nbsp;&nbsp;<input type="button" value="论坛头像列表" onclick="this.form.action='?referer=memcp.php?action=viewavatars&regsubmit=yes';this.form.submit();">
<br>宽: <input type="text" name="avatarwidth" size="1" value="*"> &nbsp; 高: <input type="text" name="avatarheight" size="1" value="*"></td>
</tr>
<? } elseif($groupinfo['allowavatar'] == 3) { ?>
<tr>
<td class="altbg1">头像:</td>
<td class="altbg2"><input type="text" name="avatar" size="25"> <input type="button" value="论坛头像列表" onclick="this.form.action='?referer=memcp.php?action=viewavatars&regsubmit=yes';this.form.submit();">
<br><input type="file" name="customavatar" size="25">
<br>宽: <input type="text" name="avatarwidth" size="1" value="*"> 高: <input type="text" name="avatarheight" size="1" value="*"></td>
</tr>
<? } if($groupinfo['maxsigsize']) { ?>
<tr>
<td class="altbg1" valign="top">个人签名
<? if($maxsigsize) { ?>
 (<?=$maxsigsize?> 字节以内)
<? } ?>
:<br><br>
<span class="smalltxt"><a href="faq.php?page=misc#1" target="_blank">Discuz! 代码</a> <span class="bold">
<? if($groupinfo['allowsigbbcode']) { ?>
可用
<? } else { ?>
禁用
<? } ?>
</span><br>
[img] 代码 <span class="bold">
<? if($groupinfo['allowsigimgcode']) { ?>
可用
<? } else { ?>
禁用
<? } ?>
</span>
</span></td>
<td class="altbg2"><textarea rows="4" cols="30" name="signature"></textarea></td>
</tr>
<? } ?>
</table><br>
<center><input type="submit" name="regsubmit" value="提 &nbsp; 交"></center>
</form>

<script language="JavaScript">
document.register.username.focus();
</script>
<? } include template('footer'); ?>
