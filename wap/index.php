<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: index.php,v $
	$Revision: 1.7 $
	$Date: 2006/02/23 13:44:54 $
*/

define('CURSCRIPT', 'wap');
require_once '../include/common.inc.php';
require_once './include/global.func.php';
@include_once(DISCUZ_ROOT.'./forumdata/cache/cache_forums.php');

$discuz_action = 191;

if($charset != 'utf-8') {
	require_once '../include/chinese.class.php';
}

$action = isset($action) ? $action : 'home';
if($action == 'goto' && !empty($url)) {
	header("Location: $url");
} else {
	wapheader($bbname);
}

include language('wap');

if(!$wapstatus) {
	wapmsg('wap_disabled');
} elseif($bbclosed) {
	wapmsg('board_closed');
}

$chs = '';
if($_POST && $charset != 'utf-8') {
	$chs = new Chinese('UTF-8', $charset);
	foreach($_POST as $key => $value) {
		$$key = $chs->Convert($value);
	}
	unset($chs);
}

if(in_array($action, array('home', 'login', 'stats', 'myphone', 'goto', 'forum', 'thread', 'post', 'pm'))) {
	require_once './include/'.$action.'.inc.php';
} else {
	wapmsg('undefined_action');
}

wapfooter();

?>
