<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: topic.php,v $
	$Revision: 1.7 $
	$Date: 2006/02/24 09:08:23 $
*/

require_once './include/common.inc.php';

if(empty($keyword)) {
	showmessage('undefined_action');
}

$tpp = intval($tpp);
$page = empty($page) || !ispage($page) ? 1 : $page;
$start = ($page - 1) * $tpp;

$site = site();
$length = intval($length);

$keyword = dhtmlspecialchars(stripslashes($keyword));
$topic = $topic ? dhtmlspecialchars(stripslashes($topic)) : $keyword;

include template('topic');

?>