<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed"> 
<tr><td class="nav" width="90%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> &raquo; Blog <?=$navigation?></td>
<td align="right" width="10%">&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>        
</tr></table><br>

<table cellspacing="0" cellpadding="0" width="<?=TABLEWIDTH?>" align="center">
<tr><td width="20%" valign="top">

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%" class="tableborder">
<tr class="header"><td colspan="7">
<table cellspacing="0" cellpadding="0" width="100%" class="header"><tr>
<td width="30%" align="right"><a href="blog.php?uid=<?=$uid?>&starttime=<?=$calendar['pstarttime']?>&endtime=<?=$calendar['pendtime']?>">&laquo;</a></td>
<td width="40%" align="center" nowrap><a href="blog.php?uid=<?=$uid?>&starttime=<?=$calendar['pendtime']?>&endtime=<?=$calendar['nstarttime']?>"><?=$curtime?></a></td>
<td width="30%" align="left">
<? if($calendar['nstarttime'] < $timestamp) { ?>
<a href="blog.php?uid=<?=$uid?>&starttime=<?=$calendar['nstarttime']?>&endtime=<?=$calendar['nendtime']?>">&raquo;</a>
<? } ?>
&nbsp;</td></tr></table></td></tr>
<tr align="center" class="category"><td>日</td><td>一</td><td>二</td><td>三</td><td>四</td><td>五</td><td>六</td></tr>
<tr><td colspan="7" class="singleborder">&nbsp;</td></tr>
<?=$calendar['html']?>
</table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%" class="tableborder">
<tr class="header"><td align="center">栏目分类</td></tr>
<tr><td class="altbg1"><ul style="margin: 0 0 0.25em 1.5em">
<? if(empty($fid)) { ?>
<li><a href="blog.php?uid=<?=$uid?>" class="bold">Blog 首页</a>
<? } else { ?>
<li><a href="blog.php?uid=<?=$uid?>">Blog 首页</a>
<? } if(is_array($_DCACHE['blog']['forums']['data'])) { foreach($_DCACHE['blog']['forums']['data'] as $forum) { if((isset($blogtopic['fid']) && $blogtopic['fid'] == $forum['fid']) || $fid == $forum['fid']) { ?>
<li><a href="blog.php?uid=<?=$uid?>&fid=<?=$forum['fid']?>" class="bold"><?=$forum['name']?></a>
<? } else { ?>
<li><a href="blog.php?uid=<?=$uid?>&fid=<?=$forum['fid']?>"><?=$forum['name']?></a>
<? } } } ?>
</ul></td></tr></table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%" class="tableborder">
<tr class="header"><td align="center">最热的 5 篇文章</td></tr>
<tr><td class="altbg1"><ul style="margin: 0 0 0.25em 1.5em">
<? if(is_array($_DCACHE['blog']['hot']['data'])) { foreach($_DCACHE['blog']['hot']['data'] as $blog) { ?>
<li><a href="blog.php?tid=<?=$blog['tid']?>&starttime=<?=$starttime?>&endtime=<?=$endtime?>" title="<?=$blog['views']?> 查看, <?=$blog['replies']?> 评论"><?=$blog['subject']?></a>
<? } } ?>
</ul></td></tr></table><br>
<? if($allowuseblog) { ?>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%" class="tableborder">
<form method="get" action="post.php">
<tr class="header"><td align="center">发表 Blog</td></tr>
<tr><td class="altbg1" align="center">
<input type="hidden" name="action" value="newthread">
<input type="hidden" name="isblog" value="yes">
<br><select name="fid" style="width: 95%"><?=$forumselect?></select><br>
<br><input type="submit" value="提 &nbsp; 交"><br><br>
</td></tr></form></table><br>
<? } ?>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%" class="tableborder">
<tr class="header"><td align="center">搜索 Blog 文章</td></tr>
<tr><td class="altbg1"><form method="post" action="search.php">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<input type="hidden" name="srchuid" value="<?=$uid?>">
<input type="hidden" name="srchtype" value="blog">
<input type="hidden" name="orderby" value="dateline">
<table cellspacing="0" cellpadding="0" width="30%" align="center"><tr><td><br>
<input type="text" name="srchtxt" size="15" maxlength="40"><br>
<br><center><input type="submit" name="searchsubmit" value="提 &nbsp; 交"></center>
</td></tr></table></form></td></tr></table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%" class="tableborder">
<tr class="header"><td align="center">在线用户: <?=$onlinenum?></td></tr>
<tr><td align="center" class="altbg1"><br>
<? if($membercount) { ?>
<span title="在线用户: <?=$whosonline?>"><span class="bold"><?=$membercount?></span> 位会员, <span class="bold"><?=$guestcount?></span> 位游客</span>
<? } else { ?>
<span class="bold"><?=$membercount?></span> 位会员, <span class="bold"><?=$guestcount?></span> 位游客
<? } ?>
<br><br></td></tr>
</tr></table>

</td><td width="2%">&nbsp;</td><td width="78%" valign="top">
<? if($tid) { include template('blog_topic'); } else { include template('blog_list'); } ?>
</td></tr></table>
<? include template('footer'); ?>
