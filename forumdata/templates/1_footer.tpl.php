<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>

<br><br></div><a name="bottom"></a>
<div class="maintable">
<?php if(!empty($advlist['footerbanner'])) { ?>
<center><?=$advlist['footerbanner']?></center><br><br>
<?php } if(!empty($advlist['float']) || !empty($advlist['couplebanner'])) { ?>
<div align="left">
<script language="JavaScript" src="include/floatadv.js"></script>
<script language="JavaScript">
<?=$advlist['float']?>
<?=$advlist['couplebanner']?>
theFloaters.play();
</script>
</div>
<?php } ?>
</div>

<div class="maintable">
<table cellspacing="2" cellpadding="0" align="center" style="font-size: 11px; font-family: Tahoma, Arial"><tr>
<td align="right"><a href="http://www.alipay.com" target="_blank"><img src="<?=IMGDIR?>/alipay.gif" border="0" align="absmiddle" alt="&#26412;&#35770;&#22363;&#25903;&#20184;&#24179;&#21488;&#30001;&#25903;&#20184;&#23453;&#25552;&#20379;<?="\n"?>&#25658;&#25163;&#25171;&#36896;&#23433;&#20840;&#35802;&#20449;&#30340;&#20132;&#26131;&#31038;&#21306;"></a> &nbsp; </td><td>
Powered by <a href="http://www.discuz.net" target="_blank"><b>Discuz!</b></a> <b style="color:#FF9900"><?=$version?></b>
<?php if($boardlicensed) { ?>
<a href="http://www.discuz.com/index.php?action=certificate&host=<?=$_SERVER['HTTP_HOST']?>" target="_blank">Licensed</a>
<?php } ?>
&nbsp;&copy; 2001-2006 <a href="http://www.comsenz.com" target="_blank">Comsenz Inc.</a>
&nbsp;&copy; 2026 <a href="https://github.com/wenyinos/discuz-410" target="_blank">Wenyin Root</a> &middot; PHP 7.4
<?php updatesession(); if(debuginfo()) { ?>
<br>Processed in <?=$debuginfo['time']?> second(s), <?=$debuginfo['queries']?> queries
<?php if($gzipcompress) { ?>
, Gzip enabled
<?php } } ?>
</td></tr></table><br>
</div>

<div class="maintable">
<table cellspacing="0" cellpadding="1" width="100%" class="outertxt">
<tr><td>
<table cellspacing="0" cellpadding="<?=TABLESPACE?>" width="100%" class="smalltxt">
<tr class="altbg1"><td>所有时间为 GMT<?=$timenow['offset']?>, 现在时间是 <?=$timenow['time']?></td>
<td align="right"><a href="member.php?action=clearcookies" class="bold">清除 Cookies</a> - <a href="mailto: <?=$adminemail?>" class="bold">联系我们</a> - <a href="<?=$siteurl?>" target="_blank" class="bold"><?=$sitename?></a>
<?php if($archiverstatus) { ?>
 - <a href="archiver/" target="_blank" class="bold">Archiver</a>
<?php } if($wapstatus) { ?>
 - <a href="wap/" target="_blank" class="bold">WAP</a>
<?php } ?>
</td>
<?php if(!empty($stylejump)) { ?>
<td align="right" width="1">
<select onchange="if(this.options[this.selectedIndex].value != '') {
var thisurl = document.URL.replace(/[&?]styleid=.+?&sid=.+?$/i, '');
window.location=(thisurl.replace(/\#.+$/, '')+(thisurl.match(/\?/) ? '&' : '?')+'styleid='+this.options[this.selectedIndex].value+'&sid=<?=$sid?>') }">
<option value="">界面风格</option>
<option value="">----------</option>
<?php if(is_array($stylejump)) { foreach($stylejump as $id => $name) { ?>
<option value="<?=$id?>"><?=$name?></option>
<?php } } ?>
</select></td>
<?php } ?>
</tr>
<tr style="font-size: 0px; line-height: 0px; spacing: 0px; padding: 0px; <?=HEADERBGCODE?>"><td colspan="3">&nbsp;</td></tr></table>
</td></tr></table>
</div>
</center><br>
</body></html>
<?php output(); ?>
