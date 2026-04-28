<? if(!defined('IN_DISCUZ')) exit('Access Denied'); if($blognum) { ?>
<?=$multipage?>
<? if(is_array($bloglist)) { foreach($bloglist as $blog) { ?>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%" class="tableborder">
<tr class="header"><td width="15%" align="center" class="bold" nowrap><?=$blog['postedon']?></td><td class="bold"> &nbsp; <a href="blog.php?tid=<?=$blog['tid']?>&starttime=<?=$starttime?>&endtime=<?=$endtime?>"><?=$blog['subject']?></a></td></tr>
<tr><td colspan="2" class="altbg2"><?=$blog['karma']?><br><?=$blog['message']?><br><br></td></tr>
<tr class="category"><td colspan="2"><table cellspacing="0" cellpadding="0" width="100%">
<tr><td><?=$blog['dateline']?> - <a href="viewpro.php?uid=<?=$uid?>&starttime=<?=$starttime?>&endtime=<?=$endtime?>"><?=$blog['author']?></a> - <a href="blog.php?tid=<?=$blog['tid']?>&starttime=<?=$starttime?>&endtime=<?=$endtime?>"><?=$blog['views']?> 查看</a> - <a href="blog.php?tid=<?=$blog['tid']?>"><?=$blog['replies']?> 评论</a></td>
<td align="right"><a href="blog.php?uid=<?=$uid?>&fid=<?=$blog['fid']?>"><?=$blog['name']?></a></td></tr>
</table></td></tr></table><br><br>
<? } } } else { ?>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%" class="tableborder">
<tr class="header"><td><?=$bbname?> 提示信息</td></tr>
<tr><td align="center" class="altbg2"><br><br>指定的时间范围内没有 Blog 文章。<br><br><br></td></tr>
</table>
<? } ?>
