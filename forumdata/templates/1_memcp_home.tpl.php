<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); include template('memcp_navbar'); ?>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr class="smalltxt">
<td class="altbg1" align="center">
<span class="bold">您好 <?=$discuz_userss?> ，欢迎进入控制面板，这里提供相关的资料设定与论坛快捷功能等。</span>
<br><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="95%" align="center" class="tableborder">
<tr class="header">
<td colspan="5">帐号信息</td>
</tr>

<tr>
<td class="altbg1" width="15%">用户名:</td>
<td class="altbg2" width="35%"><?=$discuz_userss?>
<? if($member['nickname']) { ?>
 (<?=$member['nickname']?>)
<? } ?>
</td>
<td class="altbg1" width="15%">积分:</td>
<td class="altbg2" width="35%" colspan="2"><?=$credits?></td>
</tr>

<tr>
<td class="altbg1" width="15%" valign="top">用户组:</td>
<td class="altbg2" width="35%" valign="top"><?=$grouptitle?>
<? if($regverify == 1 && $groupid == 8) { ?>
&nbsp; [ <a href="member.php?action=emailverify">重新验证 Email 有效性</a> ]
<? } ?>
</td>
<td class="altbg1" width="15%" valign="top">头像:</td>
<? if($avatarshow && $avatar) { ?>
<td class="altbg2" width="17%" align="center"><?=$avatarshow?></td>
<td class="altbg2" width="18%" align="center"><?=$avatar?></td>
<? } else { ?>
<td class="altbg2" width="35%" colspan="2"><?=$avatarshow?><?=$avatar?>&nbsp;</td>
<? } ?>
</tr>
</table><br>
<? if($validating) { ?>
<form method="post" action="member.php?action=regverify">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="95%" align="center" class="tableborder">
<tr class="header">
<td colspan="4">账户审核</td>
</tr>
<tr>
<td colspan="4" class="category">管理员设置了新注册用户需要人工验证，您的帐号已提交过 <span class="bold"><?=$validating['submittimes']?></span> 次验证请求，目前尚未通过验证。</td>
</tr>
<tr>
<td class="altbg1" width="15%">当前状态:</td>
<td class="altbg2" width="35%" class="bold">
<? if($validating['status'] == 0) { ?>
等待管理人员进行审核
<? } elseif($validating['status'] == 1) { ?>
审核被拒绝，您可以修改注册原因后再次提交
<? } ?>
</td>
<td class="altbg1" width="15%" rowspan="4" valign="top">注册原因:</td>
<td class="altbg2" width="35%" rowspan="4" align="center"><textarea rows="4" cols="30" name="regmessagenew"><?=$validating['message']?></textarea></td>
</tr>
<tr>
<td class="altbg1" width="15%">审核管理员:</td>
<td class="altbg2" width="35%">
<? if($validating['admin']) { ?>
<a href="viewpro.php?username=<?=$validating['adminenc']?>"><?=$validating['admin']?></a>
<? } else { ?>
无
<? } ?>
</td>
</tr>
<tr>
<td class="altbg1" width="15%">审核时间:</td>
<td class="altbg2" width="35%">
<? if($validating['moddate']) { ?>
<?=$validating['moddate']?>
<? } else { ?>
无
<? } ?>
</td>
</tr>
<tr>
<td class="altbg1" width="15%" valign="top">管理员给您的留言:</td>
<td class="altbg2" width="35%">
<? if($validating['remark']) { ?>
<span class="bold"><?=$validating['remark']?></span>
<? } else { ?>
无
<? } ?>
</td>
</tr>
</table>
<? if($validating['status'] == 1) { ?>
<br><input type="submit" name="verifysubmit" value="提 &nbsp; 交"><br>
<? } ?>
</form>
<? } ?>
</td></tr></table><br>

