<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed"> 
<tr><td class="nav" width="90%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> <?=$navigation?> &raquo; 推荐给朋友</td>
<td align="right" width="10%">&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>        
</tr></table><br>

<form method="post" action="misc.php?action=emailfriend">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr class="header">
<td colspan="2">推荐给朋友</td>
</tr>

<tr>
<td class="altbg1" width="21%">您的名字:</td>
<td class="altbg2"><input type="text" name="fromname" size="25" maxlength="40" value="<?=$discuz_userss?>"></td>
</tr>

<tr>
<td class="altbg1">您的 Email:</td>
<td class="altbg2"><input type="text" name="fromemail" size="25" value="<?=$email?>"></td>
</tr>

<tr>
<td class="altbg1">接收者名字:</td>
<td class="altbg2"><input type="text" name="sendtoname" size="25"></td>
</tr>
<tr>
<td class="altbg1">接收者 Email:</td>
<td class="altbg2"><input type="text" name="sendtoemail" size="25"></td>
</tr>
<tr>
<td class="altbg1" valign="top">内容:</td>
<td class="altbg2"><textarea rows="9" name="message" style="width: 80%; word-break: break-all">
你好！我在 <?=$bbname?> 看到了这篇帖子，认为很有价值，特推荐给你。<?="\n"?><?="\n"?><?=$thread['subject']?><?="\n"?>地址 <?=$threadurl?><?="\n"?><?="\n"?>希望你能喜欢。</textarea></td>
</tr>
</table>

<br><center><input type="submit" name="sendsubmit" value="提 &nbsp; 交"></center>
<input type="hidden" name="tid" value="<?=$tid?>">
</form>
<? include template('footer'); ?>
