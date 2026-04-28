<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); include template('memcp_navbar'); ?>
<form name="reg" method="post" action="memcp.php?action=profile" <?=$enctype?>>
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<? if(!$passport_status || $seccodecheck || $_DCACHE['fields_required']) { ?>
<tr>
<td colspan="2" class="header">编辑个人资料 - 必填内容</td>
</tr>
<? } if($seccodecheck) { ?>
<tr>
<td class="altbg1">验证码:</td>
<td class="altbg2"><input type="text" name="seccodeverify" size="4" maxlength="4"> <img src="seccode.php" align="absmiddle"> <span class="smalltxt">请在空白处输入图片中的数字</span></td>
</tr>
<? } if(!$passport_status) { ?>
<tr>
<td class="altbg1">原密码:</td>
<td class="altbg2"><input type="password" name="oldpassword" size="25"> <span class="smalltxt">如不需要更改密码或安全提问，此处请留空</span></td>
</tr>

<tr>
<td class="altbg1">新密码:</td>
<td class="altbg2"><input type="password" name="newpassword" size="25"> <span class="smalltxt">如不需要更改密码，此处请留空</span></td>
</tr>

<tr>
<td class="altbg1">确认新密码:</td>
<td class="altbg2"><input type="password" name="newpassword2" size="25"> <span class="smalltxt">如不需要更改密码，此处请留空</span></td>
</tr>

<tr>
<td class="altbg1" width="21%">Email:</td>
<td class="altbg2"><input type="text" name="emailnew" size="25" value="<?=$member['email']?>">
<? if($regverify == 1 && (($grouptype == 'member' && $adminid == 0) && $groupid == 8)) { ?>
 <span class="smalltxt"><span class="bold">!如更改地址，系统将修改您的密码并重新验证其有效性，请慎用</span></span>
<? } ?>
</td>
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
<select name="field_<?=$field['fieldid']?>new" 
<? if($member['field_'.$field['fieldid']] && $field['unchangeable']) { ?>
disabled
<? } ?>
>
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
<input type="text" name="field_<?=$field['fieldid']?>new" size="25" value="<?=$member['field_'.$field['fieldid']]?>" 
<? if($member['field_'.$field['fieldid']] && $field['unchangeable']) { ?>
disabled
<? } ?>
>
<? } if($field['unchangeable']) { ?>
&nbsp;<span class="smalltxt"><span class="bold">请认真填写本项目，一旦确定将不可修改</span></span>
<? } ?>
</td></tr>
<? } } ?>
<tr>
<td colspan="2" class="header">编辑个人资料 - 选填内容</td>
</tr>

<tr>
<td class="altbg1">安全提问:</td>
<td class="altbg2"><select name="questionidnew">
<? if($discuz_secques) { ?>
<option value="-1">保持原有的安全提问和答案</option>
<? } ?>
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
<td class="altbg2"><input type="text" name="answernew" size="25"> <span class="smalltxt">如您设置新的安全提问，请在此输入答案</span></td>
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
<select name="field_<?=$field['fieldid']?>new" 
<? if($member['field_'.$field['fieldid']] && $field['unchangeable']) { ?>
disabled
<? } ?>
>
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
<input type="text" name="field_<?=$field['fieldid']?>new" size="25" value="<?=$member['field_'.$field['fieldid']]?>" 
<? if($member['field_'.$field['fieldid']] && $field['unchangeable']) { ?>
disabled
<? } ?>
>
<? } if($field['unchangeable']) { ?>
&nbsp;<span class="smalltxt"><span class="bold">请认真填写本项目，一旦确定将不可修改</span></span>
<? } ?>
</td></tr>
<? } } if($allownickname) { ?>
<tr>
<td class="altbg1">昵称:</td>
<td class="altbg2"><input type="text" name="nicknamenew" size="25" value="<?=$member['nickname']?>"></td>
</tr>
<? } ?>
<tr>
<td class="altbg1" width="21%">性别:</td>
<td class="altbg2">
<input type="radio" name="gendernew" value="1" <?=$gendercheck['1']?>> 男 &nbsp; 
<input type="radio" name="gendernew" value="2" <?=$gendercheck['2']?>> 女 &nbsp; 
<input type="radio" name="gendernew" value="0" <?=$gendercheck['0']?>> 保密
</td></tr>

