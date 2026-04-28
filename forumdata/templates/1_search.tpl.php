<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed"> 
<tr><td class="nav" width="90%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> &raquo; 搜索</td>
<td align="right" width="10%">&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>        
</tr></table><br>

<form method="post" action="search.php" onSubmit="if(this.srchtype[0].value=='qihoo' && this.srchtype[0].checked) this.target='_blank'; else this.target=''; return true;">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr class="header"><td>关键字</td><td><a href="member.php?action=credits&view=search" target="_blank"><img src="<?=IMGDIR?>/credits.gif" alt="查看积分策略说明" align="right" border="0"></a>用户名</td></tr>

<tr class="smalltxt">
<td class="altbg2" width="60%">
<input type="text" name="srchtxt" size="25" maxlength="40">
&nbsp; &nbsp;关键字中可使用通配符 "*"<br><br>匹配多个关键字全部，可用空格或 "AND" 连接。如 win32 AND unix<br>匹配多个关键字其中部分，可用 "|" 或 "OR" 连接。如 win32 OR unix</td>

<td class="altbg2" width="40%"><input type="text" name="srchuname" size="25" maxlength="40">
<br><br>用户名中可使用通配符 "*"，如 *user*</td>
</tr>

<tr class="header"><td>搜索范围</td><td>排序类型</td></tr>

<tr class="smalltxt">
<td class="altbg2">

<table cellspacing="0" cellpadding="0" border="0" width="100%" class="smalltxt"><tr><td width="40%">
<select name="srchfid">
<option value="all">搜索所有开放的论坛</option>
<option value="">&nbsp;</option><?=$forumselect?></select><br><br>
<? if($qihoo_status) { ?>
<input type="radio" name="srchtype" value="qihoo" <?=$checktype['qihoo']?>> 奇虎全文 &nbsp; 
<? } ?>
<input type="radio" name="srchtype" value="title" <?=$checktype['title']?> <?=$disabled['title']?>> 标题搜索<br>
<input type="radio" name="srchtype" value="fulltext" <?=$disabled['fulltext']?>> 全文搜索 &nbsp; 
<input type="radio" name="srchtype" value="blog" <?=$disabled['blog']?>> 搜索 Blog
</td><td>
<select name="srchfrom">
<option value="0">全部时间</option>
<option value="86400">1 天前</option>
<option value="172800">2 天前</option>
<option value="432000">1 周前</option>
<option value="1296000">1 个月前</option>
<option value="5184000">3 个月前</option>
<option value="8640000">6 个月前</option>
<option value="31536000">1 年前</option>
</select><br><br>
<input type="radio" name="before" value="" checked> 之后<br>
<input type="radio" name="before" value="1"> 之前
</td></tr></table>
</td>

<td class="altbg2">
<select name="orderby">
<option value="lastpost" selected="selected">最后回复时间</option>
<option value="dateline">发布时间</option>
<option value="replies">回复数量</option>
<option value="views">浏览次数</option>
</select><br><br>
<input type="radio" name="ascdesc" value="asc"> 按升序排列<br>
<input type="radio" name="ascdesc" value="desc" checked> 按降序排列</td>
</tr>

</table><br>
<center><input type="submit" name="searchsubmit" value="提 &nbsp; 交"></center>
</form>
<? include template('footer'); ?>
