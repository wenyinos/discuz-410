<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: forum.inc.php,v $
	$Revision: 1.3 $
	$Date: 2006/02/23 13:44:54 $
*/

if(!defined('IN_DISCUZ')) {
        exit('Access Denied');
}

$discuz_action = 192;

if(isset($fup)) {

	$forums = $subforums = array();
	foreach($_DCACHE['forums'] as $fid => $forum) {
		if($forum['type'] != 'group' && $forum['fup'] == $fup) {
			$forums[$fid] = $forum;
		} elseif($forum['type'] == 'sub' && $_DCACHE['forums'][$forum['fup']]['fup'] == $fup) {
			$subforums[$forum['fup']]++;
		}
	}

	echo "<p>\n";
	foreach($forums as $fid => $forum) {
		echo "<a href=\"index.php?action=forum&amp;fid=$fid\">".dhtmlspecialchars($forum['name'])."</a>".
			($subforums[$fid] ? "<br />&nbsp;+[<a href=\"index.php?action=forum&amp;fup=$fid\">$subforums[$fid]$lang[sub_forums]</a>]" : '')."<br />\n";
	}
	echo "</p>\n";

} elseif(!empty($fid)) {

	require_once DISCUZ_ROOT.'./include/forum.func.php';

	if(empty($forum)) {
		wapmsg('forum_nonexistence');
	}

	if(($forum['viewperm'] && !forumperm($forum['viewperm']) && !$forum['allowview']) || $forum['redirect'] || $forum['password']) {
		wapmsg('forum_nopermission');
	}

	$page = empty($page) || !ispage($page) ? 1 : $page;
	$start_limit = $number = ($page - 1) * $waptpp;

	$query = $db->query("SELECT * FROM {$tablepre}threads
		WHERE fid='$fid' AND displayorder>='0'
		ORDER BY displayorder DESC, lastpost DESC LIMIT $start_limit, $waptpp");
	while($thread = $db->fetch_array($query)) {
		$thread['prefix'] = $thread['displayorder'] > 0 ? $lang['forum_thread_sticky'] : ($thread['digest'] ? $lang['forum_thread_digest'] : '');
		echo "<p><a href=\"index.php?action=thread&amp;tid=$thread[tid]\">#".++$number." $thread[prefix]".cutstr($thread['subject'], 30)."</a><br />\n".
			"&nbsp; <small>".gmdate("$wapdateformat $timeformat", $thread['lastpost'] + $timeoffset * 3600)."<br />\n".
			"&nbsp; $thread[lastposter]</small></p>\n";
	}

	echo "<p><br />$lang[page] $page ".
		($start_limit + $waptpp < $forum['threads'] ? "<a href=\"index.php?action=forum&amp;fid=$fid&amp;page=".($page + 1)."\">&gt;&gt;$lang[next_page]</a>" : $lang['end'])."<br />\n".
		($discuz_uid ? "<a href=\"index.php?action=post&amp;do=newthread&amp;fid=$fid\">$lang[post_new]</a><br />\n" : '').
		"<br />$lang[forum]:<a href=\"index.php?action=forum&amp;fid=$fid\">".dhtmlspecialchars(cutstr(strip_tags($forum['name']), 20))."</a></p>\n";
}

?>