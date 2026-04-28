<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed">
<tr><td class="nav" width="90%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> &raquo; 查看资料</td>
<td align="right" width="10%">&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>
</tr></table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td colspan="2" class="header"><?=$member['username']?> 的个人资料</td>
</tr><tr><td class="altbg2">

<table border="0" cellspacing="0" cellpadding="6" width="98%">
<tr><td width="70%">

<table border="0" cellspacing="0" cellpadding="<?=TABLESPACE?>" width="98%" style="table-layout: fixed">
<tr><td colspan="2" align="center" class="smalltxt">
<? if($member['allowuseblog']) { ?>
<a href="blog.php?uid=<?=$member['uid']?>">[ Blog ] </a> &nbsp;
<? } ?>
<a href="pm.php?action=send&uid=<?=$member['uid']?>" target="_blank">[ 发短消息 ]</a> &nbsp;
<a href="memcp.php?action=buddylist&newbuddyid=<?=$member['uid']?>&buddysubmit=yes">[ 加为好友 ]</a> &nbsp;
<a href="search.php?srchuid=<?=$member['uid']?>&srchfid=all&srchfrom=0&searchsubmit=yes">[ 搜索帖子 ]</a> &nbsp;
<? if($allowedituser || $allowbanuser) { if($adminid == 1) { ?>
<a href="admincp.php?action=members&username=<?=$member['usernameenc']?>&searchsubmit=yes&frames=yes" target="_blank">[ 编辑用户 ]</a> &nbsp;
<? } else { ?>
<a href="admincp.php?action=editmember&uid=<?=$member['uid']?>&membersubmit=yes&frames=yes" target="_blank">[ 编辑用户 ]</a> &nbsp;
<? } } if($member['adminid'] > 0 && $modworkstatus) { ?>
<a href="stats.php?type=modworks&uid=<?=$member['uid']?>">[ 工作统计 ]</a> &nbsp;
<? } ?>
<a href="###" onclick="history.go(-1);">[ 返回上一页 ]</a>

<br><br></td></tr>
<tr><td width="45%" class="bold">UID:</td><td width="55%"><?=$member['uid']?></td></tr>
<tr><td width="45%" class="bold">注册日期:</td><td width="55%"><?=$member['regdate']?></td></tr>
<? if($allowviewip) { ?>
<tr><td width="45%" class="bold">注册 IP:</td><td width="55%"><?=$member['regip']?> - <?=$member['regiplocation']?></td></tr>
<tr><td width="45%" class="bold">上次访问 IP:</td><td width="55%"><?=$member['lastip']?> - <?=$member['lastiplocation']?></td></tr>
<? } ?>
<tr><td width="45%" class="bold">上次访问:</td><td width="55%">
<? if($member['invisible'] && $adminid != 1) { ?>
隐身模式
<? } else { ?>
<?=$member['lastactivity']?>
<? } ?>
</td></tr>
<tr><td width="45%" class="bold">最后发表:</td><td width="55%"><?=$member['lastpost']?></td></tr>
<? if($pvfrequence) { ?>
<tr><td width="45%" class="bold">页面访问量:</td><td width="55%"><?=$member['pageviews']?></td></tr>
<? } if($oltimespan) { ?>
<tr><td width="45%" class="bold" valign="top">在线时间:</td><td width="55%">总计在线 <span class="bold"><?=$member['totalol']?></span> 小时, 本月在线 <span class="bold"><?=$member['thismonthol']?></span> 小时 
<? showstars(ceil(($member['totalol'] + 1) / 50)); ?>
<br>升级剩余时间 <span class="bold"><?=$member['olupgrade']?></span> 小时</td></tr>
<? } if($modforums) { ?>
<tr><td width="45%" class="bold">版主:</td><td width="55%"><?=$modforums?></td></tr>
<? } ?>
<tr><td colspan="2"><hr noshade size="0" width="95%" color="<?=BORDERCOLOR?>"></td></tr>
<? if($member['medals']) { ?>
<tr><td width="45%" class="bold">勋章:</td><td width="55%">
<? if(is_array($member['medals'])) { foreach($member['medals'] as $medal) { ?>
<img src="images/common/<?=$medal['image']?>" border="0" alt="<?=$medal['name']?>"> &nbsp;
<? } } ?>
</td></tr>
<? } ?>
<tr><td width="45%" class="bold">昵称:</td><td width="55%">
<? if($member['allownickname'] && $member['nickname']) { ?>
<?=$member['nickname']?>
<? } else { ?>
无
<? } ?>
</td></tr>
<tr><td width="45%" class="bold" valign="top">用户组:</td><td width="55%"><?=$member['grouptitle']?> 
<? showstars($member['groupstars']); if($member['maingroupexpiry']) { ?>
<br><span class="smalltxt">有效期至 <?=$member['maingroupexpiry']?></span>
<? } ?>
</td></tr>
<? if($extgrouplist) { ?>
<tr><td width="45%" class="bold" valign="top">扩展用户组:</td><td width="55%">
<? if(is_array($extgrouplist)) { foreach($extgrouplist as $extgroup) { ?>
<?=$extgroup['title']?>
<? if($extgroup['expiry']) { ?>
&nbsp;(有效期至 <?=$extgroup['expiry']?>)
<? } ?>
<br>
<? } } ?>
</td></tr>
<? } ?>
<tr><td width="45%" class="bold">发帖数级别:</td><td width="55%"><?=$member['ranktitle']?> 
<? showstars($member['rankstars']); ?>
</td></tr>
<tr><td width="45%" class="bold">阅读权限:</td><td width="55%"><?=$member['readaccess']?></td></tr>
<tr><td width="45%" class="bold">积分:</td><td width="55%"><?=$member['credits']?></td></tr>
<? if(is_array($extcredits)) { foreach($extcredits as $id => $credit) { ?>
<tr><td width="45%" class="bold"><?=$credit['title']?>:</td><td width="55%"><?=$member[extcredits.$id]?> <?=$credit['unit']?></td></tr>
<? } } ?>
<tr><td width="45%" class="bold">帖子:</td><td width="55%"><?=$member['posts']?> (占全部帖子的 <?=$percent?>%)</td></tr>
<tr><td width="45%" class="bold">平均每日发帖:</td><td width="55%"><?=$postperday?> 帖子</td></tr>
<tr><td width="45%" class="bold">精华帖:</td><td width="55%"><?=$member['digestposts']?> 帖子</td></tr>
<tr><td colspan="2" width="100%"><hr noshade size="0" width="95%" color="<?=BORDERCOLOR?>"></td></tr>
<tr><td width="45%" class="bold">性别:</td><td width="55%">
<? if($member['gender'] == 1) { ?>
男
<? } elseif($member['gender'] == 2) { ?>
女
<? } else { ?>
保密
<? } ?>
</td></tr>
<tr><td width="45%" class="bold">来自:</td><td width="55%"><?=$member['location']?>&nbsp;</td></tr>
<tr><td width="45%" class="bold">生日:</td><td width="55%"><?=$member['bday']?></td></tr>
<tr><td width="45%" valign="top" class="bold">自我介绍:</td><td width="55%"><?=$member['bio']?>&nbsp;</td></tr>
<? if(is_array($_DCACHE['fields'])) { foreach($_DCACHE['fields'] as $field) { ?>
<tr><td width="45%" class="bold"><?=$field['title']?>:</td><td width="55%">
<? if($field['selective']) { ?>
<?=$field['choices'][$member['field_'.$field['fieldid']]]?>
<? } else { ?>
<?=$member['field_'.$field['fieldid']]?>
<? } ?>
&nbsp;</td></tr>
<? } } ?>
</table>

