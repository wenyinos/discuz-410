<? if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>

<br><br></div><a name="bottom"></a>
<div class="maintable">
<? if(!empty($advlist['footerbanner'])) { ?>
<center><?=$advlist['footerbanner']?></center><br><br>
<? } if(!empty($advlist['float']) || !empty($advlist['couplebanner'])) { ?>
<div align="left">
<script language="JavaScript" src="include/floatadv.js"></script>
<script language="JavaScript">
<?=$advlist['float']?>
<?=$advlist['couplebanner']?>
theFloaters.play();
</script>
</div>
<? } ?>
</div>

<div class="maintable">
<table cellspacing="2" cellpadding="0" align="center" style="font-size: 11px; font-family: Tahoma, Arial"><tr>
<td align="right"><a href="http://www.alipay.com" target="_blank"><img src="<?=IMGDIR?>/alipay.gif" border="0" align="absmiddle" alt="&#26412;&#35770;&#22363;&#25903;&#20184;&#24179;&#21488;&#30001;&#25903;&#20184;&#23453;&#25552;&#20379;<?="\n"?>&#25658;&#25163;&#25171;&#36896;&#23433;&#20840;&#35802;&#20449;&#30340;&#20132;&#26131;&#31038;&#21306;"></a> &nbsp; </td><td>
Powered by <a href="http://www.discuz.net" target="_blank"><b>Discuz!</b></a> <b style="color:#FF9900"><?=$version?></b>
<? if($boardlicensed) { ?>
<a href="http://www.discuz.com/index.php?action=certificate&host=<?=$_SERVER['HTTP_HOST']?>" target="_blank">Licensed</a>
<? } ?>
&nbsp;&copy; 2001-2006 <a href="http://www.comsenz.com" target="_blank">Comsenz Inc.</a>
<? updatesession(); if(debuginfo()) { ?>
<br>Processed in <?=$debuginfo['time']?> second(s), <?=$debuginfo['queries']?> queries
<? if($gzipcompress) { ?>
, Gzip enabled
<? } } ?>
</td></tr></table><br>
</div>

<div class="maintable">
<table cellspacing="0" cellpadding="1" width="100%" class="outertxt">
<tr><td>
<table cellspacing="0" cellpadding="<?=TABLESPACE?>" width="100%" class="smalltxt">
<tr class="altbg1"><td>所有时间为 GMT<?=$timenow['offset']?>, 现在时间是 <?=$timenow['time']?></td>
<td align="right"><a href="member.php?action=clearcookies" class="bold">清除 Cookies</a> - <a href="mailto: <?=$adminemail?>" class="bold">联系我们</a> - <a href="<?=$siteurl?>" target="_blank" class="bold"><?=$sitename?></a>
<? if($archiverstatus) { ?>
 - <a href="archiver/" target="_blank" class="bold">Archiver</a>
<? } if($wapstatus) { ?>
 - <a href="wap/" target="_blank" class="bold">WAP</a>
<? } ?>
</td>
<? if(!empty($stylejump)) { ?>
<td align="right" width="1">
<select onchange="if(this.options[this.selectedIndex].value != '') {
var thisurl = document.URL.replace(/[&?]styleid=.+?&sid=.+?$/i, '');
window.location=(thisurl.replace(/\#.+$/, '')+(thisurl.match(/\?/) ? '&' : '?')+'styleid='+this.options[this.selectedIndex].value+'&sid=<?=$sid?>') }">
<option value="">界面风格</option>
<option value="">----------</option>
<? if(is_array($stylejump)) { foreach($stylejump as $id => $name) { ?>
<option value="<?=$id?>"><?=$name?></option>
<? } } ?>
</select></td>
<? } ?>
</tr>
<tr style="font-size: 0px; line-height: 0px; spacing: 0px; padding: 0px; <?=HEADERBGCODE?>"><td colspan="3">&nbsp;</td></tr></table>
</td></tr></table>
</div>
</center><br>
</body></html>
<? output(); ?>
