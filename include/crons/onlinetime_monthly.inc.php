<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: onlinetime_monthly.inc.php,v $
	$Revision: 1.3 $
	$Date: 2006/02/23 13:44:54 $
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$db->query("UPDATE {$tablepre}onlinetime SET thismonth='0'");
$db->query("UPDATE {$tablepre}statvars SET value='0' WHERE type='onlines' AND variable='lastupdate'");

?>