<tr>
<td class="altbg1">生日:</td>
<td class="altbg2">
<input type="text" name="year" size="4" value="<?=$bday['0']?>"> 年 
<select name="month">
<option value="" <?=$month['0']?>>&nbsp;</option>
<option value="1" <?=$month['1']?>>1</option>
<option value="2" <?=$month['2']?>>2</option>
<option value="3" <?=$month['3']?>>3</option>
<option value="4" <?=$month['4']?>>4</option>
<option value="5" <?=$month['5']?>>5</option>
<option value="6" <?=$month['6']?>>6</option>
<option value="7" <?=$month['7']?>>7</option>
<option value="8" <?=$month['8']?>>8</option>
<option value="9" <?=$month['9']?>>9</option>
<option value="10" <?=$month['10']?>>10</option>
<option value="11" <?=$month['11']?>>11</option>
<option value="12" <?=$month['12']?>>12</option>
</select> 月 
<select name="day">
<option value="">&nbsp;</option>
<?=$dayselect?>
</select> 日
</td></tr>

<tr>
<td class="altbg1" width="21%">来自:</td>
<td class="altbg2"><input type="text" name="locationnew" size="25" value="<?=$member['location']?>"></td>
</tr>

<tr>
<td class="altbg1" width="21%">主页:</td>
<td class="altbg2"><input type="text" name="sitenew" size="25" value="<?=$member['site']?>"></td>
</tr>

<tr>
<td class="altbg1" width="21%">QQ:</td>
<td class="altbg2"><input type="text" name="qqnew" size="25" value="<?=$member['qq']?>"></td>
</tr>

<tr>
<td class="altbg1" width="21%">ICQ:</td>
<td class="altbg2"><input type="text" name="icqnew" size="25" value="<?=$member['icq']?>"></td>
</tr>

<tr>
<td class="altbg1" width="21%">Yahoo:</td>
<td class="altbg2"><input type="text" name="yahoonew" size="25" value="<?=$member['yahoo']?>"></td>
</tr>

<tr>
<td class="altbg1" width="21%">MSN:</td>
<td class="altbg2"><input type="text" name="msnnew" size="25" value="<?=$member['msn']?>"></td>
</tr>

<tr>
<td class="altbg1" width="21%">淘宝旺旺:</td>
<td class="altbg2"><input type="text" name="taobaonew" size="25" value="<?=$member['taobao']?>"></td>
</tr>

<tr>
<td class="altbg1">支付宝账号:</td>
<td class="altbg2"><input type="text" name="alipaynew" size="25" value="<?=$member['alipay']?>"></td>
</tr>

<tr>
<td class="altbg1" valign="top" width="21%">自我介绍:</td>
<td class="altbg2"><textarea rows="5" cols="30" name="bionew"><?=$member['bio']?></textarea></td>
</tr>

<tr>
<td colspan="2" class="header">编辑个人资料 - 论坛个性化设置</td>
</tr>

<tr>
<td class="altbg1">界面风格:</td>
<td class="altbg2"><select name="styleidnew">
<option value="">- 使用默认 -</option>
<?=$styleselect?></select></td>
</tr>

<tr>
<td class="altbg1">每页主题数:</td>
<td class="altbg2"><select name="tppnew">
<option value="0" <?=$tppchecked['0']?>>- 使用默认 -</option>
<option value="10" <?=$tppchecked['10']?>>10</option>
<option value="20" <?=$tppchecked['20']?>>20</option>
<option value="30" <?=$tppchecked['30']?>>30</option>
</select></td>
</tr>

<tr>
<td class="altbg1">每页帖数:</td>
<td class="altbg2"><select name="pppnew">
<option value="0" <?=$pppchecked['0']?>>- 使用默认 -</option>
<option value="5" <?=$pppchecked['5']?>>5</option>
<option value="10" <?=$pppchecked['10']?>>10</option>
<option value="15" <?=$pppchecked['15']?>>15</option>
</select></td>
</tr>

