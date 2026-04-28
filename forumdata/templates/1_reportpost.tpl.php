<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed"> 
<tr><td class="nav" width="90%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> <?=$navigation?> &raquo; 报告帖子</td>
<td align="right" width="10%">&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>        
</tr></table><br>

<form method="post" name="input" action="misc.php?action=report">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr class="header">
<td colspan="2">报告帖子</td>
</tr>

<tr>
<td class="altbg1" width="21%">用户名:</td>
<td class="altbg2"><?=$discuz_userss?> <span class="smalltxt">[<a href="<?=$link_logout?>">退出登录</a>]</span></td>
</tr>
<? if($reportpost == 1) { ?>
<input type="hidden" name="to[3]" value="yes">
<? } else { ?>
<tr>
<td class="altbg1" width="21%">报告给:</td>
<td class="altbg2">
<? if($reportpost == 3) { ?>
<input type="checkbox" name="to[1]" value="yes"> 论坛管理员 &nbsp;
<? } if($reportpost >= 2) { ?>
<input type="checkbox" name="to[2]" value="yes"> 超级版主 &nbsp;
<? } ?>
<input type="checkbox" name="to[3]" value="yes" checked> 版主 &nbsp;
</tr>
<? } ?>
<tr>
<td class="altbg1" valign="top" width="21%">我的意见:</td>
<td class="altbg2"><textarea rows="9" name="reason" style="width: 80%; word-break: break-all">
我对这个帖子有异议，特向您报告</textarea>
</tr>

</table><br>

<input type="hidden" name="tid" value="<?=$tid?>">
<input type="hidden" name="fid" value="<?=$fid?>">
<input type="hidden" name="pid" value="<?=$pid?>">
<input type="hidden" name="page" value="<?=$page?>">
<center><input type="submit" name="reportsubmit" value="报告帖子"></center>
</form>
<? include template('footer'); ?>