<table border="0" cellspacing="0" cellpadding="0" width="<?=TABLEWIDTH?>" align="center">
<tr><td width="19%">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr><td valign="top" align="left">

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%" align="center" class="tableborder">
<tr>
<td class="header">好友列表</td>
</tr>
<tr><td class="altbg1">
<table border="0" cellspacing="<?=BORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="98%">
<tr>
<td class="altbg2" align="center" colspan="3" class="smalltxt"><span class="bold">在线</span></td>
</tr>
<? if(is_array($buddyonline)) { foreach($buddyonline as $buddy) { ?>
<tr class="altbg1" onMouseOver="this.className='altbg2'" onMouseOut="this.className='altbg1'">
<td width="15" align="center"><a href="pm.php?action=send&uid=<?=$buddy['uid']?>" target="_blank"><img src="<?=IMGDIR?>/buddy_sendpm.gif" border="0" alt="发送短消息"></a></td>
<td align="center"><a href="viewpro.php?uid=<?=$buddy['uid']?>" title="<?=$buddy['description']?>"><?=$buddy['username']?></a></td>
<td width="15" align="center"><a href="memcp.php?action=buddylist&delete[]=<?=$buddy['uid']?>&buddysubmit=yes"><img src="<?=IMGDIR?>/buddy_delete.gif" border="0" alt="删除该好友"></a></td></tr>
<? } } ?>
<tr>
<td class="altbg2" align="center" colspan="3" class="smalltxt"><span class="bold">离线</span></td>
</tr>
<? if(is_array($buddyoffline)) { foreach($buddyoffline as $buddy) { ?>
<tr class="altbg1" onMouseOver="this.className='altbg2'" onMouseOut="this.className='altbg1'">
<td width="15" align="center"><a href="pm.php?action=send&uid=<?=$buddy['uid']?>" target="_blank"><img src="<?=IMGDIR?>/buddy_sendpm.gif" border="0" alt="发送短消息"></a></td>
<td align="center"><a href="viewpro.php?uid=<?=$buddy['uid']?>" title="<?=$buddy['description']?>"><?=$buddy['username']?></a></td>
<td width="15" align="center"><a href="memcp.php?action=buddylist&delete[]=<?=$buddy['uid']?>&buddysubmit=yes"><img src="<?=IMGDIR?>/buddy_delete.gif" border="0" alt="删除该好友"></a></td></tr>
<? } } ?>
</table>

<tr>
<td class="altbg2" align="center"><a href="memcp.php?action=buddylist" class="bold">[编辑好友列表]</a></td>
</tr>

</table>
</form></td>

<td width="1%">&nbsp;</td>

<td align="right" valign="top" width="80%">
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%" class="tableborder">
<tr class="header">
<td colspan="3">最近的五条短消息</td>
</tr>
<tr class="category">
<td width="52%" align="center">标题</td>
<td width="23%" align="center">来自</td>
<td width="25%" align="center">时间</td>
</tr>
<? if($msgexists) { if(is_array($msglist)) { foreach($msglist as $message) { ?>
<tr>
<td class="altbg2" onMouseOver ="this.className='altbg1'" onMouseOut="this.className='altbg2'">
<table cellspacing="0" cellpadding="0" border="0" width="100%" style="table-layout: fixed; word-break: break-all">
<tr><td><a href="pm.php?action=view&pmid=<?=$message['pmid']?>" target="_blank"><?=$message['subject']?></a></td></tr></table></td>
<td class="altbg1" align="center"><?=$message['msgfrom']?></td>
<td class="altbg2" align="center" class="smalltxt"><?=$message['dateline']?></td>
</tr>
<? } } } else { ?>
<tr><td class="altbg1" colspan="4">目前收件箱中没有消息。
</td></tr>
<? } ?>
</table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%" class="tableborder">
<tr class="header">
<td colspan="5">最近被回复的五个订阅主题</td>
</tr>
<tr class="category">
<td width="52%" align="center">标题</td>
<td width="18%" align="center">论坛</td>
<td width="8%" align="center">回复</td>
<td width="22%" align="center">最后发表</td>
</tr>
<? if($subsexists) { if(is_array($subslist)) { foreach($subslist as $subs) { ?>
<tr>
<td class="altbg2" onMouseOver="this.className='altbg1'" onMouseOut="this.className='altbg2'">
<table cellspacing="0" cellpadding="0" border="0" width="100%" style="table-layout: fixed; word-break: break-all">
<tr><td><a href="viewthread.php?tid=<?=$subs['tid']?>"><?=$subs['subject']?></a></td></tr></table></td>
<td class="altbg1" align="center"><a href="forumdisplay.php?fid=<?=$subs['fid']?>"><?=$subs['name']?></a></td>
<td class="altbg2" align="center"><?=$subs['replies']?></td>
<td class="altbg1" align="center">
<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr align="right">
<td class="smalltxt" nowrap><?=$subs['lastpost']?><br>
by 
<? if($subs['lastposter']) { ?>
<a href="viewpro.php?username=<?=$subs['lastposterenc']?>"><?=$subs['lastposter']?></a>
<? } else { ?>
匿名
<? } ?>
</td><td nowrap>&nbsp;<a href="redirect.php?tid=<?=$subs['tid']?>&goto=lastpost<?=$highlight?>#lastpost"><img src="<?=IMGDIR?>/lastpost.gif" border="0"></a>
</td></tr></table></td>
</tr>
<? } } } else { ?>
<tr><td class="altbg1" colspan="6">目前没有被订阅的主题。</td></tr>
<? } ?>
</table><br><br>
</td></tr>
</table>
</td></tr>
</table>
<? include template('footer'); ?>
