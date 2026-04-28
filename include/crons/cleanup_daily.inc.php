<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: cleanup_daily.inc.php,v $
	$Revision: 1.6 $
	$Date: 2006/02/23 13:44:54 $
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$db->query("UPDATE {$tablepre}advertisements SET available='0' WHERE endtime>'0' AND endtime<='$timestamp'", 'UNBUFFERED');
$db->query("DELETE FROM {$tablepre}searchindex WHERE expiration<'$timestamp'", 'UNBUFFERED');
$db->query("DELETE FROM {$tablepre}threadsmod WHERE dateline<'$timestamp'-31536000", 'UNBUFFERED');
$db->query("DELETE FROM {$tablepre}subscriptions WHERE lastpost<'$timestamp'-7776000", 'UNBUFFERED');

if($qihoo_status && $qihoo_relatedthreads) {
	$db->query("DELETE FROM {$tablepre}relatedthreads WHERE expiration<'$timestamp'", 'UNBUFFERED');
}

?>