<tr>
<td class="altbg1">时差设定:</td>
<td class="altbg2">
<select name="timeoffsetnew">
<option value="9999" <?=$toselect['9999']?>>- 使用默认 -</option>
<option value="-12" <?=$toselect['-12']?>>(GMT -12:00) Eniwetok, Kwajalein</option>
<option value="-11" <?=$toselect['-11']?>>(GMT -11:00) Midway Island, Samoa</option>
<option value="-10" <?=$toselect['-10']?>>(GMT -10:00) Hawaii</option>
<option value="-9" <?=$toselect['-9']?>>(GMT -09:00) Alaska</option>
<option value="-8" <?=$toselect['-8']?>>(GMT -08:00) Pacific Time (US &amp; Canada), Tijuana</option>
<option value="-7" <?=$toselect['-7']?>>(GMT -07:00) Mountain Time (US &amp; Canada), Arizona</option>
<option value="-6" <?=$toselect['-6']?>>(GMT -06:00) Central Time (US &amp; Canada), Mexico City</option>
<option value="-5" <?=$toselect['-5']?>>(GMT -05:00) Eastern Time (US &amp; Canada), Bogota, Lima, Quito</option>
<option value="-4" <?=$toselect['-4']?>>(GMT -04:00) Atlantic Time (Canada), Caracas, La Paz</option>
<option value="-3.5" <?=$toselect['-3.5']?>>(GMT -03:30) Newfoundland</option>
<option value="-3" <?=$toselect['-3']?>>(GMT -03:00) Brassila, Buenos Aires, Georgetown, Falkland Is</option>
<option value="-2" <?=$toselect['-2']?>>(GMT -02:00) Mid-Atlantic, Ascension Is., St. Helena</option>
<option value="-1" <?=$toselect['-1']?>>(GMT -01:00) Azores, Cape Verde Islands</option>
<option value="0"  <?=$toselect['0']?>>(GMT) Casablanca, Dublin, Edinburgh, London, Lisbon, Monrovia</option>
<option value="1" <?=$toselect['1']?>>(GMT +01:00) Amsterdam, Berlin, Brussels, Madrid, Paris, Rome</option>
<option value="2" <?=$toselect['2']?>>(GMT +02:00) Cairo, Helsinki, Kaliningrad, South Africa</option>
<option value="3" <?=$toselect['3']?>>(GMT +03:00) Baghdad, Riyadh, Moscow, Nairobi</option>
<option value="3.5" <?=$toselect['3.5']?>>(GMT +03:30) Tehran</option>
<option value="4" <?=$toselect['4']?>>(GMT +04:00) Abu Dhabi, Baku, Muscat, Tbilisi</option>
<option value="4.5" <?=$toselect['4.5']?>>(GMT +04:30) Kabul</option>
<option value="5" <?=$toselect['5']?>>(GMT +05:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
<option value="5.5" <?=$toselect['5.5']?>>(GMT +05:30) Bombay, Calcutta, Madras, New Delhi</option>
<option value="5.75" <?=$toselect['5.75']?>>(GMT +05:45) Katmandu</option>
<option value="6" <?=$toselect['6']?>>(GMT +06:00) Almaty, Colombo, Dhaka, Novosibirsk</option>
<option value="6.5" <?=$toselect['6.5']?>>(GMT +06:30) Rangoon</option>
<option value="7" <?=$toselect['7']?>>(GMT +07:00) Bangkok, Hanoi, Jakarta</option>
<option value="8" <?=$toselect['8']?>>(GMT +08:00) Beijing, Hong Kong, Perth, Singapore, Taipei</option>
<option value="9" <?=$toselect['9']?>>(GMT +09:00) Osaka, Sapporo, Seoul, Tokyo, Yakutsk</option>
<option value="9.5" <?=$toselect['9.5']?>>(GMT +09:30) Adelaide, Darwin</option>
<option value="10" <?=$toselect['10']?>>(GMT +10:00) Canberra, Guam, Melbourne, Sydney, Vladivostok</option>
<option value="11" <?=$toselect['11']?>>(GMT +11:00) Magadan, New Caledonia, Solomon Islands</option>
<option value="12" <?=$toselect['12']?>>(GMT +12:00) Auckland, Wellington, Fiji, Marshall Island</option>
</select></td>
</tr>

<tr>
<td class="altbg1">时间格式:</td>
<td class="altbg2"><input type="radio" value="0" name="timeformatnew" <?=$tfcheck['0']?>>默认 &nbsp; 
<input type="radio" value="1" name="timeformatnew" <?=$tfcheck['1']?>>12 小时 &nbsp; 
<input type="radio" value="2" name="timeformatnew" <?=$tfcheck['2']?>>24 小时</td>
</tr>

<tr>
<td class="altbg1">日期格式:<br><span class="smalltxt">例如 yyyy/mm/dd, mm/dd/yy</span></td>
<td class="altbg2">
<input type="radio" value="0" name="dateformatnew" <?=$dfcheck['0']?>>默认 &nbsp; 
<input type="radio" value="1" name="dateformatnew" <?=$dfcheck['1']?>>自定义: 
<input type="text" name="cdateformatnew" size="25" value="<?=$member['dateformat']?>"></td>
</tr>

<tr>
<td class="altbg1">短消息提示音:</td>
<td class="altbg2"><input type="radio" value="0" name="pmsoundnew" <?=$pscheck['0']?>>无 &nbsp; 
<input type="radio" value="1" name="pmsoundnew" <?=$pscheck['1']?>><a href="images/sound/pm_1.wav">#1</a> &nbsp; 
<input type="radio" value="2" name="pmsoundnew" <?=$pscheck['2']?>><a href="images/sound/pm_2.wav">#2</a> &nbsp; 
<input type="radio" value="3" name="pmsoundnew" <?=$pscheck['3']?>><a href="images/sound/pm_3.wav">#3</a></td>
</tr>
<? if($allowcstatus) { ?>
<tr>
<td class="altbg1" width="21%">自定义头衔:</td>
<td class="altbg2">
<input type="text" name="cstatusnew" size="25" value="<?=$member['customstatus']?>"></td>
</tr>
<? } ?>
<tr>
<td class="altbg1" valign="top">其他选项:</td>
<td class="altbg2">
<? if($allowinvisible) { ?>
<input type="checkbox" name="invisiblenew" value="1" <?=$invisiblechecked?>> 在线列表中隐身<br>
<? } ?>
<input type="checkbox" name="showemailnew" value="1" <?=$emailchecked?>> Email 地址可见<br>
<input type="checkbox" name="newsletternew" value="1" <?=$newschecked?>> 同意接收论坛通知 (Email 或短消息)<br>
</td></tr>
<? if($avatarshowstatus) { ?>
<tr>
<td class="altbg1" valign="top">天下秀:<br>如果您还没有注册天下秀形象，请点击形象下的“注册天下秀”进行注册，更换喜欢的着装并保存形象，然后在论坛中绑定您的天下秀账号，您还可以同时在其他 Discuz! 论坛系统中绑定同一个天下秀账号，您的形象将直接在论坛中显示出来</td>
<td class="altbg2"><?=$avatarshow?><br>
<? if($member['avatarshowid']) { ?>
<a href="http://reg.joyinter.net/userBinding.do?uid=<?=$discuz_uid?>&license=<?=$avatarshow_license?>&dis=true&url=<?=$boardurl?>api/avatarbind.php" target="_blank">[更改绑定]</a>
<a href="api/avatarbind.php?uid=<?=$discuz_uid?>&joycode=0" onclick="return confirm('解除天下秀绑定后您的形象将不能在论坛中显示，您确定要解除绑定吗?')">[解除绑定]</a>
<a href="http://www.joyinter.net" target="_blank">[免费换装]</a>
<? } else { ?>
<a href="http://reg.joyinter.net/avatar-register/register.jsp?source=<?=$avatarshow_license?>" target="_blank">[注册天下秀]</a>
<a href="http://www.joyinter.net" target="_blank">[免费换装]</a>
<a href="http://reg.joyinter.net/userBinding.do?uid=<?=$discuz_uid?>&license=<?=$avatarshow_license?>&dis=true&url=<?=$boardurl?>api/avatarbind.php" target="_blank">[绑定天下秀]</a>
<? } ?>
</td></tr>
<? } if($avatarshowstatus != 2) { if($allowavatar == 1) { ?>
<tr>
<td class="altbg1" valign="top">头像:</td>
<td class="altbg2"><input type="button" value="论坛头像列表" onclick="window.location=('memcp.php?action=viewavatars&sid=<?=$sid?>')"></td>
</tr>
<? } elseif($allowavatar == 2) { ?>
<tr>
<td class="altbg1" valign="top">头像:</td>
<td class="altbg2"><input type="text" name="avatarnew" size="25" value="<?=$member['avatar']?>"> <input type="button" value="论坛头像列表" onclick="window.location=('memcp.php?action=viewavatars&sid=<?=$sid?>')">
<br>宽: <input type="text" name="avatarwidthnew" size="1" value="*">(<?=$member['avatarwidth']?>) &nbsp; 高: <input type="text" name="avatarheightnew" size="1" value="*">(<?=$member['avatarheight']?>)</td>
</tr>
<? } elseif($allowavatar == 3) { ?>
<tr>
<td class="altbg1" valign="top">头像:</td>
<td class="altbg2">
<input type="text" name="avatarnew" size="25" value="<?=$member['avatar']?>"> <input type="button" value="论坛头像列表" onclick="window.location=('memcp.php?action=viewavatars&sid=<?=$sid?>')">
<br><input type="file" name="customavatar" size="25">
<br>宽: <input type="text" name="avatarwidthnew" size="1" value="*">(<?=$member['avatarwidth']?>) &nbsp; 高: <input type="text" name="avatarheightnew" size="1" value="*">(<?=$member['avatarheight']?>)</td>
</tr>
<? } } if($maxsigsize) { ?>
<tr>
<td class="altbg1" valign="top">个人签名
<? if($maxsigsize) { ?>
 (<?=$maxsigsize?> 字节以内)
<? } ?>
:<br><br>
<span class="smalltxt">
<a href="faq.php?page=misc#1" target="_blank">Discuz! 代码</a> <span class="bold">
<? if($allowsigbbcode) { ?>
可用
<? } else { ?>
禁用
<? } ?>
</span><br>
[img] 代码 <span class="bold">
<? if($allowsigimgcode) { ?>
可用
<? } else { ?>
禁用
<? } ?>
</span>
</span></td>
<td class="altbg2"><textarea rows="4" cols="30" name="signaturenew"><?=$member['signature']?></textarea></td>
</tr>
<? } ?>
</table><br>

<center><input type="submit" name="editsubmit" value="提 &nbsp; 交"></center>
</form>
<? include template('footer'); ?>
