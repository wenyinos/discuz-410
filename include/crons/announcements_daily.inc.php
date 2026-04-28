<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: announcements_daily.inc.php,v $
	$Revision: 1.2 $
	$Date: 2006/02/23 13:44:54 $
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$db->query("DELETE FROM {$tablepre}announcements WHERE endtime<'$timestamp' AND endtime<>'0'");

if($db->affected_rows()) {
	require_once DISCUZ_ROOT.'./include/cache.func.php';
	updatecache('announcements');
	updatecache('announcements_forum');
}

?>