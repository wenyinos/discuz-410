<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed"> 
<tr><td class="nav" width="90%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> &raquo; 搜索</td>
<td align="right" width="10%">&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>        
</tr></table><br>

<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center">
<tr><td><?=$multipage?></td>
</tr></table>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder" style="table-layout: fixed; word-wrap: break-word">
<tr class="header">
<td align="center" width="43%">标题</td>
<td align="center" width="14%">论坛</td>
<td align="center" width="12%">作者</td>
<td align="center" width="6%">回复</td>
<td align="center" width="6%">查看</td>
<td align="center" width="19%">最后发表</td>
</tr>
<? if($threadlist) { if(is_array($threadlist)) { foreach($threadlist as $thread) { ?>
<tr>
<td class="altbg2" onMouseOver="this.className='altbg1'" onMouseOut="this.className='altbg2'">
<? if($thread['digest']) { ?>
<img src="<?=IMGDIR?>/digest.gif" align="absmiddle"> 精华<b>
<? echo substr('III', - $thread['digest']); ?>
</b>:&nbsp;
<? } elseif($thread['displayorder']) { ?>
<img src="<?=IMGDIR?>/pin.gif" align="absmiddle"> 置顶<b>
<? echo substr('III', - $thread['displayorder']); ?>
</b>:&nbsp;
<? } elseif($thread['poll']) { ?>
<img src="<?=IMGDIR?>/pollsmall.gif" align="absmiddle"> 投票:&nbsp;
<? } ?>
<?=$thread['attachment']?><a href="viewthread.php?tid=<?=$thread['tid']?>&highlight=<?=$index['keywords']?>" target="_blank" <?=$thread['highlight']?>><?=$thread['subject']?></a><?=$thread['multipage']?></td>
<td class="altbg1" align="center"><a href="forumdisplay.php?fid=<?=$thread['fid']?>"><?=$thread['forumname']?></a></td>
<td class="altbg2" align="center">
<? if($thread['authorid'] && $thread['author']) { ?>
<a href="viewpro.php?uid=<?=$thread['authorid']?>"><?=$thread['author']?></a>
<? } else { if($forum['ismoderator']) { ?>
<a href="viewpro.php?uid=<?=$thread['authorid']?>">匿名</a>
<? } else { ?>
匿名
<? } } ?>
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
匿名
<? } ?>
</font></td><td nowrap>&nbsp;<a href="redirect.php?tid=<?=$thread['tid']?>&goto=lastpost&highlight=<?=$index['keywords']?>#lastpost"><img src="<?=IMGDIR?>/lastpost.gif" border="0"></a>
</td></tr></table>
</td>
</tr>
<? } } } else { ?>
<tr><td class="altbg1" colspan="6">对不起，没有找到匹配结果。</td></tr>
<? } ?>
</table>

<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center">
<tr><td><?=$multipage?></td></tr>
</table>
<? include template('footer'); ?>
