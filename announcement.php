<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: announcement.php,v $
	$Revision: 1.4 $
	$Date: 2006/02/23 13:44:02 $
*/

require_once './include/common.inc.php';
require_once DISCUZ_ROOT.'./include/discuzcode.func.php';

$discuz_action = 21;

if(isset($id)) {
	$count = 0;
	$query = $db->query("SELECT id, starttime, endtime FROM {$tablepre}announcements WHERE starttime<='$timestamp' ORDER BY displayorder, starttime DESC, id DESC");
	while($announce = $db->fetch_array($query)) {
		$count++;
		if(isset($id) && $announce['id'] == $id) {
			$page = ceil($total / $ppp);
		}
	}
}

$page = (empty($page) || !ispage($page)) && empty($id) ? 1 : max(1, $page);
$start_limit = ($page - 1) * $ppp;

$multipage = multi($total, $ppp, $page, 'announcement.php');

$announcelist = array();
$query = $db->query("SELECT * FROM {$tablepre}announcements WHERE starttime<='$timestamp' AND (endtime='0' OR endtime>'$timestamp') ORDER BY displayorder, starttime DESC, id DESC LIMIT $start_limit, $ppp");

if((!empty($id) && !$page) || !$db->num_rows($query)) {
	showmessage('announcement_nonexistence');
}

while($announce = $db->fetch_array($query)) {
	$announce['authorenc'] = rawurlencode($announce['author']);
	$announce['starttime'] = gmdate($dateformat, $announce['starttime'] + $timeoffset * 3600);
	$announce['endtime'] = $announce['endtime'] ? gmdate($dateformat, $announce['endtime'] + $timeoffset * 3600) : '';
	$announce['message'] = nl2br(discuzcode($announce['message'], 0, 0, 1, 1, 1, 1, 1));

	$announcelist[] = $announce;
}

include template('announcement');

?>