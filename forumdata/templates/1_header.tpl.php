<? if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" >
<html>
<head>
<title><?=$bbname?> <?=$seotitle?> <?=$navtitle?> - powered by Discuz!</title>
<?=$seohead?>

<meta http-equiv="Content-Type" content="text/html; charset=<?=$charset?>">
<meta name="keywords" content="Discuz!,Board,Comsenz,forums,bulletin board,<?=$seokeywords?>">
<meta name="description" content="<?=$bbname?> <?=$seodescription?> - Discuz! Board">
<meta name="generator" content="Discuz! <?=$version?> with Templates 4.0.0">
<meta name="MSSmartTagsPreventParsing" content="TRUE">
<meta http-equiv="MSThemeCompatible" content="Yes">
<?=$extrahead?>
<? include template('css'); ?>
<script language="JavaScript" src="include/common.js"></script>
</head>

<body leftmargin="0" rightmargin="0" topmargin="0" onkeydown="if(event.keyCode==27) return false;">

<table bgcolor="<?=MAINTABLECOLOR?>" width="<?=MAINTABLEWIDTH?>" cellpadding="0" cellspacing="0" border="0" align="center">

<tr>
<td width="100%" background="<?=IMGDIR?>/topbg.gif">
<table border="0" cellspacing="0" cellpadding="0" width="<?=TABLEWIDTH?>" align="center" class="outertxt">

<tr>
<td rowspan="2" width="0"><img src="images/spacer.gif" width="0" height="0"></td>
<td rowspan="2" valign="top"><a href="index.php"><?=BOARDLOGO?></a></td><td height="80" align="right">&nbsp;
<? if(!empty($advlist['headerbanner'])) { ?>
<?=$advlist['headerbanner']?>
<? } ?>
</td>
</tr>

<tr>
<td align="right" class="smalltxt"><span class="bold">&raquo;</span>
<? if($discuz_uid) { ?>
<span class="bold"><?=$discuz_userss?>: </span> <a href="<?=$link_logout?>">退出</a>
<? if($maxpmnum) { ?>
| <a href="pm.php" target="_blank">短消息</a> 
<? } ?>
|  <a href="memcp.php">控制面板</a>
<? if(in_array($adminid, array(1,2,3))) { ?>
| <a href="admincp.php" target="_blank">系统设置</a> 
<? } } else { ?>
<span class="bold">游客: &nbsp;</span><a href="<?=$link_register?>">注册</a> 
| <a href="<?=$link_login?>">登录</a> 
<? } if($memliststatus) { ?>
| <a href="member.php?action=list">会员</a> 
<? } if($allowsearch || $qihoo_status) { ?>
| <a href="search.php">搜索</a> 
<? } if($allowviewstats) { ?>
| <a href="stats.php">统计</a> 
<? } if(is_array($plugins['links'])) { foreach($plugins['links'] as $plugin) { if(is_array($plugin)) { foreach($plugin as $module) { ?>
     
<? if(!$module['adminid'] || ($module['adminid'] && $adminid > 0 && $module['adminid'] >= $adminid)) { ?>
| <?=$module['url']?> 
<? } } } } } ?>
| <a href="faq.php">帮助</a>

</td><td rowspan="2" width="0"><img src="images/spacer.gif" width="0" height="0"></td>
</tr>

</table>
</td></tr></table>
<center>
<div class="maintable"><br>