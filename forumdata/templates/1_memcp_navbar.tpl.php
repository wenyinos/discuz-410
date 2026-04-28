<? if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>

<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed"> 
<tr><td class="nav" width="90%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> &raquo; 
<? if(empty($action)) { ?>
控制面板
<? } else { ?>
<a href="memcp.php">控制面板</a> &raquo; 
<? if($action == 'profile') { ?>
编辑个人资料
<? } elseif($action == 'credits') { ?>
积分交易
<? } elseif($action == 'usergroups') { ?>
公众用户组
<? } elseif($action == 'buddylist') { ?>
好友列表
<? } elseif($action == 'subscriptions') { ?>
订阅列表
<? } elseif($action == 'favorites') { ?>
收藏夹
<? } elseif($action == 'viewavatars') { ?>
论坛头像列表
<? } } ?>
</td><td align="right" width="10%">&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>
</tr></table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr align="center" class="smalltxt">
<? if(!$action) { ?>
<td class="altbg1" width="13%">控制面板首页</td>
<? } else { ?>
<td width="13%" class="altbg2"><a href="memcp.php">控制面板首页</a></td>
<? } if($action == 'profile') { ?>
<td class="altbg1" width="13%">编辑个人资料</td>
<? } else { ?>
<td width="13%" class="altbg2"><a href="memcp.php?action=profile">编辑个人资料</a></td>
<? } if($action == 'credits') { ?>
<td class="altbg1" width="13%">积分交易</td>
<? } else { ?>
<td width="13%" class="altbg2"><a href="memcp.php?action=credits">积分交易</a></td>
<? } if($action == 'usergroups') { ?>
<td class="altbg1" width="13%">公众用户组</td>
<? } else { ?>
<td width="13%" class="altbg2"><a href="memcp.php?action=usergroups">公众用户组</a></td>
<? } if($action == 'buddylist') { ?>
<td class="altbg1" width="13%">好友列表</td>
<? } else { ?>
<td width="13%" class="altbg2"><a href="memcp.php?action=buddylist">好友列表</a></td>
<? } if($action == 'subscriptions') { ?>
<td class="altbg1" width="13%">订阅列表</td>
<? } else { ?>
<td width="13%" class="altbg2"><a href="memcp.php?action=subscriptions">订阅列表</a></td>
<? } if($action == 'favorites') { ?>
<td class="altbg1" width="13%">收藏夹</td>
<? } else { ?>
<td width="13%" class="altbg2"><a href="memcp.php?action=favorites">收藏夹</a></td>
<? } ?>
<td class="altbg2"><a href="pm.php" target="_blank" width="13%">短消息</a></td>
</tr></table><br>
