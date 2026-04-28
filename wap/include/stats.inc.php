<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: stats.inc.php,v $
	$Revision: 1.3 $
	$Date: 2006/02/23 13:44:54 $
*/

if(!defined('IN_DISCUZ')) {
        exit('Access Denied');
}

$discuz_action = 194;

$members = $totalmembers;
$query = $db->query("SELECT SUM(threads) AS threads, SUM(posts) AS posts FROM {$tablepre}forums WHERE status='1'");
@extract($db->fetch_array($query));

echo "<p>$lang[stats]<br /><br />\n".
	"$lang[stats_members]: $members<br />\n".
	"$lang[stats_threads]: $threads<br />\n".
	"$lang[stats_posts]: $posts</p>\n";

?>