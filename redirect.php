<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: redirect.php,v $
	$Revision: 1.3 $
	$Date: 2006/02/23 13:44:02 $
*/

define('CURSCRIPT', 'viewthread');

require_once './include/common.inc.php';

if(isset($fid) && empty($forum)) {
	showmessage('forum_nonexistence', NULL, 'HALTED');
}

@include DISCUZ_ROOT.'./forumdata/cache/cache_viewthread.php';

if($goto == 'lastpost') {

	if($tid) {
		$query = $db->query("SELECT tid, replies FROM {$tablepre}threads WHERE tid='$tid' AND displayorder>='0'");
	} else {
		$query = $db->query("SELECT tid, replies FROM {$tablepre}threads WHERE fid='$fid' AND displayorder>='0' ORDER BY lastpost DESC LIMIT 1");
	}
	if(!$thread = $db->fetch_array($query)) {
		showmessage('thread_nonexistence');
	}
	$page = ceil(($thread['replies'] + 1) / $ppp);
	$tid = $thread['tid'];

	require_once DISCUZ_ROOT.'./viewthread.php';
	exit();

} elseif($goto == 'newpost') {

	$query = $db->query("SELECT COUNT(*) FROM {$tablepre}posts WHERE tid='$tid' AND dateline<='$lastvisit'");
	$page = ceil($db->result($query, 0) / $ppp);

	require_once DISCUZ_ROOT.'./viewthread.php';
	exit();

} elseif($goto == 'nextnewset') {

	if($fid && $tid) {
		$query = $db->query("SELECT lastpost FROM {$tablepre}threads WHERE tid='$tid' AND displayorder>='0'");
		$this_lastpost = $db->result($query, 0);
		$query = $db->query("SELECT tid FROM {$tablepre}threads WHERE fid='$fid' AND displayorder>='0' AND lastpost>'$this_lastpost' ORDER BY lastpost ASC LIMIT 1");
		if($next = $db->fetch_array($query)) {
			$tid = $next['tid'];
			require_once DISCUZ_ROOT.'./viewthread.php';
			exit();
		} else {
			showmessage('redirect_nextnewset_nonexistence');
		}
	} else {
		showmessage('undefined_action', NULL, 'HALTED');
	}

} elseif($goto == 'nextoldset') {

	if($fid && $tid) {
		$query = $db->query("SELECT lastpost FROM {$tablepre}threads WHERE tid='$tid' AND displayorder>='0'");
		$this_lastpost = $db->result($query, 0);
		$query = $db->query("SELECT tid FROM {$tablepre}threads WHERE fid='$fid' AND displayorder>='0' AND lastpost<'$this_lastpost' ORDER BY lastpost DESC LIMIT 1");
		if($last = $db->fetch_array($query)) {
			$tid = $last['tid'];
			require_once DISCUZ_ROOT.'./viewthread.php';
			exit();
		} else {
			showmessage('redirect_nextoldset_nonexistence');
		}
	} else {
		showmessage('undefined_action', NULL, 'HALTED');
	}

} else {

	showmessage('undefined_action', NULL, 'HALTED');

}

?>