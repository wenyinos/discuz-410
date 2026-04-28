<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed"> 
<tr><td class="nav" width="90%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> &raquo; 精华区</td>
<td align="right" width="10%">&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>        
</tr></table><br>

<br><table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center">
<tr><td><?=$multipage?></td></tr></table>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder" style="table-layout: fixed; word-wrap: break-word">
<tr class="header">
<td align="center" width="43%">标题</td>
<td align="center" width="16%">论坛</td>
<td align="center" width="12%">作者</td>
<td align="center" width="7%" nowrap>回复</td>
<td align="center" width="7%" nowrap>查看</td>
<td align="center" width="15%">最后发表</td>
</tr>
<? if(is_array($threadlist)) { foreach($threadlist as $thread) { ?>
<tr>
<td class="altbg2" onMouseOver="this.className='altbg1'" onMouseOut="this.className='altbg2'">
[ <b>
<? echo substr('III', - $thread['digest']); ?>
</b> ] 
<? if($thread['displayorder']) { ?>
<img src="<?=IMGDIR?>/pin.gif" align="absmiddle"> 置顶<b>
<? echo substr('III', - $thread['displayorder']); ?>
</b>:&nbsp;
<? } elseif($thread['poll']) { ?>
<img src="<?=IMGDIR?>/pollsmall.gif" align="absmiddle"> 投票:&nbsp;
<? } ?>
<?=$thread['attachment']?><a href="viewthread.php?tid=<?=$thread['tid']?>&fpage=1&highlight=<?=$index['keywords']?>" target="_blank" <?=$thread['highlight']?>><?=$thread['subject']?></a><?=$thread['multipage']?></td>
<td class="altbg1" align="center"><a href="forumdisplay.php?fid=<?=$thread['fid']?>"><?=$thread['forumname']?></a></td>
<td class="altbg2" align="center">
<? if($thread['authorid']) { ?>
<a href="viewpro.php?uid=<?=$thread['authorid']?>"><?=$thread['author']?></a>
<? } else { ?>
游客
<? } ?>
<br><span class="smalltxt"><?=$thread['dateline']?></span></td>
<td class="altbg1" align="center"><?=$thread['replies']?></td>
<td class="altbg2" align="center"><?=$thread['views']?></td>
<td class="altbg1" align="center">
<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr align="right">
<td nowrap><font class="smalltxt"><?=$thread['lastpost']?><br>
by 
<? if($thread['lastposter']) { ?>
<a href="viewpro.php?username=<?=$thread['lastposterenc']?>"><?=$thread['lastposter']?></a>
<? } else { ?>
游客
<? } ?>
</font></td><td nowrap>&nbsp;<a href="redirect.php?tid=<?=$thread['tid']?>&goto=lastpost&highlight=<?=$index['keywords']?>#lastpost"><img src="<?=IMGDIR?>/lastpost.gif" border="0"></a>
</td></tr></table>
</td>
</tr>
<? } } ?>
<form method="post" action="digest.php">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<tr>
<td class="altbg2" colspan="6">关键字: <input type="text" size="15" name="keyword" value="<?=$keyword?>">
&nbsp; &nbsp; 作者: <input type="text" size="15" name="author" value="<?=$author?>">
&nbsp; &nbsp; 排序方式: <select name="order">
<option value="digest" <?=$ordercheck['digest']?>>级别</option>
<option value="replies" <?=$ordercheck['replies']?>>回复</option>
<option value="views" <?=$ordercheck['views']?>>查看</option>
<option value="dateline" <?=$ordercheck['dateline']?>>发布时间</option>
<option value="lastpost" <?=$ordercheck['lastpost']?>>最后发表</option>
</select>
&nbsp; &nbsp; <input type="submit" value="提 &nbsp; 交"></td>
</tr>

<tr>
<td class="altbg2" colspan="6">以下论坛精华区:<br>
<table cellspacing="0" cellpadding="0" border="0" width="100%">
<tr>
<? if(is_array($forumlist)) { foreach($forumlist as $key => $forum) { if($key % 4 == 0) { ?>
</td></tr><tr><td width="25%" nowrap>
<? } else { ?>
</td><td width="25%" nowrap>
<? } ?>
<input type="checkbox" name="forums[]" value="<?=$forum['fid']?>" <?=$forumcheck[$forum['fid']]?>> <?=$forum['name']?>
<? } } ?>
</tr></table>
</form></td></tr></table>

<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center">
<tr><td><?=$multipage?></td></tr></table>
<? include template('footer'); ?>
