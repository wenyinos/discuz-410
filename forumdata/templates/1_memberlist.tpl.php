<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed"> 
<tr><td class="nav" width="90%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> &raquo; 会员列表</td>
<td align="right" width="10%">&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>        
</tr></table><br>

<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center">
<tr><td><?=$multipage?></td></tr>
</table>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr class="header">
<td width="18%" align="center">用户名</td>
<td width="6%" align="center">UID</td>
<td width="5%" align="center">性别</td>
<td width="6%" align="center">主页</td>
<td width="16%" align="center">来自</td>
<td width="12%" align="center">注册日期</td>
<td width="19%" align="center">上次访问</td>
<td width="8%" align="center">帖子</td>
<td width="10%" align="center">积分</td>
</tr>
<? if(is_array($memberlist)) { foreach($memberlist as $member) { ?>
<tr class="smalltxt">
<td class="altbg1" align="center" nowrap><a href="viewpro.php?uid=<?=$member['uid']?>" class="bold"><?=$member['username']?></a>
<? if($member['nickname']) { ?>
<br>(<?=$member['nickname']?>)
<? } ?>
</td>
<td class="altbg2" align="center"><?=$member['uid']?></td>
<td class="altbg1" align="center">
<? if($member['gender'] == '1') { ?>
男
<? } elseif($member['gender'] == 2) { ?>
女
<? } else { ?>
&nbsp;
<? } ?>
</td>
<td class="altbg2" align="center">
<? if($member['site']) { ?>
<a href="http://<?=$member['site']?>" target="_blank"><img src="<?=IMGDIR?>/site.gif" border="0" alt="访问主页"></a>
<? } else { ?>
&nbsp;
<? } ?>
</td>
<td class="altbg1" align="center">
<? if($member['location']) { ?>
<?=$member['location']?>
<? } else { ?>
&nbsp;
<? } ?>
</td>
<td class="altbg2" align="center"><?=$member['regdate']?></td>
<td class="altbg1" align="center"><?=$member['lastvisit']?></td>
<td class="altbg2" align="center"><?=$member['posts']?></td>
<td class="altbg1" align="center"><?=$member['credits']?></td>
</tr>
<? } } ?>
<form method="post" action="member.php?action=list">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<tr class="smalltxt">
<td class="altbg2" colspan="9">
搜索 <input type="text" size="15" name="srchmem"> <input type="submit" value="提 &nbsp; 交">
&nbsp; &nbsp;<span class="bold">或</span> &nbsp; 
排序方式: 
<a href="member.php?action=list&order=credits">积分</a> - 
<a href="member.php?action=list&order=username">用户名</a> - 
<a href="member.php?action=list&order=gender">性别</a> - 
<a href="member.php?action=list&order=regdate">注册日期</a> - 
<a href="member.php?action=list&admins=yes">管理头衔</a>
</td></tr></form>
</table>

<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center">
<tr><td><?=$multipage?></td></tr>
</table>
<? include template('footer'); ?>