</td><td width="30%" height="100%">
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="100%" class="tableborder">
<tr class="header"><td width="100%" colspan="2">个人信息</td></tr>
<tr class="altbg2"><td width="100%" align="center" colspan="2"><?=$member['avatar']?> <?=$member['avatarshow']?></td></tr>
<tr><td width="25%" class="altbg1">主页:</td><td width="80%" class="altbg2">
<table cellspacing="0" cellpadding="0" border="0" width="100%" style="table-layout: fixed">
<tr><td><a href="<?=$member['site']?>" target="_blank"><?=$member['site']?>&nbsp;</a></td></tr></table></td></tr>
<tr><td class="altbg1">Email:</td><td width="80%" class="altbg2">
<? if($member['showemail']) { ?>
<?=$member['email']?>
<? } else { ?>
&nbsp;
<? } ?>
</td></tr>
<tr><td class="altbg1">QQ:</td><td width="80%" class="altbg2">
<? if($member['qq']) { ?>
<a href="http://wpa.qq.com/msgrd?V=1&Uin=<?=$member['qq']?>&Site=<?=$bbname?>&Menu=yes" target="_blank"><img src="http://wpa.qq.com/pa?p=1:<?=$member['qq']?>:4" align="absmiddle" border="0" alt="QQ"><?=$member['qq']?></a>
<? } ?>
</td></tr>
<tr><td class="altbg1">ICQ:</td><td width="80%" class="altbg2"><?=$member['icq']?>&nbsp;</td></tr>
<tr><td class="altbg1">Yahoo:</td><td width="80%" class="altbg2"><?=$member['yahoo']?>&nbsp;</td></tr>
<tr><td class="altbg1">MSN:</td><td width="80%" class="altbg2"><?=$member['msn']?>&nbsp;</td></tr>
<tr><td class="altbg1">淘宝旺旺:</td><td width="80%" class="altbg2">
<? if($member['taobao']) { ?>
<script language="JavaScript">document.write('<a target="_blank" href="http://amos1.taobao.com/msg.ww?v=2&uid='+encodeURIComponent('<?=$member['taobaoas']?>')+'&s=2"><img src="http://amos1.taobao.com/online.ww?v=2&uid='+encodeURIComponent('<?=$member['taobaoas']?>')+'&s=2" alt="淘宝旺旺" border="0"><?=$member['taobao']?></a>&nbsp;');</script>
<? } else { ?>
&nbsp;
<? } ?>
</td></tr>
<tr><td class="altbg1">支付宝账号:</td><td width="80%" class="altbg2">
<? if($member['alipay']) { ?>
<a href="https://www.alipay.com/payto:<?=$member['alipay']?>?partner=20880020258585430156" target="_blank"><?=$member['alipay']?></a>
<? } else { ?>
&nbsp;
<? } ?>
</td></tr>
</table>

</td></tr></table></td></tr></table>
<? include template('footer'); ?>
