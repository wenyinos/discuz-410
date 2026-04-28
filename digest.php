<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: digest.php,v $
	$Revision: 1.3 $
	$Date: 2006/02/23 13:44:02 $
*/

require_once './include/common.inc.php';
require_once DISCUZ_ROOT.'./include/misc.func.php';
require_once DISCUZ_ROOT.'./forumdata/cache/cache_forums.php';

$page = empty($page) || !ispage($page) || ($threadmaxpages && $page > $threadmaxpages) ? 1 : $page;
$start_limit = ($page - 1) * $tpp;

$forumsarray = array();
if(!empty($forums)) {
	foreach((is_array($forums) ? $forums : explode('_', $forums)) as $forum) {
		if($forum = intval(trim($forum))) {
			$forumsarray[] = $forum;
		}
	}
}

$fids = '0';
$forumlist = $forumcheck = array();
foreach($_DCACHE['forums'] as $fid => $forum) {
	if($forum['type'] != 'group' && (!$forum['viewperm'] && $readaccess) || ($forum['viewperm'] && forumperm($forum['viewperm']))) {
		$forumlist[] = array('fid' => $fid, 'name' => $forum['name']);
		if(!$forumsarray || in_array($fid, $forumsarray)) {
			$fids .= ','.$fid;
			$forumcheck[$fid] = 'checked';
		}
	}
}

if(!empty($author)) {
	$query = $db->query("SELECT uid FROM {$tablepre}members WHERE username='$author'");
	$authorid = ($member = $db->fetch_array($query)) ? $member['uid'] : 0;
}

$authoradd = !empty($authorid) ? "AND authorid='$authorid'" : '';
$keywordadd = !empty($keyword) ? "AND subject LIKE '%$keyword%'" : '';

$query = $db->query("SELECT COUNT(*) FROM {$tablepre}threads WHERE digest>'0' AND fid IN ($fids) AND displayorder>='0' $authoradd $keywordadd");
$threadcount = $db->result($query, 0);

if(!$threadcount) {
	showmessage('digest_nonexistence');
}

if(empty($order) || !in_array($order, array('dateline', 'lastpost', 'replies', 'views'))) {
	$order = 'digest';
}
$ordercheck = array($order => 'selected="selected"');

$forumsarray = $threadlist = array();
$query = $db->query("SELECT * FROM {$tablepre}threads WHERE digest>'0' AND fid IN ($fids) AND displayorder>='0' $authoradd $keywordadd ORDER BY $order DESC LIMIT $start_limit, $tpp");
while($thread = $db->fetch_array($query)) {
	$threadauthor = $thread['author'];
	$threadlist[] = procthread($thread);
}

$multipage = multi($threadcount, $tpp, $page, "digest.php?order=$order&keyword=".rawurlencode($keyword)."&authorid=$authorid&forums=".str_replace(',', '_', $fids), $threadmaxpages);
$keyword = dhtmlspecialchars($keyword);
$author = $authorid ? dhtmlspecialchars($threadauthor) : '';

include template('digest');

?>